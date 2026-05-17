<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\InventoryController;

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
    Route::get('/penyewaan/notifikasi', [PenyewaanController::class, 'notifikasi'])->name('penyewaan.notifikasi');

    // ── Penyewaan ──
    Route::resource('penyewaan', PenyewaanController::class);

    // ── Penyewaan: Monitoring, Selesaikan, Extend ──
    Route::get('/penyewaan-monitoring',       [PenyewaanController::class, 'monitoring'])->name('penyewaan.monitoring');
    Route::post('/penyewaan/{id}/selesaikan', [PenyewaanController::class, 'selesaikan'])->name('penyewaan.selesaikan');
    Route::post('/penyewaan/{id}/extend',     [PenyewaanController::class, 'extend'])->name('penyewaan.extend');

    // ── Penyewaan: Cetak Invoice & Perjanjian Sewa ──
    Route::get('/penyewaan/{id}/invoice',    [PenyewaanController::class, 'invoice'])->name('penyewaan.invoice');
    Route::get('/penyewaan/{id}/perjanjian', [PenyewaanController::class, 'perjanjian'])->name('penyewaan.perjanjian');

    // ── Pembelian Barang ──
    Route::resource('pembelian', PembelianController::class);

    // ── Buy Back (harus di atas resource agar tidak konflik) ──
    Route::post('/pembelian/buy-back', [PembelianController::class, 'storeBuyBack'])
         ->name('pembelian.buyback.store');

    // ── Penjualan ──
    Route::resource('penjualan', PenjualanController::class);

    // ── Inventory ──
    Route::resource('inventory', InventoryController::class);

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