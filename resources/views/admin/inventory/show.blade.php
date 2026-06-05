{{-- resources/views/admin/inventory/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Inventory: ' . $inventory->nama_produk)
@section('breadcrumb', 'Detail Inventory')

@push('styles')
<style>
    /* ── Card ── */
    .detail-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; margin-bottom:20px; }
    .detail-card-header { padding:14px 22px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:var(--bg-primary); }
    .detail-card-title { font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .detail-card-title i { color:var(--brand-500); font-size:16px; }
    .detail-card-body { padding:22px; }

    /* ── Info Grid ── */
    .info-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
    @media(max-width:640px){ .info-grid{ grid-template-columns:1fr; } }
    .info-item { display:flex; flex-direction:column; gap:3px; }
    .info-item.full { grid-column:1/-1; }
    .info-label { font-size:11.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.4px; }
    .info-value { font-size:13.5px; color:var(--text-primary); font-weight:500; }

    /* ── Badge ── */
    .badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:99px; font-size:11.5px; font-weight:700; }
    .badge-green   { background:#D1FAE5; color:#065F46; }
    .badge-amber   { background:#FEF3C7; color:#92400E; }
    .badge-red     { background:#FEE2E2; color:#991B1B; }
    .badge-blue    { background:#DBEAFE; color:#1E40AF; }
    .badge-gray    { background:var(--bg-hover); color:var(--text-muted); }
    html.dark .badge-green  { background:rgba(6,95,70,.25);    color:#6EE7B7; }
    html.dark .badge-amber  { background:rgba(146,64,14,.25);  color:#FCD34D; }
    html.dark .badge-red    { background:rgba(153,27,27,.25);  color:#FCA5A5; }
    html.dark .badge-blue   { background:rgba(30,64,175,.25);  color:#93C5FD; }

    /* ── Stok Summary ── */
    .stok-summary { display:grid; grid-template-columns:repeat(4,1fr); gap:1px; background:var(--border); border-radius:12px; overflow:hidden; margin-bottom:20px; }
    @media(max-width:640px){ .stok-summary{ grid-template-columns:repeat(2,1fr); } }
    .stok-summary-item { background:var(--bg-primary); padding:14px 16px; text-align:center; }
    .stok-summary-label { font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:4px; }
    .stok-summary-value { font-size:18px; font-weight:800; color:var(--text-primary); }
    .stok-summary-value.green { color:#059669; }
    .stok-summary-value.amber { color:#D97706; }
    .stok-summary-value.red   { color:#DC2626; }

    /* ── Log Table ── */
    .log-table { width:100%; border-collapse:collapse; }
    .log-table thead tr { background:var(--brand-500); color:#fff; }
    .log-table th { padding:9px 12px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; white-space:nowrap; }
    .log-table td { padding:10px 12px; border-bottom:1px solid var(--border); font-size:13.5px; color:var(--text-primary); vertical-align:middle; }
    .log-table tbody tr:last-child td { border-bottom:none; }
    .log-table tbody tr:hover td { background:var(--bg-hover); }

    /* ── Buttons ── */
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 18px; height:38px; border-radius:8px; font-size:13px; font-weight:600; font-family:var(--font); cursor:pointer; border:none; transition:all .2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); }
    .btn-danger  { background:#DC2626; color:#fff; border:1px solid #DC2626; }
    .btn-danger:hover  { background:#B91C1C; }
    .btn-ghost   { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover   { background:var(--bg-hover); color:var(--text-primary); }
    .btn-sm { height:32px; padding:0 12px; font-size:12px; }

    /* ── Alert ── */
    .alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:500; margin-bottom:18px; border:1px solid transparent; }
    .alert-success { background:#ECFDF5; color:#065F46; border-color:#A7F3D0; }
    .alert-error   { background:#FFF1F2; color:#BE123C; border-color:#FECDD3; }
    html.dark .alert-success { background:rgba(6,95,70,.15);   color:#6EE7B7; border-color:rgba(6,95,70,.3); }
    html.dark .alert-error   { background:rgba(190,18,60,.12); color:#FB7185; border-color:rgba(190,18,60,.25); }

    /* ── Pagination ── */
    .pagination-wrap { display:flex; justify-content:flex-end; padding-top:12px; }
</style>
@endpush

@section('content')

{{-- ── Flash Messages ── --}}
@if(session('success'))
<div class="alert alert-success">
    <i class="ri-checkbox-circle-fill" style="font-size:17px;flex-shrink:0;margin-top:1px;"></i>
    <span>{{ session('success') }}</span>
</div>
@endif
@if(session('error'))
<div class="alert alert-error">
    <i class="ri-error-warning-fill" style="font-size:17px;flex-shrink:0;margin-top:1px;"></i>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- ── Top Action Bar ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="{{ route('inventory.index') }}" class="btn btn-ghost btn-sm">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
        <span style="font-size:15px;font-weight:700;color:var(--text-primary);">
            Detail Inventory: <span style="color:var(--brand-500)">{{ $inventory->nama_produk }}</span>
        </span>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('inventory.edit', $inventory->id) }}" class="btn btn-primary btn-sm">
            <i class="ri-edit-line"></i> Edit
        </a>
        <form method="POST" action="{{ route('inventory.destroy', $inventory->id) }}"
              onsubmit="return confirm('Hapus inventory ini? Semua log terkait juga akan dihapus.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i class="ri-delete-bin-line"></i> Hapus
            </button>
        </form>
    </div>
</div>

{{-- ════ CARD: Informasi Produk ════ --}}
<div class="detail-card">
    <div class="detail-card-header">
        <div class="detail-card-title">
            <i class="ri-archive-line"></i> Informasi Produk
        </div>
        @php
            $kategoriColor = $inventory->kategori ? 'badge-blue' : 'badge-gray';
        @endphp
        <span class="badge {{ $kategoriColor }}">
            {{ $inventory->kategori ?? 'Tanpa Kategori' }}
        </span>
    </div>
    <div class="detail-card-body">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Nama Produk</span>
                <span class="info-value">{{ $inventory->nama_produk }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Kategori</span>
                <span class="info-value">{{ $inventory->kategori ?? '-' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Satuan</span>
                <span class="info-value" style="text-transform:capitalize;">{{ $inventory->satuan }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Harga Beli Terakhir</span>
                <span class="info-value">
                    @if($inventory->harga_beli_terakhir)
                        Rp {{ number_format($inventory->harga_beli_terakhir, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </span>
            </div>
            @if($inventory->keterangan)
            <div class="info-item full">
                <span class="info-label">Keterangan</span>
                <span class="info-value">{{ $inventory->keterangan }}</span>
            </div>
            @endif
            <div class="info-item">
                <span class="info-label">Dibuat Pada</span>
                <span class="info-value">
                    {{ $inventory->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Terakhir Diupdate</span>
                <span class="info-value">
                    {{ $inventory->updated_at?->translatedFormat('d F Y, H:i') ?? '-' }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ════ CARD: Ringkasan Stok ════ --}}
<div class="detail-card">
    <div class="detail-card-header">
        <div class="detail-card-title">
            <i class="ri-stack-line"></i> Ringkasan Stok
        </div>
    </div>
    <div class="detail-card-body">
        <div class="stok-summary">
            <div class="stok-summary-item">
                <div class="stok-summary-label">Stok Tersedia</div>
                <div class="stok-summary-value green">{{ number_format($inventory->stok_tersedia ?? 0) }}</div>
            </div>
            <div class="stok-summary-item">
                <div class="stok-summary-label">Stok Baru</div>
                <div class="stok-summary-value">{{ number_format($inventory->stok_baru ?? 0) }}</div>
            </div>
            <div class="stok-summary-item">
                <div class="stok-summary-label">Stok Bekas</div>
                <div class="stok-summary-value amber">{{ number_format($inventory->stok_bekas ?? 0) }}</div>
            </div>
            <div class="stok-summary-item">
                <div class="stok-summary-label">Sedang Disewa</div>
                <div class="stok-summary-value {{ ($inventory->stok_disewa ?? 0) > 0 ? 'amber' : '' }}">
                    {{ number_format($inventory->stok_disewa ?? 0) }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════ CARD: Riwayat Log Inventory ════ --}}
<div class="detail-card">
    <div class="detail-card-header">
        <div class="detail-card-title">
            <i class="ri-history-line"></i> Riwayat Log Perubahan Stok
        </div>
        <span style="font-size:12px;color:var(--text-muted);">{{ $logs->total() }} entri</span>
    </div>
    <div class="detail-card-body" style="padding:0;">
        @if($logs->isNotEmpty())
        <div style="overflow-x:auto;">
            <table class="log-table">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>Tanggal</th>
                        <th>Tipe Referensi</th>
                        <th>Kondisi</th>
                        <th style="text-align:center;">Perubahan Qty</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $i => $log)
                    <tr>
                        <td style="text-align:center;color:var(--text-muted);font-size:12px;">
                            {{ $logs->firstItem() + $i }}
                        </td>
                        <td style="white-space:nowrap;font-size:12.5px;">
                            {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td>
                            @php
                                $refColor = match($log->reference_type) {
                                    'manual'    => 'badge-gray',
                                    'penjualan' => 'badge-blue',
                                    'sewa'      => 'badge-amber',
                                    'retur'     => 'badge-green',
                                    default     => 'badge-gray',
                                };
                            @endphp
                            <span class="badge {{ $refColor }}" style="text-transform:capitalize;">
                                {{ $log->reference_type }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $log->kondisi === 'baru' ? 'badge-blue' : 'badge-gray' }}">
                                {{ ucfirst($log->kondisi ?? 'baru') }}
                            </span>
                        </td>
                        <td style="text-align:center;font-weight:700;">
                            @if($log->qty_change > 0)
                                <span style="color:#059669;">+{{ $log->qty_change }}</span>
                            @else
                                <span style="color:#DC2626;">{{ $log->qty_change }}</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);font-size:12.5px;">
                            {{ $log->keterangan ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap" style="padding:16px 22px;">
            {{ $logs->links() }}
        </div>
        @else
        <div style="text-align:center;color:var(--text-muted);padding:32px 0;font-size:13px;">
            <i class="ri-history-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
            Belum ada riwayat perubahan stok untuk produk ini.
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
// Placeholder untuk kebutuhan JS tambahan di halaman ini
</script>
@endpush