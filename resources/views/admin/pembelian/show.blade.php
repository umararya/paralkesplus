{{-- resources/views/admin/pembelian/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Pembelian')
@section('breadcrumb', 'Detail Pembelian')

@push('styles')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .page-title { font-size:20px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:10px; line-height:1.2; }
    .page-title i { font-size:22px; color:var(--brand-500); }
    .page-subtitle { font-size:13px; color:var(--text-muted); margin-top:4px; }
    .header-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 14px; height:36px; border-radius:8px; font-size:13px; font-weight:500; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-ghost   { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); border-color:var(--brand-600); }
    .btn-danger  { background:#EF4444; color:#fff; border:1px solid #EF4444; }
    .btn-danger:hover { background:#DC2626; border-color:#DC2626; }
    .btn-warning { background:#F59E0B; color:#fff; border:1px solid #F59E0B; }
    .btn-warning:hover { background:#D97706; border-color:#D97706; }

    /* ── Grid layout ── */
    .detail-grid { display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start; }
    @media (max-width:860px) { .detail-grid { grid-template-columns:1fr; } }

    /* ── Card ── */
    .detail-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; transition:background 0.3s,border-color 0.3s; }
    .card-header { padding:14px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:8px; }
    .card-header-title { font-size:13.5px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:7px; }
    .card-header-title i { font-size:16px; color:var(--brand-500); }
    .card-body { padding:20px; }

    /* ── Info rows ── */
    .info-row { display:flex; align-items:flex-start; gap:12px; padding:11px 0; border-bottom:1px solid var(--border); }
    .info-row:last-child { border-bottom:none; padding-bottom:0; }
    .info-row:first-child { padding-top:0; }
    .info-label { font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; min-width:130px; flex-shrink:0; padding-top:2px; }
    .info-value { font-size:13.5px; color:var(--text-primary); flex:1; line-height:1.5; }

    /* ── Total highlight ── */
    .total-big { font-size:22px; font-weight:800; color:#059669; }
    html.dark .total-big { color:#34D399; }
    .total-calc { font-size:12px; color:var(--text-muted); margin-top:3px; }

    /* ── Badges ── */
    .status-badge    { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11.5px; font-weight:600; white-space:nowrap; }
    .status-normal   { background:#EFF6FF; color:#1D4ED8; }
    .status-buy-back { background:#FFFBEB; color:#D97706; }
    html.dark .status-normal   { background:rgba(29,78,216,0.12); color:#60A5FA; }
    html.dark .status-buy-back { background:rgba(217,119,6,0.12); color:#FBBF24; }

    .kondisi-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11.5px; font-weight:600; white-space:nowrap; }
    .kondisi-baru  { background:#EFF6FF; color:#1D6FA4; border:1px solid #BFDBFE; }
    .kondisi-bekas { background:#F5F3FF; color:#7C3AED; border:1px solid #DDD6FE; }
    .kondisi-baik  { background:#F0FDF4; color:#15803D; border:1px solid #BBF7D0; }
    .kondisi-rusak { background:#FFF1F2; color:#BE123C; border:1px solid #FECDD3; }
    html.dark .kondisi-baru  { background:rgba(29,111,164,0.12);  color:#60A5FA;  border-color:rgba(29,111,164,0.25); }
    html.dark .kondisi-bekas { background:rgba(124,58,237,0.12);  color:#A78BFA;  border-color:rgba(124,58,237,0.25); }
    html.dark .kondisi-baik  { background:rgba(21,128,61,0.12);   color:#4ADE80;  border-color:rgba(21,128,61,0.25); }
    html.dark .kondisi-rusak { background:rgba(190,18,60,0.12);   color:#FB7185;  border-color:rgba(190,18,60,0.25); }

    .invoice-pill { display:inline-flex; align-items:center; gap:4px; font-family:monospace; font-size:12px; font-weight:700; color:#1D4ED8; background:#EFF6FF; padding:3px 10px; border-radius:99px; border:1px solid #BFDBFE; white-space:nowrap; }
    html.dark .invoice-pill { background:rgba(29,78,216,0.15); color:#93C5FD; border-color:rgba(29,78,216,0.3); }

    .qty-badge { display:inline-flex; align-items:center; background:#EFF6FF; color:#1D4ED8; padding:2px 10px; border-radius:20px; font-size:13px; font-weight:700; }
    html.dark .qty-badge { background:rgba(29,78,216,0.15); color:#60A5FA; }

    /* ── Stok stats ── */
    .stok-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:4px; }
    .stok-item { background:var(--bg-primary); border:1px solid var(--border); border-radius:10px; padding:10px 12px; text-align:center; }
    .stok-item-val { font-size:20px; font-weight:800; color:var(--text-primary); line-height:1; }
    .stok-item-label { font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.4px; margin-top:4px; }
    .stok-tersedia .stok-item-val { color:#16A34A; }
    .stok-disewa   .stok-item-val { color:#D97706; }
    .stok-baru     .stok-item-val { color:#1D6FA4; }
    .stok-bekas    .stok-item-val { color:#7C3AED; }
    html.dark .stok-tersedia .stok-item-val { color:#4ADE80; }
    html.dark .stok-disewa   .stok-item-val { color:#FCD34D; }
    html.dark .stok-baru     .stok-item-val { color:#60A5FA; }
    html.dark .stok-bekas    .stok-item-val { color:#A78BFA; }

    .harga-beli-row { display:flex; align-items:center; justify-content:space-between; margin-top:12px; padding:10px 12px; background:var(--bg-primary); border:1px solid var(--border); border-radius:10px; }
    .harga-beli-label { font-size:12px; color:var(--text-muted); font-weight:600; display:flex; align-items:center; gap:5px; }
    .harga-beli-val   { font-size:14px; font-weight:800; color:#059669; }
    html.dark .harga-beli-val { color:#34D399; }

    .inv-not-found { text-align:center; padding:20px 10px; color:var(--text-muted); }
    .inv-not-found i { font-size:32px; display:block; margin-bottom:8px; color:var(--border); }
    .inv-not-found span { font-size:13px; }

    /* ── File preview ── */
    .file-section { display:flex; flex-direction:column; gap:10px; }
    .file-item { display:flex; align-items:center; gap:12px; padding:10px 12px; background:var(--bg-primary); border:1px solid var(--border); border-radius:10px; cursor:pointer; transition:all 0.2s; }
    .file-item:hover { border-color:var(--brand-500); background:var(--brand-50); }
    html.dark .file-item:hover { background:rgba(29,111,164,0.1); }
    .file-thumb { width:44px; height:44px; border-radius:8px; object-fit:cover; border:1px solid var(--border); flex-shrink:0; }
    .file-icon  { width:44px; height:44px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0; }
    .file-icon.pdf-bukti   { background:#FEF2F2; color:#EF4444; border:1px solid #FECACA; }
    .file-icon.pdf-invoice { background:#EFF6FF; color:#2563EB; border:1px solid #BFDBFE; }
    .file-icon.img-bukti   { background:#F0FDF4; color:#16A34A; border:1px solid #BBF7D0; }
    .file-icon.img-invoice { background:#EFF6FF; color:#2563EB; border:1px solid #BFDBFE; }
    .file-info { flex:1; min-width:0; }
    .file-name  { font-size:13px; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .file-meta  { font-size:11.5px; color:var(--text-muted); margin-top:2px; }
    .file-empty { display:flex; align-items:center; gap:10px; padding:10px 12px; background:var(--bg-primary); border:1px dashed var(--border); border-radius:10px; }
    .file-empty i { font-size:20px; color:var(--border); }
    .file-empty span { font-size:13px; color:var(--text-muted); font-style:italic; }

    /* ── Buy Back section ── */
    .buyback-card { background:linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border:1px solid #FDE68A; border-radius:14px; padding:20px; margin-top:20px; }
    html.dark .buyback-card { background:linear-gradient(135deg,rgba(180,83,9,0.12) 0%,rgba(180,83,9,0.06) 100%); border-color:rgba(180,83,9,0.3); }
    .buyback-card-title { font-size:14px; font-weight:700; color:#B45309; display:flex; align-items:center; gap:7px; margin-bottom:14px; }
    html.dark .buyback-card-title { color:#FCD34D; }
    .buyback-info-row { display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid rgba(180,83,9,0.12); }
    .buyback-info-row:last-child { border-bottom:none; padding-bottom:0; }
    .buyback-info-label { font-size:12px; font-weight:600; color:#92400E; }
    html.dark .buyback-info-label { color:#FCD34D; }
    .buyback-info-val { font-size:13px; font-weight:600; color:#78350F; }
    html.dark .buyback-info-val { color:#FEF3C7; }

    /* ── Alert ── */
    .alert { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:500; margin-bottom:18px; border:1px solid transparent; }
    .alert-success { background:#F0FDF4; color:#15803D; border-color:#BBF7D0; }
    .alert-error   { background:#FFF1F2; color:#BE123C; border-color:#FECDD3; }
    html.dark .alert-success { background:rgba(21,128,61,0.12); color:#4ADE80; border-color:rgba(21,128,61,0.25); }
    html.dark .alert-error   { background:rgba(190,18,60,0.12); color:#FB7185; border-color:rgba(190,18,60,0.25); }

    /* ── Modal ── */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from{opacity:0;}to{opacity:1;} }
    .modal { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.2); width:100%; animation:slideUp 0.2s ease; }
    @keyframes slideUp { from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);} }
    .modal-sm { max-width:420px; }
    .modal-header { padding:18px 22px 14px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .modal-title  { font-size:15px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .modal-close  { width:28px; height:28px; border:none; background:none; cursor:pointer; color:var(--text-muted); font-size:19px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:all 0.2s; }
    .modal-close:hover { background:var(--bg-hover); color:var(--text-primary); }
    .modal-body   { padding:18px 22px; }
    .modal-footer { padding:14px 22px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:flex-end; gap:8px; }
    .delete-warning { text-align:center; padding:6px 0; }
    .delete-warning i  { font-size:42px; color:#EF4444; display:block; margin-bottom:10px; }
    .delete-warning h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:7px; }
    .delete-warning p  { font-size:13px; color:var(--text-muted); line-height:1.6; }
    .delete-warning strong { color:var(--text-primary); }
    .delete-impact-box { display:flex; align-items:center; gap:10px; background:#FFF7ED; border:1px solid #FED7AA; border-radius:10px; padding:10px 14px; margin-top:12px; font-size:12.5px; color:#C2410C; text-align:left; }
    .delete-impact-box i { font-size:18px; flex-shrink:0; }
    html.dark .delete-impact-box { background:rgba(194,65,12,0.1); border-color:rgba(194,65,12,0.3); color:#FB923C; }

    /* ── Lightbox ── */
    .lightbox-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.88); z-index:2000; align-items:center; justify-content:center; padding:20px; cursor:zoom-out; }
    .lightbox-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    .lightbox-overlay img { max-width:90vw; max-height:88vh; border-radius:10px; object-fit:contain; box-shadow:0 20px 60px rgba(0,0,0,0.5); cursor:default; }
    .lightbox-close { position:fixed; top:16px; right:20px; width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .lightbox-close:hover { background:rgba(255,255,255,0.28); }
    .lightbox-caption { position:fixed; bottom:18px; left:50%; transform:translateX(-50%); font-size:13px; color:rgba(255,255,255,0.75); background:rgba(0,0,0,0.5); padding:6px 16px; border-radius:20px; white-space:nowrap; max-width:80vw; overflow:hidden; text-overflow:ellipsis; }

    /* ── PDF Modal ── */
    .file-modal { max-width:860px; width:100%; }
    .file-modal .modal-body { padding:0; }
    .pdf-frame { width:100%; height:72vh; border:none; border-radius:0 0 16px 16px; display:block; }
    .pdf-fallback { padding:28px 24px; text-align:center; }
    .pdf-fallback i { font-size:52px; color:#EF4444; display:block; margin-bottom:12px; }
    .pdf-fallback p { font-size:13px; color:var(--text-muted); margin-bottom:16px; }
</style>
@endpush

@section('content')

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

{{-- PAGE HEADER --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-shopping-cart-2-line"></i>
            Detail Pembelian
        </h1>
        <p class="page-subtitle">
            {{ $pembelian->nama_barang }}
            @if($pembelian->no_invoice)
                &nbsp;·&nbsp;
                <span style="font-family:monospace;font-weight:700;color:var(--brand-500);">
                    {{ $pembelian->no_invoice }}
                </span>
            @endif
        </p>
    </div>
    <div class="header-actions">
        <a href="{{ route('pembelian.index') }}" class="btn btn-ghost">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
        @if($pembelian->status === 'buy_back')
        <a href="{{ route('pembelian.invoice', $pembelian->id) }}"
           target="_blank" class="btn btn-warning">
            <i class="ri-printer-line"></i> Cetak Invoice
        </a>
        @endif
        <a href="{{ route('pembelian.edit', $pembelian->id) }}" class="btn btn-primary">
            <i class="ri-edit-line"></i> Edit Data
        </a>
        <button type="button" class="btn btn-danger"
                onclick="openDeleteModal(
                    '{{ addslashes($pembelian->nama_barang) }}',
                    {{ $pembelian->jumlah }},
                    '{{ route('pembelian.destroy', $pembelian->id) }}'
                )">
            <i class="ri-delete-bin-line"></i> Hapus
        </button>
    </div>
</div>

{{-- MAIN GRID --}}
<div class="detail-grid">

    {{-- ══ KOLOM KIRI: Info Pembelian ══ --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Card Info Utama --}}
        <div class="detail-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="ri-file-list-3-line"></i> Informasi Pembelian
                </div>
            </div>
            <div class="card-body">

                <div class="info-row">
                    <span class="info-label">Tanggal</span>
                    <span class="info-value">
                        <span style="display:inline-flex;align-items:center;gap:5px;background:var(--bg-hover);padding:3px 10px;border-radius:6px;font-size:13px;font-weight:500;">
                            <i class="ri-calendar-line" style="font-size:13px;color:var(--text-muted);"></i>
                            {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d F Y') }}
                        </span>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">No. Invoice</span>
                    <span class="info-value">
                        @if($pembelian->no_invoice)
                            <span class="invoice-pill">
                                <i class="ri-price-tag-3-line" style="font-size:11px;"></i>
                                {{ $pembelian->no_invoice }}
                            </span>
                        @else
                            <span style="color:var(--text-muted);font-style:italic;">Tidak ada</span>
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        @if($pembelian->status === 'buy_back')
                            <span class="status-badge status-buy-back">
                                <i class="ri-loop-left-line"></i> Buy Back
                            </span>
                        @else
                            <span class="status-badge status-normal">
                                <i class="ri-shopping-cart-line"></i> Normal
                            </span>
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Nama Barang</span>
                    <span class="info-value" style="font-weight:700;font-size:14.5px;">
                        {{ $pembelian->nama_barang }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Kondisi</span>
                    <span class="info-value">
                        @php $k = $pembelian->kondisi_barang; @endphp
                        @if($k === 'baru')
                            <span class="kondisi-badge kondisi-baru">
                                <i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Baru
                            </span>
                        @elseif($k === 'bekas')
                            <span class="kondisi-badge kondisi-bekas">
                                <i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Bekas
                            </span>
                        @elseif($k === 'baik')
                            <span class="kondisi-badge kondisi-baik">
                                <i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Baik
                            </span>
                        @elseif($k === 'rusak')
                            <span class="kondisi-badge kondisi-rusak">
                                <i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Rusak
                            </span>
                        @else
                            <span style="color:var(--text-muted);">-</span>
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Jumlah</span>
                    <span class="info-value">
                        <span class="qty-badge">{{ number_format($pembelian->jumlah) }} unit</span>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Harga Satuan</span>
                    <span class="info-value" style="font-size:13.5px;font-weight:600;">
                        {{ $pembelian->harga_formatted }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Total</span>
                    <span class="info-value">
                        <div class="total-big">{{ $pembelian->total_formatted }}</div>
                        <div class="total-calc">
                            {{ number_format($pembelian->jumlah) }} unit
                            × {{ $pembelian->harga_formatted }}
                        </div>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Keterangan</span>
                    <span class="info-value">
                        @if($pembelian->keterangan)
                            <span style="line-height:1.6;">{{ $pembelian->keterangan }}</span>
                        @else
                            <span style="color:var(--text-muted);font-style:italic;">Tidak ada keterangan</span>
                        @endif
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Dicatat</span>
                    <span class="info-value" style="font-size:12.5px;color:var(--text-muted);">
                        {{ $pembelian->created_at ? $pembelian->created_at->translatedFormat('d F Y, H:i') : '-' }}
                    </span>
                </div>

                @if($pembelian->updated_at && $pembelian->updated_at->ne($pembelian->created_at))
                <div class="info-row">
                    <span class="info-label">Diperbarui</span>
                    <span class="info-value" style="font-size:12.5px;color:var(--text-muted);">
                        {{ $pembelian->updated_at->translatedFormat('d F Y, H:i') }}
                    </span>
                </div>
                @endif

            </div>
        </div>

        {{-- Card Buy Back (hanya muncul jika status buy_back) --}}
        @if($pembelian->status === 'buy_back')
        <div class="buyback-card">
            <div class="buyback-card-title">
                <i class="ri-loop-left-line"></i> Informasi Buy Back
            </div>

            @if($pembelian->nama_pelanggan)
            <div class="buyback-info-row">
                <span class="buyback-info-label"><i class="ri-user-line"></i> Pelanggan</span>
                <span class="buyback-info-val">{{ $pembelian->nama_pelanggan }}</span>
            </div>
            @endif

            @if($pembelian->penjualan_id)
            <div class="buyback-info-row">
                <span class="buyback-info-label"><i class="ri-file-list-line"></i> Dari Penjualan</span>
                <span class="buyback-info-val">
                    <a href="{{ route('penjualan.show', $pembelian->penjualan_id) }}"
                       style="color:inherit;text-decoration:underline;text-underline-offset:3px;">
                        #{{ $pembelian->penjualan_id }}
                        <i class="ri-external-link-line" style="font-size:12px;"></i>
                    </a>
                </span>
            </div>
            @endif

            <div class="buyback-info-row">
                <span class="buyback-info-label"><i class="ri-calendar-line"></i> Tanggal Buy Back</span>
                <span class="buyback-info-val">
                    {{ \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->translatedFormat('d F Y') }}
                </span>
            </div>

            <div class="buyback-info-row">
                <span class="buyback-info-label"><i class="ri-money-dollar-circle-line"></i> Total Dibayar</span>
                <span class="buyback-info-val" style="font-size:15px;font-weight:800;">
                    {{ $pembelian->total_formatted }}
                </span>
            </div>
        </div>
        @endif

    </div>

    {{-- ══ KOLOM KANAN: Sidebar ══ --}}
    <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Card Stok Inventory --}}
        <div class="detail-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="ri-archive-line"></i> Stok Inventory
                </div>
                @if($inventoryItem)
                <a href="{{ route('inventory.show', $inventoryItem->id) }}"
                   style="font-size:12px;color:var(--brand-500);text-decoration:none;margin-left:auto;display:flex;align-items:center;gap:3px;"
                   title="Lihat di Inventory">
                    Detail <i class="ri-external-link-line" style="font-size:12px;"></i>
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($inventoryItem)
                    <div class="stok-grid">
                        <div class="stok-item stok-tersedia">
                            <div class="stok-item-val">{{ number_format($inventoryItem->stok_tersedia) }}</div>
                            <div class="stok-item-label">Tersedia</div>
                        </div>
                        <div class="stok-item stok-disewa">
                            <div class="stok-item-val">{{ number_format($inventoryItem->stok_disewa) }}</div>
                            <div class="stok-item-label">Disewa</div>
                        </div>
                        <div class="stok-item stok-baru">
                            <div class="stok-item-val">{{ number_format($inventoryItem->stok_baru ?? 0) }}</div>
                            <div class="stok-item-label">Stok Baru</div>
                        </div>
                        <div class="stok-item stok-bekas">
                            <div class="stok-item-val">{{ number_format($inventoryItem->stok_bekas ?? 0) }}</div>
                            <div class="stok-item-label">Stok Bekas</div>
                        </div>
                    </div>
                    <div class="harga-beli-row">
                        <span class="harga-beli-label">
                            <i class="ri-price-tag-3-line"></i> Harga Beli Terakhir
                        </span>
                        <span class="harga-beli-val">
                            @if($inventoryItem->harga_beli_terakhir)
                                Rp {{ number_format($inventoryItem->harga_beli_terakhir, 0, ',', '.') }}
                            @else
                                <span style="font-size:13px;font-weight:500;color:var(--text-muted);">-</span>
                            @endif
                        </span>
                    </div>
                @else
                    <div class="inv-not-found">
                        <i class="ri-archive-line"></i>
                        <span>Barang ini tidak ditemukan<br>di data inventory.</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card File Dokumen --}}
        <div class="detail-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="ri-folder-open-line"></i> File Dokumen
                </div>
            </div>
            <div class="card-body">
                <div class="file-section">

                    {{-- Bukti Pembayaran --}}
                    @if($pembelian->bukti_transaksi)
                        @php $extBukti = strtolower(pathinfo($pembelian->bukti_transaksi, PATHINFO_EXTENSION)); @endphp
                        @if($extBukti === 'pdf')
                        <div class="file-item"
                             onclick="openFileModal(
                                '{{ asset('storage/' . $pembelian->bukti_transaksi) }}',
                                'Bukti Pembayaran — {{ addslashes($pembelian->nama_barang) }}',
                                'pdf'
                             )">
                            <div class="file-icon pdf-bukti"><i class="ri-file-pdf-2-line"></i></div>
                            <div class="file-info">
                                <div class="file-name">Bukti Pembayaran</div>
                                <div class="file-meta">PDF · Klik untuk lihat</div>
                            </div>
                            <i class="ri-eye-line" style="font-size:16px;color:var(--text-muted);flex-shrink:0;"></i>
                        </div>
                        @else
                        <div class="file-item"
                             onclick="openLightbox(
                                '{{ asset('storage/' . $pembelian->bukti_transaksi) }}',
                                'Bukti Pembayaran — {{ addslashes($pembelian->nama_barang) }}'
                             )">
                            <img src="{{ asset('storage/' . $pembelian->bukti_transaksi) }}"
                                 alt="Bukti Pembayaran"
                                 class="file-thumb">
                            <div class="file-info">
                                <div class="file-name">Bukti Pembayaran</div>
                                <div class="file-meta">Gambar · Klik untuk preview</div>
                            </div>
                            <i class="ri-zoom-in-line" style="font-size:16px;color:var(--text-muted);flex-shrink:0;"></i>
                        </div>
                        @endif
                    @else
                        <div class="file-empty">
                            <i class="ri-image-line"></i>
                            <span>Bukti pembayaran belum diupload</span>
                        </div>
                    @endif

                    {{-- File Invoice --}}
                    @if($pembelian->file_invoice)
                        @php $extInv = strtolower(pathinfo($pembelian->file_invoice, PATHINFO_EXTENSION)); @endphp
                        @if($extInv === 'pdf')
                        <div class="file-item"
                             onclick="openFileModal(
                                '{{ asset('storage/' . $pembelian->file_invoice) }}',
                                'File Invoice — {{ addslashes($pembelian->nama_barang) }}',
                                'pdf'
                             )">
                            <div class="file-icon pdf-invoice"><i class="ri-file-pdf-2-line"></i></div>
                            <div class="file-info">
                                <div class="file-name">File Invoice Supplier</div>
                                <div class="file-meta">PDF · Klik untuk lihat</div>
                            </div>
                            <i class="ri-eye-line" style="font-size:16px;color:var(--text-muted);flex-shrink:0;"></i>
                        </div>
                        @else
                        <div class="file-item"
                             onclick="openLightbox(
                                '{{ asset('storage/' . $pembelian->file_invoice) }}',
                                'File Invoice — {{ addslashes($pembelian->nama_barang) }}'
                             )">
                            <img src="{{ asset('storage/' . $pembelian->file_invoice) }}"
                                 alt="File Invoice"
                                 class="file-thumb"
                                 style="border-color:#BFDBFE;">
                            <div class="file-info">
                                <div class="file-name">File Invoice Supplier</div>
                                <div class="file-meta">Gambar · Klik untuk preview</div>
                            </div>
                            <i class="ri-zoom-in-line" style="font-size:16px;color:var(--text-muted);flex-shrink:0;"></i>
                        </div>
                        @endif
                    @else
                        <div class="file-empty">
                            <i class="ri-file-text-line" style="color:#93C5FD;"></i>
                            <span>File invoice belum diupload</span>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>{{-- /kolom kanan --}}
</div>{{-- /detail-grid --}}


{{-- ══ MODAL: KONFIRMASI HAPUS ══ --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal modal-sm">
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
                <p>
                    Kamu akan menghapus data pembelian:<br>
                    <strong id="deleteNamaBarang"></strong>
                </p>
                <div class="delete-impact-box">
                    <i class="ri-archive-line"></i>
                    <span>
                        Stok inventory akan berkurang
                        <strong id="deleteJumlah"></strong>.
                        Pastikan stok ini belum dipakai di transaksi lain.
                    </span>
                </div>
                <p style="margin-top:12px;color:#EF4444;font-weight:600;font-size:12.5px;">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalHapus')">
                Batal
            </button>
            <form id="formDeleteSubmit" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- LIGHTBOX --}}
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="event.stopPropagation();closeLightbox()">
        <i class="ri-close-line"></i>
    </button>
    <img id="lightboxImg" src="" alt="Preview" onclick="event.stopPropagation()">
    <div class="lightbox-caption" id="lightboxCaption"></div>
</div>

{{-- PDF MODAL --}}
<div class="modal-overlay" id="modalFileViewer">
    <div class="modal file-modal">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-file-line"></i>
                <span id="fileViewerTitleText">Lihat File</span>
            </span>
            <div style="display:flex;align-items:center;gap:8px;">
                <a id="fileViewerOpenLink" href="#" target="_blank"
                   class="btn btn-ghost" style="height:30px;font-size:12px;padding:0 10px;">
                    <i class="ri-external-link-line"></i> Tab baru
                </a>
                <button class="modal-close" onclick="closeFileModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
        <div class="modal-body">
            <div id="pdfViewerWrap">
                <iframe id="pdfFrame" class="pdf-frame" src="" title="PDF Viewer"></iframe>
            </div>
            <div id="pdfFallback" class="pdf-fallback" style="display:none;">
                <i class="ri-file-pdf-2-line"></i>
                <p>Browser kamu tidak dapat menampilkan PDF secara langsung.</p>
                <a id="pdfFallbackLink" href="#" target="_blank" class="btn btn-primary">
                    <i class="ri-download-line"></i> Buka / Download File
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
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
        if (e.target === this) {
            if (this.id === 'modalFileViewer') closeFileModal();
            else closeModal(this.id);
        }
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeLightbox();
        closeFileModal();
        document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
    }
});

function openDeleteModal(namaBarang, jumlah, actionUrl) {
    document.getElementById('deleteNamaBarang').textContent = namaBarang;
    document.getElementById('deleteJumlah').textContent     = jumlah + ' unit';
    document.getElementById('formDeleteSubmit').action      = actionUrl;
    openModal('modalHapus');
}

function openLightbox(src, caption) {
    document.getElementById('lightboxImg').src             = src;
    document.getElementById('lightboxCaption').textContent = caption;
    document.getElementById('lightboxOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightboxOverlay').classList.remove('open');
    document.getElementById('lightboxImg').src = '';
    document.body.style.overflow = '';
}

function openFileModal(url, title, type) {
    document.getElementById('fileViewerTitleText').textContent = title;
    document.getElementById('fileViewerOpenLink').href         = url;
    document.getElementById('pdfFallbackLink').href            = url;

    const frame    = document.getElementById('pdfFrame');
    const wrap     = document.getElementById('pdfViewerWrap');
    const fallback = document.getElementById('pdfFallback');

    if (type === 'pdf') {
        frame.src              = url;
        wrap.style.display     = 'block';
        fallback.style.display = 'none';
        frame.onerror = function() {
            wrap.style.display     = 'none';
            fallback.style.display = 'block';
        };
    }
    openModal('modalFileViewer');
}
function closeFileModal() {
    closeModal('modalFileViewer');
    setTimeout(() => {
        document.getElementById('pdfFrame').src                = '';
        document.getElementById('pdfViewerWrap').style.display = 'block';
        document.getElementById('pdfFallback').style.display   = 'none';
    }, 200);
}
</script>
@endpush