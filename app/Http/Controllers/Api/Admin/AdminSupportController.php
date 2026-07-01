<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSupportStatusRequest;
use App\Models\SupportRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSupportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SupportRequest::with([
            'report:tracking_id,id',
            'supportType:id,name',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $requests = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(fn ($r) => [
                'id' => $r->id,
                'tracking_id' => $r->report?->tracking_id,
                'support_type' => $r->supportType?->name,
                'description' => $r->description,
                'status' => $r->status,
                'admin_notes' => $r->admin_notes,
                'created_at' => $r->created_at->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $requests->items(),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'total' => $requests->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $support = SupportRequest::with([
            'report:id,tracking_id,description',
            'supportType:id,name',
            'assignedTo:id,name',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $support->id,
                'tracking_id' => $support->report?->tracking_id,
                'report_description' => $support->report?->description,
                'support_type' => $support->supportType?->name,
                'description' => $support->description,
                'status' => $support->status,
                'admin_notes' => $support->admin_notes,
                'assigned_to' => $support->assignedTo?->name,
                'created_at' => $support->created_at->toIso8601String(),
                'updated_at' => $support->updated_at->toIso8601String(),
            ],
        ]);
    }

    public function updateStatus(UpdateSupportStatusRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $support = SupportRequest::findOrFail($id);
        $support->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $support->admin_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status permohonan berhasil diperbarui.',
        ]);
    }
}
