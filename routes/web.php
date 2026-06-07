<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// TEMPORARY: dashboard is publicly accessible (auth disabled until further notice).
// TODO: move this back inside the 'auth' middleware group to re-enable login protection.
Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/dashboard-kpi', function () {
    return view('admin.dashboard-kpi');
})->name('admin.dashboard.kpi');

Route::get('/dashboard-trend', function () {
    return view('admin.dashboard-trend');
})->name('admin.dashboard.trend');
