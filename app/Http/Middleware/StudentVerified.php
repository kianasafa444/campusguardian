<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Verification-Token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Header X-Verification-Token wajib diisi.',
            ], 401);
        }

        $verification = \App\Models\StudentVerification::where('verification_token', $token)
            ->where('is_verified', true)
            ->where('verified_at', '>', now()->subHours(24))
            ->first();

        if (!$verification) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi verifikasi tidak valid atau sudah kadaluarsa.',
            ], 401);
        }

        $request->merge(['student_verification' => $verification]);

        return $next($request);
    }
}
