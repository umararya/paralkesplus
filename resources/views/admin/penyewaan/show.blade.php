{{-- resources/views/admin/penyewaan/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Detail Penyewaan — ' . $penyewaan->nama_penyewa)
@section('breadcrumb', 'Detail Penyewaan')

@push('styles')
<style>
    /* ── PAGE LAYOUT ── */
    .detail-page { display:flex; flex-direction:column; gap:20px; }
    .page-header-bar {
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:12px; margin-bottom:4px;
    }
    .page-back-btn {
        display:inline-flex; align-items:center; gap:6px;
        height:36px; padding:0 14px; border-radius:8px;
        font-size:13px; font-weight:500; color:var(--text-secondary);
        border:1px solid var(--border); background:var(--bg-card);
        text-decoration:none; transition:all 0.2s;
    }
    .page-back-btn:hover { background:var(--bg-hover); color:var(--text-primary); }
    .page-title-row { display:flex; align-items:center; gap:10px; }
    .page-title-row h1 { font-size:20px; font-weight:700; color:var(--text-primary); }
    .page-title-row i  { font-size:22px; color:var(--brand-500); }
    .page-action-row   { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }

    /* ── CARD ── */
    .detail-card {
        background:var(--bg-card); border:1px solid var(--border);
        border-radius:14px; box-shadow:var(--shadow); overflow:hidden;
    }
    .detail-card-header {
        padding:14px 20px; border-bottom:1px solid var(--border);
        display:flex; align-items:center; justify-content:space-between;
        background:var(--bg-primary);
    }
    .detail-card-title {
        font-size:13px; font-weight:700; text-transform:uppercase;
        letter-spacing:0.7px; color:var(--text-muted);
        display:flex; align-items:center; gap:7px;
    }
    .detail-card-title i { font-size:16px; color:var(--brand-500); }
    .detail-card-body { padding:18px 20px; }

    /* ── INFO GRID ── */
    .info-grid {
        display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr));
        gap:14px;
    }
    .info-item {}
    .info-label {
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:0.6px; color:var(--text-muted); margin-bottom:4px;
    }
    .info-value {
        font-size:13.5px; font-weight:600; color:var(--text-primary);
        line-height:1.5;
    }
    .info-value.muted { font-weight:400; color:var(--text-secondary); }

    /* ── STATUS BADGE ── */
    .status-badge {
        display:inline-flex; align-items:center; gap:4px;
        padding:4px 12px; border-radius:99px; font-size:12.5px; font-weight:700;
    }
    .status-berjalan   { background:#F0FDF4; color:#16A34A; }
    .status-konfirmasi { background:#FFFBEB; color:#B45309; }
    .status-selesai    { background:#F0F9FF; color:#0369A1; }
    html.dark .status-berjalan   { background:rgba(22,163,74,0.12); color:#4ADE80; }
    html.dark .status-konfirmasi { background:rgba(180,83,9,0.12); color:#FCD34D; }
    html.dark .status-selesai    { background:rgba(3,105,161,0.12); color:#38BDF8; }

    /* ── DETAIL TABLE (items alkes) ── */
    .items-table { width:100%; border-collapse:collapse; }
    .items-table thead tr { background:var(--bg-primary); border-bottom:2px solid var(--border); }
    .items-table th {
        padding:9px 14px; font-size:10.5px; font-weight:700;
        text-transform:uppercase; letter-spacing:0.6px; color:var(--text-muted);
        white-space:nowrap; text-align:left;
    }
    .items-table th.right, .items-table td.right { text-align:right; }
    .items-table th.center, .items-table td.center { text-align:center; }
    .items-table td {
        padding:10px 14px; font-size:13px; color:var(--text-primary);
        border-bottom:1px solid var(--border); vertical-align:middle;
    }
    .items-table tbody tr:last-child td { border-bottom:none; }
    .items-table tbody tr:hover td { background:var(--bg-hover); }
    .items-table tfoot td {
        padding:10px 14px; font-size:13px; font-weight:700;
        border-top:2px solid var(--border);
    }
    .kondisi-badge-baru  { background:#F0FDF4; color:#16A34A; border-radius:5px; padding:2px 8px; font-size:11.5px; font-weight:600; }
    .kondisi-badge-bekas { background:#FFF7ED; color:#C2410C; border-radius:5px; padding:2px 8px; font-size:11.5px; font-weight:600; }

    /* ── FILE PREVIEW ── */
    .file-thumb-wrap { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .file-thumb-btn {
        display:inline-flex; align-items:center; gap:5px;
        height:34px; padding:0 12px; border-radius:7px;
        font-size:12.5px; font-weight:500;
        background:var(--bg-hover); color:var(--text-secondary);
        border:1px solid var(--border); cursor:pointer;
        text-decoration:none; transition:all 0.2s; font-family:var(--font);
    }
    .file-thumb-btn:hover { background:var(--brand-50); color:var(--brand-500); border-color:var(--brand-200); }
    .file-thumb-btn i { font-size:14px; }

    /* ── RINGKASAN BIAYA ── */
    .biaya-summary {
        display:flex; flex-direction:column; gap:0;
        border:1px solid var(--border); border-radius:10px; overflow:hidden;
        max-width:340px; margin-left:auto;
    }
    .biaya-row {
        display:flex; justify-content:space-between; align-items:center;
        padding:9px 14px; font-size:13px; border-bottom:1px solid var(--border);
    }
    .biaya-row:last-child { border-bottom:none; }
    .biaya-row .bl { color:var(--text-muted); }
    .biaya-row .bv { font-weight:600; color:var(--text-primary); }
    .biaya-row.total { background:var(--brand-500); }
    .biaya-row.total .bl,
    .biaya-row.total .bv { color:#fff; font-weight:700; font-size:13.5px; }

    /* ══════════════════════════════════════
       RIWAYAT EXTEND TABLE
    ══════════════════════════════════════ */
    .extend-table { width:100%; border-collapse:collapse; font-size:13px; }
    .extend-table thead tr { background:var(--bg-primary); border-bottom:2px solid var(--border); }
    .extend-table th {
        padding:9px 14px; font-size:10.5px; font-weight:700;
        text-transform:uppercase; letter-spacing:0.6px; color:var(--text-muted);
        white-space:nowrap; text-align:left;
    }
    .extend-table th.center, .extend-table td.center { text-align:center; }
    .extend-table td {
        padding:10px 14px; color:var(--text-primary);
        border-bottom:1px solid var(--border); vertical-align:middle;
    }
    .extend-table tbody tr:last-child td { border-bottom:none; }
    .extend-table tbody tr:hover td { background:var(--bg-hover); }

    .extend-no-badge {
        display:inline-flex; align-items:center; gap:4px;
        background:var(--brand-50); color:var(--brand-500);
        border:1px solid var(--brand-100); border-radius:6px;
        padding:2px 9px; font-size:11.5px; font-weight:700;
    }
    html.dark .extend-no-badge {
        background:rgba(29,111,164,0.12); color:#60A5FA;
        border-color:rgba(29,111,164,0.25);
    }
    .extend-tgl-lama { color:var(--text-muted); text-decoration:line-through; font-size:12px; }
    .extend-tgl-baru { font-weight:700; color:#F59E0B; }
    .extend-tambah-hari {
        display:inline-flex; align-items:center;
        background:#FEF3C7; color:#92400E;
        border-radius:6px; padding:2px 8px;
        font-size:12px; font-weight:700;
    }
    html.dark .extend-tambah-hari { background:rgba(146,64,14,0.18); color:#FCD34D; }
    .extend-harga { font-weight:700; color:var(--text-primary); }
    .extend-cetak-wrap { display:flex; gap:5px; justify-content:center; }
    .btn-cetak-sm {
        display:inline-flex; align-items:center; gap:4px;
        height:30px; padding:0 10px; border-radius:6px;
        font-size:11.5px; font-weight:600; text-decoration:none;
        transition:all 0.2s; white-space:nowrap;
    }
    .btn-cetak-sm i { font-size:13px; }
    .btn-inv  { background:#F0FDF4; color:#16A34A; border:1px solid #BBF7D0; }
    .btn-inv:hover  { background:#DCFCE7; }
    .btn-perj { background:#F5F3FF; color:#7C3AED; border:1px solid #DDD6FE; }
    .btn-perj:hover { background:#EDE9FE; }
    html.dark .btn-inv  { background:rgba(22,163,74,0.12); color:#4ADE80; border-color:rgba(22,163,74,0.25); }
    html.dark .btn-perj { background:rgba(124,58,237,0.12); color:#A78BFA; border-color:rgba(124,58,237,0.25); }

    .extend-empty {
        text-align:center; padding:36px 24px; color:var(--text-muted);
    }
    .extend-empty i { font-size:36px; display:block; margin-bottom:8px; color:var(--border); }
    .extend-empty p { font-size:13px; }

    /* ── MODAL BASE (Preview File) ── */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from{opacity:0}to{opacity:1} }
    .modal { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.2); width:100%; animation:slideUp 0.2s ease; }
    .modal-md { max-width:600px; }
    @keyframes slideUp { from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)} }
    .modal-header { padding:16px 20px 12px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .modal-title { font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:7px; }
    .modal-close { width:28px; height:28px; border:none; background:none; cursor:pointer; color:var(--text-muted); font-size:19px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:all 0.2s; }
    .modal-close:hover { background:var(--bg-hover); color:var(--text-primary); }
    .modal-body { padding:16px; }
    .file-preview-wrap { display:flex; flex-direction:column; align-items:center; gap:12px; }
    .file-preview-img  { max-width:100%; max-height:70vh; border-radius:8px; border:1px solid var(--border); object-fit:contain; }
    .file-preview-pdf  { width:100%; height:70vh; border:1px solid var(--border); border-radius:8px; }
    .file-preview-info { font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:5px; }

    /* Btn util */
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 14px; height:36px; border-radius:8px; font-size:13px; font-weight:500; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-ghost   { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover   { background:var(--bg-hover); color:var(--text-primary); }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); }
    .btn-warning { background:#F59E0B; color:#fff; border:1px solid #F59E0B; }
    .btn-warning:hover { background:#D97706; }
    .btn-success { background:#16A34A; color:#fff; border:1px solid #16A34A; }
    .btn-success:hover { background:#15803D; }
    .btn-purple  { background:#7C3AED; color:#fff; border:1px solid #7C3AED; }
    .btn-purple:hover  { background:#6D28D9; }
</style>
@endpush

@section('content')

<div class="detail-page">

    {{-- ══ HEADER ══ --}}
    <div class="page-header-bar">
        <div>
            <a href="{{ route('penyewaan.index') }}" class="page-back-btn" style="margin-bottom:8px;">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <div class="page-title-row">
                <i class="ri-store-2-line"></i>
                <h1>Detail Penyewaan</h1>
                <span class="status-badge {{ $penyewaan->status_class }}">
                    {{ $penyewaan->status_label }}
                </span>
            </div>
        </div>
        <div class="page-action-row">
            <a href="{{ route('penyewaan.invoice', $penyewaan->id) }}" target="_blank"
               class="btn btn-success">
                <i class="ri-receipt-line"></i> Invoice
            </a>
            <a href="{{ route('penyewaan.perjanjian', $penyewaan->id) }}" target="_blank"
               class="btn btn-purple">
                <i class="ri-file-text-line"></i> Perjanjian
            </a>
            <a href="{{ route('penyewaan.edit', $penyewaan->id) }}"
               class="btn btn-primary">
                <i class="ri-edit-line"></i> Edit
            </a>
        </div>
    </div>

    {{-- ══ CARD: DATA PENYEWA ══ --}}
    <div class="detail-card">
        <div class="detail-card-header">
            <span class="detail-card-title">
                <i class="ri-user-line"></i> Data Penyewa
            </span>
        </div>
        <div class="detail-card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nama Lengkap</div>
                    <div class="info-value">{{ $penyewaan->nama_penyewa }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">No. Telepon</div>
                    <div class="info-value">
                        <a href="tel:{{ $penyewaan->nomor_telepon }}"
                           style="color:var(--brand-500); text-decoration:none;">
                            {{ $penyewaan->nomor_telepon }}
                        </a>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tempat, Tanggal Lahir</div>
                    <div class="info-value {{ $penyewaan->tempat_tanggal_lahir ? '' : 'muted' }}">
                        {{ $penyewaan->tempat_tanggal_lahir ?: '—' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nomor KTP</div>
                    <div class="info-value {{ $penyewaan->nomor_ktp ? '' : 'muted' }}">
                        {{ $penyewaan->nomor_ktp ?: '—' }}
                    </div>
                </div>
                <div class="info-item" style="grid-column: span 2;">
                    <div class="info-label">Alamat</div>
                    <div class="info-value">{{ $penyewaan->alamat_penyewa }}</div>
                </div>
            </div>

            {{-- Foto KTP/SIM --}}
            @if($penyewaan->foto_ktp_sim)
            <div style="margin-top:14px;">
                <div class="info-label" style="margin-bottom:6px;">Foto KTP / SIM</div>
                @php $ktpExt = strtolower(pathinfo($penyewaan->foto_ktp_sim, PATHINFO_EXTENSION)); @endphp
                <div class="file-thumb-wrap">
                    <button type="button" class="file-thumb-btn"
                            onclick="previewFile(
                                '{{ asset('storage/' . $penyewaan->foto_ktp_sim) }}',
                                '{{ $ktpExt === 'pdf' ? 'pdf' : 'image' }}',
                                'Foto KTP/SIM — {{ addslashes($penyewaan->nama_penyewa) }}'
                            )">
                        <i class="{{ $ktpExt === 'pdf' ? 'ri-file-pdf-line' : 'ri-id-card-line' }}"
                           style="{{ $ktpExt === 'pdf' ? 'color:#EF4444;' : 'color:var(--brand-500);' }}"></i>
                        Lihat Foto KTP/SIM
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ CARD: DETAIL SEWA ══ --}}
    <div class="detail-card">
        <div class="detail-card-header">
            <span class="detail-card-title">
                <i class="ri-calendar-2-line"></i> Detail Penyewaan
            </span>
        </div>
        <div class="detail-card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Tanggal Mulai</div>
                    <div class="info-value">
                        {{ $penyewaan->tgl_mulai?->translatedFormat('d F Y') ?? '—' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tanggal Selesai (Deadline)</div>
                    <div class="info-value" style="color:#F59E0B; font-weight:700;">
                        {{ $penyewaan->tgl_selesai?->translatedFormat('d F Y') ?? '—' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Durasi</div>
                    <div class="info-value">{{ $penyewaan->durasi_hari }} hari</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Sisa Hari</div>
                    <div class="info-value">
                        @php $sisaHari = $penyewaan->sisa_hari; @endphp
                        @if($sisaHari <= 0)
                            <span style="color:#DC2626; font-weight:700;">Lewat deadline</span>
                        @elseif($sisaHari <= 7)
                            <span style="color:#B45309; font-weight:700;">{{ $sisaHari }} hari lagi</span>
                        @else
                            <span style="color:#16A34A; font-weight:700;">{{ $sisaHari }} hari lagi</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Pengiriman</div>
                    <div class="info-value">{{ $penyewaan->pengiriman_label }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Biaya Ongkir</div>
                    <div class="info-value">{{ $penyewaan->biaya_ongkir_formatted }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Metode Pembayaran</div>
                    <div class="info-value">{{ $penyewaan->metode_pembayaran }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="status-badge {{ $penyewaan->status_class }}">
                            {{ $penyewaan->status_label }}
                        </span>
                    </div>
                </div>
                @if($penyewaan->keterangan)
                <div class="info-item" style="grid-column: span 2;">
                    <div class="info-label">Keterangan</div>
                    <div class="info-value muted">{{ $penyewaan->keterangan }}</div>
                </div>
                @endif
            </div>

            {{-- Bukti Pembayaran --}}
            @if($penyewaan->bukti_pembayaran)
            <div style="margin-top:14px;">
                <div class="info-label" style="margin-bottom:6px;">Bukti Pembayaran</div>
                @php $bpExt = strtolower(pathinfo($penyewaan->bukti_pembayaran, PATHINFO_EXTENSION)); @endphp
                <button type="button" class="file-thumb-btn"
                        onclick="previewFile(
                            '{{ asset('storage/' . $penyewaan->bukti_pembayaran) }}',
                            '{{ $bpExt === 'pdf' ? 'pdf' : 'image' }}',
                            'Bukti Pembayaran — {{ addslashes($penyewaan->nama_penyewa) }}'
                        )">
                    <i class="{{ $bpExt === 'pdf' ? 'ri-file-pdf-line' : 'ri-image-line' }}"
                       style="{{ $bpExt === 'pdf' ? 'color:#EF4444;' : 'color:#16A34A;' }}"></i>
                    Lihat Bukti Pembayaran
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ CARD: DETAIL ALAT KESEHATAN ══ --}}
    <div class="detail-card">
        <div class="detail-card-header">
            <span class="detail-card-title">
                <i class="ri-medicine-bottle-line"></i> Alat Kesehatan Disewa
            </span>
        </div>
        <div style="overflow-x:auto;">
            @if($penyewaan->details->isNotEmpty())
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width:36px;" class="center">#</th>
                        <th>Nama Alat</th>
                        <th class="center">Kondisi</th>
                        <th class="center">Qty</th>
                        <th class="center">Satuan</th>
                        <th class="right">Harga Satuan</th>
                        <th class="center">Diskon</th>
                        <th class="right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penyewaan->details as $i => $detail)
                    <tr>
                        <td class="center" style="color:var(--text-muted); font-size:12px;">{{ $i + 1 }}</td>
                        <td style="font-weight:600;">{{ $detail->nama_alat }}</td>
                        <td class="center">
                            <span class="kondisi-badge-{{ $detail->kondisi ?? 'baru' }}">
                                {{ ucfirst($detail->kondisi ?? 'baru') }}
                            </span>
                        </td>
                        <td class="center">{{ $detail->qty }}</td>
                        <td class="center" style="color:var(--text-muted);">{{ $detail->satuan }}</td>
                        <td class="right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="center">
                            @if($detail->diskon > 0)
                                <span style="color:#DC2626; font-weight:600;">{{ $detail->diskon }}%</span>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td class="right" style="font-weight:700;">
                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="right" style="font-weight:600; color:var(--text-muted);">
                            Subtotal Alat
                        </td>
                        <td class="right" style="font-weight:700;">
                            Rp {{ number_format($penyewaan->total_harga_sewa, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            {{-- Ringkasan Biaya --}}
            <div style="padding:14px 20px;">
                <div class="biaya-summary">
                    <div class="biaya-row">
                        <span class="bl">Subtotal Alat</span>
                        <span class="bv">Rp {{ number_format($penyewaan->total_harga_sewa, 0, ',', '.') }}</span>
                    </div>
                    @if($penyewaan->diskon_global > 0)
                    <div class="biaya-row">
                        <span class="bl">Diskon</span>
                        <span class="bv" style="color:#DC2626;">
                            − Rp {{ number_format($penyewaan->diskon_global, 0, ',', '.') }}
                        </span>
                    </div>
                    @endif
                    @if($penyewaan->biaya_ongkir > 0)
                    <div class="biaya-row">
                        <span class="bl">Ongkir</span>
                        <span class="bv">+ Rp {{ number_format($penyewaan->biaya_ongkir, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="biaya-row total">
                        <span class="bl">Total Tagihan</span>
                        <span class="bv">Rp {{ number_format($penyewaan->total_tagihan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @else
            {{-- Legacy --}}
            <div style="padding:20px;">
                <div class="info-label" style="margin-bottom:4px;">Produk (Legacy)</div>
                <div class="info-value">{{ $penyewaan->produk_alkes ?? '—' }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ CARD: RIWAYAT EXTEND ══ --}}
    <div class="detail-card">
        <div class="detail-card-header">
            <span class="detail-card-title">
                <i class="ri-calendar-2-line" style="color:#F59E0B;"></i> Riwayat Perpanjangan (Extend)
            </span>
            <span style="font-size:12px; color:var(--text-muted);">
                {{ $penyewaan->extends->count() }} kali extend
            </span>
        </div>

        @if($penyewaan->extends->isNotEmpty())
        <div style="overflow-x:auto;">
            <table class="extend-table">
                <thead>
                    <tr>
                        <th class="center" style="width:36px;">#</th>
                        <th>No. Extend</th>
                        <th>Tgl Extend</th>
                        <th>Deadline Lama</th>
                        <th>Deadline Baru</th>
                        <th class="center">+Hari</th>
                        <th>Harga Extend</th>
                        <th>Metode Bayar</th>
                        <th>Bukti</th>
                        <th>Catatan</th>
                        <th class="center">Cetak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penyewaan->extends as $i => $ext)
                    <tr>
                        <td class="center" style="color:var(--text-muted); font-size:12px;">
                            {{ $i + 1 }}
                        </td>
                        <td>
                            <span class="extend-no-badge">{{ $ext->nomor_extend }}</span>
                        </td>
                        <td style="white-space:nowrap; font-size:12.5px;">
                            {{ $ext->created_at->translatedFormat('d M Y') }}
                        </td>
                        <td>
                            <span class="extend-tgl-lama">
                                {{ $ext->tgl_selesai_lama->translatedFormat('d M Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="extend-tgl-baru">
                                {{ $ext->tgl_selesai_baru->translatedFormat('d M Y') }}
                            </span>
                        </td>
                        <td class="center">
                            <span class="extend-tambah-hari">+{{ $ext->tambah_hari }} hari</span>
                        </td>
                        <td class="extend-harga" style="white-space:nowrap;">
                            Rp {{ number_format($ext->harga_extend, 0, ',', '.') }}
                        </td>
                        <td style="white-space:nowrap;">{{ $ext->metode_bayar }}</td>
                        <td>
                            @if($ext->bukti_transfer)
                                @php $extFile = strtolower(pathinfo($ext->bukti_transfer, PATHINFO_EXTENSION)); @endphp
                                <button type="button" class="file-thumb-btn" style="height:28px; padding:0 10px; font-size:12px;"
                                        onclick="previewFile(
                                            '{{ asset('storage/' . $ext->bukti_transfer) }}',
                                            '{{ $extFile === 'pdf' ? 'pdf' : 'image' }}',
                                            'Bukti Transfer Extend #{{ $i + 1 }}'
                                        )">
                                    <i class="{{ $extFile === 'pdf' ? 'ri-file-pdf-line' : 'ri-image-line' }}"
                                       style="{{ $extFile === 'pdf' ? 'color:#EF4444;' : 'color:#16A34A;' }}"></i>
                                    Lihat
                                </button>
                            @else
                                <span style="color:var(--text-muted); font-size:12px; font-style:italic;">—</span>
                            @endif
                        </td>
                        <td style="max-width:160px; font-size:12px; color:var(--text-muted);
                                   white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                            title="{{ $ext->catatan }}">
                            {{ $ext->catatan ?: '—' }}
                        </td>
                        <td class="center">
                            <div class="extend-cetak-wrap">
                                <a href="{{ route('penyewaan.invoiceExtend', $ext->id) }}"
                        target="_blank" class="btn-cetak-sm btn-inv">
                            <i class="ri-receipt-line"></i> Inv
                        </a>
                        <a href="{{ route('penyewaan.perjanjianExtend', $ext->id) }}"
                        target="_blank" class="btn-cetak-sm btn-perj">
                            <i class="ri-file-text-line"></i> Perj
                        </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="extend-empty">
            <i class="ri-calendar-2-line"></i>
            <p>Belum ada riwayat perpanjangan untuk penyewaan ini.</p>
        </div>
        @endif
    </div>

</div>{{-- /detail-page --}}

{{-- ════ MODAL PREVIEW FILE ════ --}}
<div class="modal-overlay" id="modalPreviewFile">
    <div class="modal modal-md">
        <div class="modal-header">
            <span class="modal-title" id="previewModalTitle">
                <i class="ri-file-line"></i> Preview File
            </span>
            <div style="display:flex;align-items:center;gap:8px;">
                <a id="previewDownloadBtn" href="#" target="_blank" download
                   class="btn btn-ghost" style="height:30px;padding:0 12px;font-size:12px;">
                    <i class="ri-download-2-line"></i> Buka di Tab Baru
                </a>
                <button class="modal-close" onclick="closePreviewFile()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
        <div class="modal-body">
            <div class="file-preview-wrap" id="previewContent"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function previewFile(url, type, title) {
    document.getElementById('previewModalTitle').innerHTML =
        `<i class="ri-${type === 'pdf' ? 'file-pdf-line' : 'image-line'}"
             style="${type === 'pdf' ? 'color:#EF4444;' : 'color:var(--brand-500);'}"></i> ${title}`;

    document.getElementById('previewDownloadBtn').href = url;

    const content = document.getElementById('previewContent');
    if (type === 'pdf') {
        content.innerHTML = `
            <iframe src="${url}" class="file-preview-pdf" title="Preview PDF"></iframe>
            <p class="file-preview-info">
                <i class="ri-information-line"></i>
                Jika PDF tidak tampil, klik tombol <strong>Buka di Tab Baru</strong>.
            </p>`;
    } else {
        content.innerHTML = `
            <img src="${url}" class="file-preview-img" alt="Preview"
                 onerror="this.outerHTML='<div style=\\'text-align:center;padding:32px;color:var(--text-muted);\\'><i class=\\'ri-image-2-line\\' style=\\'font-size:40px;display:block;margin-bottom:8px;\\'></i>Gambar tidak dapat dimuat.</div>'">
            <p class="file-preview-info">
                <i class="ri-information-line"></i>
                Klik <strong>Buka di Tab Baru</strong> untuk zoom atau download.
            </p>`;
    }
    document.getElementById('modalPreviewFile').classList.add('open');
}

function closePreviewFile() {
    document.getElementById('modalPreviewFile').classList.remove('open');
    setTimeout(() => { document.getElementById('previewContent').innerHTML = ''; }, 200);
}

document.getElementById('modalPreviewFile').addEventListener('click', function(e) {
    if (e.target === this) closePreviewFile();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreviewFile();
});
</script>
@endpush