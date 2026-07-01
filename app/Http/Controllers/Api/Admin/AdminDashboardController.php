<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\StudentVerification;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalReports = Report::count();
        $activeCases = Report::active()->count();
        $emergencyCases = Report::where('severity', 'Emergency')->active()->count();
        $verifiedReporters = StudentVerification::where('is_verified', true)->count();

        $recentReports = Report::with('category:id,name')
            ->latest()
            ->take(5)
            ->get(['id', 'tracking_id', 'incident_category_id', 'severity', 'status', 'submitted_at']);

        return response()->json([
            'success' => true,
            'data' => [
                'total_reports' => $totalReports,
                'active_cases' => $activeCases,
                'emergency_cases' => $emergencyCases,
                'verified_reporters' => $verifiedReporters,
                'recent_reports' => $recentReports->map(fn ($r) => [
                    'tracking_id' => $r->tracking_id,
                    'category' => $r->category?->name,
                    'severity' => $r->severity,
                    'status' => $r->status,
                    'submitted_at' => $r->submitted_at->toIso8601String(),
                ]),
            ],
        ]);
    }
}
