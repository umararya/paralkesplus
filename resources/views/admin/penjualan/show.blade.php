{{-- resources/views/admin/penjualan/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Penjualan #' . $penjualan->id)
@section('breadcrumb', 'Detail Penjualan')

@push('styles')
<style>
    .detail-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; margin-bottom:20px; }
    .detail-card-header { padding:14px 22px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; background:var(--bg-primary); }
    .detail-card-title { font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .detail-card-title i { color:var(--brand-500); font-size:16px; }
    .detail-card-body { padding:22px; }

    .info-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; }
    @media(max-width:640px){ .info-grid{ grid-template-columns:1fr; } }
    .info-item { display:flex; flex-direction:column; gap:3px; }
    .info-item.full { grid-column:1/-1; }
    .info-label { font-size:11.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.4px; }
    .info-value { font-size:13.5px; color:var(--text-primary); font-weight:500; }

    .badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:99px; font-size:11.5px; font-weight:700; }
    .badge-green  { background:#D1FAE5; color:#065F46; }
    .badge-amber  { background:#FEF3C7; color:#92400E; }
    .badge-red    { background:#FEE2E2; color:#991B1B; }
    .badge-blue   { background:#DBEAFE; color:#1E40AF; }
    .badge-gray   { background:var(--bg-hover); color:var(--text-muted); }
    .badge-purple { background:#EDE9FE; color:#5B21B6; }
    html.dark .badge-green  { background:rgba(6,95,70,.25);    color:#6EE7B7; }
    html.dark .badge-amber  { background:rgba(146,64,14,.25);  color:#FCD34D; }
    html.dark .badge-red    { background:rgba(153,27,27,.25);  color:#FCA5A5; }
    html.dark .badge-blue   { background:rgba(30,64,175,.25);  color:#93C5FD; }
    html.dark .badge-purple { background:rgba(91,33,182,.25);  color:#C4B5FD; }

    .kirim-show-badge { display:inline-flex; align-items:center; gap:8px; padding:7px 14px; border-radius:10px; font-size:13px; font-weight:600; border:1px solid transparent; }
    .kirim-show-badge i { font-size:16px; }
    .kirim-ambil  { background:#FEF3C7; color:#92400E; border-color:#FDE68A; }
    .kirim-gosend { background:#D1FAE5; color:#065F46; border-color:#A7F3D0; }
    .kirim-rental { background:#DBEAFE; color:#1E40AF; border-color:#BFDBFE; }
    html.dark .kirim-ambil  { background:rgba(146,64,14,.2);  color:#FCD34D; border-color:rgba(146,64,14,.35); }
    html.dark .kirim-gosend { background:rgba(6,95,70,.2);    color:#6EE7B7; border-color:rgba(6,95,70,.35); }
    html.dark .kirim-rental { background:rgba(30,64,175,.2);  color:#93C5FD; border-color:rgba(30,64,175,.35); }

    .section-divider { display:flex; align-items:center; gap:10px; margin:20px 0 16px; color:var(--text-muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; }
    .section-divider::before,.section-divider::after { content:''; flex:1; height:1px; background:var(--border); }

    .items-table { width:100%; border-collapse:collapse; }
    .items-table thead tr { background:var(--brand-500); color:#fff; }
    .items-table th { padding:9px 12px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; white-space:nowrap; }
    .items-table td { padding:10px 12px; border-bottom:1px solid var(--border); font-size:13.5px; color:var(--text-primary); vertical-align:middle; }
    .items-table tbody tr:last-child td { border-bottom:none; }
    .items-table tbody tr:hover td { background:var(--bg-hover); }
    .items-table tfoot td { padding:10px 12px; font-size:13px; font-weight:700; border-top:2px solid var(--border); color:var(--text-primary); }

    .pay-summary { display:grid; grid-template-columns:repeat(3,1fr); gap:1px; background:var(--border); border-radius:12px; overflow:hidden; margin-bottom:20px; }
    .pay-summary-item { background:var(--bg-primary); padding:14px 16px; text-align:center; }
    .pay-summary-label { font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:4px; }
    .pay-summary-value { font-size:15px; font-weight:800; color:var(--text-primary); }
    .pay-summary-value.green { color:#059669; }
    .pay-summary-value.red   { color:#DC2626; }

    .pay-table { width:100%; border-collapse:collapse; margin-bottom:16px; }
    .pay-table th { padding:8px 12px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.3px; color:var(--text-muted); border-bottom:2px solid var(--border); text-align:left; background:var(--bg-primary); white-space:nowrap; }
    .pay-table td { padding:10px 12px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); vertical-align:middle; }
    .pay-table tbody tr:last-child td { border-bottom:none; }
    .pay-table tbody tr:hover td { background:var(--bg-hover); }

    .form-group { display:flex; flex-direction:column; gap:5px; }
    .form-label { font-size:12px; font-weight:600; color:var(--text-secondary); }
    .form-label .req { color:#EF4444; margin-left:2px; }
    .form-control { width:100%; padding:9px 13px; border:1px solid var(--border); border-radius:8px; font-size:13px; background:var(--bg-primary); color:var(--text-primary); outline:none; transition:border-color .2s,box-shadow .2s; font-family:var(--font); box-sizing:border-box; }
    .form-control:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,.1); }
    select.form-control { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:36px; }
    .input-prefix-wrap { position:relative; }
    .input-prefix { position:absolute; left:11px; top:50%; transform:translateY(-50%); font-size:13px; color:var(--text-muted); pointer-events:none; }
    .input-prefix-wrap .form-control { padding-left:36px; }
    .pay-form-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    @media(max-width:768px){ .pay-form-grid{ grid-template-columns:1fr 1fr; } }
    @media(max-width:480px){ .pay-form-grid{ grid-template-columns:1fr; } }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 18px; height:38px; border-radius:8px; font-size:13px; font-weight:600; font-family:var(--font); cursor:pointer; border:none; transition:all .2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-primary { background:var(--brand-500); color:#fff; }
    .btn-primary:hover { background:var(--brand-600); }
    .btn-success { background:#059669; color:#fff; }
    .btn-success:hover { background:#047857; }
    .btn-warning { background:#D97706; color:#fff; }
    .btn-warning:hover { background:#B45309; }
    .btn-danger  { background:#DC2626; color:#fff; }
    .btn-danger:hover  { background:#B91C1C; }
    .btn-ghost   { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover   { background:var(--bg-hover); }
    .btn-sm { height:32px; padding:0 12px; font-size:12px; }
    .btn-link-danger { background:none; border:none; color:#DC2626; font-size:12px; cursor:pointer; font-family:var(--font); padding:0; font-weight:600; }
    .btn-link-danger:hover { text-decoration:underline; }

    .alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:500; margin-bottom:18px; border:1px solid transparent; }
    .alert-success { background:#ECFDF5; color:#065F46; border-color:#A7F3D0; }
    .alert-error   { background:#FFF1F2; color:#BE123C; border-color:#FECDD3; }
    .alert-warning { background:#FFFBEB; color:#92400E; border-color:#FDE68A; }
    .alert-info    { background:#EFF6FF; color:#1E40AF; border-color:#BFDBFE; }
    html.dark .alert-success { background:rgba(6,95,70,.15);   color:#6EE7B7; border-color:rgba(6,95,70,.3); }
    html.dark .alert-error   { background:rgba(190,18,60,.12); color:#FB7185; border-color:rgba(190,18,60,.25); }
    html.dark .alert-warning { background:rgba(146,64,14,.15); color:#FCD34D; border-color:rgba(146,64,14,.3); }
    html.dark .alert-info    { background:rgba(30,64,175,.15); color:#93C5FD; border-color:rgba(30,64,175,.3); }

    .tambah-bayar-section { border-top:1px solid var(--border); padding-top:20px; margin-top:4px; }
    .tambah-bayar-title { font-size:13.5px; font-weight:700; color:var(--text-primary); margin-bottom:14px; display:flex; align-items:center; gap:7px; }
    .tambah-bayar-title i { color:var(--brand-500); }

    .modal-overlay { display:none; position:fixed; inset:0; z-index:1000; background:rgba(0,0,0,.5); align-items:center; justify-content:center; }
    .modal-overlay.active { display:flex; }
    .modal-box { background:var(--bg-card); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,.2); padding:24px; width:100%; max-width:560px; margin:16px; max-height:90vh; overflow-y:auto; }
    .modal-title { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:8px; }
    .modal-desc  { font-size:13px; color:var(--text-muted); margin-bottom:16px; line-height:1.5; }
    .modal-footer { display:flex; gap:8px; justify-content:flex-end; margin-top:16px; }

    /* ── Buyback table ── */
    .bb-table { width:100%; border-collapse:collapse; }
    .bb-table th { padding:8px 10px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; color:var(--text-muted); border-bottom:2px solid var(--border); text-align:left; background:var(--bg-primary); white-space:nowrap; }
    .bb-table td { padding:10px 10px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); vertical-align:middle; }
    .bb-table tbody tr:last-child td { border-bottom:none; }
    .bb-row-done td { opacity:.55; }
    .bb-qty-bar { display:flex; align-items:center; gap:8px; }
    .bb-bar-wrap { flex:1; background:var(--border); border-radius:99px; height:6px; overflow:hidden; min-width:60px; }
    .bb-bar-fill { height:6px; border-radius:99px; background:var(--brand-500); transition:width .3s; }
    .bb-bar-fill.full { background:#059669; }
    .bb-bar-fill.warn { background:#D97706; }
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
@if($errors->any())
<div class="alert alert-error">
    <i class="ri-error-warning-fill" style="font-size:17px;flex-shrink:0;margin-top:1px;"></i>
    <div>
        <strong>Terjadi kesalahan:</strong>
        <ul style="margin:4px 0 0 16px;padding:0;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

@php
    use App\Models\Pembelian;

    $transaksiColor = match($penjualan->status_transaksi) {
        'aktif'   => 'badge-blue',
        'selesai' => 'badge-green',
        'batal'   => 'badge-red',
        default   => 'badge-gray',
    };
    $bayarColor = match($penjualan->status_pembayaran) {
        'lunas'       => 'badge-green',
        'dp'          => 'badge-amber',
        'belum_lunas' => 'badge-red',
        default       => 'badge-gray',
    };
    $kirimKey   = $penjualan->jasa_pengiriman ?? 'ambil_sendiri';
    $kirimClass = match($kirimKey) {
        'gosend_grab'  => 'kirim-gosend',
        'rental_mobil' => 'kirim-rental',
        default        => 'kirim-ambil',
    };

    // ─────────────────────────────────────────────────────────────────
    // Hitung buyback per detail item — PAKAI detail_penjualan_id
    // agar tidak terpengaruh kesamaan nama_barang di transaksi lain
    // ─────────────────────────────────────────────────────────────────
    $buybackMap = [];
    foreach ($penjualan->details as $detail) {
        $sudah = Pembelian::where('status', 'buy_back')
            ->where('detail_penjualan_id', $detail->id)
            ->sum('jumlah');
        $buybackMap[$detail->id] = [
            'sudah' => (int) $sudah,
            'sisa'  => max(0, $detail->qty - (int) $sudah),
            'total' => (int) $detail->qty,
        ];
    }

    $semuaSudahBuyback  = collect($buybackMap)->every(fn($b) => $b['sisa'] === 0);
    $adaYangBisaBuyback = collect($buybackMap)->some(fn($b)  => $b['sisa'] > 0);
@endphp

{{-- ── Top Action Bar ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="{{ route('penjualan.index') }}" class="btn btn-ghost btn-sm">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
        <span style="font-size:15px;font-weight:700;color:var(--text-primary);">
            Detail Penjualan <span style="color:var(--brand-500)">#{{ $penjualan->id }}</span>
        </span>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        @if(!$penjualan->isBatal())
            <a href="{{ route('penjualan.invoice', $penjualan->id) }}" target="_blank"
               class="btn btn-ghost btn-sm">
                <i class="ri-printer-line"></i> Cetak Invoice
            </a>
            <a href="{{ route('penjualan.edit', $penjualan->id) }}" class="btn btn-primary btn-sm">
                <i class="ri-edit-line"></i> Edit
            </a>

            {{-- Tombol Buy Back: aktif jika masih ada sisa, disabled jika semua sudah habis --}}
            @if($adaYangBisaBuyback)
                <button type="button" onclick="openModalBuyBack()" class="btn btn-warning btn-sm">
                    <i class="ri-arrow-go-back-line"></i> Buy Back
                </button>
            @else
                <button type="button" class="btn btn-ghost btn-sm"
                        disabled title="Semua barang sudah di-buyback"
                        style="cursor:not-allowed;opacity:.5;">
                    <i class="ri-arrow-go-back-line"></i> Buy Back
                </button>
            @endif

            <button type="button" onclick="openModalBatal()" class="btn btn-danger btn-sm">
                <i class="ri-close-circle-line"></i> Batalkan
            </button>
        @endif
        <form method="POST" action="{{ route('penjualan.destroy', $penjualan->id) }}"
              onsubmit="return confirm('Hapus data penjualan ini permanen?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-ghost btn-sm"
                    style="color:#DC2626;border-color:#DC2626;">
                <i class="ri-delete-bin-line"></i> Hapus
            </button>
        </form>
    </div>
</div>

{{-- ════ CARD 1: Info Transaksi ════ --}}
<div class="detail-card">
    <div class="detail-card-header">
        <div class="detail-card-title">
            <i class="ri-file-list-3-line"></i> Informasi Transaksi
        </div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
            <span class="badge {{ $transaksiColor }}">{{ $penjualan->status_transaksi_label }}</span>
            <span class="badge {{ $bayarColor }}">{{ $penjualan->status_pembayaran_label }}</span>
        </div>
    </div>
    <div class="detail-card-body">
        <div class="info-grid">

            <div class="info-item">
                <span class="info-label">Tanggal Penjualan</span>
                <span class="info-value">
                    {{ $penjualan->tanggal_penjualan?->translatedFormat('d F Y') ?? '-' }}
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Metode Pembayaran</span>
                <span class="info-value" style="text-transform:capitalize;">
                    {{ $penjualan->metode_pembayaran ?? $penjualan->jenis_pembayaran ?? '-' }}
                </span>
            </div>

            <div class="info-item">
                <span class="info-label">Nama Pelanggan</span>
                <span class="info-value">{{ $penjualan->nama_pelanggan }}</span>
            </div>

            <div class="info-item">
                <span class="info-label">No. Telepon</span>
                <span class="info-value">{{ $penjualan->nomor_telepon ?? '-' }}</span>
            </div>

            <div class="info-item full">
                <span class="info-label">Alamat</span>
                <span class="info-value">{{ $penjualan->alamat_pelanggan ?? '-' }}</span>
            </div>

            @if($penjualan->keterangan)
            <div class="info-item full">
                <span class="info-label">Keterangan</span>
                <span class="info-value">{{ $penjualan->keterangan }}</span>
            </div>
            @endif

            @if($penjualan->foto_bukti)
            <div class="info-item full">
                <span class="info-label">Bukti Pembayaran</span>
                @php $extUtama = strtolower(pathinfo($penjualan->foto_bukti, PATHINFO_EXTENSION)); @endphp
                @if($extUtama === 'pdf')
                    <a href="{{ Storage::url($penjualan->foto_bukti) }}" target="_blank"
                       style="font-size:13px;color:var(--brand-500);display:inline-flex;align-items:center;gap:4px;">
                        <i class="ri-file-pdf-line"></i> Lihat PDF
                    </a>
                @else
                    <a href="{{ Storage::url($penjualan->foto_bukti) }}" target="_blank"
                       style="font-size:13px;color:var(--brand-500);display:inline-flex;align-items:center;gap:4px;">
                        <i class="ri-image-line"></i> Lihat Foto
                    </a>
                @endif
            </div>
            @endif

        </div>

        {{-- ── Blok Pengiriman ── --}}
        <div class="section-divider">
            <i class="ri-truck-line" style="font-size:13px;"></i> Informasi Pengiriman
        </div>

        <div class="info-grid">
            <div class="info-item full">
                <span class="info-label">Jasa Pengiriman</span>
                <span class="info-value">
                    <span class="kirim-show-badge {{ $kirimClass }}">
                        <i class="{{ $penjualan->jasa_pengiriman_icon }}"></i>
                        {{ $penjualan->jasa_pengiriman_label }}
                    </span>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Ongkos Kirim</span>
                <span class="info-value">
                    @if(($penjualan->harga_pengiriman ?? 0) > 0)
                        <strong>Rp {{ number_format($penjualan->harga_pengiriman, 0, ',', '.') }}</strong>
                    @else
                        <span style="color:#059669;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
                            <i class="ri-check-double-line"></i> Gratis (Rp 0)
                        </span>
                    @endif
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Jasa Instalasi</span>
                <span class="info-value">
                    @if(($penjualan->jasa_instalasi ?? 0) > 0)
                        <strong style="color:#7C3AED;display:inline-flex;align-items:center;gap:4px;">
                            <i class="ri-tools-line"></i>
                            Rp {{ number_format($penjualan->jasa_instalasi, 0, ',', '.') }}
                        </strong>
                    @else
                        <span style="color:var(--text-muted);font-style:italic;">Tidak ada</span>
                    @endif
                </span>
            </div>
        </div>

        @if($penjualan->isBatal() && $penjualan->catatan_pembatalan)
        <div class="alert alert-error" style="margin-top:16px;margin-bottom:0;">
            <i class="ri-close-circle-fill" style="font-size:17px;flex-shrink:0;margin-top:1px;"></i>
            <div>
                <strong>Transaksi Dibatalkan.</strong>
                Alasan: {{ $penjualan->catatan_pembatalan }}
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ════ CARD 2: Detail Barang ════ --}}
<div class="detail-card">
    <div class="detail-card-header">
        <div class="detail-card-title">
            <i class="ri-shopping-bag-line"></i> Detail Barang
        </div>
        <span style="font-size:12px;color:var(--text-muted);">
            {{ $penjualan->details->count() }} item
        </span>
    </div>
    <div class="detail-card-body" style="padding:0;">
        <div style="overflow-x:auto;">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>Nama Barang</th>
                        <th style="width:90px;">Kondisi</th>
                        <th style="width:60px;text-align:center;">Qty</th>
                        <th style="width:70px;">Satuan</th>
                        <th style="width:130px;text-align:right;">Harga Satuan</th>
                        <th style="width:60px;text-align:center;">Diskon</th>
                        <th style="width:130px;text-align:right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualan->details as $i => $detail)
                    <tr>
                        <td style="text-align:center;color:var(--text-muted);font-size:12px;">{{ $i + 1 }}</td>
                        <td>
                            <span style="font-weight:600;">{{ $detail->nama_barang }}</span>
                            @if($detail->inventory)
                            <br><span style="font-size:11px;color:var(--text-muted);">{{ $detail->inventory->nama_produk ?? '' }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ ($detail->kondisi ?? 'baru') === 'baru' ? 'badge-blue' : 'badge-gray' }}">
                                {{ ucfirst($detail->kondisi ?? 'baru') }}
                            </span>
                        </td>
                        <td style="text-align:center;">{{ $detail->qty }}</td>
                        <td style="color:var(--text-muted);">{{ $detail->satuan ?? 'unit' }}</td>
                        <td style="text-align:right;">Rp {{ number_format($detail->harga_satuan ?? 0, 0, ',', '.') }}</td>
                        <td style="text-align:center;color:var(--text-muted);">{{ $detail->diskon ?? 0 }}%</td>
                        <td style="text-align:right;font-weight:700;">Rp {{ number_format($detail->subtotal ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;color:var(--text-muted);padding:24px;">
                            Tidak ada data barang.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" style="text-align:right;color:var(--text-muted);font-weight:400;">Subtotal Barang</td>
                        <td style="text-align:right;">Rp {{ number_format($penjualan->total_harga ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @if(($penjualan->diskon_global ?? 0) > 0)
                    <tr>
                        <td colspan="7" style="text-align:right;color:#DC2626;font-weight:400;">Diskon Global</td>
                        <td style="text-align:right;color:#DC2626;">- Rp {{ number_format($penjualan->diskon_global, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if(($penjualan->harga_pengiriman ?? 0) > 0)
                    <tr>
                        <td colspan="7" style="text-align:right;color:var(--text-secondary);font-weight:400;">
                            <span style="display:inline-flex;align-items:center;gap:5px;">
                                <i class="ri-truck-line" style="font-size:12px;"></i> Ongkos Kirim
                                <span style="font-size:11px;color:var(--text-muted);">({{ $penjualan->jasa_pengiriman_label }})</span>
                            </span>
                        </td>
                        <td style="text-align:right;">+ Rp {{ number_format($penjualan->harga_pengiriman, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if(($penjualan->jasa_instalasi ?? 0) > 0)
                    <tr>
                        <td colspan="7" style="text-align:right;color:#7C3AED;font-weight:400;">
                            <span style="display:inline-flex;align-items:center;gap:5px;">
                                <i class="ri-tools-line" style="font-size:12px;"></i> Jasa Instalasi
                            </span>
                        </td>
                        <td style="text-align:right;color:#7C3AED;">+ Rp {{ number_format($penjualan->jasa_instalasi, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr style="background:var(--bg-hover);">
                        <td colspan="7" style="text-align:right;font-size:14px;"><strong>Total Tagihan</strong></td>
                        <td style="text-align:right;font-size:15px;color:var(--brand-500);">
                            <strong>Rp {{ number_format($penjualan->total_tagihan, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- ════ CARD 3: Status Buy Back ════ --}}
@if(!$penjualan->isBatal())
<div class="detail-card">
    <div class="detail-card-header">
        <div class="detail-card-title">
            <i class="ri-arrow-go-back-line"></i> Status Buy Back
        </div>
        @if($semuaSudahBuyback)
            <span class="badge badge-green">
                <i class="ri-check-double-line" style="margin-right:3px;"></i> Semua Sudah Di-Buyback
            </span>
        @elseif(collect($buybackMap)->every(fn($b) => $b['sudah'] === 0))
            <span class="badge badge-gray">Belum Ada Buyback</span>
        @else
            <span class="badge badge-amber">Sebagian Di-Buyback</span>
        @endif
    </div>
    <div class="detail-card-body" style="padding:0;">

        {{-- Notif jika semua sudah habis --}}
        @if($semuaSudahBuyback)
        <div class="alert alert-info" style="margin:16px 16px 0;">
            <i class="ri-information-line" style="font-size:17px;flex-shrink:0;margin-top:1px;"></i>
            <div>
                <strong>Semua barang sudah di-buyback.</strong>
                Tidak ada barang yang bisa di-buyback lagi dari transaksi ini.
            </div>
        </div>
        @endif

        <div style="overflow-x:auto;padding-bottom:4px;">
            <table class="bb-table">
                <thead>
                    <tr>
                        <th style="width:36px;">#</th>
                        <th>Nama Barang</th>
                        <th style="width:80px;text-align:center;">Qty Jual</th>
                        <th style="width:110px;text-align:center;">Sudah Buyback</th>
                        <th style="width:120px;text-align:center;">Sisa Bisa Buyback</th>
                        <th style="min-width:140px;">Progress</th>
                        <th style="width:90px;text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->details as $i => $detail)
                    @php
                        $bb     = $buybackMap[$detail->id];
                        $pct    = $bb['total'] > 0 ? round(($bb['sudah'] / $bb['total']) * 100) : 0;
                        $isDone = $bb['sisa'] === 0;
                        $barCls = $isDone ? 'full' : ($pct >= 50 ? 'warn' : '');
                    @endphp
                    <tr class="{{ $isDone ? 'bb-row-done' : '' }}">
                        <td style="text-align:center;color:var(--text-muted);font-size:12px;">{{ $i + 1 }}</td>
                        <td style="font-weight:600;">{{ $detail->nama_barang }}</td>
                        <td style="text-align:center;">{{ $bb['total'] }}</td>
                        <td style="text-align:center;">
                            @if($bb['sudah'] > 0)
                                <span style="color:#059669;font-weight:700;">{{ $bb['sudah'] }}</span>
                            @else
                                <span style="color:var(--text-muted);">0</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($isDone)
                                <span style="color:#DC2626;font-weight:700;">0</span>
                            @else
                                <span style="color:var(--brand-500);font-weight:700;">{{ $bb['sisa'] }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="bb-qty-bar">
                                <div class="bb-bar-wrap">
                                    <div class="bb-bar-fill {{ $barCls }}" style="width:{{ $pct }}%;"></div>
                                </div>
                                <span style="font-size:11px;color:var(--text-muted);white-space:nowrap;">{{ $pct }}%</span>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            @if($isDone)
                                <span class="badge badge-green" style="font-size:10.5px;">Habis</span>
                            @elseif($bb['sudah'] > 0)
                                <span class="badge badge-amber" style="font-size:10.5px;">Sebagian</span>
                            @else
                                <span class="badge badge-gray" style="font-size:10.5px;">Belum</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($adaYangBisaBuyback)
        <div style="padding:14px 16px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;">
            <button type="button" onclick="openModalBuyBack()" class="btn btn-warning btn-sm">
                <i class="ri-arrow-go-back-line"></i> Proses Buy Back
            </button>
        </div>
        @endif

    </div>
</div>
@endif

{{-- ════ CARD 4: Riwayat Pembayaran ════ --}}
<div class="detail-card">
    <div class="detail-card-header">
        <div class="detail-card-title">
            <i class="ri-wallet-3-line"></i> Riwayat Pembayaran
        </div>
    </div>
    <div class="detail-card-body">

        <div class="pay-summary">
            <div class="pay-summary-item">
                <div class="pay-summary-label">Total Tagihan</div>
                <div class="pay-summary-value">Rp {{ number_format($penjualan->total_tagihan, 0, ',', '.') }}</div>
            </div>
            <div class="pay-summary-item">
                <div class="pay-summary-label">Sudah Dibayar</div>
                <div class="pay-summary-value green">Rp {{ number_format($penjualan->total_terbayar ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="pay-summary-item">
                <div class="pay-summary-label">Sisa Tagihan</div>
                <div class="pay-summary-value {{ $penjualan->sisa_tagihan > 0 ? 'red' : 'green' }}">
                    Rp {{ number_format($penjualan->sisa_tagihan, 0, ',', '.') }}
                </div>
            </div>
        </div>

        @if($penjualan->pembayarans->count() > 0)
        <div style="overflow-x:auto;">
            <table class="pay-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Metode</th>
                        <th style="text-align:right;">Jumlah</th>
                        <th>Dicatat Oleh</th>
                        <th>Keterangan</th>
                        <th style="width:60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->pembayarans as $i => $bayar)
                    <tr>
                        <td style="color:var(--text-muted);">{{ $i + 1 }}</td>
                        <td>{{ $bayar->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <span class="badge {{
                                match($bayar->tipe ?? '') {
                                    'pelunasan' => 'badge-green',
                                    'dp'        => 'badge-amber',
                                    'cicilan'   => 'badge-blue',
                                    default     => 'badge-gray',
                                }
                            }}">{{ ucfirst($bayar->tipe ?? '-') }}</span>
                        </td>
                        <td style="text-transform:uppercase;font-size:12px;font-weight:700;color:var(--text-muted);">
                            {{ $bayar->metode ?? '-' }}
                        </td>
                        <td style="text-align:right;font-weight:700;">
                            Rp {{ number_format($bayar->jumlah_bayar ?? 0, 0, ',', '.') }}
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);">{{ $bayar->createdBy->name ?? '-' }}</td>
                        <td style="font-size:12px;color:var(--text-muted);max-width:160px;">
                            {{ $bayar->keterangan ?? '-' }}
                            @if($bayar->foto_bukti)
                            <br>
                            @php $extBayar = strtolower(pathinfo($bayar->foto_bukti, PATHINFO_EXTENSION)); @endphp
                            @if($extBayar === 'pdf')
                                <a href="{{ Storage::url($bayar->foto_bukti) }}" target="_blank"
                                   style="color:var(--brand-500);font-size:11px;">
                                    <i class="ri-file-pdf-line"></i> Lihat PDF
                                </a>
                            @else
                                <a href="{{ Storage::url($bayar->foto_bukti) }}" target="_blank"
                                   style="color:var(--brand-500);font-size:11px;">
                                    <i class="ri-image-line"></i> Lihat bukti
                                </a>
                            @endif
                            @endif
                        </td>
                        <td>
                            @if(!$penjualan->isBatal())
                            <form method="POST"
                                  action="{{ route('penjualan.hapusPembayaran', [$penjualan->id, $bayar->id]) }}"
                                  onsubmit="return confirm('Hapus data pembayaran ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-link-danger" title="Hapus pembayaran">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center;color:var(--text-muted);padding:20px 0;font-size:13px;">
            <i class="ri-wallet-line" style="font-size:28px;display:block;margin-bottom:6px;opacity:.4;"></i>
            Belum ada riwayat pembayaran.
        </div>
        @endif

        @if($penjualan->bisaTambahPembayaran())
        <div class="tambah-bayar-section">
            <div class="tambah-bayar-title">
                <i class="ri-add-circle-line"></i> Tambah Pembayaran
            </div>
            <form method="POST"
                  action="{{ route('penjualan.tambahPembayaran', $penjualan->id) }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="pay-form-grid" style="margin-bottom:12px;">
                    <div class="form-group">
                        <label class="form-label">Tipe <span class="req">*</span></label>
                        <select name="tipe" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="dp"        {{ old('tipe') === 'dp'        ? 'selected' : '' }}>DP</option>
                            <option value="cicilan"   {{ old('tipe') === 'cicilan'   ? 'selected' : '' }}>Cicilan</option>
                            <option value="pelunasan" {{ old('tipe') === 'pelunasan' ? 'selected' : '' }}>Pelunasan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Metode <span class="req">*</span></label>
                        <select name="metode" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="cash"     {{ old('metode') === 'cash'     ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ old('metode') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="qris"     {{ old('metode') === 'qris'     ? 'selected' : '' }}>QRIS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal Bayar <span class="req">*</span></label>
                        <input type="date" name="tanggal_bayar" class="form-control"
                               value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="pay-form-grid" style="margin-bottom:12px;">
                    <div class="form-group">
                        <label class="form-label">
                            Jumlah Bayar <span class="req">*</span>
                            <span style="font-weight:400;color:var(--text-muted);">
                                (maks Rp {{ number_format($penjualan->sisa_tagihan, 0, ',', '.') }})
                            </span>
                        </label>
                        <div class="input-prefix-wrap">
                            <span class="input-prefix">Rp</span>
                            <input type="number" name="jumlah_bayar" class="form-control"
                                   min="1" max="{{ $penjualan->sisa_tagihan }}"
                                   value="{{ old('jumlah_bayar') }}" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bukti Pembayaran</label>
                        <input type="file" name="foto_bukti" class="form-control"
                               accept="image/jpg,image/jpeg,image/png,image/webp,application/pdf">
                        <small style="color:var(--text-muted);font-size:11px;margin-top:3px;display:block;">
                            Format: JPG, PNG, WEBP, PDF. Maks 5 MB.
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control"
                               placeholder="Opsional" value="{{ old('keterangan') }}">
                    </div>
                </div>
                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn btn-success">
                        <i class="ri-save-line"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
        @endif

    </div>
</div>

{{-- ════ Modal: Buy Back ════ --}}
@if(!$penjualan->isBatal() && $adaYangBisaBuyback)
<div class="modal-overlay" id="modalBuyBack">
    <div class="modal-box" style="max-width:620px;">
        <div class="modal-title">
            <i class="ri-arrow-go-back-line" style="color:#D97706;margin-right:6px;"></i>
            Proses Buy Back — #{{ $penjualan->id }}
        </div>
        <div class="modal-desc">
            Masukkan jumlah yang ingin di-buyback. Item yang sudah habis tidak bisa diisi.
        </div>

        <form method="POST" action="{{ route('pembelian.buyback.store') }}">
            @csrf
            <input type="hidden" name="penjualan_id" value="{{ $penjualan->id }}">

            <div style="margin-bottom:14px;">
                @foreach($penjualan->details as $i => $detail)
                @php
                    $bb     = $buybackMap[$detail->id];
                    $isDone = $bb['sisa'] === 0;
                @endphp
                <div style="border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:10px;
                            background:{{ $isDone ? 'var(--bg-hover)' : 'var(--bg-primary)' }};
                            {{ $isDone ? 'opacity:.55;' : '' }}">

                    <input type="hidden" name="items[{{ $i }}][detail_id]" value="{{ $detail->id }}">

                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                        <div>
                            <span style="font-weight:700;font-size:13.5px;">{{ $detail->nama_barang }}</span>
                            <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">
                                Terjual: <strong>{{ $bb['total'] }}</strong>
                                &nbsp;|&nbsp;
                                Sudah buyback: <strong style="color:{{ $bb['sudah'] > 0 ? '#059669' : 'var(--text-muted)' }};">{{ $bb['sudah'] }}</strong>
                                &nbsp;|&nbsp;
                                Sisa bisa buyback: <strong style="color:{{ $isDone ? '#DC2626' : 'var(--brand-500)' }};">{{ $bb['sisa'] }}</strong>
                            </div>
                        </div>
                        @if($isDone)
                            <span class="badge badge-red" style="font-size:11px;">
                                <i class="ri-close-circle-line" style="margin-right:3px;"></i> Sudah Habis
                            </span>
                        @endif
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div class="form-group">
                            <label class="form-label">
                                Qty Buyback
                                @if(!$isDone)<span class="req">*</span>@endif
                            </label>
                            <input type="number"
                                   name="items[{{ $i }}][qty_buyback]"
                                   class="form-control"
                                   min="0"
                                   max="{{ $bb['sisa'] }}"
                                   value="{{ $isDone ? 0 : $bb['sisa'] }}"
                                   @if($isDone) disabled readonly @endif
                                   placeholder="Maks {{ $bb['sisa'] }}">
                            @if($isDone)
                                <small style="color:#DC2626;font-size:11px;">Tidak bisa di-buyback lagi</small>
                            @else
                                <small style="color:var(--text-muted);font-size:11px;">Maks: {{ $bb['sisa'] }} unit</small>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga Buyback / unit</label>
                            <div class="input-prefix-wrap">
                                <span class="input-prefix">Rp</span>
                                <input type="number"
                                       name="items[{{ $i }}][harga_buyback]"
                                       class="form-control"
                                       min="0"
                                       value="{{ round(($detail->harga_satuan ?? 0) * 0.5) }}"
                                       placeholder="Default 50% harga jual"
                                       @if($isDone) disabled readonly @endif>
                            </div>
                            @if(!$isDone)
                            <small style="color:var(--text-muted);font-size:11px;">
                                Harga jual: Rp {{ number_format($detail->harga_satuan ?? 0, 0, ',', '.') }}
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Keterangan (opsional)</label>
                <textarea name="keterangan" class="form-control" rows="2"
                          placeholder="Catatan buy back..."
                          style="resize:vertical;">{{ old('keterangan') }}</textarea>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeModalBuyBack()" class="btn btn-ghost btn-sm">
                    Batal
                </button>
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="ri-arrow-go-back-line"></i> Proses Buy Back
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ════ Modal: Batalkan Transaksi ════ --}}
@if(!$penjualan->isBatal())
<div class="modal-overlay" id="modalBatal">
    <div class="modal-box">
        <div class="modal-title">
            <i class="ri-error-warning-line" style="color:#DC2626;margin-right:6px;"></i>
            Batalkan Transaksi
        </div>
        <div class="modal-desc">
            Transaksi <strong>#{{ $penjualan->id }}</strong> akan dibatalkan dan stok barang
            akan dikembalikan otomatis. Tindakan ini tidak dapat diurungkan.
        </div>
        <form method="POST" action="{{ route('penjualan.batalkan', $penjualan->id) }}">
            @csrf
            <div class="form-group" style="margin-bottom:4px;">
                <label class="form-label">
                    Alasan Pembatalan <span class="req">*</span>
                </label>
                <textarea name="catatan_pembatalan" class="form-control" rows="3"
                          placeholder="Tulis alasan pembatalan..." required
                          style="resize:vertical;min-height:80px;">{{ old('catatan_pembatalan') }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeModalBatal()" class="btn btn-ghost btn-sm">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="ri-close-circle-line"></i> Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function openModalBuyBack() {
    document.getElementById('modalBuyBack')?.classList.add('active');
}
function closeModalBuyBack() {
    document.getElementById('modalBuyBack')?.classList.remove('active');
}
document.getElementById('modalBuyBack')?.addEventListener('click', function(e) {
    if (e.target === this) closeModalBuyBack();
});

function openModalBatal() {
    document.getElementById('modalBatal')?.classList.add('active');
}
function closeModalBatal() {
    document.getElementById('modalBatal')?.classList.remove('active');
}
document.getElementById('modalBatal')?.addEventListener('click', function(e) {
    if (e.target === this) closeModalBatal();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModalBuyBack();
        closeModalBatal();
    }
});

@if($errors->has('catatan_pembatalan'))
    document.addEventListener('DOMContentLoaded', () => openModalBatal());
@endif
</script>
@endpush