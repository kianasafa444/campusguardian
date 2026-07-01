<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateReportStatusRequest;
use App\Models\Report;
use App\Models\ReportEvidence;
use App\Models\ReportTimeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Report::with('category:id,name');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('category_id')) {
            $query->where('incident_category_id', $request->input('category_id'));
        }

        if ($request->filled('search')) {
            $query->where('tracking_id', 'like', '%' . $request->input('search') . '%');
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(fn ($r) => [
                'id' => $r->id,
                'tracking_id' => $r->tracking_id,
                'category' => $r->category?->name,
                'severity' => $r->severity,
                'status' => $r->status,
                'submitted_at' => $r->submitted_at->toIso8601String(),
            ]);

        return response()->json([
            'success' => true,
            'data' => $reports->items(),
            'meta' => [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'total' => $reports->total(),
            ],
        ]);
    }

    public function show(string $trackingId): JsonResponse
    {
        $report = Report::with([
            'category:id,name',
            'evidences:id,report_id,file_name,file_path,file_type,file_size,mime_type',
            'timeline' => fn ($q) => $q->with('actionBy:id,name'),
            'supportRequests' => fn ($q) => $q->with('supportType:id,name'),
        ])->where('tracking_id', $trackingId)->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $report->id,
                'tracking_id' => $report->tracking_id,
                'category' => $report->category?->name,
                'severity' => $report->severity,
                'description' => $report->description,
                'location' => $report->location,
                'incident_date' => $report->incident_date?->toIso8601String(),
                'status' => $report->status,
                'created_at' => $report->created_at->toIso8601String(),
                'submitted_at' => $report->submitted_at->toIso8601String(),
                'resolved_at' => $report->resolved_at?->toIso8601String(),
                'evidences' => $report->evidences->map(fn ($e) => [
                    'id' => $e->id,
                    'file_name' => $e->file_name,
                    'file_path' => $e->file_path,
                    'file_type' => $e->file_type,
                    'file_size' => $e->file_size,
                    'mime_type' => $e->mime_type,
                    'download_url' => url("/api/admin/evidences/{$e->id}/download"),
                    'stream_url' => url("/api/admin/evidences/{$e->id}/stream"),
                ]),
                'recent_timeline' => $report->timeline->map(fn ($t) => [
                    'id' => $t->id,
                    'previous_status' => $t->previous_status,
                    'new_status' => $t->new_status,
                    'note' => $t->note,
                    'is_admin_note' => $t->is_admin_note,
                    'action_by' => $t->actionBy?->name,
                    'created_at' => $t->created_at->toIso8601String(),
                ]),
                'support_requests' => $report->supportRequests->map(fn ($s) => [
                    'id' => $s->id,
                    'type' => $s->supportType?->name,
                    'status' => $s->status,
                    'description' => $s->description,
                    'created_at' => $s->created_at->toIso8601String(),
                ]),
            ],
        ]);
    }

    public function updateStatus(UpdateReportStatusRequest $request, string $trackingId): JsonResponse
    {
        $validated = $request->validated();

        $report = Report::where('tracking_id', $trackingId)->firstOrFail();
        $previousStatus = $report->status;

        $report->update([
            'status' => $validated['status'],
            'resolved_at' => in_array($validated['status'], ['Resolved', 'Closed', 'Rejected']) ? now() : $report->resolved_at,
        ]);

        ReportTimeline::create([
            'report_id' => $report->id,
            'previous_status' => $previousStatus,
            'new_status' => $validated['status'],
            'note' => $validated['note'] ?? null,
            'is_admin_note' => true,
            'action_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status laporan berhasil diperbarui.',
        ]);
    }

    public function downloadEvidence(int $id): StreamedResponse|JsonResponse
    {
        $evidence = ReportEvidence::findOrFail($id);

        if (!Storage::disk('local')->exists($evidence->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.',
            ], 404);
        }

        return Storage::disk('local')->download($evidence->file_path, $evidence->file_name);
    }

    public function streamEvidence(int $id): StreamedResponse|JsonResponse
    {
        $evidence = ReportEvidence::findOrFail($id);

        if (!Storage::disk('local')->exists($evidence->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.',
            ], 404);
        }

        $mime = $evidence->mime_type ?? 'application/octet-stream';

        return Storage::disk('local')->response($evidence->file_path, $evidence->file_name, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $evidence->file_name . '"',
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}
