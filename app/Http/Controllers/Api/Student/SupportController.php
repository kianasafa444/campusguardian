<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupportRequest;
use App\Models\Report;
use App\Models\SupportRequest;
use Illuminate\Http\JsonResponse;

class SupportController extends Controller
{
    public function store(StoreSupportRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $report = Report::where('tracking_id', $validated['tracking_id'])->firstOrFail();

        $support = SupportRequest::create([
            'report_id' => $report->id,
            'support_type_id' => $validated['support_type_id'],
            'description' => $validated['description'],
            'status' => 'Pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan dukungan berhasil dikirim.',
            'data' => [
                'id' => $support->id,
                'status' => $support->status,
            ],
        ], 201);
    }

    public function index(string $trackingId): JsonResponse
    {
        $report = Report::where('tracking_id', $trackingId)->firstOrFail();

        $requests = $report->supportRequests()
            ->with('supportType:id,name')
            ->get(['id', 'support_type_id', 'description', 'status', 'admin_notes', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $requests->map(fn ($r) => [
                'id' => $r->id,
                'support_type' => $r->supportType?->name,
                'description' => $r->description,
                'status' => $r->status,
                'admin_notes' => $r->admin_notes,
                'created_at' => $r->created_at->toIso8601String(),
            ]),
        ]);
    }
}
