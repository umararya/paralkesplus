@extends('layouts.app')
@section('title', 'Login')

@section('content')
<a href="#main-content" class="sr-only">Skip to main content</a>

{{-- Theme Toggle --}}
<button class="theme-toggle" data-theme-toggle aria-label="Ganti tema" type="button"></button>

<main class="login-page" id="main-content">

    {{-- Background Geometric Decoration --}}
    <div class="login-bg" aria-hidden="true">
        <svg viewBox="0 0 1440 900" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice">
            {{-- Plus kecil — atas kiri --}}
            <g transform="translate(80, 80)">
                <rect class="deco-plus" x="-6" y="30" width="12" height="60" rx="3"/>
                <rect class="deco-plus" x="15" y="51" width="60" height="12" rx="3"/>
            </g>
            {{-- Plus besar — bawah kiri --}}
            <g transform="translate(120, 640)">
                <rect class="deco-shape" x="-10" y="50" width="20" height="100" rx="4"/>
                <rect class="deco-shape" x="25" y="85" width="100" height="20" rx="4"/>
            </g>
            {{-- Kotak outline --}}
            <rect class="deco-shape" x="40" y="500" width="70" height="70" rx="6"/>
            {{-- Cross — kanan atas --}}
            <g transform="translate(1290, 60)">
                <rect class="deco-shape" x="-8" y="35" width="16" height="80" rx="4"/>
                <rect class="deco-shape" x="20" y="63" width="80" height="16" rx="4"/>
            </g>
            {{-- Hexagon — kanan tengah --}}
            <polygon class="deco-shape" points="1310,360 1340,310 1380,310 1400,360 1380,410 1340,410"/>
            {{-- Hexagon kecil --}}
            <polygon class="deco-shape" points="1170,620 1190,585 1220,585 1240,620 1220,655 1190,655"/>
            {{-- Kotak kecil kanan --}}
            <rect class="deco-shape" x="1390" y="520" width="50" height="50" rx="4"/>
            {{-- Segitiga --}}
            <polygon class="deco-shape" points="1360,200 1400,280 1320,280"/>
            {{-- Lingkaran outline --}}
            <circle class="deco-circle" cx="1420" cy="120" r="22"/>
            <circle class="deco-circle" cx="40" cy="400" r="15"/>
            <circle class="deco-circle" cx="1200" cy="200" r="10"/>
            {{-- Dot grid kiri --}}
            @for ($r = 0; $r < 6; $r++)
                @for ($c = 0; $c < 5; $c++)
                    <circle class="deco-dot" cx="{{ 30 + $c * 18 }}" cy="{{ 200 + $r * 18 }}" r="2"/>
                @endfor
            @endfor
            {{-- Dot grid kanan --}}
            @for ($r = 0; $r < 5; $r++)
                @for ($c = 0; $c < 5; $c++)
                    <circle class="deco-dot" cx="{{ 1280 + $c * 18 }}" cy="{{ 310 + $r * 18 }}" r="2"/>
                @endfor
            @endfor
            {{-- Cross kecil bawah kanan --}}
            <g transform="translate(1060, 750)">
                <rect class="deco-plus" x="-4" y="16" width="8" height="40" rx="2"/>
                <rect class="deco-plus" x="12" y="30" width="40" height="8" rx="2"/>
            </g>
        </svg>
    </div>

    {{-- Login Container --}}
    <div class="login-container">

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="login-logo" aria-label="Paralkes+ Beranda">
            <img
                id="logo-img"
                src="{{ asset('images/logo-paralkes-white.png') }}"
                alt="Paralkes+ — Mitra Layanan Kesehatan Anda"
                width="280"
                height="80"
                loading="eager"
                style="max-width: 280px; height: auto;"
            >
        </a>

        {{-- Card --}}
        <div class="login-card">

            {{-- Header --}}
            <div class="login-card-header">
                <div class="login-icon-wrap" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                </div>
                <div>
                    <h1 class="login-card-title">Welcome Back</h1>
                    <p class="login-card-subtitle">Login to access your account</p>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </span>
                </div>
                <div style="height: var(--space-4)"></div>
            @endif

            @if (session('status'))
                <div class="alert" style="background: #f0faf4; border: 1px solid rgba(39,174,96,.2); color: var(--color-success)" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
                <div style="height: var(--space-4)"></div>
            @endif

            {{-- Form --}}
            <form class="login-form" method="POST" action="{{ route('login') }}" novalidate id="login-form">
                @csrf

                {{-- Username --}}
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <div class="form-input-wrap">
                        <span class="form-input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input
                            id="username" type="text" name="username"
                            class="form-input @error('username') is-invalid @enderror"
                            placeholder="Enter your username"
                            value="{{ old('username') }}"
                            autocomplete="username"
                            autofocus required
                            aria-invalid="{{ $errors->has('username') ? 'true' : 'false' }}"
                        >
                    </div>
                    @error('username')
                        <span class="form-error" role="alert">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="form-input-wrap">
                        <span class="form-input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            id="password" type="password" name="password"
                            class="form-input has-toggle @error('password') is-invalid @enderror"
                            placeholder="Enter your password"
                            autocomplete="current-password" required
                            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        >
                        <button type="button" class="password-toggle" id="password-toggle"
                            aria-label="Tampilkan password" aria-controls="password">
                            <svg id="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="form-error" role="alert">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Forgot --}}
                <div class="login-forgot">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login" id="login-btn">
                    <span class="btn-login-text">LOGIN</span>
                    <span class="btn-login-loading" aria-hidden="true">
                        <span class="spinner"></span>
                        <span>Memproses...</span>
                    </span>
                </button>

                <p class="login-footer-note">
                    &copy; {{ date('Y') }} Paralkes+ — Mitra Layanan Kesehatan Anda
                </p>
            </form>
        </div>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Logo Theme Switch ──
    const logoImg   = document.getElementById('logo-img');
    const logoBlack = '{{ asset("images/logo-paralkes-white.png") }}';
    const logoWhite = '{{ asset("images/logo-paralkes-black.png") }}';

    function updateLogo(theme) {
        if (!logoImg) return;
        logoImg.src = theme === 'dark' ? logoWhite : logoBlack;
    }

    // Set logo sesuai tema saat ini
    updateLogo(document.documentElement.getAttribute('data-theme') || 'light');

    // Pantau perubahan data-theme di <html>
    const themeObserver = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            if (mutation.attributeName === 'data-theme') {
                updateLogo(document.documentElement.getAttribute('data-theme'));
            }
        });
    });
    themeObserver.observe(document.documentElement, { attributes: true });

    // ── Password Toggle ──
    const pwInput  = document.getElementById('password');
    const pwToggle = document.getElementById('password-toggle');
    const eyeIcon  = document.getElementById('eye-icon');

    const eyeOpen   = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
    const eyeClosed = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;

    if (pwToggle && pwInput) {
        pwToggle.addEventListener('click', function () {
            const show = pwInput.type === 'password';
            pwInput.type = show ? 'text' : 'password';
            eyeIcon.innerHTML = show ? eyeClosed : eyeOpen;
            pwToggle.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    }

    // ── Form Loading State ──
    const form = document.getElementById('login-form');
    const btn  = document.getElementById('login-btn');
    if (form && btn) {
        form.addEventListener('submit', function () {
            const u = document.getElementById('username').value.trim();
            const p = pwInput.value.trim();
            if (!u || !p) return;
            btn.classList.add('is-loading');
            btn.disabled = true;
        });
    }

    // ── Input focus color icons ──
    document.querySelectorAll('.form-input').forEach(function (inp) {
        inp.addEventListener('focus', function () {
            const icon = this.closest('.form-input-wrap').querySelector('.form-input-icon');
            if (icon) icon.style.color = 'var(--color-accent)';
        });
        inp.addEventListener('blur', function () {
            const icon = this.closest('.form-input-wrap').querySelector('.form-input-icon');
            if (icon) icon.style.color = '';
        });
    });

});
</script>
@endpush
@endsection