<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Models\Pembelian;
use App\Models\PembayaranPenjualan;
use App\Models\Penyewaan;
use App\Observers\PembelianObserver;
use App\Observers\PembayaranPenjualanObserver;
use App\Observers\PenyewaanObserver;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── Custom auth provider: login by username ──────────────────────
        Auth::provider('username-eloquent', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends EloquentUserProvider {
                public function retrieveByCredentials(array $credentials)
                {
                    $credentialsWithoutPassword = collect($credentials)
                        ->reject(fn($value, $key) => str_contains($key, 'password'))
                        ->toArray();

                    return $this->createModel()
                        ->newQuery()
                        ->where('username', $credentialsWithoutPassword['username'] ?? '')
                        ->first();
                }
            };
        });

        // ── Observer ─────────────────────────────────────────────────────
        // CATATAN: PenjualanObserver TIDAK didaftarkan — inventory sudah
        // dikelola langsung di PenjualanController::syncDetails() agar
        // tidak terjadi double-deduction stok.

        Pembelian::observe(PembelianObserver::class);
        Penyewaan::observe(PenyewaanObserver::class);
        PembayaranPenjualan::observe(PembayaranPenjualanObserver::class);
    }
}