<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// ── Auth (Guest only) ──
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Logout ──
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ── Theme Toggle ──
Route::post('/theme', function (\Illuminate\Http\Request $request) {
    session(['theme' => $request->input('theme', 'light')]);
    return response()->json(['ok' => true]);
})->middleware('auth');

// ── Protected Routes ──
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ── Root redirect ──
Route::get('/', fn() => redirect()->route('login'));