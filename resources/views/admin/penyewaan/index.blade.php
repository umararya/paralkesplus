{{-- resources/views/admin/penyewaan/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Penyewaan')
@section('breadcrumb', 'Penyewaan')

@push('styles')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .page-title { font-size:20px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:10px; line-height:1.2; }
    .page-title i { font-size:22px; color:var(--brand-500); }
    .page-subtitle { font-size:13px; color:var(--text-muted); margin-top:4px; }
    .table-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; transition:background 0.3s, border-color 0.3s; }
    .table-toolbar { padding:14px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .toolbar-left { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .toolbar-right { display:flex; align-items:center; gap:8px; }
    .per-page-wrap { display:flex; align-items:center; gap:7px; font-size:13px; color:var(--text-secondary); }
    .per-page-select { height:36px; padding:0 30px 0 10px; border:1px solid var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-primary); font-size:13px; font-family:var(--font); outline:none; cursor:pointer; transition:border-color 0.2s, box-shadow 0.2s; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; }
    .per-page-select:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .search-form { display:flex; align-items:center; gap:7px; }
    .search-input-wrap { display:flex; align-items:center; background:var(--bg-primary); border:1px solid var(--border); border-radius:8px; padding:0 11px; height:36px; gap:7px; transition:border-color 0.2s, box-shadow 0.2s; }
    .search-input-wrap:focus-within { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .search-input-wrap i { color:var(--text-muted); font-size:14px; flex-shrink:0; }
    .search-input-wrap input { border:none; background:transparent; outline:none; font-size:13px; color:var(--text-primary); font-family:var(--font); width:220px; }
    .search-input-wrap input::placeholder { color:var(--text-muted); }
    .toolbar-divider { width:1px; height:24px; background:var(--border); flex-shrink:0; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 14px; height:36px; border-radius:8px; font-size:13px; font-weight:500; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-search { background:var(--brand-50); color:var(--brand-500); border:1px solid var(--brand-100); }
    .btn-search:hover { background:var(--brand-100); color:var(--brand-600); }
    html.dark .btn-search { background:rgba(29,111,164,0.12); color:#60A5FA; border-color:rgba(29,111,164,0.25); }
    .btn-reset { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-reset:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); border-color:var(--brand-600); }
    .btn-danger { background:#EF4444; color:#fff; border:1px solid #EF4444; }
    .btn-danger:hover { background:#DC2626; border-color:#DC2626; }
    .btn-warning { background:#F59E0B; color:#fff; border:1px solid #F59E0B; }
    .btn-warning:hover { background:#D97706; border-color:#D97706; }
    .btn-success { background:#16A34A; color:#fff; border:1px solid #16A34A; }
    .btn-success:hover { background:#15803D; border-color:#15803D; }
    .btn-secondary { background:var(--bg-hover); color:var(--text-secondary); border:1px solid var(--border); }
    .btn-secondary:hover { background:var(--border); color:var(--text-primary); }
    .btn-monitoring { background:#7C3AED; color:#fff; border:1px solid #7C3AED; }
    .btn-monitoring:hover { background:#6D28D9; border-color:#6D28D9; }
    html.dark .btn-monitoring { background:#7C3AED; color:#fff; }

    .info-bar { padding:9px 18px; border-bottom:1px solid var(--border); background:var(--bg-primary); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px; }
    .info-bar-text { font-size:12.5px; color:var(--text-muted); display:flex; align-items:center; gap:6px; }
    .info-bar-text strong { color:var(--text-primary); }
    .badge-count { display:inline-flex; align-items:center; background:var(--brand-50); color:var(--brand-500); border:1px solid var(--brand-100); border-radius:99px; padding:1px 9px; font-size:11.5px; font-weight:600; }
    html.dark .badge-count { background:rgba(29,111,164,0.12); color:#60A5FA; border-color:rgba(29,111,164,0.25); }
    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead tr { background:var(--bg-primary); border-bottom:2px solid var(--border); }
    .data-table th { padding:10px 14px; text-align:left; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--text-muted); white-space:nowrap; }
    .data-table td { padding:11px 14px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); vertical-align:middle; }
    .data-table tbody tr:last-child td { border-bottom:none; }
    .data-table tbody tr { transition:background 0.15s; }
    .data-table tbody tr:hover td { background:var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align:center; }

    /* ── DROPDOWN AKSI ── */
    .action-wrap { position:relative; display:inline-block; }
    .btn-aksi-toggle { width:32px; height:32px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:18px; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); transition:all 0.2s; }
    .btn-aksi-toggle:hover { background:var(--brand-500); color:#fff; border-color:var(--brand-500); }
    .dropdown-menu-aksi { display:none; position:absolute; right:0; top:calc(100% + 6px); background:var(--bg-card); border:1px solid var(--border); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.15); z-index:200; min-width:185px; padding:5px; }
    .dropdown-menu-aksi.open { display:block; animation:fadeDropdown 0.15s ease; }
    @keyframes fadeDropdown { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
    .dropdown-item { display:flex; align-items:center; gap:9px; padding:8px 12px; border-radius:7px; font-size:13px; font-weight:500; color:var(--text-primary); text-decoration:none; cursor:pointer; border:none; background:none; width:100%; font-family:var(--font); transition:background 0.15s; }
    .dropdown-item:hover { background:var(--bg-hover); }
    .dropdown-item i { font-size:16px; width:18px; text-align:center; }
    .dropdown-item.item-edit i   { color:var(--brand-500); }
    .dropdown-item.item-invoice i { color:#16A34A; }
    .dropdown-item.item-perjanjian i { color:#7C3AED; }
    .dropdown-item.item-delete i { color:#EF4444; }
    .dropdown-item.item-delete:hover { background:#FFF1F2; color:#DC2626; }
    html.dark .dropdown-item.item-delete:hover { background:rgba(225,29,72,0.1); color:#FB7185; }
    .dropdown-divider { height:1px; background:var(--border); margin:4px 0; }

    .table-footer { padding:12px 18px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
    .pagination-meta { font-size:12.5px; color:var(--text-muted); }
    .pagination-meta strong { color:var(--text-primary); }
    .pagination-nav { display:flex; align-items:center; gap:3px; }
    .page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 6px; border-radius:7px; font-size:13px; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); text-decoration:none; cursor:pointer; transition:all 0.18s; font-family:var(--font); }
    .page-btn:hover { background:var(--bg-hover); color:var(--text-primary); border-color:var(--text-muted); }
    .page-btn.active { background:var(--brand-500); color:#fff; border-color:var(--brand-500); font-weight:700; }
    .page-btn.disabled { opacity:0.35; cursor:not-allowed; pointer-events:none; }
    .page-ellipsis { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; font-size:13px; color:var(--text-muted); }
    .empty-state { text-align:center; padding:56px 24px; }
    .empty-state i { font-size:48px; color:var(--border); display:block; margin-bottom:12px; }
    .empty-state h3 { font-size:15px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; }
    .empty-state p { font-size:13px; color:var(--text-muted); }
    .alert { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:500; margin-bottom:18px; border:1px solid transparent; }
    .alert-success { background:#F0FDF4; color:#15803D; border-color:#BBF7D0; }
    html.dark .alert-success { background:rgba(21,128,61,0.12); color:#4ADE80; border-color:rgba(21,128,61,0.25); }

    /* ===== MODAL BASE ===== */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from{opacity:0}to{opacity:1} }
    .modal { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.2); width:100%; animation:slideUp 0.2s ease; }
    .modal-sm { max-width:420px; }
    .modal-lg { max-width:860px; }
    @keyframes slideUp { from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)} }
    .modal-header { padding:18px 22px 14px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .modal-title { font-size:15px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .modal-close { width:28px; height:28px; border:none; background:none; cursor:pointer; color:var(--text-muted); font-size:19px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:all 0.2s; }
    .modal-close:hover { background:var(--bg-hover); color:var(--text-primary); }
    .modal-body { padding:18px 22px; }
    .modal-footer { padding:14px 22px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:flex-end; gap:8px; }
    .delete-warning { text-align:center; padding:6px 0; }
    .delete-warning i { font-size:42px; color:#EF4444; display:block; margin-bottom:10px; }
    .delete-warning h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:7px; }
    .delete-warning p { font-size:13px; color:var(--text-muted); line-height:1.6; }
    .delete-warning strong { color:var(--text-primary); }

    /* Status badge */
    .status-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:99px; font-size:12px; font-weight:600; white-space:nowrap; }
    .status-berjalan   { background:#F0FDF4; color:#16A34A; }
    .status-konfirmasi { background:#FFFBEB; color:#B45309; }
    .status-selesai    { background:#F0F9FF; color:#0369A1; }
    html.dark .status-berjalan   { background:rgba(22,163,74,0.12); color:#4ADE80; }
    html.dark .status-konfirmasi { background:rgba(180,83,9,0.12); color:#FCD34D; }
    html.dark .status-selesai    { background:rgba(3,105,161,0.12); color:#38BDF8; }

    .link-badge { display:inline-flex; align-items:center; gap:5px; background:var(--bg-hover); border:1px solid var(--border); border-radius:6px; padding:3px 10px; font-size:12px; color:var(--text-secondary); text-decoration:none; transition:all 0.2s; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .link-badge:hover { color:var(--brand-500); border-color:var(--brand-200); }
    .no-file { color:var(--text-muted); font-style:italic; font-size:12.5px; }

    .badge-mandiri { background:#F0FDF4; color:#16A34A; border-radius:6px; padding:2px 8px; font-size:12px; font-weight:600; }
    .badge-gosend  { background:#FFF7ED; color:#C2410C; border-radius:6px; padding:2px 8px; font-size:12px; font-weight:600; }
    .badge-rental  { background:#EFF6FF; color:#1D6FA4; border-radius:6px; padding:2px 8px; font-size:12px; font-weight:600; }
    html.dark .badge-mandiri { background:rgba(22,163,74,0.12); color:#4ADE80; }
    html.dark .badge-gosend  { background:rgba(194,65,12,0.12); color:#FB923C; }
    html.dark .badge-rental  { background:rgba(29,111,164,0.12); color:#38BDF8; }

    /* ===== MONITORING MODAL ===== */
    .monitoring-table { width:100%; border-collapse:collapse; font-size:13px; }
    .monitoring-table th { padding:9px 12px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--text-muted); background:var(--bg-primary); border-bottom:2px solid var(--border); white-space:nowrap; }
    .monitoring-table td { padding:10px 12px; border-bottom:1px solid var(--border); color:var(--text-primary); vertical-align:middle; }
    .monitoring-table tbody tr:last-child td { border-bottom:none; }
    .monitoring-table tbody tr:hover td { background:var(--bg-hover); }
    .sisa-hari-normal  { font-weight:700; color:#16A34A; }
    .sisa-hari-warning { font-weight:700; color:#B45309; }
    .sisa-hari-danger  { font-weight:700; color:#DC2626; }

    /* ===== SELESAIKAN MODAL ===== */
    .confirm-box { text-align:center; padding:10px 0 6px; }
    .confirm-box i { font-size:44px; color:#7C3AED; display:block; margin-bottom:10px; }
    .confirm-box h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:8px; }
    .confirm-box p { font-size:13px; color:var(--text-muted); line-height:1.6; }
    .confirm-box .durasi-info { display:inline-block; margin-top:8px; background:#FEF3C7; color:#92400E; border-radius:8px; padding:6px 16px; font-size:13px; font-weight:600; }
    html.dark .confirm-box .durasi-info { background:rgba(146,64,14,0.18); color:#FCD34D; }
    .action-buttons { display:flex; gap:8px; justify-content:center; flex-wrap:wrap; margin-top:14px; }
    .btn-full { width:100%; justify-content:center; height:40px; font-size:14px; }

    .konfirmasi-box { text-align:center; padding:8px 0; }
    .konfirmasi-box i { font-size:44px; color:#F59E0B; display:block; margin-bottom:10px; }
    .konfirmasi-box h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:6px; }
    .konfirmasi-box p { font-size:13px; color:var(--text-muted); line-height:1.6; }

    .monitoring-loading { text-align:center; padding:40px; color:var(--text-muted); font-size:14px; }
    .monitoring-loading i { font-size:32px; display:block; margin-bottom:8px; animation:spin 1s linear infinite; }
    @keyframes spin { from{transform:rotate(0deg)}to{transform:rotate(360deg)} }

    .extend-body { padding:18px 22px 10px; }
    .extend-body label { display:block; font-size:13px; color:var(--text-secondary); margin-bottom:6px; font-weight:500; }
    .extend-body input[type="date"] { width:100%; height:40px; border:1px solid var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-primary); font-size:14px; padding:0 12px; outline:none; font-family:var(--font); transition:border-color 0.2s, box-shadow 0.2s; }
    .extend-body input[type="date"]:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.12); }
    .extend-note { font-size:12px; color:var(--text-muted); margin-top:6px; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success">
    <i class="ri-checkbox-circle-fill"></i> {{ session('success') }}
</div>
@endif

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-store-2-line"></i> Penyewaan
        </h1>
        <p class="page-subtitle">Kelola data penyewaan alat kesehatan</p>
    </div>
</div>

{{-- Table Card --}}
<div class="table-card">

    {{-- TOOLBAR --}}
    <div class="table-toolbar">
        <div class="toolbar-left">
            <form method="GET" action="{{ route('penyewaan.index') }}" id="perPageForm">
                <input type="hidden" name="search" value="{{ $search }}">
                <div class="per-page-wrap">
                    <span>Tampilkan</span>
                    <select name="per_page" class="per-page-select"
                            onchange="document.getElementById('perPageForm').submit()">
                        @foreach([5, 10, 25, 50] as $pp)
                        <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                    <span>data</span>
                </div>
            </form>

            <div class="toolbar-divider"></div>

            <form method="GET" action="{{ route('penyewaan.index') }}" class="search-form">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama, telepon, produk, status..."
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                @if($search)
                <a href="{{ route('penyewaan.index', ['per_page' => $perPage]) }}" class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                @endif
            </form>
        </div>

        <div class="toolbar-right">
            <button type="button" class="btn btn-monitoring" onclick="openMonitoring()">
                <i class="ri-radar-line"></i> Monitoring
            </button>
            <a href="{{ route('penyewaan.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Input Data
            </a>
        </div>
    </div>

    {{-- INFO BAR --}}
    <div class="info-bar">
        <div class="info-bar-text">
            @if($search)
                <i class="ri-filter-3-line"></i>
                Hasil pencarian: <strong>"{{ $search }}"</strong>
                &nbsp;<span class="badge-count">{{ $penyewaans->total() }} data</span>
            @else
                <i class="ri-store-2-line"></i>
                Total <span class="badge-count">{{ $penyewaans->total() }} data</span>
            @endif
        </div>
        @if($penyewaans->total() > 0)
        <div class="info-bar-text">
            Halaman <strong>{{ $penyewaans->currentPage() }}</strong> dari <strong>{{ $penyewaans->lastPage() }}</strong>
        </div>
        @endif
    </div>

    {{-- TABLE --}}
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:42px;">#</th>
                    <th>Nama</th>
                    <th>No. Telepon/HP</th>
                    <th>Produk Alat Kesehatan</th>
                    <th class="center">Durasi (Hari)</th>
                    <th class="center">Pengiriman</th>
                    <th>Biaya Ongkir</th>
                    <th>Alamat Penyewa</th>
                    <th>Metode Pembayaran</th>
                    <th class="center">Bukti Pembayaran</th>
                    <th class="center">Foto KTP/SIM</th>
                    <th class="center">Status</th>
                    <th>Keterangan</th>
                    <th class="center" style="width:60px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penyewaans as $item)
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;font-weight:500;">
                        {{ $penyewaans->firstItem() + $loop->index }}
                    </td>
                    <td style="font-weight:600; white-space:nowrap;">
                        @if($search)
                            {!! preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                '<mark style="background:#FEF08A;border-radius:3px;padding:0 2px;">$1</mark>',
                                e($item->nama_penyewa)) !!}
                        @else
                            {{ $item->nama_penyewa }}
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="tel:{{ $item->nomor_telepon }}"
                           style="color:var(--text-primary); text-decoration:none; display:inline-flex; align-items:center; gap:5px;">
                            <i class="ri-phone-line" style="color:var(--brand-500); font-size:13px;"></i>
                            {{ $item->nomor_telepon }}
                        </a>
                    </td>
                    <td style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                        title="{{ $item->produk_alkes }}">
                        {{ $item->produk_alkes }}
                    </td>
                    <td class="center">
                        <span style="font-weight:600;">{{ $item->durasi_hari }}</span>
                        <span style="font-size:11px; color:var(--text-muted);"> hari</span>
                    </td>
                    <td class="center">
                        @php
                            $badgeClass = match($item->pengiriman) {
                                'mandiri'               => 'badge-mandiri',
                                'Gosend / GrabExpress'  => 'badge-gosend',
                                'Rental Mobil Paralkes' => 'badge-rental',
                                default                 => 'badge-mandiri',
                            };
                        @endphp
                        <span class="{{ $badgeClass }}">{{ $item->pengiriman_label }}</span>
                    </td>
                    <td style="white-space:nowrap;">
                        {{ $item->biaya_ongkir > 0 ? $item->biaya_ongkir_formatted : '—' }}
                    </td>
                    <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                        title="{{ $item->alamat_penyewa }}">
                        {{ $item->alamat_penyewa }}
                    </td>
                    <td style="white-space:nowrap;">
                        <span style="display:inline-flex; align-items:center; gap:5px;">
                            <i class="ri-bank-card-line" style="color:var(--brand-500); font-size:13px;"></i>
                            {{ $item->metode_pembayaran }}
                        </span>
                    </td>
                    <td class="center">
                        @if($item->bukti_pembayaran)
                            @if(str_starts_with($item->bukti_pembayaran, 'http'))
                                <a href="{{ $item->bukti_pembayaran }}" target="_blank" class="link-badge">
                                    <i class="ri-external-link-line"></i> Lihat
                                </a>
                            @else
                                <span style="font-size:12px; color:var(--text-secondary);">{{ $item->bukti_pembayaran }}</span>
                            @endif
                        @else
                            <span class="no-file">—</span>
                        @endif
                    </td>
                    <td class="center">
                        @if($item->foto_ktp_sim)
                            <a href="{{ asset('storage/' . $item->foto_ktp_sim) }}"
                               target="_blank" class="link-badge">
                                <i class="ri-id-card-line"></i> Lihat
                            </a>
                        @else
                            <span class="no-file">—</span>
                        @endif
                    </td>
                    <td class="center">
                        <span class="status-badge {{ $item->status_class }}">
                            {{ $item->status_label }}
                        </span>
                    </td>
                    <td style="max-width:140px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
                               color:var(--text-muted); font-size:12.5px;"
                        title="{{ $item->keterangan }}">
                        {{ $item->keterangan ?: '—' }}
                    </td>

                    {{-- ── KOLOM AKSI: DROPDOWN ── --}}
                    <td class="center">
                        <div class="action-wrap">
                            <button class="btn-aksi-toggle"
                                    onclick="toggleDropdown(this)"
                                    title="Aksi">
                                <i class="ri-more-2-fill"></i>
                            </button>
                            <div class="dropdown-menu-aksi">
                                <a href="{{ route('penyewaan.edit', $item->id) }}"
                                   class="dropdown-item item-edit">
                                    <i class="ri-edit-line"></i> Edit Data
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('penyewaan.invoice', $item->id) }}"
                                   target="_blank"
                                   class="dropdown-item item-invoice">
                                    <i class="ri-receipt-line"></i> Cetak Invoice
                                </a>
                                <a href="{{ route('penyewaan.perjanjian', $item->id) }}"
                                   target="_blank"
                                   class="dropdown-item item-perjanjian">
                                    <i class="ri-file-text-line"></i> Cetak Perjanjian
                                </a>
                                <div class="dropdown-divider"></div>
                                <button type="button"
                                        class="dropdown-item item-delete"
                                        onclick="closeAllDropdowns(); openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_penyewa) }}')">
                                    <i class="ri-delete-bin-line"></i> Hapus Data
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14">
                        <div class="empty-state">
                            <i class="ri-store-2-line"></i>
                            <h3>{{ $search ? 'Tidak ditemukan' : 'Belum ada data penyewaan' }}</h3>
                            <p>{{ $search ? 'Coba kata kunci lain atau klik Reset.' : 'Klik "Input Data" untuk mencatat penyewaan baru.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($penyewaans->total() > 0)
    <div class="table-footer">
        <div class="pagination-meta">
            Menampilkan
            <strong>{{ $penyewaans->firstItem() }} – {{ $penyewaans->lastItem() }}</strong>
            dari <strong>{{ $penyewaans->total() }}</strong> data
        </div>
        @if($penyewaans->hasPages())
        <nav class="pagination-nav">
            @if($penyewaans->onFirstPage())
                <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
            @else
                <a class="page-btn" href="{{ $penyewaans->previousPageUrl() }}"><i class="ri-arrow-left-s-line"></i></a>
            @endif
            @php
                $current = $penyewaans->currentPage();
                $last    = $penyewaans->lastPage();
                $pages   = [];
                for ($p = 1; $p <= $last; $p++) {
                    if ($p === 1 || $p === $last || ($p >= $current - 2 && $p <= $current + 2)) {
                        $pages[] = $p;
                    }
                }
                $rendered = []; $prev = null;
                foreach ($pages as $p) {
                    if ($prev !== null && $p - $prev > 1) $rendered[] = '...';
                    $rendered[] = $p;
                    $prev = $p;
                }
            @endphp
            @foreach($rendered as $rItem)
                @if($rItem === '...')
                    <span class="page-ellipsis">…</span>
                @elseif($rItem == $current)
                    <span class="page-btn active">{{ $rItem }}</span>
                @else
                    <a class="page-btn" href="{{ $penyewaans->url($rItem) }}">{{ $rItem }}</a>
                @endif
            @endforeach
            @if($penyewaans->hasMorePages())
                <a class="page-btn" href="{{ $penyewaans->nextPageUrl() }}"><i class="ri-arrow-right-s-line"></i></a>
            @else
                <span class="page-btn disabled"><i class="ri-arrow-right-s-line"></i></span>
            @endif
        </nav>
        @endif
    </div>
    @endif

</div>

{{-- ======================== MODAL HAPUS ======================== --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-delete-bin-line" style="color:#EF4444;"></i> Hapus Data
            </span>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="ri-error-warning-line"></i>
                <h3>Yakin ingin menghapus?</h3>
                <p>Data penyewaan atas nama <strong id="modal-nama">-</strong> akan dihapus permanen beserta file KTP/SIM-nya dan tidak dapat dikembalikan.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-reset" onclick="closeDeleteModal()">
                <i class="ri-close-line"></i> Batal
            </button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ======================== MODAL MONITORING ======================== --}}
<div class="modal-overlay" id="modalMonitoring">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-radar-line" style="color:#7C3AED;"></i> Monitoring Penyewaan Aktif
            </span>
            <button class="modal-close" onclick="closeMonitoring()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body" style="padding:0;">
            <div id="monitoringContent">
                <div class="monitoring-loading">
                    <i class="ri-loader-4-line"></i>
                    Memuat data...
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================== MODAL SELESAIKAN — Kondisi 1 (durasi > 3) ======================== --}}
<div class="modal-overlay" id="modalSelesaikanNormal">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-checkbox-circle-line" style="color:#16A34A;"></i> Selesaikan Penyewaan
            </span>
            <button class="modal-close" onclick="closeSelesaikanNormal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="confirm-box">
                <i class="ri-checkbox-circle-line" style="color:#16A34A;"></i>
                <h3>Apakah anda ingin menyelesaikan penyewaan?</h3>
                <p>
                    <span class="durasi-info">Durasi penyewaan masih <span id="sisaHariNormal">0</span> hari lagi</span>
                </p>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:center;">
            <button class="btn btn-reset" onclick="closeSelesaikanNormal()">
                <i class="ri-close-line"></i> Batal
            </button>
            <button class="btn btn-success" id="btnSelesaikanNormal" onclick="doSelesaikan('selesai_sekarang')">
                <i class="ri-checkbox-circle-line"></i> Selesaikan Penyewaan
            </button>
        </div>
    </div>
</div>

{{-- ======================== MODAL SELESAIKAN — Kondisi 2 (durasi <= 3) ======================== --}}
<div class="modal-overlay" id="modalKonfirmasiDulu">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-error-warning-line" style="color:#F59E0B;"></i> Perlu Konfirmasi
            </span>
            <button class="modal-close" onclick="closeKonfirmasiDulu()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="konfirmasi-box">
                <i class="ri-error-warning-line"></i>
                <h3>Diharap melakukan konfirmasi ke customer terlebih dahulu!</h3>
                <p>Hubungi customer dan tanyakan kelanjutan penyewaan sebelum mengambil tindakan.</p>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:center; gap:10px;">
            <button class="btn btn-success" onclick="sudahKonfirmasi()">
                <i class="ri-check-line"></i> Sudah Konfirmasi
            </button>
            <button class="btn btn-reset" onclick="closeKonfirmasiDulu()">
                <i class="ri-close-line"></i> Belum Konfirmasi
            </button>
        </div>
    </div>
</div>

{{-- ======================== MODAL PILIH ACTION ======================== --}}
<div class="modal-overlay" id="modalPilihAction">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-settings-3-line" style="color:#7C3AED;"></i> Pilih Action
            </span>
            <button class="modal-close" onclick="closePilihAction()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="confirm-box">
                <i class="ri-settings-3-line" style="color:#7C3AED; font-size:40px;"></i>
                <h3 style="margin-bottom:4px;">Pilih action berdasarkan hasil konfirmasi customer</h3>
            </div>
            <div class="action-buttons" style="flex-direction:column; margin-top:12px;">
                <button class="btn btn-danger btn-full" onclick="doSelesaikan('selesai_sekarang')">
                    <i class="ri-checkbox-circle-line"></i> Selesai Sekarang
                </button>
                <button class="btn btn-primary btn-full" onclick="doSelesaikan('sesuai_deadline')">
                    <i class="ri-calendar-check-line"></i> Sesuai Deadline
                </button>
                <button class="btn btn-warning btn-full" onclick="openExtend()">
                    <i class="ri-calendar-2-line"></i> Extend
                </button>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-reset" onclick="closePilihAction()">
                <i class="ri-close-line"></i> Batal
            </button>
        </div>
    </div>
</div>

{{-- ======================== MODAL EXTEND ======================== --}}
<div class="modal-overlay" id="modalExtend">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-calendar-2-line" style="color:#F59E0B;"></i> Extend Deadline
            </span>
            <button class="modal-close" onclick="closeExtend()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="confirm-box" style="padding-bottom:0;">
                <p style="font-size:14px; font-weight:600; color:var(--text-primary); margin-bottom:14px;">
                    Tambahkan deadline baru pada tanggal
                </p>
            </div>
            <div class="extend-body" style="padding:0 0 10px 0;">
                <label>Pilih tanggal extend:</label>
                <input type="date" id="extendTanggal" min="">
                <p class="extend-note" id="extendNote">Dihitung dari deadline awal.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-reset" onclick="closeExtend()">
                <i class="ri-close-line"></i> Batal
            </button>
            <button class="btn btn-warning" onclick="doExtend()">
                <i class="ri-calendar-2-line"></i> Simpan Extend
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ===================== DROPDOWN AKSI =====================
function toggleDropdown(btn) {
    const menu = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');
    closeAllDropdowns();
    if (!isOpen) menu.classList.add('open');
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu-aksi.open').forEach(m => m.classList.remove('open'));
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-wrap')) closeAllDropdowns();
});

// ===================== HAPUS =====================
function openDeleteModal(id, nama) {
    document.getElementById('modal-nama').textContent = nama;
    document.getElementById('deleteForm').action = '/penyewaan/' + id;
    document.getElementById('modalHapus').classList.add('open');
}
function closeDeleteModal() {
    document.getElementById('modalHapus').classList.remove('open');
}
document.getElementById('modalHapus').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// ===================== MONITORING =====================
let currentPenyewaanId = null;
let currentSisaHari    = 0;
let currentTglSelesai  = null;

function openMonitoring() {
    document.getElementById('modalMonitoring').classList.add('open');
    loadMonitoringData();
    if (window.location.hash === '#monitoring') {
        history.replaceState(null, '', window.location.pathname + window.location.search);
    }
}
function closeMonitoring() {
    document.getElementById('modalMonitoring').classList.remove('open');
}
document.getElementById('modalMonitoring').addEventListener('click', function(e) {
    if (e.target === this) closeMonitoring();
});

function loadMonitoringData() {
    const el = document.getElementById('monitoringContent');
    el.innerHTML = `<div class="monitoring-loading"><i class="ri-loader-4-line"></i>Memuat data...</div>`;

    fetch('{{ route("penyewaan.monitoring") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.length) {
            el.innerHTML = `
                <div class="monitoring-loading" style="color:var(--text-muted);">
                    <i class="ri-inbox-2-line" style="animation:none; font-size:40px;"></i>
                    Tidak ada penyewaan aktif saat ini.
                </div>`;
            return;
        }
        let rows = data.map(d => {
            const sisaClass = d.sisa_hari <= 0 ? 'sisa-hari-danger' : (d.sisa_hari <= 3 ? 'sisa-hari-warning' : 'sisa-hari-normal');
            const sisaText  = d.sisa_hari <= 0 ? 'Lewat deadline' : d.sisa_hari + ' hari';
            return `<tr>
                <td style="font-weight:600;">${d.nama}</td>
                <td>${d.nomor_hp}</td>
                <td style="max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${d.barang}">${d.barang}</td>
                <td style="max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${d.alamat}">${d.alamat}</td>
                <td class="center"><span class="${sisaClass}">${sisaText}</span></td>
                <td class="center"><span class="status-badge ${d.status_class}">${d.status_label}</span></td>
                <td class="center">
                    <button class="btn btn-sm" style="height:30px; padding:0 12px; font-size:12px; background:#7C3AED; color:#fff; border:none; border-radius:7px; cursor:pointer;"
                            onclick="openSelesaikan(${d.id}, ${d.sisa_hari}, '${d.tgl_selesai}')">
                        <i class="ri-check-double-line"></i> Selesaikan
                    </button>
                </td>
            </tr>`;
        }).join('');

        el.innerHTML = `
            <div style="overflow-x:auto;">
                <table class="monitoring-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Nomor HP</th>
                            <th>Barang</th>
                            <th>Alamat</th>
                            <th class="center">Durasi</th>
                            <th class="center">Status</th>
                            <th class="center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    })
    .catch(() => {
        el.innerHTML = `<div class="monitoring-loading" style="color:#EF4444;">Gagal memuat data. Coba lagi.</div>`;
    });
}

// ===================== SELESAIKAN =====================
function openSelesaikan(id, sisaHari, tglSelesai) {
    currentPenyewaanId = id;
    currentSisaHari    = sisaHari;
    currentTglSelesai  = tglSelesai;

    if (sisaHari > 3) {
        document.getElementById('sisaHariNormal').textContent = sisaHari;
        document.getElementById('modalSelesaikanNormal').classList.add('open');
    } else {
        document.getElementById('modalKonfirmasiDulu').classList.add('open');
    }
}

function closeSelesaikanNormal() { document.getElementById('modalSelesaikanNormal').classList.remove('open'); }
function closeKonfirmasiDulu()   { document.getElementById('modalKonfirmasiDulu').classList.remove('open'); }
function closePilihAction()      { document.getElementById('modalPilihAction').classList.remove('open'); }

function sudahKonfirmasi() {
    closeKonfirmasiDulu();
    document.getElementById('modalPilihAction').classList.add('open');
}

function doSelesaikan(action) {
    if (!currentPenyewaanId) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    fetch(`/penyewaan/${currentPenyewaanId}/selesaikan`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ action })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeSelesaikanNormal();
            closePilihAction();
            loadMonitoringData();
            setTimeout(() => location.reload(), 500);
        }
    });
}

// ===================== EXTEND =====================
function openExtend() {
    closePilihAction();
    if (currentTglSelesai) {
        const d = new Date(currentTglSelesai);
        d.setDate(d.getDate() + 1);
        const minDate = d.toISOString().split('T')[0];
        document.getElementById('extendTanggal').min   = minDate;
        document.getElementById('extendTanggal').value = minDate;
        const tglLabel = new Date(currentTglSelesai).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'});
        document.getElementById('extendNote').textContent = `Deadline awal: ${tglLabel}. Extend dihitung mulai dari tanggal tersebut.`;
    }
    document.getElementById('modalExtend').classList.add('open');
}
function closeExtend() { document.getElementById('modalExtend').classList.remove('open'); }

function doExtend() {
    const tglBaru = document.getElementById('extendTanggal').value;
    if (!tglBaru || !currentPenyewaanId) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    fetch(`/penyewaan/${currentPenyewaanId}/extend`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ tgl_selesai_baru: tglBaru })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeExtend();
            loadMonitoringData();
            setTimeout(() => location.reload(), 500);
        }
    });
}

// ===================== ESCAPE KEY =====================
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeMonitoring();
        closeSelesaikanNormal();
        closeKonfirmasiDulu();
        closePilihAction();
        closeExtend();
        closeAllDropdowns();
    }
});

['modalSelesaikanNormal','modalKonfirmasiDulu','modalPilihAction','modalExtend'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});

// ===================== AUTO-OPEN MONITORING DARI HASH #monitoring =====================
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#monitoring') {
        setTimeout(openMonitoring, 200);
    }
});
</script>
@endpush