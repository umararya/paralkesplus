<?php
// routes/web.php

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



    // ── Dashboard ──
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



    // ════════════════════════════════════════════════════════
    //  PENYEWAAN
    //  ATURAN URUTAN:
    //  1. Route statis / fixed segment (tanpa wildcard) → PALING ATAS
    //  2. Route::resource
    //  3. Sub-route dengan {id} wildcard
    // ════════════════════════════════════════════════════════

    // ── 1a. Fixed routes SEBELUM resource ──
    Route::get('/penyewaan/notifikasi', [PenyewaanController::class, 'notifikasi'])->name('penyewaan.notifikasi');
    Route::get('/penyewaan/export',     [PenyewaanController::class, 'export'])->name('penyewaan.export');
    Route::get('/penyewaan-monitoring', [PenyewaanController::class, 'monitoring'])->name('penyewaan.monitoring');

    // ── 1b. Route extend/{extendId} WAJIB sebelum resource ──
    Route::get('/penyewaan/extend/{extendId}/invoice',
        [PenyewaanController::class, 'invoiceExtend'])
        ->name('penyewaan.invoiceExtend');

    Route::get('/penyewaan/extend/{extendId}/perjanjian',
        [PenyewaanController::class, 'perjanjianExtend'])
        ->name('penyewaan.perjanjianExtend');

    // ── BATALKAN EXTEND — wajib sebelum resource ──
    Route::post('/penyewaan/extend/{extendId}/batalkan',
        [PenyewaanController::class, 'batalkanExtend'])
        ->name('penyewaan.batalkanExtend');

    // ── 2. Resource ──
    Route::resource('penyewaan', PenyewaanController::class);

    // ── 3. Sub-route dengan {id} wildcard (sesudah resource) ──
    Route::post('/penyewaan/{id}/selesaikan',
        [PenyewaanController::class, 'selesaikan'])->name('penyewaan.selesaikan');

    Route::post('/penyewaan/{id}/extend',
        [PenyewaanController::class, 'extend'])->name('penyewaan.extend');

    Route::post('/penyewaan/{id}/extend-store',
        [PenyewaanController::class, 'extendStore'])->name('penyewaan.extendStore');

    Route::get('/penyewaan/{id}/invoice',
        [PenyewaanController::class, 'invoice'])->name('penyewaan.invoice');

    Route::get('/penyewaan/{id}/perjanjian',
        [PenyewaanController::class, 'perjanjian'])->name('penyewaan.perjanjian');

    Route::post('/penyewaan/{id}/batalkan',
        [PenyewaanController::class, 'batalkan'])->name('penyewaan.batalkan');

    Route::post('/penyewaan/{id}/restore',
        [PenyewaanController::class, 'restore'])->name('penyewaan.restore');



    // ════════════════════════════════════════════════════════
    //  PEMBELIAN
    //  ATURAN: semua fixed routes & wildcard khusus SEBELUM resource
    // ════════════════════════════════════════════════════════

    // ── 1. Fixed routes (tanpa {id} wildcard) — WAJIB sebelum resource ──
    Route::get('/pembelian/export',    [PembelianController::class, 'export'])->name('pembelian.export');
    Route::post('/pembelian/buy-back', [PembelianController::class, 'storeBuyBack'])->name('pembelian.buyback.store');

    // ── 2. Resource ──
    Route::resource('pembelian', PembelianController::class);

    // ── 3. Sub-route {id} wildcard — SESUDAH resource ──
    // CATATAN: route ini AMAN setelah resource karena Laravel resource tidak mendaftarkan
    // GET /pembelian/{id}/invoice. Namun agar konsisten dengan pola penyewaan,
    // idealnya dipindah SEBELUM resource jika menggunakan segment string statis.
    // Karena di sini segmennya adalah {id} (integer), TIDAK akan konflik dengan resource.
    // Tetap diletakkan sesudah resource sudah benar.
    Route::get('/pembelian/{id}/invoice', [PembelianController::class, 'invoice'])->name('pembelian.invoice');



    // ════════════════════════════════════════════════════════
    //  PENJUALAN
    // ════════════════════════════════════════════════════════

    Route::get('/penjualan/export', [PenjualanController::class, 'export'])->name('penjualan.export');
    Route::resource('penjualan', PenjualanController::class);

    Route::get('/penjualan/{id}/invoice',
        [PenjualanController::class, 'invoice'])->name('penjualan.invoice');
    Route::post('/penjualan/{penjualan}/pembayaran',
        [PenjualanController::class, 'tambahPembayaran'])->name('penjualan.tambahPembayaran');
    Route::delete('/penjualan/{penjualan}/pembayaran/{pembayaran}',
        [PenjualanController::class, 'hapusPembayaran'])->name('penjualan.hapusPembayaran');
    Route::post('/penjualan/{penjualan}/batalkan',
        [PenjualanController::class, 'batalkan'])->name('penjualan.batalkan');



    // ════════════════════════════════════════════════════════
    //  INVENTORY
    // ════════════════════════════════════════════════════════

    Route::resource('inventory', InventoryController::class);



    // ── Inventory API ──
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/inventory',      [InventoryApiController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/{id}', [InventoryApiController::class, 'show'])->name('inventory.show');
    });



    // ════════════════════════════════════════════════════════
    //  OWNER ROUTES
    // ════════════════════════════════════════════════════════

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