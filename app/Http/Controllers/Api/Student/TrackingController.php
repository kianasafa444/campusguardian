<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackReportRequest;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    private const array PROGRESS_STEPS = [
        'Submitted', 'Under Review', 'Investigation', 'Action Taken', 'Resolved',
    ];

    public function track(TrackReportRequest $request): JsonResponse
    {

        $report = Report::where('tracking_id', $request->input('tracking_id'))
            ->with('category')
            ->with('evidences')
            ->first();

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan. Periksa kembali Tracking ID kamu.',
            ], 404);
        }

        $currentIndex = array_search($report->status, self::PROGRESS_STEPS);
        if ($currentIndex === false) {
            $currentIndex = -1;
        }

        $progress = [];
        foreach (self::PROGRESS_STEPS as $i => $step) {
            $progress[] = [
                'status' => $step,
                'completed' => $i <= $currentIndex,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tracking_id' => $report->tracking_id,
                'status' => $report->status,
                'severity' => $report->severity,
                'category' => $report->category?->name,
                'submitted_at' => $report->submitted_at->toIso8601String(),
                'progress' => $progress,
                'evidences' => $report->evidences->map(fn ($e) => [
                    'id' => $e->id,
                    'file_name' => $e->file_name,
                    'file_type' => $e->file_type,
                    'file_size' => $e->file_size,
                    'mime_type' => $e->mime_type,
                    'stream_url' => url("/api/evidences/{$e->id}/stream?tracking_id={$report->tracking_id}"),
                ]),
            ],
        ]);
    }
}
