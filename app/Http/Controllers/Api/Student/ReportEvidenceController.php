<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvidenceRequest;
use App\Models\Report;
use App\Models\ReportEvidence;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportEvidenceController extends Controller
{
    private const array ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/webm', 'video/quicktime',
        'audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/mp4', 'audio/aac', 'audio/ogg', 'audio/webm',
        'application/pdf',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    private const array MIME_TO_TYPE = [
        'image/jpeg' => 'image', 'image/png' => 'image', 'image/gif' => 'image', 'image/webp' => 'image',
        'video/mp4' => 'video', 'video/webm' => 'video', 'video/quicktime' => 'video',
        'audio/mpeg' => 'audio', 'audio/wav' => 'audio', 'audio/x-wav' => 'audio', 'audio/mp4' => 'audio', 'audio/aac' => 'audio', 'audio/ogg' => 'audio', 'audio/webm' => 'audio',
        'application/pdf' => 'pdf',
        'application/msword' => 'document',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'document',
    ];

    public function store(StoreEvidenceRequest $request, string $trackingId): JsonResponse
    {
        $report = Report::where('tracking_id', $trackingId)->firstOrFail();

        $existingCount = ReportEvidence::where('report_id', $report->id)->count();
        if ($existingCount >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal 5 file bukti per laporan.',
            ], 422);
        }

        $file = $request->file('evidence');
        $mime = $file->getMimeType();

        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs("evidences/{$report->id}", $fileName, 'local');

        $evidence = ReportEvidence::create([
            'report_id' => $report->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => self::MIME_TO_TYPE[$mime] ?? 'document',
            'file_size' => $file->getSize(),
            'mime_type' => $mime,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File bukti berhasil diupload.',
            'data' => [
                'id' => $evidence->id,
                'file_name' => $evidence->file_name,
                'file_type' => $evidence->file_type,
                'file_size' => $evidence->file_size,
            ],
        ], 201);
    }

    public function destroy(string $trackingId, int $evidenceId): JsonResponse
    {
        $report = Report::where('tracking_id', $trackingId)->firstOrFail();
        $evidence = ReportEvidence::where('id', $evidenceId)
            ->where('report_id', $report->id)
            ->firstOrFail();

        Storage::disk('local')->delete($evidence->file_path);
        $evidence->delete();

        return response()->json([
            'success' => true,
            'message' => 'File bukti berhasil dihapus.',
        ]);
    }

    public function publicStream(int $id): StreamedResponse|JsonResponse
    {
        $evidence = ReportEvidence::with('report')->findOrFail($id);

        $trackingId = request()->query('tracking_id');
        if (!$trackingId || $evidence->report->tracking_id !== $trackingId) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.',
            ], 404);
        }

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
