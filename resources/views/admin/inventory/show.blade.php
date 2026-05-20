{{-- resources/views/admin/inventory/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Inventory — ' . $inventory->nama_produk)
@section('breadcrumb', 'Detail Inventory')

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    @media (min-width: 768px) { .detail-grid { grid-template-columns: repeat(4, 1fr); } }

    .detail-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px 18px;
        box-shadow: var(--shadow);
    }
    .detail-card-label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--text-muted);
    }
    .detail-card-value {
        font-size: 22px; font-weight: 700;
        color: var(--text-primary); margin-top: 6px; line-height: 1;
    }
    .detail-card-value.green  { color: #16A34A; }
    .detail-card-value.orange { color: #EA580C; }
    .detail-card-value.blue   { color: #2563EB; }
    .detail-card-value.purple { color: #9333EA; }

    .table-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
    }
    .table-card-header {
        padding: 16px 20px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px;
    }
    .table-card-title {
        font-size: 15px; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 8px;
    }
    .table-card-title i { color: var(--brand-500); }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead tr { background: var(--bg-primary); border-bottom: 2px solid var(--border); }
    .data-table th {
        padding: 10px 16px; text-align: left;
        font-size: 10.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.7px; color: var(--text-muted); white-space: nowrap;
    }
    .data-table td {
        padding: 13px 16px; font-size: 13px; color: var(--text-primary);
        border-bottom: 1px solid var(--border); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align: center; }

    .badge {
        display: inline-flex; align-items: center;
        padding: 2px 10px; border-radius: 20px;
        font-size: 11.5px; font-weight: 600; white-space: nowrap;
    }
    .badge-purchase  { background: #DCFCE7; color: #15803D; }
    .badge-buyback   { background: #FEF3C7; color: #B45309; }
    .badge-sale      { background: #FEE2E2; color: #B91C1C; }
    .badge-rent      { background: #DBEAFE; color: #1D4ED8; }
    .badge-manual    { background: #F3E8FF; color: #7C3AED; }
    .badge-return    { background: #ECFDF5; color: #059669; }
    .badge-baru      { background: #DBEAFE; color: #1D4ED8; }
    .badge-bekas     { background: #F3E8FF; color: #7C3AED; }
    html.dark .badge-purchase { background: rgba(21,128,61,0.15);  color: #4ADE80; }
    html.dark .badge-buyback  { background: rgba(180,83,9,0.15);   color: #FCD34D; }
    html.dark .badge-sale     { background: rgba(185,28,28,0.15);  color: #FCA5A5; }
    html.dark .badge-rent     { background: rgba(29,78,216,0.15);  color: #60A5FA; }
    html.dark .badge-manual   { background: rgba(124,58,237,0.15); color: #C084FC; }
    html.dark .badge-return   { background: rgba(5,150,105,0.15);  color: #34D399; }
    html.dark .badge-baru     { background: rgba(29,78,216,0.15);  color: #60A5FA; }
    html.dark .badge-bekas    { background: rgba(124,58,237,0.15); color: #C084FC; }

    .qty-plus  { color: #16A34A; font-weight: 700; }
    .qty-minus { color: #DC2626; font-weight: 700; }

    .empty-state { text-align: center; padding: 48px 24px; }
    .empty-state i { font-size: 44px; color: var(--border); display: block; margin-bottom: 12px; }
    .empty-state p  { font-size: 13px; color: var(--text-muted); }

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
        color: var(--text-secondary); text-decoration: none;
        transition: all 0.18s; font-family: var(--font);
    }
    .page-btn:hover { background: var(--bg-hover); color: var(--text-primary); }
    .page-btn.active { background: var(--brand-500); color: #fff; border-color: var(--brand-500); font-weight: 700; }
    .page-btn.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }
    .page-ellipsis { display: inline-flex; align-items: center; min-width: 32px; height: 32px; font-size: 13px; color: var(--text-muted); justify-content: center; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; justify-content:space-between;
            gap:12px; margin-bottom:24px; flex-wrap:wrap;">
    <div style="display:flex; align-items:center; gap:12px;">
        <a href="{{ route('inventory.index') }}"
           style="display:inline-flex; align-items:center; justify-content:center;
                  width:36px; height:36px; border-radius:8px; background:var(--bg-card);
                  border:1px solid var(--border); color:var(--text-secondary);
                  text-decoration:none; transition:all 0.2s;"
           onmouseover="this.style.background='var(--bg-hover)'"
           onmouseout="this.style.background='var(--bg-card)'">
            <i class="ri-arrow-left-line"></i>
        </a>
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">
                {{ $inventory->nama_produk }}
            </h1>
            <p style="font-size:13px; color:var(--text-muted);">
                {{ $inventory->kategori ? ucwords(str_replace('_', ' ', $inventory->kategori)) : 'Tanpa kategori' }}
                &mdash; Satuan: {{ strtoupper($inventory->satuan ?? 'unit') }}
            </p>
        </div>
    </div>
    <a href="{{ route('inventory.edit', $inventory->id) }}"
       style="display:inline-flex; align-items:center; gap:6px;
              padding:0 16px; height:36px; border-radius:8px;
              background:var(--brand-500); color:#fff; font-size:13px; font-weight:500;
              text-decoration:none; transition:background 0.2s;"
       onmouseover="this.style.background='var(--brand-600)'"
       onmouseout="this.style.background='var(--brand-500)'">
        <i class="ri-edit-line"></i> Edit
    </a>
</div>

{{-- Summary Cards --}}
<div class="detail-grid">
    <div class="detail-card">
        <div class="detail-card-label">Stok Tersedia</div>
        <div class="detail-card-value green">{{ $inventory->stok_tersedia ?? 0 }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-card-label">Sedang Disewa</div>
        <div class="detail-card-value orange">{{ $inventory->stok_disewa ?? 0 }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-card-label">Stok Baru</div>
        <div class="detail-card-value blue">{{ $inventory->stok_baru ?? 0 }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-card-label">Stok Bekas</div>
        <div class="detail-card-value purple">{{ $inventory->stok_bekas ?? 0 }}</div>
    </div>
    <div class="detail-card" style="grid-column: span 2;">
        <div class="detail-card-label">Harga Beli Terakhir</div>
        <div class="detail-card-value" style="font-size:18px;">
            {{ $inventory->harga_beli_terakhir
                ? 'Rp ' . number_format($inventory->harga_beli_terakhir, 0, ',', '.')
                : '—' }}
        </div>
    </div>
    <div class="detail-card" style="grid-column: span 2;">
        <div class="detail-card-label">Keterangan</div>
        <div style="font-size:13.5px; color:var(--text-secondary); margin-top:6px; line-height:1.6;">
            {{ $inventory->keterangan ?? '—' }}
        </div>
    </div>
</div>

{{-- Riwayat Pergerakan Stok --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            <i class="ri-history-line"></i>
            Riwayat Pergerakan Stok
        </div>
        <span style="font-size:12.5px; color:var(--text-muted);">
            {{ $logs->total() }} entri
        </span>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:46px;">#</th>
                    <th>Tanggal</th>
                    <th>Tipe Transaksi</th>
                    <th class="center">Perubahan Qty</th>
                    <th>Kondisi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="color:var(--text-muted); font-size:12.5px;">
                        {{ $logs->firstItem() + $loop->index }}
                    </td>
                    <td style="font-size:12.5px; color:var(--text-muted); white-space:nowrap;">
                        {{ $log->created_at->format('d M Y, H:i') }}
                    </td>
                    <td>
                        @php
                            $typeMap = [
                                'purchase' => ['label' => 'Pembelian',  'class' => 'badge-purchase'],
                                'buyback'  => ['label' => 'Buy Back',   'class' => 'badge-buyback'],
                                'sale'     => ['label' => 'Penjualan',  'class' => 'badge-sale'],
                                'rent'     => ['label' => 'Sewa',       'class' => 'badge-rent'],
                                'return'   => ['label' => 'Kembali',    'class' => 'badge-return'],
                                'manual'   => ['label' => 'Manual',     'class' => 'badge-manual'],
                            ];
                            $type = $typeMap[$log->reference_type] ?? ['label' => ucfirst($log->reference_type), 'class' => 'badge-manual'];
                        @endphp
                        <span class="badge {{ $type['class'] }}">{{ $type['label'] }}</span>
                    </td>
                    <td class="center">
                        <span class="{{ $log->qty_change >= 0 ? 'qty-plus' : 'qty-minus' }}">
                            {{ $log->qty_change >= 0 ? '+' : '' }}{{ $log->qty_change }}
                        </span>
                    </td>
                    <td>
                        @if($log->kondisi)
                        <span class="badge {{ $log->kondisi === 'bekas' ? 'badge-bekas' : 'badge-baru' }}">
                            {{ ucfirst($log->kondisi) }}
                        </span>
                        @else
                            <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td style="color:var(--text-secondary); font-size:13px; max-width:260px;">
                        {{ $log->keterangan ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="ri-history-line"></i>
                            <p>Belum ada riwayat pergerakan stok untuk produk ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="table-footer">
        <div class="pagination-meta">
            Menampilkan
            <strong>{{ $logs->firstItem() }} – {{ $logs->lastItem() }}</strong>
            dari <strong>{{ $logs->total() }}</strong> entri
        </div>
        <nav class="pagination-nav">
            @if($logs->onFirstPage())
                <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
            @else
                <a class="page-btn" href="{{ $logs->previousPageUrl() }}"><i class="ri-arrow-left-s-line"></i></a>
            @endif

            @php
                $current = $logs->currentPage();
                $last    = $logs->lastPage();
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
                    <a class="page-btn" href="{{ $logs->url($pg) }}">{{ $pg }}</a>
                @endif
            @endforeach

            @if($logs->hasMorePages())
                <a class="page-btn" href="{{ $logs->nextPageUrl() }}"><i class="ri-arrow-right-s-line"></i></a>
            @else
                <span class="page-btn disabled"><i class="ri-arrow-right-s-line"></i></span>
            @endif
        </nav>
    </div>
    @endif
</div>

@endsection