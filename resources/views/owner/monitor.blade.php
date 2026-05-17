@extends('admin.layouts.app')

@section('title', 'Monitor Aktivitas — Owner')
@section('breadcrumb', 'Monitor Aktivitas')

@push('styles')
<style>
    /* ── STAT CARDS ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow);
        transition: background 0.3s, border-color 0.3s;
    }
    .stat-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0;
    }
    .stat-icon.green  { background: #F0FDF4; color: #16A34A; }
    .stat-icon.blue   { background: #EFF6FF; color: #2563EB; }
    .stat-icon.yellow { background: #FFFBEB; color: #D97706; }
    .stat-icon.red    { background: #FFF1F2; color: #E11D48; }
    html.dark .stat-icon.green  { background: rgba(22,163,74,0.12);  color: #4ADE80; }
    html.dark .stat-icon.blue   { background: rgba(37,99,235,0.12);  color: #60A5FA; }
    html.dark .stat-icon.yellow { background: rgba(217,119,6,0.12);  color: #FCD34D; }
    html.dark .stat-icon.red    { background: rgba(225,29,72,0.12);  color: #FB7185; }
    .stat-value { font-size: 26px; font-weight: 800; color: var(--text-primary); line-height: 1; }
    .stat-label { font-size: 12px; color: var(--text-muted); margin-top: 3px; }

    /* ── PAGE HEADER ── */
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
    }
    .page-title {
        font-size: 20px; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 10px; line-height: 1.2;
    }
    .page-title i { font-size: 22px; color: var(--brand-500); }
    .page-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

    /* ── FILTER BAR ── */
    .filter-bar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 16px;
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 10px;
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-label { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .filter-control {
        height: 36px; padding: 0 11px;
        border: 1px solid var(--border); border-radius: 8px;
        background: var(--bg-primary); color: var(--text-primary);
        font-size: 13px; font-family: var(--font); outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .filter-control:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(29,111,164,0.1); }
    select.filter-control {
        padding-right: 30px; cursor: pointer; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 8px center;
    }
    .search-filter-wrap {
        display: flex; align-items: center;
        background: var(--bg-primary); border: 1px solid var(--border);
        border-radius: 8px; height: 36px; padding: 0 10px; gap: 6px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-filter-wrap:focus-within { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(29,111,164,0.1); }
    .search-filter-wrap i { color: var(--text-muted); font-size: 14px; }
    .search-filter-wrap input {
        border: none; background: transparent; outline: none;
        font-size: 13px; color: var(--text-primary); font-family: var(--font); width: 180px;
    }
    .search-filter-wrap input::placeholder { color: var(--text-muted); }

    /* ── BUTTONS ── */
    .btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0 14px; height: 36px; border-radius: 8px;
        font-size: 13px; font-weight: 500; font-family: var(--font);
        cursor: pointer; border: none; transition: all 0.2s;
        text-decoration: none; white-space: nowrap;
    }
    .btn i { font-size: 15px; }
    .btn-primary { background: var(--brand-500); color: #fff; border: 1px solid var(--brand-500); }
    .btn-primary:hover { background: var(--brand-600); }
    .btn-ghost { background: transparent; color: var(--text-secondary); border: 1px solid var(--border); }
    .btn-ghost:hover { background: var(--bg-hover); color: var(--text-primary); }

    /* ── TABS ── */
    .tab-bar {
        display: flex; gap: 4px;
        background: var(--bg-primary);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 4px;
        margin-bottom: 16px;
        width: fit-content;
    }
    .tab-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 16px; border-radius: 7px;
        font-size: 13px; font-weight: 500;
        cursor: pointer; border: none;
        color: var(--text-secondary); background: transparent;
        font-family: var(--font); transition: all 0.2s;
        text-decoration: none;
    }
    .tab-btn.active { background: var(--bg-card); color: var(--brand-500); box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
    .tab-btn i { font-size: 15px; }

    /* ── TABLE CARD ── */
    .table-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
        transition: background 0.3s, border-color 0.3s;
    }
    .table-toolbar {
        padding: 12px 16px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        gap: 8px; flex-wrap: wrap;
    }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead tr { background: var(--bg-primary); border-bottom: 2px solid var(--border); }
    .data-table th {
        padding: 10px 14px; text-align: left;
        font-size: 10.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--text-muted); white-space: nowrap;
    }
    .data-table td {
        padding: 12px 14px; font-size: 13px; color: var(--text-primary);
        border-bottom: 1px solid var(--border); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.15s; }
    .data-table tbody tr:hover td { background: var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align: center; }

    /* ── USER CELL ── */
    .user-cell { display: flex; align-items: center; gap: 9px; }
    .user-avatar-sm {
        width: 32px; height: 32px; border-radius: 8px;
        background: linear-gradient(135deg, var(--brand-500), var(--accent));
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 12px; font-weight: 700; flex-shrink: 0;
    }
    .user-name { font-weight: 600; font-size: 13px; color: var(--text-primary); }
    .user-username { font-size: 11.5px; color: var(--text-muted); font-family: monospace; }

    /* ── ROLE BADGE ── */
    .role-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 99px; font-size: 11.5px; font-weight: 600;
    }
    .role-owner   { background: #F5F3FF; color: #7C3AED; }
    .role-admin   { background: #FFF1F2; color: #E11D48; }
    .role-manager { background: #FFF7ED; color: #EA580C; }
    .role-staff   { background: #F0FDF4; color: #16A34A; }
    html.dark .role-owner   { background: rgba(124,58,237,0.12); color: #A78BFA; }
    html.dark .role-admin   { background: rgba(225,29,72,0.12);  color: #FB7185; }
    html.dark .role-manager { background: rgba(234,88,12,0.12);  color: #FB923C; }
    html.dark .role-staff   { background: rgba(22,163,74,0.12);  color: #4ADE80; }

    /* ── STATUS BADGE ── */
    .status-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 9px; border-radius: 99px; font-size: 11.5px; font-weight: 600;
    }
    .status-online  { background: #F0FDF4; color: #16A34A; }
    .status-offline { background: var(--bg-primary); color: var(--text-muted); border: 1px solid var(--border); }
    html.dark .status-online { background: rgba(22,163,74,0.12); color: #4ADE80; }

    /* ── ACTION BADGE (log) ── */
    .action-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 600;
    }
    .action-create { background: #F0FDF4; color: #16A34A; }
    .action-update { background: #FFFBEB; color: #D97706; }
    .action-delete { background: #FFF1F2; color: #E11D48; }
    html.dark .action-create { background: rgba(22,163,74,0.12);  color: #4ADE80; }
    html.dark .action-update { background: rgba(217,119,6,0.12);  color: #FCD34D; }
    html.dark .action-delete { background: rgba(225,29,72,0.12);  color: #FB7185; }

    /* ── ACTION BUTTONS ── */
    .btn-action {
        width: 29px; height: 29px; border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 14px; cursor: pointer;
        border: 1px solid var(--border); background: var(--bg-card);
        color: var(--text-secondary); transition: all 0.2s; text-decoration: none;
    }
    .btn-action:hover { background: var(--bg-hover); color: var(--text-primary); }
    .btn-action.view:hover { background: #F5F3FF; color: #7C3AED; border-color: #EDE9FE; }

    /* ── TABLE FOOTER / PAGINATION ── */
    .table-footer {
        padding: 12px 16px; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 8px;
    }
    .pagination-meta { font-size: 12px; color: var(--text-muted); }
    .pagination-meta strong { color: var(--text-primary); }
    .pagination-nav { display: flex; align-items: center; gap: 3px; }
    .page-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 30px; height: 30px; padding: 0 6px;
        border-radius: 6px; font-size: 12.5px;
        border: 1px solid var(--border); background: var(--bg-card);
        color: var(--text-secondary); text-decoration: none;
        cursor: pointer; transition: all 0.18s; font-family: var(--font);
    }
    .page-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
    .page-btn.active { background: var(--brand-500); color: #fff; border-color: var(--brand-500); font-weight: 700; }
    .page-btn.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }
    .page-ellipsis { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; font-size: 12.5px; color: var(--text-muted); }

    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 50px 24px; }
    .empty-state i { font-size: 44px; color: var(--border); display: block; margin-bottom: 10px; }
    .empty-state h3 { font-size: 14px; font-weight: 600; color: var(--text-secondary); margin-bottom: 5px; }
    .empty-state p  { font-size: 12.5px; color: var(--text-muted); }

    /* ── DIFF POPUP ── */
    .diff-wrap { display: flex; flex-direction: column; gap: 4px; font-size: 12px; }
    .diff-old { color: #E11D48; background: #FFF1F2; padding: 2px 6px; border-radius: 4px; }
    .diff-new { color: #16A34A; background: #F0FDF4; padding: 2px 6px; border-radius: 4px; }
    html.dark .diff-old { color: #FB7185; background: rgba(225,29,72,0.12); }
    html.dark .diff-new { color: #4ADE80; background: rgba(22,163,74,0.12); }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-shield-user-line"></i> Monitor Aktivitas Pengguna
        </h1>
        <p class="page-subtitle">Lacak login, logout, dan semua perubahan yang dilakukan pengguna</p>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon green"><i class="ri-wifi-line"></i></div>
        <div>
            <div class="stat-value">{{ $totalOnline }}</div>
            <div class="stat-label">Sedang Online</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ri-login-circle-line"></i></div>
        <div>
            <div class="stat-value">{{ $totalLoginHariIni }}</div>
            <div class="stat-label">Login Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon yellow"><i class="ri-edit-2-line"></i></div>
        <div>
            <div class="stat-value">{{ $totalAktivitasHariIni }}</div>
            <div class="stat-label">Aktivitas Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ri-user-unfollow-line"></i></div>
        <div>
            <div class="stat-value">{{ $userTidakAktif }}</div>
            <div class="stat-label">Tidak Aktif 7 Hari</div>
        </div>
    </div>
</div>

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('owner.monitor') }}" id="filterForm">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="filter-bar">

        {{-- Search --}}
        <div class="filter-group">
            <span class="filter-label">Cari</span>
            <div class="search-filter-wrap">
                <i class="ri-search-line"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Nama / username / modul...">
            </div>
        </div>

        {{-- Role --}}
        <div class="filter-group">
            <span class="filter-label">Role</span>
            <select name="role" class="filter-control" style="width:130px;">
                <option value="">Semua Role</option>
                <option value="admin"   {{ $roleFilter=='admin'   ? 'selected' : '' }}>Admin</option>
                <option value="manager" {{ $roleFilter=='manager' ? 'selected' : '' }}>Manager</option>
                <option value="staff"   {{ $roleFilter=='staff'   ? 'selected' : '' }}>Staff</option>
            </select>
        </div>

        {{-- Date From --}}
        <div class="filter-group">
            <span class="filter-label">Dari Tanggal</span>
            <input type="date" name="date_from" class="filter-control" value="{{ $dateFrom }}">
        </div>

        {{-- Date To --}}
        <div class="filter-group">
            <span class="filter-label">Sampai Tanggal</span>
            <input type="date" name="date_to" class="filter-control" value="{{ $dateTo }}">
        </div>

        {{-- Per Page --}}
        <div class="filter-group">
            <span class="filter-label">Tampilkan</span>
            <select name="per_page" class="filter-control" style="width:80px;">
                @foreach($allowedPerPage as $pp)
                <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                @endforeach
            </select>
        </div>

        {{-- Buttons --}}
        <button type="submit" class="btn btn-primary" style="margin-top:auto;">
            <i class="ri-filter-3-line"></i> Filter
        </button>
        <a href="{{ route('owner.monitor') }}" class="btn btn-ghost" style="margin-top:auto;">
            <i class="ri-refresh-line"></i> Reset
        </a>

    </div>
</form>

{{-- TABS --}}
<div class="tab-bar">
    <a href="{{ route('owner.monitor', array_merge(request()->query(), ['tab' => 'sesi'])) }}"
       class="tab-btn {{ $tab === 'sesi' ? 'active' : '' }}">
        <i class="ri-login-circle-line"></i> Sesi Login / Logout
    </a>
    <a href="{{ route('owner.monitor', array_merge(request()->query(), ['tab' => 'aktivitas'])) }}"
       class="tab-btn {{ $tab === 'aktivitas' ? 'active' : '' }}">
        <i class="ri-history-line"></i> Log Aktivitas
    </a>
</div>


{{-- ══ TAB: SESI LOGIN/LOGOUT ══ --}}
@if($tab === 'sesi')
<div class="table-card">
    <div class="table-toolbar">
        <span style="font-size:13px;font-weight:600;color:var(--text-primary);display:flex;align-items:center;gap:6px;">
            <i class="ri-login-circle-line" style="color:var(--brand-500);"></i>
            Riwayat Sesi Login / Logout
        </span>
        <span style="font-size:12px;color:var(--text-muted);">{{ $loginLogs->total() }} sesi ditemukan</span>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Login</th>
                    <th>Logout</th>
                    <th>Durasi</th>
                    <th>IP Address</th>
                    <th class="center">Status</th>
                    <th class="center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loginLogs as $log)
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;">{{ $loginLogs->firstItem() + $loop->index }}</td>

                    {{-- Pengguna --}}
                    <td>
                        @if($log->user)
                        <div class="user-cell">
                            <div class="user-avatar-sm">{{ strtoupper(substr($log->user->name, 0, 1)) }}</div>
                            <div>
                                <div class="user-name">{{ $log->user->name }}</div>
                                <div class="user-username">@{{ $log->user->username }}</div>
                            </div>
                        </div>
                        @else
                        <span style="color:var(--text-muted);font-size:12px;">User dihapus</span>
                        @endif
                    </td>

                    {{-- Role --}}
                    <td>
                        @if($log->user)
                        @php
                            $rc = match($log->user->role) {
                                'owner'=>'role-owner','admin'=>'role-admin',
                                'manager'=>'role-manager', default=>'role-staff'
                            };
                            $ri = match($log->user->role) {
                                'owner'=>'ri-vip-crown-fill','admin'=>'ri-shield-fill',
                                'manager'=>'ri-user-star-fill', default=>'ri-user-fill'
                            };
                        @endphp
                        <span class="role-badge {{ $rc }}">
                            <i class="{{ $ri }}" style="font-size:10px;"></i>
                            {{ ucfirst($log->user->role) }}
                        </span>
                        @endif
                    </td>

                    {{-- Login At --}}
                    <td>
                        <div style="font-size:13px;font-weight:500;">{{ $log->login_at?->format('D, d M Y') }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $log->login_at?->format('H:i:s') }}</div>
                    </td>

                    {{-- Logout At --}}
                    <td>
                        @if($log->logout_at)
                        <div style="font-size:13px;font-weight:500;">{{ $log->logout_at->format('D, d M Y') }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $log->logout_at->format('H:i:s') }}</div>
                        @else
                        <span style="color:var(--text-muted);font-size:12.5px;">—</span>
                        @endif
                    </td>

                    {{-- Durasi --}}
                    <td style="font-size:12.5px;font-weight:500;">{{ $log->durasi }}</td>

                    {{-- IP --}}
                    <td>
                        <span style="font-family:monospace;font-size:12px;background:var(--bg-primary);padding:2px 7px;border-radius:5px;border:1px solid var(--border);">
                            {{ $log->ip_address ?? '—' }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="center">
                        @if(!$log->logout_at)
                        <span class="status-badge status-online">
                            <i class="ri-record-circle-fill" style="font-size:8px;"></i> Online
                        </span>
                        @else
                        <span class="status-badge status-offline">
                            <i class="ri-stop-circle-line" style="font-size:10px;"></i> Selesai
                        </span>
                        @endif
                    </td>

                    {{-- Detail --}}
                    <td class="center">
                        @if($log->user)
                        <a href="{{ route('owner.monitor.detail', $log->user_id) }}?date_from={{ $log->login_at?->toDateString() }}&date_to={{ $log->login_at?->toDateString() }}"
                           class="btn-action view" title="Lihat aktivitas user">
                            <i class="ri-eye-line"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="ri-login-circle-line"></i>
                            <h3>Belum ada data sesi</h3>
                            <p>Tidak ada login ditemukan untuk filter yang dipilih.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($loginLogs->total() > 0)
    @include('owner._pagination', ['paginator' => $loginLogs, 'pageName' => 'sesi_page'])
    @endif
</div>
@endif


{{-- ══ TAB: LOG AKTIVITAS ══ --}}
@if($tab === 'aktivitas')
<div class="table-card">
    <div class="table-toolbar">
        <span style="font-size:13px;font-weight:600;color:var(--text-primary);display:flex;align-items:center;gap:6px;">
            <i class="ri-history-line" style="color:var(--brand-500);"></i>
            Log Aktivitas / Perubahan Data
        </span>
        <span style="font-size:12px;color:var(--text-muted);">{{ $activityLogs->total() }} aktivitas ditemukan</span>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Modul</th>
                    <th>Aksi</th>
                    <th>Subjek</th>
                    <th>Perubahan</th>
                    <th>Waktu</th>
                    <th class="center">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activityLogs as $log)
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;">{{ $activityLogs->firstItem() + $loop->index }}</td>

                    {{-- Pengguna --}}
                    <td>
                        @if($log->user)
                        <div class="user-cell">
                            <div class="user-avatar-sm">{{ strtoupper(substr($log->user->name, 0, 1)) }}</div>
                            <div>
                                <div class="user-name">{{ $log->user->name }}</div>
                                <div class="user-username">@{{ $log->user->username }}</div>
                            </div>
                        </div>
                        @else
                        <span style="color:var(--text-muted);font-size:12px;">User dihapus</span>
                        @endif
                    </td>

                    {{-- Role --}}
                    <td>
                        @if($log->user)
                        @php
                            $rc = match($log->user->role) {
                                'owner'=>'role-owner','admin'=>'role-admin',
                                'manager'=>'role-manager', default=>'role-staff'
                            };
                        @endphp
                        <span class="role-badge {{ $rc }}">{{ ucfirst($log->user->role) }}</span>
                        @endif
                    </td>

                    {{-- Modul --}}
                    <td>
                        <span style="font-size:12.5px;font-weight:500;display:flex;align-items:center;gap:5px;">
                            <i class="ri-folder-line" style="color:var(--brand-500);font-size:13px;"></i>
                            {{ $log->module }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td>
                        @php
                            $ac = match($log->action) {
                                'create'=>'action-create','update'=>'action-update',
                                'delete'=>'action-delete', default=>''
                            };
                            $ai = match($log->action) {
                                'create'=>'ri-add-circle-line','update'=>'ri-edit-line',
                                'delete'=>'ri-delete-bin-line', default=>'ri-question-line'
                            };
                            $al = match($log->action) {
                                'create'=>'Tambah','update'=>'Edit','delete'=>'Hapus', default=>ucfirst($log->action)
                            };
                        @endphp
                        <span class="action-badge {{ $ac }}">
                            <i class="{{ $ai }}" style="font-size:11px;"></i> {{ $al }}
                        </span>
                    </td>

                    {{-- Subjek --}}
                    <td style="font-size:12.5px;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $log->subject ?: '—' }}
                    </td>

                    {{-- Perubahan (diff ringkas) --}}
                    <td>
                        @if($log->old_value || $log->new_value)
                        <div class="diff-wrap">
                            @if($log->old_value)
                            <span class="diff-old">
                                <i class="ri-arrow-left-line" style="font-size:10px;"></i>
                                {{ implode(', ', array_map(fn($k,$v) => "$k: $v", array_keys($log->old_value), $log->old_value)) }}
                            </span>
                            @endif
                            @if($log->new_value)
                            <span class="diff-new">
                                <i class="ri-arrow-right-line" style="font-size:10px;"></i>
                                {{ implode(', ', array_map(fn($k,$v) => "$k: $v", array_keys($log->new_value), $log->new_value)) }}
                            </span>
                            @endif
                        </div>
                        @else
                        <span style="color:var(--text-muted);font-size:12px;">—</span>
                        @endif
                    </td>

                    {{-- Waktu --}}
                    <td>
                        <div style="font-size:12.5px;font-weight:500;">{{ $log->created_at->format('D, d M Y') }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>

                    {{-- Detail --}}
                    <td class="center">
                        @if($log->user)
                        <a href="{{ route('owner.monitor.detail', $log->user_id) }}?date_from={{ $log->created_at->toDateString() }}&date_to={{ $log->created_at->toDateString() }}"
                           class="btn-action view" title="Lihat detail user">
                            <i class="ri-eye-line"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="ri-history-line"></i>
                            <h3>Belum ada aktivitas</h3>
                            <p>Tidak ada aktivitas ditemukan untuk filter yang dipilih.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($activityLogs->total() > 0)
    @include('owner._pagination', ['paginator' => $activityLogs, 'pageName' => 'aktv_page'])
    @endif
</div>
@endif

@endsection