<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerController;

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

    // ── Owner Routes (hanya role 'owner') ──
    Route::middleware('owner.only')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/user-login',             [OwnerController::class, 'userLogin'])->name('user-login');
        Route::post('/user-login',            [OwnerController::class, 'store'])->name('user-login.store');
        Route::get('/user-login/{user}/edit', [OwnerController::class, 'edit'])->name('user-login.edit');
        Route::put('/user-login/{user}',      [OwnerController::class, 'update'])->name('user-login.update');
        Route::delete('/user-login/{user}',   [OwnerController::class, 'destroy'])->name('user-login.destroy');
    });

});

// ── Root redirect ──
Route::get('/', fn() => redirect()->route('login'));