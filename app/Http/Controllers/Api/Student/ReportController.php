<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Models\IncidentCategory;
use App\Models\Report;
use App\Models\ReportTimeline;
use App\Models\StudentVerification;
use App\Services\SeverityService;
use App\Services\TrackingIdService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly TrackingIdService $trackingIdService,
        private readonly SeverityService $severityService,
    ) {}

    public function categories(): JsonResponse
    {
        $categories = IncidentCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'description']);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function store(StoreReportRequest $request): JsonResponse
    {
        /** @var StudentVerification $verification */
        $verification = $request->input('student_verification');

        $severity = $this->severityService->determineByCategory(
            (int) $request->input('incident_category_id')
        );

        $trackingId = $this->trackingIdService->generate();

        $report = Report::create([
            'tracking_id' => $trackingId,
            'incident_category_id' => $request->input('incident_category_id'),
            'severity' => $severity,
            'description' => $request->input('description'),
            'location' => $request->input('location'),
            'incident_date' => $request->input('incident_date'),
            'status' => 'Submitted',
            'student_verification_id' => $verification->id,
            'ip_address_hash' => hash('sha256', $request->ip()),
            'user_agent_hash' => hash('sha256', $request->userAgent() ?? ''),
            'submitted_at' => now(),
        ]);

        ReportTimeline::create([
            'report_id' => $report->id,
            'previous_status' => null,
            'new_status' => 'Submitted',
            'is_admin_note' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim.',
            'data' => [
                'tracking_id' => $report->tracking_id,
                'status' => $report->status,
                'severity' => $report->severity,
                'submitted_at' => $report->submitted_at->toIso8601String(),
            ],
        ], 201);
    }
}
