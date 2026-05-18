{{-- resources/views/admin/inventory/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Inventory')
@section('breadcrumb', 'Inventory')

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

    /* Summary Cards */
    .summary-grid {
        display: grid; grid-template-columns: repeat(2, 1fr); gap: 14px;
        margin-bottom: 24px;
    }
    @media (min-width: 768px) { .summary-grid { grid-template-columns: repeat(4, 1fr); } }
    .summary-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 14px; padding: 16px 18px;
        box-shadow: var(--shadow); transition: background 0.3s;
        border-left: 4px solid transparent;
    }
    .summary-card.blue   { border-left-color: #3B82F6; }
    .summary-card.green  { border-left-color: #22C55E; }
    .summary-card.orange { border-left-color: #F97316; }
    .summary-card.purple { border-left-color: #A855F7; }
    .summary-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-muted); }
    .summary-value { font-size: 26px; font-weight: 700; color: var(--text-primary); margin-top: 6px; line-height: 1; }

    /* Table */
    .table-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
    }
    .table-toolbar {
        padding: 14px 18px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        gap: 10px; flex-wrap: wrap;
    }
    .toolbar-left  { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .per-page-wrap { display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--text-secondary); }
    .per-page-select {
        height: 36px; padding: 0 30px 0 10px;
        border: 1px solid var(--border); border-radius: 8px;
        background: var(--bg-primary); color: var(--text-primary);
        font-size: 13px; font-family: var(--font); outline: none;
        cursor: pointer; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 8px center;
    }
    .toolbar-divider { width: 1px; height: 24px; background: var(--border); flex-shrink: 0; }
    .search-form { display: flex; align-items: center; gap: 7px; }
    .search-input-wrap {
        display: flex; align-items: center; background: var(--bg-primary);
        border: 1px solid var(--border); border-radius: 8px;
        padding: 0 11px; height: 36px; gap: 7px;
    }
    .search-input-wrap:focus-within { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(29,111,164,0.1); }
    .search-input-wrap i { color: var(--text-muted); font-size: 14px; }
    .search-input-wrap input {
        border: none; background: transparent; outline: none;
        font-size: 13px; color: var(--text-primary); font-family: var(--font); width: 200px;
    }
    .search-input-wrap input::placeholder { color: var(--text-muted); }

    .btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0 14px; height: 36px; border-radius: 8px;
        font-size: 13px; font-weight: 500; font-family: var(--font);
        cursor: pointer; border: none; transition: all 0.2s;
        text-decoration: none; white-space: nowrap;
    }
    .btn i { font-size: 15px; }
    .btn-search { background: var(--brand-50); color: var(--brand-500); border: 1px solid var(--brand-100); }
    .btn-search:hover { background: var(--brand-100); color: var(--brand-600); }
    .btn-reset { background: transparent; color: var(--text-secondary); border: 1px solid var(--border); }
    .btn-reset:hover { background: var(--bg-hover); color: var(--text-primary); }

    .info-bar {
        padding: 9px 18px; border-bottom: 1px solid var(--border);
        background: var(--bg-primary);
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;
    }
    .info-bar-text { font-size: 12.5px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
    .info-bar-text strong { color: var(--text-primary); }
    .badge-count {
        display: inline-flex; align-items: center;
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100); border-radius: 99px;
        padding: 1px 9px; font-size: 11.5px; font-weight: 600;
    }

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
    .data-table tbody tr:hover td { background: var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align: center; }
    .data-table th.right,  .data-table td.right  { text-align: right; }

    .stok-badge {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; padding: 2px 10px; border-radius: 20px;
        font-size: 12.5px; font-weight: 700;
    }
    .stok-ada      { background: #DCFCE7; color: #15803D; }
    .stok-habis    { background: #FEE2E2; color: #B91C1C; }
    .stok-disewa   { background: #FEF3C7; color: #B45309; }
    .stok-baru     { background: #DBEAFE; color: #1D4ED8; }
    .stok-bekas    { background: #F3E8FF; color: #7C3AED; }
    html.dark .stok-ada    { background: rgba(21,128,61,0.15); color: #4ADE80; }
    html.dark .stok-habis  { background: rgba(185,28,28,0.15); color: #FCA5A5; }
    html.dark .stok-disewa { background: rgba(180,83,9,0.15);  color: #FCD34D; }
    html.dark .stok-baru   { background: rgba(29,78,216,0.15); color: #60A5FA; }
    html.dark .stok-bekas  { background: rgba(124,58,237,0.15);color: #C084FC; }

    .btn-detail {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px; border-radius: 7px; font-size: 12.5px; font-weight: 500;
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100);
        text-decoration: none; transition: all 0.2s;
    }
    .btn-detail:hover { background: var(--brand-100); color: var(--brand-600); }
    html.dark .btn-detail { background: rgba(29,111,164,0.12); color: #60A5FA; border-color: rgba(29,111,164,0.25); }

    .table-footer {
        padding: 12px 18px; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;
    }
    .pagination-meta { font-size: 12.5px; color: var(--text-muted); }
    .pagination-meta strong { color: var(--text-primary); }
    .pagination-nav { display: flex; align-items: center; gap: 3px; }
    .page-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px; padding: 0 6px; border-radius: 7px; font-size: 13px;
        border: 1px solid var(--border); background: var(--bg-card);
        color: var(--text-secondary); text-decoration: none; cursor: pointer;
        transition: all 0.18s; font-family: var(--font);
    }
    .page-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
    .page-btn.active { background: var(--brand-500); color: #fff; border-color: var(--brand-500); font-weight: 700; }
    .page-btn.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }
    .page-ellipsis { display: inline-flex; align-items: center; min-width: 32px; height: 32px; font-size: 13px; color: var(--text-muted); justify-content: center; }

    .empty-state { text-align: center; padding: 56px 24px; }
    .empty-state i { font-size: 48px; color: var(--border); display: block; margin-bottom: 12px; }
    .empty-state h3 { font-size: 15px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
    .empty-state p  { font-size: 13px; color: var(--text-muted); }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-archive-drawer-line"></i> Inventory
        </h1>
        <p class="page-subtitle">Rekap stok otomatis dari pembelian, penjualan, dan penyewaan</p>
    </div>
</div>

{{-- Summary Cards --}}
<div class="summary-grid">
    <div class="summary-card blue">
        <div class="summary-label">Total Item</div>
        <div class="summary-value">{{ $summary['total_item'] }}</div>
    </div>
    <div class="summary-card green">
        <div class="summary-label">Stok Tersedia</div>
        <div class="summary-value">{{ $summary['total_tersedia'] }}</div>
    </div>
    <div class="summary-card orange">
        <div class="summary-label">Sedang Disewa</div>
        <div class="summary-value">{{ $summary['total_disewa'] }}</div>
    </div>
    <div class="summary-card purple">
        <div class="summary-label">Stok Bekas</div>
        <div class="summary-value">{{ $summary['total_bekas'] }}</div>
    </div>
</div>

<div class="table-card">

    {{-- Toolbar --}}
    <div class="table-toolbar">
        <div class="toolbar-left">
            <form method="GET" action="{{ route('inventory.index') }}" id="perPageForm">
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

            <form method="GET" action="{{ route('inventory.index') }}" class="search-form">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama produk atau kategori..." autocomplete="off">
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                @if($search)
                <a href="{{ route('inventory.index', ['per_page' => $perPage]) }}" class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Info Bar --}}
    <div class="info-bar">
        <div class="info-bar-text">
            @if($search)
                <i class="ri-filter-3-line"></i>
                Hasil pencarian: <strong>"{{ $search }}"</strong>
                &nbsp;<span class="badge-count">{{ $inventories->total() }} data</span>
            @else
                <i class="ri-archive-drawer-line"></i>
                Total <span class="badge-count">{{ $inventories->total() }} item</span>
            @endif
        </div>
        @if($inventories->total() > 0)
        <div class="info-bar-text">
            Halaman <strong>{{ $inventories->currentPage() }}</strong> dari <strong>{{ $inventories->lastPage() }}</strong>
        </div>
        @endif
    </div>

    {{-- Table --}}
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:46px;">#</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="center">Stok Tersedia</th>
                    <th class="center">Sedang Disewa</th>
                    <th class="center">Stok Baru</th>
                    <th class="center">Stok Bekas</th>
                    <th class="right">Harga Beli Terakhir</th>
                    <th class="center" style="width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventories as $item)
                <tr>
                    <td style="color:var(--text-muted); font-size:12.5px; font-weight:500;">
                        {{ $inventories->firstItem() + $loop->index }}
                    </td>
                    <td>
                        <div style="font-weight:600;">
                            @if($search)
                                {!! preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                    '<mark style="background:#FEF08A; border-radius:3px; padding:0 2px;">$1</mark>',
                                    e($item->nama_produk)) !!}
                            @else
                                {{ $item->nama_produk }}
                            @endif
                        </div>
                    </td>
                    <td style="color:var(--text-muted);">{{ $item->kategori ?? '—' }}</td>
                    <td class="center">
                        <span class="stok-badge {{ $item->stok_tersedia > 0 ? 'stok-ada' : 'stok-habis' }}">
                            {{ $item->stok_tersedia }}
                        </span>
                    </td>
                    <td class="center">
                        <span class="stok-badge {{ $item->stok_disewa > 0 ? 'stok-disewa' : '' }}"
                              style="{{ $item->stok_disewa == 0 ? 'color:var(--text-muted);' : '' }}">
                            {{ $item->stok_disewa }}
                        </span>
                    </td>
                    <td class="center">
                        <span class="stok-badge stok-baru">{{ $item->stok_baru }}</span>
                    </td>
                    <td class="center">
                        <span class="stok-badge stok-bekas">{{ $item->stok_bekas }}</span>
                    </td>
                    <td class="right" style="font-size:13px; white-space:nowrap;">
                        {{ $item->harga_beli_terakhir
                            ? 'Rp ' . number_format($item->harga_beli_terakhir, 0, ',', '.')
                            : '—' }}
                    </td>
                    <td class="center">
                        <a href="{{ route('inventory.show', $item->id) }}" class="btn-detail">
                            <i class="ri-eye-line"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="ri-archive-drawer-line"></i>
                            <h3>{{ $search ? 'Tidak ditemukan' : 'Inventory masih kosong' }}</h3>
                            <p>{{ $search ? 'Coba kata kunci lain atau klik Reset.' : 'Data akan otomatis muncul setelah ada transaksi pembelian.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($inventories->total() > 0)
    <div class="table-footer">
        <div class="pagination-meta">
            Menampilkan
            <strong>{{ $inventories->firstItem() }} – {{ $inventories->lastItem() }}</strong>
            dari <strong>{{ $inventories->total() }}</strong> data
        </div>

        @if($inventories->hasPages())
        <nav class="pagination-nav">
            @if($inventories->onFirstPage())
                <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
            @else
                <a class="page-btn" href="{{ $inventories->previousPageUrl() }}"><i class="ri-arrow-left-s-line"></i></a>
            @endif

            @php
                $current = $inventories->currentPage();
                $last    = $inventories->lastPage();
                $pages   = [];
                for ($p = 1; $p <= $last; $p++) {
                    if ($p === 1 || $p === $last || ($p >= $current - 2 && $p <= $current + 2))
                        $pages[] = $p;
                }
                $rendered = []; $prev = null;
                foreach ($pages as $p) {
                    if ($prev !== null && $p - $prev > 1) $rendered[] = '...';
                    $rendered[] = $p;
                    $prev = $p;
                }
            @endphp

            @foreach($rendered as $pg)
                @if($pg === '...')
                    <span class="page-ellipsis">…</span>
                @elseif($pg == $current)
                    <span class="page-btn active">{{ $pg }}</span>
                @else
                    <a class="page-btn" href="{{ $inventories->url($pg) }}">{{ $pg }}</a>
                @endif
            @endforeach

            @if($inventories->hasMorePages())
                <a class="page-btn" href="{{ $inventories->nextPageUrl() }}"><i class="ri-arrow-right-s-line"></i></a>
            @else
                <span class="page-btn disabled"><i class="ri-arrow-right-s-line"></i></span>
            @endif
        </nav>
        @endif
    </div>
    @endif

</div>

@endsection