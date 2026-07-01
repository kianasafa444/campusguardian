<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTimelineNoteRequest;
use App\Models\Report;
use App\Models\ReportTimeline;
use Illuminate\Http\JsonResponse;

class AdminTimelineController extends Controller
{
    public function index(string $trackingId): JsonResponse
    {
        $report = Report::where('tracking_id', $trackingId)->firstOrFail();

        $timeline = $report->timeline()
            ->with('actionBy:id,name')
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'previous_status' => $t->previous_status,
                'new_status' => $t->new_status,
                'note' => $t->note,
                'is_admin_note' => $t->is_admin_note,
                'action_by' => $t->actionBy?->name,
                'created_at' => $t->created_at->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $timeline,
        ]);
    }

    public function store(StoreTimelineNoteRequest $request, string $trackingId): JsonResponse
    {
        $validated = $request->validated();

        $report = Report::where('tracking_id', $trackingId)->firstOrFail();

        $entry = ReportTimeline::create([
            'report_id' => $report->id,
            'note' => $validated['note'],
            'is_admin_note' => $validated['is_admin_note'] ?? true,
            'action_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil ditambahkan.',
            'data' => [
                'id' => $entry->id,
                'note' => $entry->note,
                'created_at' => $entry->created_at->toIso8601String(),
            ],
        ], 201);
    }
}
