<?php

namespace App\Http\Controllers;

use App\Models\UserLoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->intended(route('dashboard'));
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            if (!Auth::user()->is_active) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'username' => 'Akun Anda tidak aktif. Hubungi administrator.',
                ]);
            }

            $request->session()->regenerate();

            Auth::user()->update(['last_login_at' => now()]);

            // ── Catat Login Log ──
            $log = UserLoginLog::create([
                'user_id'    => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'login_at'   => now(),
            ]);

            // Simpan log ID ke session agar bisa di-update saat logout
            session(['login_log_id' => $log->id]);

            return redirect()->intended(route('dashboard'))
                ->with('status', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        throw ValidationException::withMessages([
            'username' => 'Username atau password tidak valid.',
        ]);
    }

    public function logout(Request $request)
    {
        // ── Update Logout Time ──
        $logId = session('login_log_id');
        if ($logId) {
            UserLoginLog::where('id', $logId)->update(['logout_at' => now()]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('status', 'Anda berhasil logout.');
    }
}