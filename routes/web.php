<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Blade pages
Route::prefix('admin')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    });

    Route::post('/logout', function () {
        if (auth()->check()) {
            auth()->user()->currentAccessToken()->delete();
        }
        return redirect('/admin/login');
    });

    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    });

    Route::get('/reports', function () {
        return view('pages.reports');
    });

    Route::get('/reports/{trackingId}', function () {
        return view('pages.report-detail');
    });

    Route::get('/reports/{trackingId}/timeline', function () {
        return view('pages.report-timeline');
    });

    Route::get('/support', function () {
        return view('pages.support');
    });

    Route::get('/resources', function () {
        return view('pages.resources');
    });
});
