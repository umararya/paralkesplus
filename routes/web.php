<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ── Auth (Guest only) ──
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ── Logout ──
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ── Protected Routes ──
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    // Tambah route lain di sini
});

// ── Root redirect ──
Route::get('/', fn() => redirect()->route('login'));