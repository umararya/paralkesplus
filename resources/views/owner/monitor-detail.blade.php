@extends('admin.layouts.app')

@section('title', 'Detail Aktivitas — ' . $user->name)
@section('breadcrumb', 'Detail Aktivitas')

@push('styles')
<style>
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 20px; flex-wrap: wrap; gap: 12px;
    }
    .page-title {
        font-size: 20px; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 10px;
    }
    .page-title i { font-size: 22px; color: var(--brand-500); }
    .page-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

    .btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0 14px; height: 36px; border-radius: 8px;
        font-size: 13px; font-weight: 500; font-family: var(--font);
        cursor: pointer; border: none; transition: all 0.2s;
        text-decoration: none; white-space: nowrap;
    }
    .btn-ghost { background: transparent; color: var(--text-secondary); border: 1px solid var(--border); }
    .btn-ghost:hover { background: var(--bg-hover); color: var(--text-primary); }
    .btn-primary { background: var(--brand-500); color: #fff; border: 1px solid var(--brand-500); }
    .btn-primary:hover { background: var(--brand-600); }

    /* ── USER PROFILE CARD ── */
    .profile-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 14px; padding: 20px 22px; margin-bottom: 20px;
        display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
        box-shadow: var(--shadow);
    }
    .profile-avatar {
        width: 56px; height: 56px; border-radius: 14px;
        background: linear-gradient(135deg, var(--brand-500), var(--accent));
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 22px; font-weight: 800; flex-shrink: 0;
    }
    .profile-name { font-size: 17px; font-weight: 700; color: var(--text-primary); }
    .profile-meta { font-size: 13px; color: var(--text-muted); margin-top: 3px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .role-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 9px; border-radius: 99px; font-size: 12px; font-weight: 600;
    }
    .role-owner   { background: #F5F3FF; color: #7C3AED; }
    .role-admin   { background: #FFF1F2; color: #E11D48; }
    .role-manager { background: #FFF7ED; color: #EA580C; }
    .role-staff   { background: #F0FDF4; color: #16A34A; }
    html.dark .role-owner   { background: rgba(124,58,237,0.12); color: #A78BFA; }
    html.dark .role-admin   { background: rgba(225,29,72,0.12);  color: #FB7185; }
    html.dark .role-manager { background: rgba(234,88,12,0.12);  color: #FB923C; }
    html.dark .role-staff   { background: rgba(22,163,74,0.12);  color: #4ADE80; }

    /* ── DATE FILTER ── */
    .filter-bar {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 12px; padding: 14px 16px; margin-bottom: 18px;
        display: flex; align-items: flex-end; flex-wrap: wrap; gap: 10px;
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-label { font-size: 11px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .filter-control {
        height: 36px; padding: 0 11px;
        border: 1px solid var(--border); border-radius: 8px;
        background: var(--bg-primary); color: var(--text-primary);
        font-size: 13px; font-family: var(--font); outline: none;
        transition: border-color 0.2s;
    }
    .filter-control:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(29,111,164,0.1); }
    select.filter-control {
        padding-right: 28px; cursor: pointer; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 8px center;
    }

    /* ── SESI SUMMARY ── */
    .sesi-list { margin-bottom: 22px; display: flex; flex-direction: column; gap: 8px; }
    .sesi-item {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 10px; padding: 12px 16px;
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    }
    .sesi-icon {
        width: 36px; height: 36px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
    }
    .sesi-icon.online  { background: #F0FDF4; color: #16A34A; }
    .sesi-icon.offline { background: var(--bg-primary); color: var(--text-muted); border: 1px solid var(--border); }
    html.dark .sesi-icon.online { background: rgba(22,163,74,0.12); color: #4ADE80; }
    .sesi-time { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .sesi-dur  { font-size: 12px; color: var(--text-muted); }
    .sesi-ip   { font-family: monospace; font-size: 11.5px; background: var(--bg-primary); border: 1px solid var(--border); padding: 2px 7px; border-radius: 5px; color: var(--text-secondary); }

    /* ── TIMELINE ── */
    .timeline { position: relative; padding-left: 28px; }
    .timeline::before {
        content: ''; position: absolute; left: 9px; top: 6px; bottom: 6px;
        width: 2px; background: var(--border); border-radius: 2px;
    }
    .timeline-item { position: relative; margin-bottom: 14px; }
    .timeline-dot {
        position: absolute; left: -24px; top: 4px;
        width: 14px; height: 14px; border-radius: 50%;
        border: 2px solid var(--bg-card); flex-shrink: 0;
    }
    .dot-create { background: #16A34A; }
    .dot-update { background: #D97706; }
    .dot-delete { background: #E11D48; }
    .timeline-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 10px; padding: 12px 14px;
        transition: background 0.15s;
    }
    .timeline-card:hover { background: var(--bg-hover); }
    .timeline-header { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 6px; }
    .timeline-time { font-size: 11.5px; color: var(--text-muted); font-family: monospace; }
    .timeline-module { font-size: 12px; font-weight: 600; color: var(--brand-500); }
    .timeline-subject { font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
    .action-badge {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 1px 7px; border-radius: 5px; font-size: 11px; font-weight: 600;
    }
    .action-create { background: #F0FDF4; color: #16A34A; }
    .action-update { background: #FFFBEB; color: #D97706; }
    .action-delete { background: #FFF1F2; color: #E11D48; }
    html.dark .action-create { background: rgba(22,163,74,0.12);  color: #4ADE80; }
    html.dark .action-update { background: rgba(217,119,6,0.12);  color: #FCD34D; }
    html.dark .action-delete { background: rgba(225,29,72,0.12);  color: #FB7185; }

    .diff-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 6px; }
    .diff-pill {
        font-size: 11.5px; padding: 2px 8px; border-radius: 5px; font-family: monospace;
    }
    .diff-old { background: #FFF1F2; color: #E11D48; }
    .diff-new { background: #F0FDF4; color: #16A34A; }
    html.dark .diff-old { background: rgba(225,29,72,0.12); color: #FB7185; }
    html.dark .diff-new { background: rgba(22,163,74,0.12); color: #4ADE80; }

    /* Pagination */
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
        min-width: 30px; height: 30px; padding: 0 6px; border-radius: 6px; font-size: 12.5px;
        border: 1px solid var(--border); background: var(--bg-card); color: var(--text-secondary);
        text-decoration: none; cursor: pointer; transition: all 0.18s; font-family: var(--font);
    }
    .page-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
    .page-btn.active { background: var(--brand-500); color: #fff; border-color: var(--brand-500); font-weight: 700; }
    .page-btn.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }
    .page-ellipsis { display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px; font-size: 12.5px; color: var(--text-muted); }

    .empty-state { text-align: center; padding: 40px 20px; }
    .empty-state i { font-size: 40px; color: var(--border); display: block; margin-bottom: 8px; }
    .empty-state h3 { font-size: 13.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px; }
    .empty-state p { font-size: 12px; color: var(--text-muted); }

    /* Section title */
    .section-title {
        font-size: 13.5px; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 7px; margin-bottom: 12px;
    }
    .section-title i { color: var(--brand-500); font-size: 15px; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-user-search-line"></i> Detail Aktivitas Pengguna
        </h1>
        <p class="page-subtitle">Timeline lengkap sesi dan perubahan data</p>
    </div>
    <a href="{{ route('owner.monitor') }}" class="btn btn-ghost">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
</div>

{{-- PROFILE CARD --}}
<div class="profile-card">
    <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
    <div style="flex:1;">
        <div class="profile-name">{{ $user->name }}</div>
        <div class="profile-meta">
            <span style="font-family:monospace;">@{{ $user->username }}</span>
            @php
                $rc = match($user->role) {
                    'owner'=>'role-owner','admin'=>'role-admin',
                    'manager'=>'role-manager', default=>'role-staff'
                };
                $ri = match($user->role) {
                    'owner'=>'ri-vip-crown-fill','admin'=>'ri-shield-fill',
                    'manager'=>'ri-user-star-fill', default=>'ri-user-fill'
                };
            @endphp
            <span class="role-badge {{ $rc }}">
                <i class="{{ $ri }}" style="font-size:10px;"></i> {{ ucfirst($user->role) }}
            </span>
            @if($user->last_login_at)
            <span><i class="ri-time-line" style="font-size:12px;"></i> Login terakhir: {{ $user->last_login_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>
</div>

{{-- DATE FILTER --}}
<form method="GET" action="{{ route('owner.monitor.detail', $user->id) }}">
    <div class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Dari Tanggal</span>
            <input type="date" name="date_from" class="filter-control" value="{{ $dateFrom }}">
        </div>
        <div class="filter-group">
            <span class="filter-label">Sampai Tanggal</span>
            <input type="date" name="date_to" class="filter-control" value="{{ $dateTo }}">
        </div>
        <div class="filter-group">
            <span class="filter-label">Per Halaman</span>
            <select name="per_page" class="filter-control" style="width:80px;">
                @foreach($allowedPerPage as $pp)
                <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-top:auto;">
            <i class="ri-filter-3-line"></i> Filter
        </button>
        <a href="{{ route('owner.monitor.detail', $user->id) }}" class="btn btn-ghost" style="margin-top:auto;">
            <i class="ri-refresh-line"></i> Reset
        </a>
    </div>
</form>

{{-- SESI LOGIN/LOGOUT --}}
<div class="section-title">
    <i class="ri-login-circle-line"></i> Sesi Login / Logout pada Rentang Tanggal
    <span style="font-size:12px;color:var(--text-muted);font-weight:400;">({{ count($sesiList) }} sesi)</span>
</div>

@if($sesiList->isEmpty())
<div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;margin-bottom:20px;">
    <div class="empty-state">
        <i class="ri-login-circle-line"></i>
        <h3>Tidak ada sesi</h3>
        <p>User ini tidak login pada rentang tanggal yang dipilih.</p>
    </div>
</div>
@else
<div class="sesi-list">
    @foreach($sesiList as $s)
    <div class="sesi-item">
        <div class="sesi-icon {{ $s->logout_at ? 'offline' : 'online' }}">
            <i class="{{ $s->logout_at ? 'ri-stop-circle-line' : 'ri-record-circle-fill' }}"></i>
        </div>
        <div style="flex:1;">
            <div class="sesi-time">
                Login: {{ $s->login_at->format('D, d M Y — H:i:s') }}
                @if($s->logout_at)
                &nbsp;→&nbsp; Logout: {{ $s->logout_at->format('H:i:s') }}
                @else
                &nbsp; <span style="color:#16A34A;font-size:12px;font-weight:600;">● Masih Online</span>
                @endif
            </div>
            <div class="sesi-dur">Durasi: {{ $s->durasi }}</div>
        </div>
        <span class="sesi-ip">{{ $s->ip_address ?? '—' }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- TIMELINE AKTIVITAS --}}
<div class="section-title" style="margin-top:4px;">
    <i class="ri-history-line"></i> Timeline Perubahan Data
    <span style="font-size:12px;color:var(--text-muted);font-weight:400;">({{ $activities->total() }} aktivitas)</span>
</div>

@if($activities->isEmpty())
<div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;">
    <div class="empty-state">
        <i class="ri-history-line"></i>
        <h3>Tidak ada aktivitas</h3>
        <p>User ini tidak melakukan perubahan data pada rentang tanggal yang dipilih.</p>
    </div>
</div>
@else
<div class="timeline">
    @foreach($activities as $act)
    @php
        $dotClass = match($act->action) {
            'create'=>'dot-create','update'=>'dot-update','delete'=>'dot-delete', default=>''
        };
        $badgeClass = match($act->action) {
            'create'=>'action-create','update'=>'action-update','delete'=>'action-delete', default=>''
        };
        $badgeIcon = match($act->action) {
            'create'=>'ri-add-circle-line','update'=>'ri-edit-line','delete'=>'ri-delete-bin-line', default=>'ri-question-line'
        };
        $badgeLabel = match($act->action) {
            'create'=>'Tambah','update'=>'Edit','delete'=>'Hapus', default=>ucfirst($act->action)
        };
    @endphp
    <div class="timeline-item">
        <div class="timeline-dot {{ $dotClass }}"></div>
        <div class="timeline-card">
            <div class="timeline-header">
                <span class="timeline-time">{{ $act->created_at->format('H:i:s') }}</span>
                <span class="timeline-module">
                    <i class="ri-folder-line"></i> {{ $act->module }}
                </span>
                <span style="font-size:11.5px;color:var(--text-muted);">/{{ $act->page_url }}</span>
                <span class="action-badge {{ $badgeClass }}">
                    <i class="{{ $badgeIcon }}" style="font-size:10px;"></i> {{ $badgeLabel }}
                </span>
            </div>
            @if($act->subject)
            <div class="timeline-subject">{{ $act->subject }}</div>
            @endif
            @if($act->old_value || $act->new_value)
            <div class="diff-row">
                @if($act->old_value)
                <span class="diff-pill diff-old">
                    ← {{ implode(' | ', array_map(fn($k,$v) => "$k: $v", array_keys($act->old_value), $act->old_value)) }}
                </span>
                @endif
                @if($act->new_value)
                <span class="diff-pill diff-new">
                    → {{ implode(' | ', array_map(fn($k,$v) => "$k: $v", array_keys($act->new_value), $act->new_value)) }}
                </span>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- Pagination timeline --}}
@if($activities->hasPages())
<div style="background:var(--bg-card);border:1px solid var(--border);border-radius:12px;margin-top:12px;">
    @include('owner._pagination', ['paginator' => $activities])
</div>
@endif
@endif

@endsection