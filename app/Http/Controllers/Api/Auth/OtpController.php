<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\StudentVerification;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService
    ) {}

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $email = $request->input('email');
        $domain = substr(strrchr($email, '@'), 1);

        $verification = $this->otpService->findOrCreateVerification($email);

        if ($verification->is_verified && $verification->verified_at && $verification->verified_at->gt(now()->subHours(24))) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi verifikasi masih aktif.',
                'data' => [
                    'verification_token' => $verification->verification_token,
                    'session_active' => true,
                    'expires_at' => $verification->verified_at->addHours(24)->toIso8601String(),
                ],
            ]);
        }

        if ($verification->is_verified) {
            $this->otpService->resetVerification($verification);
        }

        if ($verification->otp_expires_at && $verification->otp_expires_at->diffInSeconds(now()) < 60) {
            return response()->json([
                'success' => false,
                'message' => 'Tunggu 60 detik sebelum mengirim ulang OTP.',
                'data' => [
                    'cooldown_until' => $verification->otp_expires_at->addSeconds(60)->toIso8601String(),
                ],
            ], 429);
        }

        $token = $this->otpService->regenerateToken($verification);
        $otp = $this->otpService->generateOtp();
        $otpHash = $this->otpService->hashOtp($otp);

        $verification->update([
            'verification_token' => $token,
            'otp_hash' => $otpHash,
            'otp_attempts' => 0,
            'otp_expires_at' => now()->addMinutes(5),
            'ip_address_hash' => hash('sha256', $request->ip()),
            'user_agent_hash' => hash('sha256', $request->userAgent() ?? ''),
        ]);

        Mail::raw(
            "Kode OTP kamu adalah: {$otp}\n\nKode berlaku selama 5 menit.\nJangan bagikan kode ini kepada siapa pun.",
            fn ($msg) => $msg->to($email)->subject('Kode OTP CampusGuardian')
        );

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirim ke email kamu.',
            'data' => [
                'verification_token' => $token,
                'cooldown_until' => now()->addSeconds(60)->toIso8601String(),
            ],
        ]);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $verification = StudentVerification::where(
            'verification_token',
            $request->input('verification_token')
        )->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Token verifikasi tidak valid.',
            ], 404);
        }

        if ($verification->is_verified) {
            return response()->json([
                'success' => true,
                'message' => 'Email sudah terverifikasi.',
                'data' => [
                    'is_verified' => true,
                    'expires_at' => $verification->verified_at->addHours(24)->toIso8601String(),
                ],
            ]);
        }

        if (!$verification->otp_expires_at || $this->otpService->isExpired($verification->otp_expires_at->toIso8601String())) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kadaluarsa. Silakan kirim ulang OTP.',
            ], 403);
        }

        if ($this->otpService->isAttemptsExceeded($verification->otp_attempts, $verification->max_attempts)) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan. Silakan kirim ulang OTP.',
            ], 403);
        }

        $otp = (int) $request->input('otp');

        if (!$this->otpService->verifyOtp($otp, $verification->otp_hash)) {
            $verification->increment('otp_attempts');
            $remaining = $verification->max_attempts - $verification->otp_attempts;

            return response()->json([
                'success' => false,
                'message' => $remaining > 0
                    ? "Kode OTP salah. Sisa percobaan: {$remaining}."
                    : 'Terlalu banyak percobaan. Silakan kirim ulang OTP.',
                'errors' => ['otp' => ['Kode yang kamu masukkan salah.']],
            ], 422);
        }

        $verification->update([
            'is_verified' => true,
            'verified_at' => now(),
            'otp_hash' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diverifikasi.',
            'data' => [
                'is_verified' => true,
                'expires_at' => now()->addHours(24)->toIso8601String(),
            ],
        ]);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'verification_token' => ['required', 'string', 'exists:student_verifications,verification_token'],
        ]);

        $verification = StudentVerification::where(
            'verification_token',
            $request->input('verification_token')
        )->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Token verifikasi tidak valid.',
            ], 404);
        }

        if ($verification->is_verified && $verification->verified_at && $verification->verified_at->gt(now()->subHours(24))) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi verifikasi masih aktif.',
                'data' => [
                    'verification_token' => $verification->verification_token,
                    'session_active' => true,
                    'expires_at' => $verification->verified_at->addHours(24)->toIso8601String(),
                ],
            ]);
        }

        if ($verification->is_verified) {
            $this->otpService->resetVerification($verification);
        }

        if ($verification->otp_expires_at && $verification->otp_expires_at->diffInSeconds(now()) < 60) {
            return response()->json([
                'success' => false,
                'message' => 'Tunggu 60 detik sebelum mengirim ulang OTP.',
                'data' => [
                    'cooldown_until' => $verification->otp_expires_at->addSeconds(60)->toIso8601String(),
                ],
            ], 429);
        }

        $otp = $this->otpService->generateOtp();
        $otpHash = $this->otpService->hashOtp($otp);

        $verification->update([
            'otp_hash' => $otpHash,
            'otp_attempts' => 0,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        Mail::raw(
            "Kode OTP kamu adalah: {$otp}\n\nKode berlaku selama 5 menit.\nJangan bagikan kode ini kepada siapa pun.",
            fn ($msg) => $msg->to($verification->email)->subject('Kode OTP CampusGuardian')
        );

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirim ulang.',
            'data' => [
                'cooldown_until' => now()->addSeconds(60)->toIso8601String(),
            ],
        ]);
    }
}
