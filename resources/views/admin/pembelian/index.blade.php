{{-- resources/views/admin/pembelian/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Pembelian Barang')
@section('breadcrumb', 'Pembelian Barang')

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

    /* ── Table Card ── */
    .table-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
        transition: background 0.3s, border-color 0.3s;
    }

    /* ── Toolbar ── */
    .table-toolbar {
        padding: 14px 18px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        gap: 10px; flex-wrap: wrap;
    }
    .toolbar-left  { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .toolbar-right { display: flex; align-items: center; gap: 8px; }

    /* Per-page selector */
    .per-page-wrap {
        display: flex; align-items: center; gap: 7px;
        font-size: 13px; color: var(--text-secondary);
    }
    .per-page-select {
        height: 36px; padding: 0 30px 0 10px;
        border: 1px solid var(--border); border-radius: 8px;
        background: var(--bg-primary); color: var(--text-primary);
        font-size: 13px; font-family: var(--font); outline: none;
        cursor: pointer; transition: border-color 0.2s, box-shadow 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 8px center;
    }
    .per-page-select:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(29,111,164,0.1); }

    /* Search */
    .search-form { display: flex; align-items: center; gap: 7px; }
    .search-input-wrap {
        display: flex; align-items: center;
        background: var(--bg-primary); border: 1px solid var(--border);
        border-radius: 8px; padding: 0 11px; height: 36px; gap: 7px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-input-wrap:focus-within {
        border-color: var(--brand-500);
        box-shadow: 0 0 0 3px rgba(29,111,164,0.1);
    }
    .search-input-wrap i { color: var(--text-muted); font-size: 14px; flex-shrink: 0; }
    .search-input-wrap input {
        border: none; background: transparent; outline: none;
        font-size: 13px; color: var(--text-primary);
        font-family: var(--font); width: 220px;
    }
    .search-input-wrap input::placeholder { color: var(--text-muted); }

    /* Divider */
    .toolbar-divider {
        width: 1px; height: 24px; background: var(--border); flex-shrink: 0;
    }

    /* Buttons */
    .btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0 14px; height: 36px; border-radius: 8px;
        font-size: 13px; font-weight: 500; font-family: var(--font);
        cursor: pointer; border: none; transition: all 0.2s;
        text-decoration: none; white-space: nowrap;
    }
    .btn i { font-size: 15px; }
    .btn-search {
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100);
    }
    .btn-search:hover { background: var(--brand-100); color: var(--brand-600); }
    html.dark .btn-search { background: rgba(29,111,164,0.12); color: #60A5FA; border-color: rgba(29,111,164,0.25); }
    html.dark .btn-search:hover { background: rgba(29,111,164,0.22); }

    .btn-reset {
        background: transparent; color: var(--text-secondary); border: 1px solid var(--border);
    }
    .btn-reset:hover { background: var(--bg-hover); color: var(--text-primary); }

    .btn-primary { background: var(--brand-500); color: #fff; border: 1px solid var(--brand-500); }
    .btn-primary:hover { background: var(--brand-600); border-color: var(--brand-600); }
    .btn-ghost { background: transparent; color: var(--text-secondary); border: 1px solid var(--border); }
    .btn-ghost:hover { background: var(--bg-hover); color: var(--text-primary); }
    .btn-danger { background: #EF4444; color: #fff; border: 1px solid #EF4444; }
    .btn-danger:hover { background: #DC2626; border-color: #DC2626; }

    /* Info bar */
    .info-bar {
        padding: 9px 18px; border-bottom: 1px solid var(--border);
        background: var(--bg-primary);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 6px;
    }
    .info-bar-text { font-size: 12.5px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
    .info-bar-text strong { color: var(--text-primary); }
    .badge-count {
        display: inline-flex; align-items: center;
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100); border-radius: 99px;
        padding: 1px 9px; font-size: 11.5px; font-weight: 600;
    }
    html.dark .badge-count { background: rgba(29,111,164,0.12); color: #60A5FA; border-color: rgba(29,111,164,0.25); }

    /* ── Table ── */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead tr { background: var(--bg-primary); border-bottom: 2px solid var(--border); }
    .data-table th {
        padding: 10px 16px; text-align: left;
        font-size: 10.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.7px; color: var(--text-muted); white-space: nowrap;
    }
    .data-table td {
        padding: 13px 16px; font-size: 13.5px; color: var(--text-primary);
        border-bottom: 1px solid var(--border); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.15s; }
    .data-table tbody tr:hover td { background: var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align: center; }
    .data-table th.right,  .data-table td.right  { text-align: right; }

    /* Action buttons */
    .action-group { display: flex; align-items: center; gap: 4px; justify-content: center; }
    .btn-action {
        width: 30px; height: 30px; border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 15px; cursor: pointer;
        border: 1px solid var(--border); background: var(--bg-card);
        color: var(--text-secondary); transition: all 0.2s;
    }
    .btn-action:hover { background: var(--bg-hover); color: var(--text-primary); }
    .btn-action.edit:hover   { background: #EFF6FF; color: var(--brand-500); border-color: var(--brand-100); }
    .btn-action.delete:hover { background: #FFF1F2; color: #E11D48; border-color: #FFE4E6; }
    html.dark .btn-action.edit:hover   { background: rgba(29,111,164,0.15); color: #60A5FA; border-color: rgba(29,111,164,0.3); }
    html.dark .btn-action.delete:hover { background: rgba(225,29,72,0.12); color: #FB7185; border-color: rgba(225,29,72,0.25); }

    /* ── PAGINATION ── */
    .table-footer {
        padding: 12px 18px; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px;
    }
    .pagination-meta { font-size: 12.5px; color: var(--text-muted); }
    .pagination-meta strong { color: var(--text-primary); }
    .pagination-nav { display: flex; align-items: center; gap: 3px; }
    .page-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px; padding: 0 6px;
        border-radius: 7px; font-size: 13px;
        border: 1px solid var(--border); background: var(--bg-card);
        color: var(--text-secondary); text-decoration: none;
        cursor: pointer; transition: all 0.18s; font-family: var(--font);
        white-space: nowrap;
    }
    .page-btn:hover { background: var(--bg-hover); color: var(--text-primary); border-color: var(--text-muted); }
    .page-btn.active {
        background: var(--brand-500); color: #fff;
        border-color: var(--brand-500); font-weight: 700;
    }
    .page-btn.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }
    .page-ellipsis {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px;
        font-size: 13px; color: var(--text-muted);
    }

    /* ── Empty state ── */
    .empty-state { text-align: center; padding: 56px 24px; }
    .empty-state i { font-size: 48px; color: var(--border); display: block; margin-bottom: 12px; }
    .empty-state h3 { font-size: 15px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
    .empty-state p  { font-size: 13px; color: var(--text-muted); }

    /* ── Flash Alert ── */
    .alert {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 16px; border-radius: 10px; font-size: 13.5px;
        font-weight: 500; margin-bottom: 18px; border: 1px solid transparent;
    }
    .alert-success { background: #F0FDF4; color: #15803D; border-color: #BBF7D0; }
    .alert-error   { background: #FFF1F2; color: #BE123C; border-color: #FECDD3; }
    html.dark .alert-success { background: rgba(21,128,61,0.12); color: #4ADE80; border-color: rgba(21,128,61,0.25); }
    html.dark .alert-error   { background: rgba(190,18,60,0.12); color: #FB7185; border-color: rgba(190,18,60,0.25); }

    /* ── Modal ── */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.45); z-index: 1000;
        align-items: center; justify-content: center;
        padding: 16px; backdrop-filter: blur(2px);
    }
    .modal-overlay.open { display: flex; animation: fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from { opacity:0; } to { opacity:1; } }

    .modal {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        width: 100%; max-width: 460px; animation: slideUp 0.2s ease;
    }
    @keyframes slideUp {
        from { opacity:0; transform: translateY(18px); }
        to   { opacity:1; transform: translateY(0); }
    }
    .modal-header {
        padding: 18px 22px 14px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title {
        font-size: 15px; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 8px;
    }
    .modal-title i { font-size: 17px; color: var(--brand-500); }
    .modal-close {
        width: 28px; height: 28px; border: none; background: none;
        cursor: pointer; color: var(--text-muted); font-size: 19px;
        border-radius: 6px; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .modal-close:hover { background: var(--bg-hover); color: var(--text-primary); }
    .modal-body { padding: 18px 22px; }
    .modal-footer {
        padding: 14px 22px; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: flex-end; gap: 8px;
    }

    /* Delete warning */
    .delete-warning { text-align: center; padding: 6px 0; }
    .delete-warning i { font-size: 42px; color: #EF4444; display: block; margin-bottom: 10px; }
    .delete-warning h3 { font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 7px; }
    .delete-warning p { font-size: 13px; color: var(--text-muted); line-height: 1.6; }
    .delete-warning strong { color: var(--text-primary); }

    /* Badges */
    .qty-badge {
        display: inline-flex; align-items: center;
        background: #EFF6FF; color: #1D4ED8;
        padding: 2px 10px; border-radius: 20px;
        font-size: 12.5px; font-weight: 700;
    }
    html.dark .qty-badge { background: rgba(29,78,216,0.15); color: #60A5FA; }

    .total-value { font-weight: 700; color: #059669; }
    html.dark .total-value { color: #34D399; }

    .tanggal-badge {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--bg-hover); padding: 3px 10px;
        border-radius: 6px; font-size: 12.5px; font-weight: 500;
        color: var(--text-primary);
    }

    /* tfoot total row */
    .tfoot-total td {
        padding: 12px 16px; font-size: 13px; font-weight: 700;
        color: var(--text-primary); background: var(--bg-hover);
        border-top: 2px solid var(--border);
    }
</style>
@endpush

@section('content')

{{-- Flash Messages --}}
@if(session('success'))
<div class="alert alert-success">
    <i class="ri-checkbox-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-error">
    <i class="ri-error-warning-fill"></i> {{ session('error') }}
</div>
@endif

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-shopping-cart-2-line"></i> Pembelian Barang
        </h1>
        <p class="page-subtitle">Kelola data pembelian barang dan alat kesehatan</p>
    </div>
</div>

{{-- Table Card --}}
<div class="table-card">

    {{-- ── TOOLBAR ── --}}
    <div class="table-toolbar">

        {{-- KIRI: Per-page + Divider + Search --}}
        <div class="toolbar-left">

            {{-- Per-page selector --}}
            <form method="GET" action="{{ route('pembelian.index') }}" id="perPageForm">
                <input type="hidden" name="search" value="{{ $search }}">
                <div class="per-page-wrap">
                    <span>Tampilkan</span>
                    <select
                        name="per_page"
                        class="per-page-select"
                        onchange="document.getElementById('perPageForm').submit()"
                        title="Jumlah baris per halaman"
                    >
                        @foreach([5, 10, 25, 50] as $pp)
                        <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                    <span>data</span>
                </div>
            </form>

            <div class="toolbar-divider"></div>

            {{-- Search form (server-side) --}}
            <form method="GET" action="{{ route('pembelian.index') }}" class="search-form" id="searchForm">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input
                        type="text"
                        name="search"
                        id="searchInput"
                        value="{{ $search }}"
                        placeholder="Cari nama barang, keterangan, tanggal..."
                        autocomplete="off"
                    >
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                @if($search)
                <a href="{{ route('pembelian.index', ['per_page' => $perPage]) }}" class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                @endif
            </form>
        </div>

        {{-- KANAN: Tombol Tambah --}}
        <div class="toolbar-right">
            <a href="{{ route('pembelian.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Pembelian
            </a>
        </div>
    </div>

    {{-- ── INFO BAR ── --}}
    <div class="info-bar">
        <div class="info-bar-text">
            @if($search)
                <i class="ri-filter-3-line"></i>
                Hasil pencarian: <strong>"{{ $search }}"</strong>
                &nbsp;<span class="badge-count">{{ $pembelians->total() }} data</span>
            @else
                <i class="ri-shopping-cart-line"></i>
                Total <span class="badge-count">{{ $pembelians->total() }} data</span>
            @endif
        </div>
        @if($pembelians->total() > 0)
        <div class="info-bar-text">
            Halaman <strong>{{ $pembelians->currentPage() }}</strong> dari <strong>{{ $pembelians->lastPage() }}</strong>
        </div>
        @endif
    </div>

    {{-- ── TABLE ── --}}
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:46px;">#</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th class="center">Qty</th>
                    <th class="right">Harga Satuan</th>
                    <th class="right">Total</th>
                    <th>Keterangan</th>
                    <th class="center" style="width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembelians as $item)
                <tr>
                    {{-- No --}}
                    <td style="color:var(--text-muted); font-size:12.5px; font-weight:500;">
                        {{ $pembelians->firstItem() + $loop->index }}
                    </td>

                    {{-- Tanggal --}}
                    <td>
                        <span class="tanggal-badge">
                            <i class="ri-calendar-line" style="font-size:12px; color:var(--text-muted);"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y') }}
                        </span>
                    </td>

                    {{-- Nama Barang --}}
                    <td style="font-weight:600;">
                        @if($search)
                            {!! preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                '<mark style="background:#FEF08A; border-radius:3px; padding:0 2px;">$1</mark>',
                                e($item->nama_barang)) !!}
                        @else
                            {{ $item->nama_barang }}
                        @endif
                    </td>

                    {{-- Qty --}}
                    <td class="center">
                        <span class="qty-badge">{{ number_format($item->jumlah) }}</span>
                    </td>

                    {{-- Harga Satuan --}}
                    <td class="right" style="font-size:13px; white-space:nowrap;">
                        {{ $item->harga_formatted }}
                    </td>

                    {{-- Total --}}
                    <td class="right total-value" style="font-size:13px; white-space:nowrap;">
                        {{ $item->total_formatted }}
                    </td>

                    {{-- Keterangan --}}
                    <td style="max-width:180px;">
                        @if($item->keterangan)
                            <span title="{{ $item->keterangan }}"
                                  style="display:block; overflow:hidden; text-overflow:ellipsis;
                                         white-space:nowrap; font-size:13px; color:var(--text-muted);">
                                @if($search)
                                    {!! preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                        '<mark style="background:#FEF08A; border-radius:3px; padding:0 2px;">$1</mark>',
                                        e($item->keterangan)) !!}
                                @else
                                    {{ $item->keterangan }}
                                @endif
                            </span>
                        @else
                            <span style="color:var(--text-muted); font-style:italic; font-size:13px;">—</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="center">
                        <div class="action-group">
                            {{-- Edit --}}
                            <a href="{{ route('pembelian.edit', $item->id) }}"
                               class="btn-action edit" title="Edit data">
                                <i class="ri-edit-line"></i>
                            </a>
                            {{-- Hapus --}}
                            <button type="button"
                                    class="btn-action delete"
                                    title="Hapus data"
                                    onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_barang) }}')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            {{-- Hidden form hapus --}}
                            <form id="formHapus-{{ $item->id }}"
                                  action="{{ route('pembelian.destroy', $item->id) }}"
                                  method="POST" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="ri-shopping-cart-2-line"></i>
                            <h3>{{ $search ? 'Tidak ditemukan' : 'Belum ada data pembelian' }}</h3>
                            <p>{{ $search ? 'Coba kata kunci lain atau klik Reset.' : 'Klik "Tambah Pembelian" untuk mencatat pembelian baru.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>

            {{-- Footer: Total keseluruhan (dari query yang sudah difilter) --}}
            @if($pembelians->count() > 0)
            <tfoot>
                <tr class="tfoot-total">
                    <td colspan="5" class="right">
                        Total {{ $search ? 'hasil filter' : 'keseluruhan' }}:
                    </td>
                    <td class="right total-value" style="font-size:14px; white-space:nowrap;">
                        Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- ── PAGINATION SERVER-SIDE ── --}}
    @if($pembelians->total() > 0)
    <div class="table-footer">

        {{-- Meta info --}}
        <div class="pagination-meta">
            Menampilkan
            <strong>{{ $pembelians->firstItem() }} – {{ $pembelians->lastItem() }}</strong>
            dari <strong>{{ $pembelians->total() }}</strong> data
        </div>

        {{-- Pagination nav --}}
        @if($pembelians->hasPages())
        <nav class="pagination-nav" aria-label="Pagination">

            {{-- Prev --}}
            @if($pembelians->onFirstPage())
                <span class="page-btn disabled" title="Halaman sebelumnya">
                    <i class="ri-arrow-left-s-line"></i>
                </span>
            @else
                <a class="page-btn" href="{{ $pembelians->previousPageUrl() }}" title="Halaman sebelumnya">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            @endif

            {{-- Smart page numbers --}}
            @php
                $current = $pembelians->currentPage();
                $last    = $pembelians->lastPage();
                $window  = 2;
                $pages   = [];

                for ($p = 1; $p <= $last; $p++) {
                    if (
                        $p === 1 || $p === $last ||
                        ($p >= $current - $window && $p <= $current + $window)
                    ) {
                        $pages[] = $p;
                    }
                }

                $rendered = [];
                $prev = null;
                foreach ($pages as $p) {
                    if ($prev !== null && $p - $prev > 1) {
                        $rendered[] = '...';
                    }
                    $rendered[] = $p;
                    $prev = $p;
                }
            @endphp

            @foreach($rendered as $item)
                @if($item === '...')
                    <span class="page-ellipsis">…</span>
                @elseif($item == $current)
                    <span class="page-btn active">{{ $item }}</span>
                @else
                    <a class="page-btn"
                       href="{{ $pembelians->url($item) }}"
                       title="Halaman {{ $item }}">{{ $item }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($pembelians->hasMorePages())
                <a class="page-btn" href="{{ $pembelians->nextPageUrl() }}" title="Halaman selanjutnya">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            @else
                <span class="page-btn disabled" title="Halaman selanjutnya">
                    <i class="ri-arrow-right-s-line"></i>
                </span>
            @endif

        </nav>
        @endif

    </div>
    @endif

</div>{{-- /table-card --}}


{{-- ══════════════ MODAL: KONFIRMASI HAPUS ══════════════ --}}
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
                <h3>Hapus Data Pembelian?</h3>
                <p>Kamu akan menghapus data pembelian:<br><strong id="deleteNamaBarang"></strong><br><br>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalHapus')">Batal</button>
            <form id="formDeleteSubmit" method="POST">
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
    // ── MODAL ──
    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }
    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
        }
    });

    // ── DELETE MODAL ──
    function openDeleteModal(id, namaBarang) {
        document.getElementById('deleteNamaBarang').textContent = namaBarang;
        document.getElementById('formDeleteSubmit').action = '/pembelian/' + id;
        openModal('modalHapus');
    }
</script>
@endpush