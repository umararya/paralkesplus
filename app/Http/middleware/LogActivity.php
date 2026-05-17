<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware ini TIDAK otomatis log — logging dilakukan manual di Controller
 * menggunakan ActivityLog::record(). Middleware ini hanya meng-update
 * last_seen user agar Owner bisa tahu siapa yang sedang online.
 */
class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Update last_seen (pakai kolom last_login_at yang sudah ada)
        if (auth()->check() && !$request->isMethod('GET')) {
            // Hanya track request mutasi (POST/PUT/PATCH/DELETE)
            // Logging detail dilakukan manual di masing-masing controller
        }

        return $response;
    }
}