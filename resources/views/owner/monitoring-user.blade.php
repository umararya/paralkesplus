@extends('admin.layouts.app')

@section('title', 'Monitoring User — Owner')
@section('breadcrumb', 'Monitoring User')

@push('styles')
<style>
    /* ── Page Header ── */
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
    }
    .page-title {
        font-size: 20px; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 10px; line-height: 1.2;
    }
    .page-title i { font-size: 22px; color: var(--brand-500); }
    .page-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

    /* ── Empty / Coming Soon Card ── */
    .coming-soon-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: var(--shadow);
        padding: 72px 24px;
        text-align: center;
        transition: background 0.3s, border-color 0.3s;
    }
    .coming-soon-icon {
        width: 80px; height: 80px; border-radius: 20px;
        background: linear-gradient(135deg, var(--brand-50), #EFF6FF);
        border: 1px solid var(--brand-100);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 38px; color: var(--brand-500);
        margin-bottom: 20px;
    }
    html.dark .coming-soon-icon {
        background: rgba(29,111,164,0.12);
        border-color: rgba(29,111,164,0.25);
        color: #60A5FA;
    }
    .coming-soon-title {
        font-size: 18px; font-weight: 700; color: var(--text-primary);
        margin-bottom: 8px;
    }
    .coming-soon-desc {
        font-size: 14px; color: var(--text-muted);
        max-width: 420px; margin: 0 auto; line-height: 1.7;
    }
    .coming-soon-badge {
        display: inline-flex; align-items: center; gap: 5px;
        margin-top: 20px;
        padding: 5px 14px; border-radius: 99px;
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100);
        font-size: 12px; font-weight: 600;
    }
    html.dark .coming-soon-badge {
        background: rgba(29,111,164,0.12);
        color: #60A5FA;
        border-color: rgba(29,111,164,0.25);
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-user-search-line"></i> Monitoring User
        </h1>
        <p class="page-subtitle">Pantau dan kelola aktivitas seluruh pengguna sistem</p>
    </div>
</div>

{{-- Coming Soon / Placeholder --}}
<div class="coming-soon-card">
    <div class="coming-soon-icon">
        <i class="ri-user-search-line"></i>
    </div>
    <h2 class="coming-soon-title">Halaman Sedang Disiapkan</h2>
    <p class="coming-soon-desc">
        Fitur <strong>Monitoring User</strong> akan segera hadir. Halaman ini akan menampilkan
        data pemantauan aktivitas pengguna secara lengkap dan real-time.
    </p>
    <div>
        <span class="coming-soon-badge">
            <i class="ri-time-line"></i> Coming Soon
        </span>
    </div>
</div>

@endsection