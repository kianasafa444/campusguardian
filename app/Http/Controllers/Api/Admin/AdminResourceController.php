<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreResourceRequest;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminResourceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Resource::with('category:id,name');

        if ($request->filled('resource_category_id')) {
            $query->where('resource_category_id', $request->input('resource_category_id'));
        }

        $resources = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(fn ($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'slug' => $r->slug,
                'type' => $r->type,
                'category' => $r->category?->name,
                'is_published' => $r->is_published,
                'published_at' => $r->published_at?->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $resources->items(),
            'meta' => [
                'current_page' => $resources->currentPage(),
                'last_page' => $resources->lastPage(),
                'total' => $resources->total(),
            ],
        ]);
    }

    public function store(StoreResourceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $resource = Resource::create([
            'resource_category_id' => $validated['resource_category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(4),
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'type' => $validated['type'],
            'is_published' => $validated['is_published'] ?? false,
            'published_at' => ($validated['is_published'] ?? false) ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resource berhasil dibuat.',
            'data' => ['id' => $resource->id, 'slug' => $resource->slug],
        ], 201);
    }

    public function update(StoreResourceRequest $request, int $id): JsonResponse
    {
        $resource = Resource::findOrFail($id);

        $validated = $request->validated();

        $resource->update([
            'resource_category_id' => $validated['resource_category_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? null,
            'type' => $validated['type'],
            'is_published' => $validated['is_published'] ?? $resource->is_published,
            'published_at' => ($validated['is_published'] ?? false) && !$resource->published_at ? now() : $resource->published_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resource berhasil diperbarui.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $resource = Resource::findOrFail($id);
        $resource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource berhasil dihapus.',
        ]);
    }
}
