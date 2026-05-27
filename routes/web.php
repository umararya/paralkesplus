<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Api\InventoryApiController;


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


    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // ── Penyewaan ──
    Route::get('/penyewaan/notifikasi',       [PenyewaanController::class, 'notifikasi'])->name('penyewaan.notifikasi');
    Route::get('/penyewaan-monitoring',       [PenyewaanController::class, 'monitoring'])->name('penyewaan.monitoring');
    Route::get('/penyewaan/export',           [PenyewaanController::class, 'export'])->name('penyewaan.export');   // ← TAMBAH
    Route::resource('penyewaan', PenyewaanController::class);
    Route::post('/penyewaan/{id}/selesaikan', [PenyewaanController::class, 'selesaikan'])->name('penyewaan.selesaikan');
    Route::post('/penyewaan/{id}/extend',     [PenyewaanController::class, 'extend'])->name('penyewaan.extend');
    Route::get('/penyewaan/{id}/invoice',     [PenyewaanController::class, 'invoice'])->name('penyewaan.invoice');
    Route::get('/penyewaan/{id}/perjanjian',  [PenyewaanController::class, 'perjanjian'])->name('penyewaan.perjanjian');


    // ── Pembelian ──
    Route::get('/pembelian/export',    [PembelianController::class, 'export'])->name('pembelian.export');           // ← TAMBAH
    Route::post('/pembelian/buy-back', [PembelianController::class, 'storeBuyBack'])
         ->name('pembelian.buyback.store');
    Route::resource('pembelian', PembelianController::class);


    // ── Penjualan ──
    Route::get('/penjualan/export',   [PenjualanController::class, 'export'])->name('penjualan.export');            // ← TAMBAH
    Route::resource('penjualan', PenjualanController::class);
    Route::get('/penjualan/{id}/invoice', [PenjualanController::class, 'invoice'])->name('penjualan.invoice');


    // ── Inventory (CRUD penuh) ──
    Route::resource('inventory', InventoryController::class);


    // ── Inventory API ──
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/inventory',      [InventoryApiController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/{id}', [InventoryApiController::class, 'show'])->name('inventory.show');
    });


    // ── Owner Routes ──
    Route::middleware('owner.only')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/user-login',           [OwnerController::class, 'userLogin'])->name('user-login');
        Route::post('/user-login',          [OwnerController::class, 'userLoginStore'])->name('user-login.store');
        Route::put('/user-login/{user}',    [OwnerController::class, 'userLoginUpdate'])->name('user-login.update');
        Route::delete('/user-login/{user}', [OwnerController::class, 'userLoginDestroy'])->name('user-login.destroy');

        Route::get('/monitor',              [OwnerController::class, 'monitor'])->name('monitor');
        Route::get('/monitor/{user}',       [OwnerController::class, 'monitorDetail'])->name('monitor.detail');
    });


});


// ── Root redirect ──
Route::get('/', fn() => redirect()->route('login'));