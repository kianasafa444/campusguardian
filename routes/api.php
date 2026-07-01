<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminReportController;
use App\Http\Controllers\Api\Admin\AdminResourceController;
use App\Http\Controllers\Api\Admin\AdminSupportController;
use App\Http\Controllers\Api\Admin\AdminTimelineController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\Student\FeedbackController;
use App\Http\Controllers\Api\Student\ReportController;
use App\Http\Controllers\Api\Student\ReportEvidenceController;
use App\Http\Controllers\Api\Student\ResourceController;
use App\Http\Controllers\Api\Student\SupportController;
use App\Http\Controllers\Api\Student\TimelineController;
use App\Http\Controllers\Api\Student\TrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'CampusGuardian API',
        'version' => '1.0',
    ]);
});

// OTP Verification
Route::post('/auth/send-otp', [OtpController::class, 'sendOtp'])
    ->middleware('throttle:3,1');

Route::post('/auth/verify-otp', [OtpController::class, 'verifyOtp'])
    ->middleware('throttle:5,1');

Route::post('/auth/resend-otp', [OtpController::class, 'resendOtp'])
    ->middleware('throttle:3,1');

// Student (verified) endpoints
Route::middleware(['student.verified'])->group(function () {
    Route::get('/categories', [ReportController::class, 'categories']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::post('/reports/{trackingId}/evidences', [ReportEvidenceController::class, 'store']);
    Route::delete('/reports/{trackingId}/evidences/{evidenceId}', [ReportEvidenceController::class, 'destroy']);
    Route::post('/support-requests', [SupportController::class, 'store']);
});

// Public tracking & timeline
Route::post('/tracking', [TrackingController::class, 'track'])
    ->middleware('throttle:10,60');

Route::get('/tracking/{trackingId}/timeline', [TimelineController::class, 'index']);
Route::get('/support-requests/{trackingId}', [SupportController::class, 'index']);
Route::post('/feedback', [FeedbackController::class, 'store']);

// Public resource center
Route::get('/resource-categories', [ResourceController::class, 'categories']);
Route::get('/resources', [ResourceController::class, 'resources']);
Route::get('/resources/{slug}', [ResourceController::class, 'resourceDetail']);
Route::get('/emergency-contacts', [ResourceController::class, 'emergencyContacts']);
Route::get('/faq', [ResourceController::class, 'faq']);

// Admin endpoints (sanctum auth)
Route::post('/admin/auth/login', [AdminAuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    Route::post('/auth/logout', [AdminAuthController::class, 'logout']);
    Route::get('/auth/me', [AdminAuthController::class, 'me']);

    Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats']);

    Route::get('/reports', [AdminReportController::class, 'index']);
    Route::get('/reports/{trackingId}', [AdminReportController::class, 'show']);
    Route::put('/reports/{trackingId}/status', [AdminReportController::class, 'updateStatus']);
    Route::get('/evidences/{id}/stream', [AdminReportController::class, 'streamEvidence']);

    Route::get('/reports/{trackingId}/timeline', [AdminTimelineController::class, 'index']);
    Route::post('/reports/{trackingId}/timeline', [AdminTimelineController::class, 'store']);

    Route::get('/support-requests', [AdminSupportController::class, 'index']);
    Route::get('/support-requests/{id}', [AdminSupportController::class, 'show']);
    Route::put('/support-requests/{id}/status', [AdminSupportController::class, 'updateStatus']);

    Route::get('/resources', [AdminResourceController::class, 'index']);
    Route::post('/resources', [AdminResourceController::class, 'store']);
    Route::put('/resources/{id}', [AdminResourceController::class, 'update']);
    Route::delete('/resources/{id}', [AdminResourceController::class, 'destroy']);
});

// Evidence download (protected by sanctum)
Route::middleware(['auth:sanctum'])->get('/admin/evidences/{id}/download', [\App\Http\Controllers\Api\Admin\AdminReportController::class, 'downloadEvidence']);

// Public evidence stream (protected by tracking_id query param)
Route::get('/evidences/{id}/stream', [\App\Http\Controllers\Api\Student\ReportEvidenceController::class, 'publicStream']);
