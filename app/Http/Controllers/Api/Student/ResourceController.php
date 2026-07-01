<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function categories(): JsonResponse
    {
        $categories = ResourceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'description', 'icon']);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function resources(Request $request): JsonResponse
    {
        $query = Resource::where('is_published', true)
            ->select(['id', 'resource_category_id', 'title', 'slug', 'excerpt', 'type', 'published_at']);

        if ($request->has('resource_category_id')) {
            $query->where('resource_category_id', $request->input('resource_category_id'));
        }

        $resources = $query->with('category:id,name,slug')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

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

    public function resourceDetail(string $slug): JsonResponse
    {
        $resource = Resource::where('slug', $slug)
            ->where('is_published', true)
            ->with('category:id,name,slug')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $resource->id,
                'title' => $resource->title,
                'slug' => $resource->slug,
                'content' => $resource->content,
                'excerpt' => $resource->excerpt,
                'type' => $resource->type,
                'category' => $resource->category?->name,
                'resource_category' => $resource->category ? ['id' => $resource->category->id, 'name' => $resource->category->name, 'slug' => $resource->category->slug] : null,
                'created_at' => $resource->created_at->toIso8601String(),
                'published_at' => $resource->published_at?->toIso8601String(),
            ],
        ]);
    }

    public function emergencyContacts(): JsonResponse
    {
        $contacts = Resource::where('is_published', true)
            ->where('type', 'contact')
            ->get(['id', 'title', 'content', 'excerpt']);

        return response()->json([
            'success' => true,
            'data' => $contacts,
        ]);
    }

    public function faq(): JsonResponse
    {
        $faqs = Resource::where('is_published', true)
            ->where('type', 'faq')
            ->get(['id', 'title', 'content']);

        return response()->json([
            'success' => true,
            'data' => $faqs,
        ]);
    }
}
