<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    public function store(StoreFeedbackRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $report = Report::where('tracking_id', $validated['tracking_id'])->firstOrFail();

        $exists = Feedback::where('report_id', $report->id)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah memberikan feedback untuk laporan ini.',
            ], 409);
        }

        $feedback = Feedback::create([
            'report_id' => $report->id,
            'rating' => $validated['rating'],
            'satisfaction_level' => $validated['satisfaction_level'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback berhasil dikirim. Terima kasih!',
            'data' => [
                'id' => $feedback->id,
                'rating' => $feedback->rating,
            ],
        ], 201);
    }
}
