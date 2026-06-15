<?php
// routes/console.php

use App\Console\Commands\SyncPenyewaanStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Schedule — Sync Status Penyewaan
|--------------------------------------------------------------------------
| Dijalankan setiap hari tengah malam (00:05) secara otomatis.
|
| Untuk test manual: php artisan penyewaan:sync-status
|
| Setup Windows Task Scheduler (Laragon):
|   Program : php
|   Argument: C:\laragon\www\paralkesplus\artisan schedule:run
|   Trigger : Daily at 00:05
|
| Setup Linux/Mac (crontab):
|   * * * * * cd /path/to/paralkesplus && php artisan schedule:run >> /dev/null 2>&1
|--------------------------------------------------------------------------
*/
Schedule::command(SyncPenyewaanStatus::class)
    ->dailyAt('00:05')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/sync-penyewaan.log'));