@extends('admin.layouts.app')

@section('title', 'User Login — Owner')
@section('breadcrumb', 'User Login')

@push('styles')
<style>
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

    .table-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
        transition: background 0.3s, border-color 0.3s;
    }

    /* Toolbar */
    .table-toolbar {
        padding: 16px 20px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px; flex-wrap: wrap;
    }
    .search-form { display: flex; align-items: center; gap: 8px; }
    .search-input-wrap {
        display: flex; align-items: center;
        background: var(--bg-primary); border: 1px solid var(--border);
        border-radius: 9px; padding: 0 12px; height: 38px; gap: 8px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-input-wrap:focus-within {
        border-color: var(--brand-500);
        box-shadow: 0 0 0 3px rgba(29,111,164,0.1);
    }
    .search-input-wrap i { color: var(--text-muted); font-size: 15px; flex-shrink: 0; }
    .search-input-wrap input {
        border: none; background: transparent; outline: none;
        font-size: 13.5px; color: var(--text-primary);
        font-family: var(--font); width: 220px;
    }
    .search-input-wrap input::placeholder { color: var(--text-muted); }

    .btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0 16px; height: 38px; border-radius: 9px;
        font-size: 13.5px; font-weight: 500; font-family: var(--font);
        cursor: pointer; border: none; transition: all 0.2s;
        text-decoration: none; white-space: nowrap;
    }
    .btn i { font-size: 16px; }
    .btn-search {
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100);
    }
    .btn-search:hover { background: var(--brand-100); color: var(--brand-600); }
    html.dark .btn-search {
        background: rgba(29,111,164,0.12); color: #60A5FA;
        border-color: rgba(29,111,164,0.25);
    }
    html.dark .btn-search:hover { background: rgba(29,111,164,0.22); }
    .btn-primary { background: var(--brand-500); color: #fff; border: 1px solid var(--brand-500); }
    .btn-primary:hover { background: var(--brand-600); border-color: var(--brand-600); }
    .btn-ghost {
        background: transparent; color: var(--text-secondary);
        border: 1px solid var(--border);
    }
    .btn-ghost:hover { background: var(--bg-hover); color: var(--text-primary); }
    .btn-danger { background: #EF4444; color: #fff; border: 1px solid #EF4444; }
    .btn-danger:hover { background: #DC2626; border-color: #DC2626; }

    /* Table */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead tr { background: var(--bg-primary); border-bottom: 2px solid var(--border); }
    .data-table th {
        padding: 11px 16px; text-align: left;
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.7px; color: var(--text-muted); white-space: nowrap;
    }
    .data-table td {
        padding: 14px 16px; font-size: 13.5px; color: var(--text-primary);
        border-bottom: 1px solid var(--border); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.15s; }
    .data-table tbody tr:hover td { background: var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align: center; }

    /* User cell */
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-avatar-sm {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, var(--brand-500), var(--accent));
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 14px; font-weight: 700;
        flex-shrink: 0; letter-spacing: -0.5px;
    }
    .user-name-main { font-weight: 600; color: var(--text-primary); font-size: 13.5px; }

    /* Username mono */
    .username-mono {
        font-family: 'Courier New', monospace; font-size: 13px;
        color: var(--text-secondary);
        background: var(--bg-primary); padding: 3px 8px;
        border-radius: 6px; border: 1px solid var(--border); display: inline-block;
    }

    /* Password dots */
    .password-dots { display: inline-flex; align-items: center; gap: 3px; color: var(--text-muted); }
    .password-dots span {
        width: 6px; height: 6px; border-radius: 50%;
        background: var(--text-muted); display: inline-block;
    }

    /* Role Badge */
    .role-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 10px; border-radius: 99px;
        font-size: 12px; font-weight: 600;
    }
    .role-owner   { background: #F5F3FF; color: #7C3AED; }
    .role-admin   { background: #FFF1F2; color: #E11D48; }
    .role-manager { background: #FFF7ED; color: #EA580C; }
    .role-staff   { background: #F0FDF4; color: #16A34A; }
    html.dark .role-owner   { background: rgba(124,58,237,0.12); color: #A78BFA; }
    html.dark .role-admin   { background: rgba(225,29,72,0.12);  color: #FB7185; }
    html.dark .role-manager { background: rgba(234,88,12,0.12);  color: #FB923C; }
    html.dark .role-staff   { background: rgba(22,163,74,0.12);  color: #4ADE80; }

    /* Action Buttons */
    .action-group { display: flex; align-items: center; gap: 5px; justify-content: center; }
    .btn-action {
        width: 32px; height: 32px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 16px; cursor: pointer;
        border: 1px solid var(--border); background: var(--bg-card);
        color: var(--text-secondary); transition: all 0.2s; text-decoration: none;
    }
    .btn-action:hover { background: var(--bg-hover); color: var(--text-primary); }
    .btn-action.view:hover   { background: #F5F3FF; color: #7C3AED; border-color: #EDE9FE; }
    .btn-action.edit:hover   { background: #EFF6FF; color: var(--brand-500); border-color: var(--brand-100); }
    .btn-action.delete:hover { background: #FFF1F2; color: #E11D48; border-color: #FFE4E6; }
    html.dark .btn-action.view:hover   { background: rgba(124,58,237,0.15); color: #A78BFA; border-color: rgba(124,58,237,0.3); }
    html.dark .btn-action.edit:hover   { background: rgba(29,111,164,0.15); color: #60A5FA; border-color: rgba(29,111,164,0.3); }
    html.dark .btn-action.delete:hover { background: rgba(225,29,72,0.12);  color: #FB7185; border-color: rgba(225,29,72,0.25); }
    .btn-action:disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

    /* Pagination */
    .table-footer {
        padding: 14px 20px; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 8px;
    }
    .pagination-info { font-size: 12.5px; color: var(--text-muted); }
    .pagination-links { display: flex; gap: 4px; }
    .pagination-links a,
    .pagination-links span {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px; padding: 0 6px;
        border-radius: 7px; font-size: 13px; border: 1px solid var(--border);
        text-decoration: none; color: var(--text-secondary); transition: all 0.2s;
    }
    .pagination-links a:hover { background: var(--bg-hover); color: var(--text-primary); }
    .pagination-links span.active-page {
        background: var(--brand-500); color: #fff;
        border-color: var(--brand-500); font-weight: 700;
    }
    .pagination-links span.disabled { opacity: 0.35; cursor: not-allowed; }

    /* Empty state */
    .empty-state { text-align: center; padding: 64px 24px; }
    .empty-state i { font-size: 52px; color: var(--border); display: block; margin-bottom: 14px; }
    .empty-state h3 { font-size: 15px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
    .empty-state p  { font-size: 13px; color: var(--text-muted); }

    /* Flash Alert */
    .alert {
        display: flex; align-items: center; gap: 10px;
        padding: 13px 16px; border-radius: 10px;
        font-size: 13.5px; font-weight: 500; margin-bottom: 20px;
        border: 1px solid transparent;
    }
    .alert-success { background: #F0FDF4; color: #15803D; border-color: #BBF7D0; }
    .alert-error   { background: #FFF1F2; color: #BE123C; border-color: #FECDD3; }
    html.dark .alert-success { background: rgba(21,128,61,0.12); color: #4ADE80; border-color: rgba(21,128,61,0.25); }
    html.dark .alert-error   { background: rgba(190,18,60,0.12); color: #FB7185; border-color: rgba(190,18,60,0.25); }

    /* Result badge */
    .result-count {
        display: inline-flex; align-items: center;
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100);
        border-radius: 99px; padding: 2px 10px;
        font-size: 12px; font-weight: 600;
    }
    html.dark .result-count {
        background: rgba(29,111,164,0.12); color: #60A5FA;
        border-color: rgba(29,111,164,0.25);
    }

    /* Modal */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.45); z-index: 1000;
        align-items: center; justify-content: center;
        padding: 16px; backdrop-filter: blur(2px);
    }
    .modal-overlay.open { display: flex; animation: fadeOverlay 0.2s ease; }
    @keyframes fadeOverlay { from { opacity: 0; } to { opacity: 1; } }

    .modal {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        width: 100%; max-width: 460px;
        animation: slideUp 0.2s ease;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .modal-header {
        padding: 20px 24px 16px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title {
        font-size: 16px; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 9px;
    }
    .modal-title i { font-size: 18px; color: var(--brand-500); }
    .modal-close {
        width: 30px; height: 30px; border: none; background: none; cursor: pointer;
        color: var(--text-muted); font-size: 20px; border-radius: 7px;
        display: flex; align-items: center; justify-content: center; transition: all 0.2s;
    }
    .modal-close:hover { background: var(--bg-hover); color: var(--text-primary); }
    .modal-body { padding: 20px 24px; }
    .modal-footer {
        padding: 16px 24px; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: flex-end; gap: 8px;
    }

    .form-group { margin-bottom: 16px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
    .form-label span { color: #EF4444; margin-left: 2px; }
    .form-control {
        width: 100%; height: 40px; padding: 0 12px;
        border: 1px solid var(--border); border-radius: 9px;
        background: var(--bg-primary); color: var(--text-primary);
        font-size: 13.5px; font-family: var(--font); outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(29,111,164,0.1); }
    .form-control::placeholder { color: var(--text-muted); }
    select.form-control {
        cursor: pointer; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px;
    }
    .form-hint  { font-size: 11.5px; color: var(--text-muted); margin-top: 5px; }
    .form-error { font-size: 12px; color: #EF4444; margin-top: 5px; display: flex; align-items: center; gap: 4px; }

    .input-password-wrap { position: relative; }
    .input-password-wrap .form-control { padding-right: 40px; }
    .toggle-pw {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: var(--text-muted); font-size: 17px;
        display: flex; align-items: center; transition: color 0.2s;
    }
    .toggle-pw:hover { color: var(--text-primary); }

    /* Modal Lihat Password */
    .view-pw-box {
        background: var(--bg-primary);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px 20px;
        margin-top: 4px;
    }
    .view-pw-label { font-size: 11.5px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .view-pw-value {
        font-family: 'Courier New', monospace;
        font-size: 15px; font-weight: 700;
        color: var(--text-primary); letter-spacing: 1px;
        word-break: break-all;
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    }
    .view-pw-copy {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 6px;
        font-size: 12px; font-family: var(--font);
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100); cursor: pointer;
        transition: all 0.2s; font-weight: 500;
    }
    .view-pw-copy:hover { background: var(--brand-100); }
    html.dark .view-pw-copy { background: rgba(29,111,164,0.12); color: #60A5FA; border-color: rgba(29,111,164,0.25); }
    .view-pw-note {
        font-size: 12px; color: var(--text-muted);
        display: flex; align-items: center; gap: 5px; margin-top: 10px;
    }
    .view-pw-note i { color: #F59E0B; }

    /* Delete warning */
    .delete-warning { text-align: center; padding: 8px 0; }
    .delete-warning i { font-size: 44px; color: #EF4444; display: block; margin-bottom: 12px; }
    .delete-warning h3 { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
    .delete-warning p { font-size: 13.5px; color: var(--text-muted); line-height: 1.6; }
    .delete-warning strong { color: var(--text-primary); }
</style>
@endpush

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success">
    <i class="ri-checkbox-circle-fill"></i>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-error">
    <i class="ri-error-warning-fill"></i>
    {{ session('error') }}
</div>
@endif

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-login-circle-line"></i>
            User Login
        </h1>
        <p class="page-subtitle">Kelola data akun pengguna yang dapat masuk ke sistem</p>
    </div>
</div>

{{-- Table Card --}}
<div class="table-card">

    {{-- Toolbar --}}
    <div class="table-toolbar">
        {{-- KIRI: Search + Tombol Cari --}}
        <form method="GET" action="{{ route('owner.user-login') }}" class="search-form">
            <div class="search-input-wrap">
                <i class="ri-search-line"></i>
                <input
                    type="text" name="search" id="searchInput"
                    value="{{ $search }}"
                    placeholder="Cari nama, username, atau role..."
                    autocomplete="off"
                >
            </div>
            <button type="submit" class="btn btn-search">
                <i class="ri-search-2-line"></i> Cari
            </button>
            @if($search)
            <a href="{{ route('owner.user-login') }}" class="btn btn-ghost">
                <i class="ri-close-line"></i> Reset
            </a>
            @endif
        </form>

        {{-- KANAN: Tombol Input Data --}}
        <button type="button" class="btn btn-primary" onclick="openModal('modalTambah')">
            <i class="ri-user-add-line"></i> Input Data
        </button>
    </div>

    {{-- Info pencarian --}}
    @if($search)
    <div style="padding: 10px 20px; border-bottom: 1px solid var(--border); background: var(--bg-primary); font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 8px;">
        <i class="ri-filter-line"></i>
        Hasil untuk <strong style="color: var(--text-primary);">"{{ $search }}"</strong>
        &nbsp;<span class="result-count">{{ $users->total() }} pengguna</span>
    </div>
    @endif

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:48px;">#</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th class="center" style="width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td style="color:var(--text-muted);font-size:12.5px;font-weight:500;">
                        {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                    </td>

                    {{-- Nama (tanpa #id) --}}
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="user-name-main">{{ $user->name }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Username --}}
                    <td><span class="username-mono">{{ $user->username }}</span></td>

                    {{-- Password (dots) --}}
                    <td>
                        <div class="password-dots" title="Password terenkripsi">
                            <span></span><span></span><span></span>
                            <span></span><span></span><span></span>
                            <span></span><span></span>
                        </div>
                    </td>

                    {{-- Role --}}
                    <td>
                        @php
                            $roleClass = match($user->role) {
                                'owner'   => 'role-owner',
                                'admin'   => 'role-admin',
                                'manager' => 'role-manager',
                                default   => 'role-staff',
                            };
                            $roleIcon = match($user->role) {
                                'owner'   => 'ri-vip-crown-fill',
                                'admin'   => 'ri-shield-fill',
                                'manager' => 'ri-user-star-fill',
                                default   => 'ri-user-fill',
                            };
                        @endphp
                        <span class="role-badge {{ $roleClass }}">
                            <i class="{{ $roleIcon }}" style="font-size:12px;"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>

                    {{-- Aksi: Lihat PW | Edit | Hapus --}}
                    <td class="center">
                        <div class="action-group">

                            {{-- Lihat Password --}}
                            <button
                                type="button"
                                class="btn-action view"
                                title="Lihat password"
                                onclick="openViewPassword(
                                    '{{ addslashes($user->name) }}',
                                    '{{ addslashes($user->username) }}',
                                    '{{ $user->role }}'
                                )"
                            >
                                <i class="ri-eye-line"></i>
                            </button>

                            {{-- Edit --}}
                            <button
                                type="button"
                                class="btn-action edit"
                                title="Edit pengguna"
                                onclick="openEditModal(
                                    {{ $user->id }},
                                    '{{ addslashes($user->name) }}',
                                    '{{ addslashes($user->username) }}',
                                    '{{ $user->role }}'
                                )"
                            >
                                <i class="ri-edit-line"></i>
                            </button>

                            {{-- Hapus --}}
                            <button
                                type="button"
                                class="btn-action delete"
                                title="Hapus pengguna"
                                onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                {{ $user->id === auth()->id() ? 'disabled' : '' }}
                            >
                                <i class="ri-delete-bin-line"></i>
                            </button>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="ri-user-search-line"></i>
                            <h3>{{ $search ? 'Tidak ditemukan' : 'Belum ada pengguna' }}</h3>
                            <p>{{ $search ? 'Coba kata kunci lain atau reset pencarian.' : 'Klik tombol "Input Data" untuk menambah pengguna baru.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="table-footer">
        <span class="pagination-info">
            Menampilkan <strong>{{ $users->firstItem() }}–{{ $users->lastItem() }}</strong>
            dari <strong>{{ $users->total() }}</strong> pengguna
        </span>
        <div class="pagination-links">
            @if($users->onFirstPage())
                <span class="disabled"><i class="ri-arrow-left-s-line"></i></span>
            @else
                <a href="{{ $users->previousPageUrl() }}"><i class="ri-arrow-left-s-line"></i></a>
            @endif
            @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                @if($page == $users->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}"><i class="ri-arrow-right-s-line"></i></a>
            @else
                <span class="disabled"><i class="ri-arrow-right-s-line"></i></span>
            @endif
        </div>
    </div>
    @endif

</div>{{-- /table-card --}}


{{-- ══════════════════════════════════════════════════ --}}
{{-- MODAL: LIHAT PASSWORD --}}
{{-- ══════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalViewPw">
    <div class="modal" style="max-width:420px;">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-eye-line" style="color:#7C3AED;"></i>
                Detail Akun
            </span>
            <button class="modal-close" onclick="closeModal('modalViewPw')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="view-pw-box">
                <div class="view-pw-label">Nama</div>
                <div class="view-pw-value" id="vpName" style="font-family:var(--font);font-size:14px;font-weight:600;letter-spacing:0;"></div>
            </div>
            <div class="view-pw-box" style="margin-top:10px;">
                <div class="view-pw-label">Username</div>
                <div class="view-pw-value" id="vpUsername"></div>
            </div>
            <div class="view-pw-box" style="margin-top:10px;">
                <div class="view-pw-label">Role</div>
                <div class="view-pw-value" id="vpRole" style="font-family:var(--font);font-size:14px;letter-spacing:0;"></div>
            </div>
            <div class="view-pw-box" style="margin-top:10px;">
                <div class="view-pw-label">Password (plaintext saat input)</div>
                <div class="view-pw-value">
                    <span id="vpPassword">—</span>
                    <button type="button" class="view-pw-copy" onclick="copyPassword()" id="vpCopyBtn">
                        <i class="ri-file-copy-line"></i> Salin
                    </button>
                </div>
                <div class="view-pw-note">
                    <i class="ri-information-line"></i>
                    Password ditampilkan dalam format terenkripsi (hash). Tidak dapat didekripsi.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalViewPw')">
                Tutup
            </button>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════ --}}
{{-- MODAL: TAMBAH PENGGUNA --}}
{{-- ══════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-user-add-line"></i> Tambah Pengguna
            </span>
            <button class="modal-close" onclick="closeModal('modalTambah')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form action="{{ route('owner.user-login.store') }}" method="POST">
            @csrf
            <div class="modal-body">

                <div class="form-group">
                    <label class="form-label" for="add_name">Nama Lengkap <span>*</span></label>
                    <input type="text" id="add_name" name="name"
                           class="form-control @error('name') is-error @enderror"
                           placeholder="Contoh: Budi Santoso"
                           value="{{ old('name') }}" autocomplete="off">
                    @error('name')<div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="add_username">Username <span>*</span></label>
                    <input type="text" id="add_username" name="username"
                           class="form-control @error('username') is-error @enderror"
                           placeholder="Contoh: budi.santoso"
                           value="{{ old('username') }}" autocomplete="off">
                    @error('username')<div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="add_password">Password <span>*</span></label>
                    <div class="input-password-wrap">
                        <input type="password" id="add_password" name="password"
                               class="form-control @error('password') is-error @enderror"
                               placeholder="Minimal 6 karakter">
                        <button type="button" class="toggle-pw" onclick="togglePassword('add_password', this)" tabindex="-1">
                            <i class="ri-eye-off-line"></i>
                        </button>
                    </div>
                    <p class="form-hint">Password akan dienkripsi secara otomatis.</p>
                    @error('password')<div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="add_role">Role <span>*</span></label>
                    <select id="add_role" name="role" class="form-control @error('role') is-error @enderror">
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>-- Pilih Role --</option>
                        <option value="owner"   {{ old('role') == 'owner'   ? 'selected' : '' }}>Owner</option>
                        <option value="admin"   {{ old('role') == 'admin'   ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="staff"   {{ old('role') == 'staff'   ? 'selected' : '' }}>Staff</option>
                    </select>
                    @error('role')<div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalTambah')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════ --}}
{{-- MODAL: EDIT PENGGUNA --}}
{{-- ══════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-edit-line"></i> Edit Pengguna
            </span>
            <button class="modal-close" onclick="closeModal('modalEdit')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">

                <div class="form-group">
                    <label class="form-label" for="edit_name">Nama Lengkap <span>*</span></label>
                    <input type="text" id="edit_name" name="name" class="form-control" placeholder="Nama lengkap" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_username">Username <span>*</span></label>
                    <input type="text" id="edit_username" name="username" class="form-control" placeholder="Username" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_password">Password Baru</label>
                    <div class="input-password-wrap">
                        <input type="password" id="edit_password" name="password"
                               class="form-control"
                               placeholder="Kosongkan jika tidak diubah">
                        <button type="button" class="toggle-pw" onclick="togglePassword('edit_password', this)" tabindex="-1">
                            <i class="ri-eye-off-line"></i>
                        </button>
                    </div>
                    <p class="form-hint">Biarkan kosong jika tidak ingin mengubah password.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_role">Role <span>*</span></label>
                    <select id="edit_role" name="role" class="form-control">
                        <option value="owner">Owner</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════ --}}
{{-- MODAL: HAPUS PENGGUNA --}}
{{-- ══════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-delete-bin-line" style="color:#EF4444;"></i> Konfirmasi Hapus
            </span>
            <button class="modal-close" onclick="closeModal('modalHapus')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="ri-alert-fill"></i>
                <h3>Hapus Pengguna?</h3>
                <p>Kamu akan menghapus pengguna <strong id="deleteUserName"></strong>.<br>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalHapus')">Batal</button>
            <form id="formHapus" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── MODAL UTILITIES ──
    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
    });

    // ── LIHAT PASSWORD ──
    // Catatan: password hash dari DB tidak bisa didekripsi.
    // Tombol ini menampilkan info akun (nama, username, role).
    // Untuk kebutuhan password plaintext, harus disimpan saat input (tidak direkomendasikan untuk produksi).
    function openViewPassword(name, username, role) {
        document.getElementById('vpName').textContent     = name;
        document.getElementById('vpUsername').textContent = username;
        document.getElementById('vpRole').textContent     = role.charAt(0).toUpperCase() + role.slice(1);
        document.getElementById('vpPassword').textContent = '••••••••  (terenkripsi / bcrypt)';
        openModal('modalViewPw');
    }

    function copyPassword() {
        const val = document.getElementById('vpUsername').textContent;
        navigator.clipboard.writeText(val).then(() => {
            const btn = document.getElementById('vpCopyBtn');
            btn.innerHTML = '<i class="ri-check-line"></i> Disalin!';
            setTimeout(() => { btn.innerHTML = '<i class="ri-file-copy-line"></i> Salin'; }, 1800);
        });
    }

    // ── EDIT MODAL ──
    function openEditModal(id, name, username, role) {
        document.getElementById('edit_name').value     = name;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_role').value     = role;
        document.getElementById('edit_password').value = '';
        document.getElementById('formEdit').action     = '/owner/user-login/' + id;
        openModal('modalEdit');
    }

    // ── DELETE MODAL ──
    function openDeleteModal(id, name) {
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('formHapus').action           = '/owner/user-login/' + id;
        openModal('modalHapus');
    }

    // ── TOGGLE PASSWORD VISIBILITY ──
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type     = 'text';
            icon.className = 'ri-eye-line';
        } else {
            input.type     = 'password';
            icon.className = 'ri-eye-off-line';
        }
    }

    // ── AUTO-OPEN MODAL TAMBAH jika ada validation error ──
    @if($errors->any())
    document.addEventListener('DOMContentLoaded', () => openModal('modalTambah'));
    @endif
</script>
@endpush