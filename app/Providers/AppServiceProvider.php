<?php

namespace App\Providers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Penyewaan;
use App\Observers\PembelianObserver;
use App\Observers\PenjualanObserver;
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
        // Daftarkan custom auth provider agar login by username bisa jalan
        Auth::provider('username-eloquent', function ($app, array $config) {
            return new class($app['hash'], $config['model']) extends EloquentUserProvider {
                public function retrieveByCredentials(array $credentials)
                {
                    // Hapus password dari credentials sebelum query
                    $credentialsWithoutPassword = collect($credentials)
                        ->reject(fn($value, $key) => str_contains($key, 'password'))
                        ->toArray();

                    // Query by username (bukan email)
                    return $this->createModel()
                        ->newQuery()
                        ->where('username', $credentialsWithoutPassword['username'] ?? '')
                        ->first();
                }
            };
        });

        // Observer untuk inventory
        Pembelian::observe(PembelianObserver::class);
        Penjualan::observe(PenjualanObserver::class);
        Penyewaan::observe(PenyewaanObserver::class);
    }
}