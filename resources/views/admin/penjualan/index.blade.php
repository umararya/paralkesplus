{{-- resources/views/admin/penjualan/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Penjualan')
@section('breadcrumb', 'Penjualan')

@push('styles')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .page-title { font-size:20px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:10px; line-height:1.2; }
    .page-title i { font-size:22px; color:var(--brand-500); }
    .page-subtitle { font-size:13px; color:var(--text-muted); margin-top:4px; }

    .table-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; transition:background 0.3s,border-color 0.3s; }
    .table-toolbar { padding:14px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .toolbar-left { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .toolbar-right { display:flex; align-items:center; gap:8px; }
    .per-page-wrap { display:flex; align-items:center; gap:7px; font-size:13px; color:var(--text-secondary); }
    .per-page-select { height:36px; padding:0 30px 0 10px; border:1px solid var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-primary); font-size:13px; font-family:var(--font); outline:none; cursor:pointer; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; }
    .per-page-select:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .search-form { display:flex; align-items:center; gap:7px; }
    .search-input-wrap { display:flex; align-items:center; background:var(--bg-primary); border:1px solid var(--border); border-radius:8px; padding:0 11px; height:36px; gap:7px; transition:border-color 0.2s,box-shadow 0.2s; }
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
    .btn-ghost { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-danger { background:#EF4444; color:#fff; border:1px solid #EF4444; }
    .btn-danger:hover { background:#DC2626; border-color:#DC2626; }
    .btn-export { background:#10B981; color:#fff; border:1px solid #10B981; }
    .btn-export:hover { background:#059669; border-color:#059669; }
    html.dark .btn-export { background:rgba(16,185,129,0.2); color:#34D399; border-color:rgba(16,185,129,0.3); }

    .info-bar { padding:9px 18px; border-bottom:1px solid var(--border); background:var(--bg-primary); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px; }
    .info-bar-text { font-size:12.5px; color:var(--text-muted); display:flex; align-items:center; gap:6px; }
    .info-bar-text strong { color:var(--text-primary); }
    .badge-count { display:inline-flex; align-items:center; background:var(--brand-50); color:var(--brand-500); border:1px solid var(--brand-100); border-radius:99px; padding:1px 9px; font-size:11.5px; font-weight:600; }
    html.dark .badge-count { background:rgba(29,111,164,0.12); color:#60A5FA; border-color:rgba(29,111,164,0.25); }

    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead tr { background:var(--bg-primary); border-bottom:2px solid var(--border); }
    .data-table th { padding:10px 14px; text-align:left; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--text-muted); white-space:nowrap; }
    .data-table td { padding:12px 14px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); vertical-align:middle; }
    .data-table tbody tr:last-child td { border-bottom:none; }
    .data-table tbody tr { transition:background 0.15s; }
    .data-table tbody tr:hover td { background:var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align:center; }
    .data-table th.right, .data-table td.right { text-align:right; }

    /* ── Detail Items dalam row ── */
    .items-list { display:flex; flex-direction:column; gap:4px; }
    .item-pill { display:inline-flex; align-items:center; gap:5px; background:var(--bg-hover); border:1px solid var(--border); border-radius:6px; padding:3px 8px; font-size:12px; white-space:nowrap; }
    .item-pill .item-name { font-weight:600; color:var(--text-primary); }
    .item-pill .item-qty { background:var(--brand-50); color:var(--brand-500); border-radius:4px; padding:1px 5px; font-size:11px; font-weight:700; }
    html.dark .item-pill .item-qty { background:rgba(29,111,164,0.15); color:#60A5FA; }
    .item-pill .item-sub { color:var(--text-muted); font-size:11px; }
    .items-more { font-size:11.5px; color:var(--brand-500); cursor:pointer; margin-top:2px; }

    .action-group { display:flex; align-items:center; gap:4px; justify-content:center; }
    .btn-action { width:30px; height:30px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:15px; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); transition:all 0.2s; text-decoration:none; }
    .btn-action:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-action.edit:hover { background:#EFF6FF; color:var(--brand-500); border-color:var(--brand-100); }
    .btn-action.view:hover { background:#F0FDF4; color:#15803D; border-color:#BBF7D0; }
    .btn-action.delete:hover { background:#FFF1F2; color:#E11D48; border-color:#FFE4E6; }
    html.dark .btn-action.edit:hover { background:rgba(29,111,164,0.15); color:#60A5FA; border-color:rgba(29,111,164,0.3); }
    html.dark .btn-action.view:hover { background:rgba(21,128,61,0.12); color:#4ADE80; border-color:rgba(21,128,61,0.25); }
    html.dark .btn-action.delete:hover { background:rgba(225,29,72,0.12); color:#FB7185; border-color:rgba(225,29,72,0.25); }

    .table-footer { padding:12px 18px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
    .pagination-meta { font-size:12.5px; color:var(--text-muted); }
    .pagination-meta strong { color:var(--text-primary); }
    .pagination-nav { display:flex; align-items:center; gap:3px; }
    .page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 6px; border-radius:7px; font-size:13px; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); text-decoration:none; cursor:pointer; transition:all 0.18s; font-family:var(--font); }
    .page-btn:hover { background:var(--bg-hover); color:var(--text-primary); }
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

    .tanggal-badge { display:inline-flex; align-items:center; gap:5px; background:var(--bg-hover); padding:3px 10px; border-radius:6px; font-size:12.5px; font-weight:500; color:var(--text-primary); }
    .total-value { font-weight:700; color:#059669; }
    html.dark .total-value { color:#34D399; }
    .pay-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11.5px; font-weight:600; white-space:nowrap; }
    .pay-tunai    { background:#F0FDF4; color:#16A34A; }
    .pay-transfer { background:#EFF6FF; color:#1D4ED8; }
    .pay-qris     { background:#FFF7ED; color:#C2410C; }
    .pay-kredit   { background:#FDF4FF; color:#7C3AED; }
    html.dark .pay-tunai    { background:rgba(22,163,74,0.12);  color:#4ADE80; }
    html.dark .pay-transfer { background:rgba(29,78,216,0.12);  color:#60A5FA; }
    html.dark .pay-qris     { background:rgba(194,65,12,0.12);  color:#FB923C; }
    html.dark .pay-kredit   { background:rgba(124,58,237,0.12); color:#C084FC; }
    .foto-thumb { width:40px; height:40px; border-radius:7px; object-fit:cover; border:1px solid var(--border); cursor:pointer; transition:transform 0.15s,box-shadow 0.15s; display:block; }
    .foto-thumb:hover { transform:scale(1.1); box-shadow:0 4px 12px rgba(0,0,0,0.18); }
    .foto-none { display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:7px; background:var(--bg-primary); border:1px dashed var(--border); color:var(--text-muted); font-size:18px; }
    .tfoot-total td { padding:12px 14px; font-size:13px; font-weight:700; color:var(--text-primary); background:var(--bg-hover); border-top:2px solid var(--border); }

    /* ── Modal ── */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from{opacity:0;}to{opacity:1;} }
    .modal { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.2); width:100%; max-width:460px; animation:slideUp 0.2s ease; }
    .modal-xl { max-width:680px; }
    @keyframes slideUp { from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);} }
    .modal-header { padding:18px 22px 14px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .modal-title { font-size:15px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .modal-close { width:28px; height:28px; border:none; background:none; cursor:pointer; color:var(--text-muted); font-size:19px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:all 0.2s; }
    .modal-close:hover { background:var(--bg-hover); color:var(--text-primary); }
    .modal-body { padding:18px 22px; max-height:75vh; overflow-y:auto; }
    .modal-footer { padding:14px 22px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:flex-end; gap:8px; }
    .delete-warning { text-align:center; padding:6px 0; }
    .delete-warning i { font-size:42px; color:#EF4444; display:block; margin-bottom:10px; }
    .delete-warning h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:7px; }
    .delete-warning p { font-size:13px; color:var(--text-muted); line-height:1.6; }
    .delete-warning strong { color:var(--text-primary); }

    /* ── Detail Modal ── */
    .detail-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px; }
    .detail-info-item { background:var(--bg-primary); border:1px solid var(--border); border-radius:8px; padding:10px 12px; }
    .detail-info-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); margin-bottom:3px; }
    .detail-info-value { font-size:13px; font-weight:600; color:var(--text-primary); }
    .detail-items-table { width:100%; border-collapse:collapse; border:1px solid var(--border); border-radius:10px; overflow:hidden; }
    .detail-items-table th { padding:8px 10px; background:var(--bg-primary); font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); border-bottom:1px solid var(--border); text-align:left; }
    .detail-items-table td { padding:9px 10px; border-bottom:1px solid var(--border); font-size:12.5px; }
    .detail-items-table tr:last-child td { border-bottom:none; }
    .detail-items-table td.right { text-align:right; }
    .detail-total-row td { font-weight:700; background:var(--bg-hover); font-size:13px; }

    /* ── Lightbox ── */
    .lightbox-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.88); z-index:2000; align-items:center; justify-content:center; padding:20px; cursor:zoom-out; }
    .lightbox-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    .lightbox-overlay img { max-width:90vw; max-height:88vh; border-radius:10px; object-fit:contain; cursor:default; }
    .lightbox-close { position:fixed; top:16px; right:20px; width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .lightbox-close:hover { background:rgba(255,255,255,0.28); }
    .lightbox-caption { position:fixed; bottom:18px; left:50%; transform:translateX(-50%); font-size:13px; color:rgba(255,255,255,0.75); background:rgba(0,0,0,0.5); padding:6px 16px; border-radius:20px; white-space:nowrap; max-width:80vw; overflow:hidden; text-overflow:ellipsis; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success">
    <i class="ri-checkbox-circle-fill"></i> {{ session('success') }}
</div>
@endif

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-exchange-dollar-line"></i> Penjualan
        </h1>
        <p class="page-subtitle">Kelola data transaksi penjualan alat kesehatan</p>
    </div>
</div>

<div class="table-card">

    {{-- TOOLBAR --}}
    <div class="table-toolbar">
        <div class="toolbar-left">
            <form method="GET" action="{{ route('penjualan.index') }}" id="perPageForm">
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

            <form method="GET" action="{{ route('penjualan.index') }}" class="search-form" id="searchForm">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search" id="searchInput"
                           value="{{ $search }}"
                           placeholder="Cari pelanggan, barang, keterangan..."
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                @if($search)
                <a href="{{ route('penjualan.index', ['per_page' => $perPage]) }}" class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                @endif
            </form>
        </div>

        <div class="toolbar-right">
            <a href="{{ route('penjualan.export', ['search' => $search]) }}"
               class="btn btn-export" title="Export ke Excel">
                <i class="ri-file-excel-2-line"></i> Export XLSX
            </a>
            <a href="{{ route('penjualan.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Penjualan
            </a>
        </div>
    </div>

    {{-- INFO BAR --}}
    <div class="info-bar">
        <div class="info-bar-text">
            @if($search)
                <i class="ri-filter-3-line"></i>
                Hasil pencarian: <strong>"{{ $search }}"</strong>
                &nbsp;<span class="badge-count">{{ $penjualans->total() }} transaksi</span>
            @else
                <i class="ri-exchange-dollar-line"></i>
                Total <span class="badge-count">{{ $penjualans->total() }} transaksi</span>
            @endif
        </div>
        @if($penjualans->total() > 0)
        <div class="info-bar-text">
            Halaman <strong>{{ $penjualans->currentPage() }}</strong> dari <strong>{{ $penjualans->lastPage() }}</strong>
        </div>
        @endif
    </div>

    {{-- TABLE --}}
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:42px;">#</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Barang (Detail)</th>
                    <th>Pembayaran</th>
                    <th class="right">Total</th>
                    <th>Keterangan</th>
                    <th class="center">Bukti</th>
                    <th class="center" style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjualans as $item)
                <tr>
                    <td style="color:var(--text-muted);font-size:12.5px;font-weight:500;">
                        {{ $penjualans->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <span class="tanggal-badge">
                            <i class="ri-calendar-line" style="font-size:12px;color:var(--text-muted);"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y') }}
                        </span>
                    </td>

                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $item->nama_pelanggan }}</div>
                        @if($item->nomor_telepon)
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">
                            <i class="ri-phone-line" style="font-size:11px;"></i> {{ $item->nomor_telepon }}
                        </div>
                        @endif
                        @if($item->alamat_pelanggan)
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:1px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <i class="ri-map-pin-line" style="font-size:11px;"></i> {{ $item->alamat_pelanggan }}
                        </div>
                        @endif
                    </td>

                    {{-- Detail Barang --}}
                    <td style="min-width:220px;">
                        @if($item->details->count() > 0)
                        <div class="items-list">
                            @foreach($item->details->take(2) as $d)
                            <div class="item-pill">
                                <span class="item-name">{{ $d->nama_barang }}</span>
                                <span class="item-qty">×{{ $d->qty }}</span>
                                <span class="item-sub">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                            @if($item->details->count() > 2)
                            <span class="items-more"
                                  onclick="openDetailModal({{ $item->id }})"
                                  title="Lihat semua barang">
                                <i class="ri-add-circle-line"></i>
                                +{{ $item->details->count() - 2 }} barang lainnya
                            </span>
                            @endif
                        </div>
                        @else
                        <span style="color:var(--text-muted);font-size:12.5px;font-style:italic;">—</span>
                        @endif
                    </td>

                    <td>
                        @php
                            $payClass = match($item->jenis_pembayaran) {
                                'tunai'    => 'pay-tunai',
                                'transfer' => 'pay-transfer',
                                'qris'     => 'pay-qris',
                                'kredit'   => 'pay-kredit',
                                default    => 'pay-tunai',
                            };
                            $payIcon = match($item->jenis_pembayaran) {
                                'tunai'    => 'ri-money-dollar-circle-line',
                                'transfer' => 'ri-bank-line',
                                'qris'     => 'ri-qr-code-line',
                                'kredit'   => 'ri-bank-card-line',
                                default    => 'ri-money-dollar-circle-line',
                            };
                        @endphp
                        <span class="pay-badge {{ $payClass }}">
                            <i class="{{ $payIcon }}"></i>
                            {{ ucfirst($item->jenis_pembayaran) }}
                        </span>
                    </td>

                    <td class="right total-value" style="font-size:13px;white-space:nowrap;">
                        Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                    </td>

                    <td style="max-width:130px;">
                        @if($item->keterangan)
                            <span title="{{ $item->keterangan }}"
                                  style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12.5px;color:var(--text-muted);">
                                {{ $item->keterangan }}
                            </span>
                        @else
                            <span style="color:var(--text-muted);font-size:12px;">—</span>
                        @endif
                    </td>

                    <td class="center">
                        @if($item->foto_bukti)
                            <img src="{{ Storage::url($item->foto_bukti) }}"
                                 alt="Bukti"
                                 class="foto-thumb"
                                 onclick="openLightbox('{{ Storage::url($item->foto_bukti) }}', 'Bukti — {{ addslashes($item->nama_pelanggan) }}')"
                                 title="Klik untuk perbesar">
                        @else
                            <span class="foto-none" title="Tidak ada bukti">
                                <i class="ri-image-line"></i>
                            </span>
                        @endif
                    </td>

                    <td class="center">
                        <div class="action-group">
                            <button type="button"
                                    class="btn-action view"
                                    title="Lihat detail"
                                    onclick="openDetailModal({{ $item->id }})">
                                <i class="ri-eye-line"></i>
                            </button>
                            <a href="{{ route('penjualan.edit', $item->id) }}"
                               class="btn-action edit" title="Edit">
                                <i class="ri-edit-line"></i>
                            </a>
                            <button type="button"
                                    class="btn-action delete"
                                    title="Hapus"
                                    onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_pelanggan) }}')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            <form id="formHapus-{{ $item->id }}"
                                  action="{{ route('penjualan.destroy', $item->id) }}"
                                  method="POST" style="display:none;">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="ri-exchange-dollar-line"></i>
                            <h3>{{ $search ? 'Tidak ditemukan' : 'Belum ada data penjualan' }}</h3>
                            <p>{{ $search ? 'Coba kata kunci lain atau klik Reset.' : 'Klik "Tambah Penjualan" untuk mencatat transaksi baru.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>

            @if($penjualans->count() > 0)
            <tfoot>
                <tr class="tfoot-total">
                    <td colspan="5" class="right">Total keseluruhan:</td>
                    <td class="right total-value" style="font-size:14px;white-space:nowrap;">
                        Rp {{ number_format($penjualans->sum('total_harga'), 0, ',', '.') }}
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($penjualans->total() > 0)
    <div class="table-footer">
        <div class="pagination-meta">
            Menampilkan
            <strong>{{ $penjualans->firstItem() }} – {{ $penjualans->lastItem() }}</strong>
            dari <strong>{{ $penjualans->total() }}</strong> transaksi
        </div>

        @if($penjualans->hasPages())
        <nav class="pagination-nav" aria-label="Pagination">
            @if($penjualans->onFirstPage())
                <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
            @else
                <a class="page-btn" href="{{ $penjualans->previousPageUrl() }}">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            @endif

            @php
                $current = $penjualans->currentPage();
                $last    = $penjualans->lastPage();
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

            @foreach($rendered as $pageItem)
                @if($pageItem === '...')
                    <span class="page-ellipsis">…</span>
                @elseif($pageItem == $current)
                    <span class="page-btn active">{{ $pageItem }}</span>
                @else
                    <a class="page-btn" href="{{ $penjualans->url($pageItem) }}">{{ $pageItem }}</a>
                @endif
            @endforeach

            @if($penjualans->hasMorePages())
                <a class="page-btn" href="{{ $penjualans->nextPageUrl() }}">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            @else
                <span class="page-btn disabled"><i class="ri-arrow-right-s-line"></i></span>
            @endif
        </nav>
        @endif
    </div>
    @endif

</div>{{-- /table-card --}}


{{-- ══ MODAL: DETAIL TRANSAKSI ══ --}}
<div class="modal-overlay" id="modalDetail">
    <div class="modal modal-xl">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-receipt-line"></i> Detail Transaksi
            </span>
            <button class="modal-close" onclick="closeModal('modalDetail')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body" id="modalDetailBody">
            <div style="text-align:center;padding:32px;color:var(--text-muted);">
                <i class="ri-loader-4-line" style="font-size:28px;"></i>
                <p style="margin-top:8px;font-size:13px;">Memuat data...</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalDetail')">Tutup</button>
        </div>
    </div>
</div>

{{-- ══ MODAL: KONFIRMASI HAPUS ══ --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal">
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
                <h3>Hapus Data Penjualan?</h3>
                <p>Kamu akan menghapus transaksi atas nama:<br>
                   <strong id="deleteNamaPelanggan"></strong><br><br>
                   Stok barang akan dikembalikan dan tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalHapus')">Batal</button>
            <form id="formDeleteSubmit" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ══ LIGHTBOX ══ --}}
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="event.stopPropagation();closeLightbox()">
        <i class="ri-close-line"></i>
    </button>
    <img id="lightboxImg" src="" alt="Bukti" onclick="event.stopPropagation()">
    <div class="lightbox-caption" id="lightboxCaption"></div>
</div>

{{-- Data JSON untuk modal detail --}}
@php
$penjualanData = $penjualans->map(function($item) {
    return [
        'id'               => $item->id,
        'nama_pelanggan'   => $item->nama_pelanggan,
        'nomor_telepon'    => $item->nomor_telepon,
        'alamat_pelanggan' => $item->alamat_pelanggan,
        'tanggal'          => \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y'),
        'jenis_pembayaran' => ucfirst($item->jenis_pembayaran),
        'diskon_global'    => $item->diskon_global ?? 0,
        'total_harga'      => $item->total_harga ?? 0,
        'total_tagihan'    => $item->total_tagihan,
        'keterangan'       => $item->keterangan ?? '',
        'details'          => $item->details->map(fn($d) => [
            'nama_barang'  => $d->nama_barang,
            'kondisi'      => ucfirst($d->kondisi ?? 'baru'),
            'qty'          => $d->qty,
            'satuan'       => $d->satuan,
            'harga_satuan' => $d->harga_satuan,
            'diskon'       => $d->diskon ?? 0,
            'subtotal'     => $d->subtotal,
        ])->values(),
    ];
})->keyBy('id');
@endphp

@endsection

@push('scripts')
<script>
const penjualanData = @json($penjualanData);

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
        closeLightbox();
        document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
    }
});

function openDeleteModal(id, nama) {
    document.getElementById('deleteNamaPelanggan').textContent = nama;
    document.getElementById('formDeleteSubmit').action = '/penjualan/' + id;
    openModal('modalHapus');
}

function openDetailModal(id) {
    const d = penjualanData[id];
    if (!d) return;

    const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID');

    let rows = d.details.map(item => `
        <tr>
            <td>${item.nama_barang}
                <div style="font-size:11px;color:var(--text-muted);">${item.kondisi}</div>
            </td>
            <td class="right">${item.qty} ${item.satuan}</td>
            <td class="right">${fmt(item.harga_satuan)}</td>
            <td class="right">${item.diskon > 0 ? item.diskon + '%' : '—'}</td>
            <td class="right" style="font-weight:600;color:#059669;">${fmt(item.subtotal)}</td>
        </tr>
    `).join('');

    let diskonRow = d.diskon_global > 0 ? `
        <tr class="detail-total-row">
            <td colspan="4" style="text-align:right;">Diskon Global:</td>
            <td class="right" style="color:#EF4444;">- ${fmt(d.diskon_global)}</td>
        </tr>
    ` : '';

    document.getElementById('modalDetailBody').innerHTML = `
        <div class="detail-info-grid">
            <div class="detail-info-item">
                <div class="detail-info-label">Pelanggan</div>
                <div class="detail-info-value">${d.nama_pelanggan}</div>
            </div>
            <div class="detail-info-item">
                <div class="detail-info-label">Tanggal</div>
                <div class="detail-info-value">${d.tanggal}</div>
            </div>
            <div class="detail-info-item">
                <div class="detail-info-label">No. Telepon</div>
                <div class="detail-info-value">${d.nomor_telepon || '—'}</div>
            </div>
            <div class="detail-info-item">
                <div class="detail-info-label">Pembayaran</div>
                <div class="detail-info-value">${d.jenis_pembayaran}</div>
            </div>
            <div class="detail-info-item" style="grid-column:1/-1;">
                <div class="detail-info-label">Alamat</div>
                <div class="detail-info-value">${d.alamat_pelanggan || '—'}</div>
            </div>
            ${d.keterangan ? `
            <div class="detail-info-item" style="grid-column:1/-1;">
                <div class="detail-info-label">Keterangan</div>
                <div class="detail-info-value" style="font-weight:400;font-size:12.5px;">${d.keterangan}</div>
            </div>` : ''}
        </div>

        <div style="font-size:12.5px;font-weight:700;color:var(--text-secondary);margin-bottom:8px;display:flex;align-items:center;gap:6px;">
            <i class="ri-list-check" style="color:var(--brand-500);"></i> Detail Barang (${d.details.length} item)
        </div>
        <div style="overflow-x:auto;">
            <table class="detail-items-table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th style="text-align:right;">Qty</th>
                        <th style="text-align:right;">Harga Satuan</th>
                        <th style="text-align:right;">Diskon</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
                <tfoot>
                    ${diskonRow}
                    <tr class="detail-total-row">
                        <td colspan="4" style="text-align:right;">Total Tagihan:</td>
                        <td class="right" style="color:#059669;">${fmt(d.total_tagihan)}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

    openModal('modalDetail');
}

function openLightbox(src, caption) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxCaption').textContent = caption;
    document.getElementById('lightboxOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightboxOverlay').classList.remove('open');
    document.getElementById('lightboxImg').src = '';
    document.body.style.overflow = '';
}
</script>
@endpush