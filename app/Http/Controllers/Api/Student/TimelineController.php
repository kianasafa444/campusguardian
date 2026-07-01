<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class TimelineController extends Controller
{
    public function index(string $trackingId): JsonResponse
    {
        $report = Report::where('tracking_id', $trackingId)->firstOrFail();

        $timeline = $report->visibleTimeline()
            ->get(['previous_status', 'new_status', 'note', 'is_admin_note', 'created_at'])
            ->map(fn ($entry) => [
                'status_from' => $entry->previous_status,
                'status_to' => $entry->new_status,
                'note' => $entry->note,
                'is_system' => $entry->note === null,
                'timestamp' => $entry->created_at->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $timeline,
        ]);
    }
}
