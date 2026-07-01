<?php

namespace App\Services;

use App\Models\StudentVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OtpService
{
    public function generateOtp(): int
    {
        return random_int(100000, 999999);
    }

    public function hashOtp(int $otp): string
    {
        return Hash::make((string) $otp);
    }

    public function verifyOtp(int $otp, string $hash): bool
    {
        return Hash::check((string) $otp, $hash);
    }

    public function isExpired(string $expiresAt): bool
    {
        return now()->gt($expiresAt);
    }

    public function isAttemptsExceeded(int $attempts, int $max): bool
    {
        return $attempts >= $max;
    }

    public function findOrCreateVerification(string $email): StudentVerification
    {
        $domain = substr(strrchr($email, '@'), 1);

        return StudentVerification::firstOrCreate(
            ['email' => $email],
            [
                'email_domain' => $domain,
                'verification_token' => Str::random(32),
                'max_attempts' => 5,
            ]
        );
    }

    public function regenerateToken(StudentVerification $verification): string
    {
        $token = Str::random(32);
        $verification->update(['verification_token' => $token]);
        return $token;
    }

    public function resetVerification(StudentVerification $verification): void
    {
        $verification->update([
            'is_verified' => false,
            'verified_at' => null,
            'otp_hash' => null,
            'otp_attempts' => 0,
            'otp_expires_at' => null,
            'verification_token' => Str::random(32),
        ]);
    }
}
