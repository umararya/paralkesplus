{{-- resources/views/admin/penjualan/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Penjualan')
@section('breadcrumb', 'Tambah Penjualan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
    .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; margin-bottom:20px; }
    .form-card-header { padding:16px 22px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; background:var(--bg-primary); }
    .form-card-title { font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .form-card-title i { color:var(--brand-500); font-size:16px; }
    .form-card-body { padding:22px; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .form-group { display:flex; flex-direction:column; gap:5px; }
    .form-group.full { grid-column:1/-1; }
    .form-label { font-size:12.5px; font-weight:600; color:var(--text-secondary); }
    .form-label .req { color:#EF4444; margin-left:2px; }
    .form-control {
        width:100%; padding:10px 14px; border:1px solid var(--border);
        border-radius:8px; font-size:13.5px; background:var(--bg-primary);
        color:var(--text-primary); outline:none;
        transition:border-color 0.2s,box-shadow 0.2s;
        font-family:var(--font); box-sizing:border-box;
    }
    .form-control:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .form-control.is-invalid { border-color:#EF4444; }
    textarea.form-control { resize:vertical; min-height:80px; }
    select.form-control {
        appearance:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 12px center; padding-right:36px;
    }
    .invalid-feedback { font-size:12px; color:#EF4444; display:flex; align-items:center; gap:4px; }
    .alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:500; margin-bottom:18px; border:1px solid transparent; }
    .alert-error { background:#FFF1F2; color:#BE123C; border-color:#FECDD3; }
    html.dark .alert-error { background:rgba(190,18,60,0.12); color:#FB7185; border-color:rgba(190,18,60,0.25); }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 18px; height:40px; border-radius:8px; font-size:13px; font-weight:600; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); }
    .btn-ghost { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover { background:var(--bg-hover); color:var(--text-primary); }

    /* ── Items Table ── */
    .items-table { width:100%; border-collapse:collapse; margin-bottom:10px; }
    .items-table thead tr { background:var(--brand-500); color:#fff; }
    .items-table th { padding:9px 10px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; white-space:nowrap; }
    .items-table td { padding:7px 6px; border-bottom:1px solid var(--border); vertical-align:middle; }
    .items-table tbody tr:nth-child(even) td { background:var(--bg-hover); }
    .items-table .form-control { padding:7px 10px; font-size:13px; }

    .btn-remove-row { width:28px; height:28px; border-radius:6px; background:rgba(239,68,68,0.1); color:#EF4444; border:none; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:14px; transition:background 0.2s; }
    .btn-remove-row:hover { background:rgba(239,68,68,0.2); }
    .btn-add-row { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; border:1.5px dashed var(--brand-500); background:transparent; color:var(--brand-500); font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; font-family:var(--font); }
    .btn-add-row:hover { background:var(--brand-50); }
    html.dark .btn-add-row:hover { background:rgba(29,111,164,0.1); }

    /* ── Total Preview ── */
    .total-preview { background:var(--bg-primary); border:1px solid var(--border); border-radius:10px; padding:14px 18px; }
    .total-preview-row { display:flex; justify-content:space-between; align-items:center; font-size:13px; color:var(--text-secondary); padding:3px 0; }
    .total-preview-row.grand { font-size:15px; font-weight:800; color:var(--text-primary); border-top:1px solid var(--border); margin-top:6px; padding-top:10px; }
    .total-preview-row span:last-child { font-weight:600; color:var(--text-primary); }
    .total-preview-row.grand span:last-child { color:var(--brand-500); font-size:16px; }

    /* ── Stok Badge ── */
    .stok-baru-badge  { background:#DBEAFE; color:#1D4ED8; padding:1px 7px; border-radius:99px; font-size:11px; font-weight:600; }
    .stok-bekas-badge { background:#F3E8FF; color:#7C3AED; padding:1px 7px; border-radius:99px; font-size:11px; font-weight:600; }
    .stok-info-row { display:flex; align-items:center; gap:6px; margin-top:5px; min-height:20px; flex-wrap:wrap; }
    .stok-info-row span { font-size:12px; }

    /* ── Input prefix Rp ── */
    .input-prefix-wrap { position:relative; }
    .input-prefix { position:absolute; left:11px; top:50%; transform:translateY(-50%); font-size:13px; color:var(--text-muted); pointer-events:none; font-weight:500; }
    .input-prefix-wrap .form-control { padding-left:36px; }

    /* ── Pembayaran Awal Section ── */
    .pay-divider { display:flex; align-items:center; gap:10px; margin:6px 0 18px; color:var(--text-muted); font-size:11.5px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; }
    .pay-divider::before,.pay-divider::after { content:''; flex:1; height:1px; background:var(--border); }
    .pay-info-box { display:flex; align-items:flex-start; gap:8px; padding:10px 14px; border-radius:8px; font-size:12.5px; font-weight:500; background:#FFFBEB; color:#92400E; border:1px solid #FDE68A; margin-top:6px; }
    .pay-info-box i { font-size:15px; flex-shrink:0; margin-top:1px; }
    html.dark .pay-info-box { background:rgba(146,64,14,0.15); color:#FCD34D; border-color:rgba(146,64,14,0.3); }

    /* ── Select2 Overrides ── */
    .select2-container { width:100% !important; }
    .select2-container--default .select2-selection--single { height:36px; border:1px solid var(--border); border-radius:7px; background:var(--bg-primary); display:flex; align-items:center; padding:0 10px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color:var(--text-primary); font-size:13px; line-height:1; padding:0; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; right:8px; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open  .select2-selection--single { border-color:var(--brand-500); outline:none; box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .select2-dropdown { border:1px solid var(--border); border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,0.1); background:var(--bg-card); z-index:9999; }
    .select2-container--default .select2-results__option { font-size:13px; color:var(--text-primary); padding:8px 12px; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background:var(--brand-500); color:#fff; }
    .select2-search--dropdown .select2-search__field { border:1px solid var(--border); border-radius:6px; padding:6px 10px; font-size:13px; background:var(--bg-primary); color:var(--text-primary); width:100%; box-sizing:border-box; }
</style>
@endpush

@section('content')

@if($errors->any())
<div class="alert alert-error">
    <i class="ri-error-warning-fill" style="font-size:17px;flex-shrink:0;margin-top:1px;"></i>
    <div>
        <strong>Terjadi kesalahan:</strong>
        <ul style="margin:4px 0 0 16px;padding:0;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST" action="{{ route('penjualan.store') }}"
      enctype="multipart/form-data" id="formPenjualan">
@csrf

{{-- ════ CARD 1: INFO PELANGGAN ════ --}}
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-title"><i class="ri-user-line"></i> Informasi Pelanggan & Transaksi</div>
    </div>
    <div class="form-card-body">
        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Nama Pelanggan <span class="req">*</span></label>
                <input type="text" name="nama_pelanggan"
                       class="form-control {{ $errors->has('nama_pelanggan') ? 'is-invalid' : '' }}"
                       value="{{ old('nama_pelanggan') }}"
                       placeholder="Nama lengkap pelanggan" required autofocus>
                @error('nama_pelanggan')<div class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="nomor_telepon" class="form-control"
                       value="{{ old('nomor_telepon') }}" placeholder="08xx-xxxx-xxxx">
            </div>

            <div class="form-group full">
                <label class="form-label">Alamat Pelanggan <span class="req">*</span></label>
                <textarea name="alamat_pelanggan"
                          class="form-control {{ $errors->has('alamat_pelanggan') ? 'is-invalid' : '' }}"
                          placeholder="Alamat lengkap pelanggan" required>{{ old('alamat_pelanggan') }}</textarea>
                @error('alamat_pelanggan')<div class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Penjualan <span class="req">*</span></label>
                <input type="date" name="tanggal_penjualan"
                       class="form-control {{ $errors->has('tanggal_penjualan') ? 'is-invalid' : '' }}"
                       value="{{ old('tanggal_penjualan', date('Y-m-d')) }}" required>
                @error('tanggal_penjualan')<div class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Foto Bukti Transaksi</label>
                <input type="file" name="foto_bukti" class="form-control" accept="image/*">
            </div>

            <div class="form-group full">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control"
                          placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
            </div>

        </div>
    </div>
</div>

{{-- ════ CARD 2: DETAIL BARANG ════ --}}
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-title"><i class="ri-shopping-bag-line"></i> Detail Barang</div>
    </div>
    <div class="form-card-body">
        <div style="overflow-x:auto;">
            <table class="items-table" id="items-table">
                <thead>
                    <tr>
                        <th style="width:32px;">#</th>
                        <th style="min-width:240px;">Nama Barang <span style="color:#fde68a">*</span></th>
                        <th style="width:105px;">Kondisi <span style="color:#fde68a">*</span></th>
                        <th style="width:64px;">Qty <span style="color:#fde68a">*</span></th>
                        <th style="width:85px;">Satuan</th>
                        <th style="width:140px;">Harga Satuan <span style="color:#fde68a">*</span></th>
                        <th style="width:72px;">Diskon %</th>
                        <th style="width:130px;text-align:right;">Subtotal</th>
                        <th style="width:34px;"></th>
                    </tr>
                </thead>
                <tbody id="items-body"></tbody>
            </table>
        </div>
        <button type="button" class="btn-add-row" onclick="addRow()">
            <i class="ri-add-line"></i> Tambah Barang
        </button>

        {{-- ── Total Preview ── --}}
        <div style="display:flex;justify-content:flex-end;margin-top:20px;">
            <div class="total-preview" style="min-width:280px;">
                <div class="total-preview-row">
                    <span>Subtotal</span>
                    <span id="preview-subtotal">Rp 0</span>
                </div>
                <div class="total-preview-row" id="row-diskon-global" style="color:#DC2626;">
                    <span>Diskon Global</span>
                    <span id="preview-diskon">- Rp 0</span>
                </div>
                <div class="total-preview-row grand">
                    <span>Total Tagihan</span>
                    <span id="preview-total">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- Diskon Global Input --}}
        <div style="display:flex;justify-content:flex-end;margin-top:12px;align-items:center;gap:10px;">
            <label class="form-label" style="margin:0;white-space:nowrap;">Diskon Global (Rp)</label>
            <div class="input-prefix-wrap" style="width:200px;">
                <span class="input-prefix">Rp</span>
                <input type="number" name="diskon_global" id="diskon_global"
                       class="form-control" value="{{ old('diskon_global', 0) }}"
                       min="0" placeholder="0"
                       oninput="recalcTotal()">
            </div>
        </div>

    </div>
</div>

{{-- ════ CARD 3: PEMBAYARAN AWAL ════ --}}
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-title">
            <i class="ri-money-dollar-circle-line"></i> Pembayaran Awal
        </div>
    </div>
    <div class="form-card-body">
        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Metode Pembayaran <span class="req">*</span></label>
                <select name="metode_pembayaran" id="metode_pembayaran"
                        class="form-control {{ $errors->has('metode_pembayaran') ? 'is-invalid' : '' }}"
                        onchange="onMetodePembayaranChange(this.value)" required>
                    <option value="cash"     {{ old('metode_pembayaran','cash') === 'cash'     ? 'selected' : '' }}>💵 Cash — Lunas Langsung</option>
                    <option value="dp"       {{ old('metode_pembayaran')        === 'dp'       ? 'selected' : '' }}>📋 DP — Down Payment</option>
                    <option value="transfer" {{ old('metode_pembayaran')        === 'transfer' ? 'selected' : '' }}>🏦 Transfer — Lunas Langsung</option>
                </select>
                @error('metode_pembayaran')<div class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Via <span class="req">*</span></label>
                <select name="metode_bayar_awal" id="metode_bayar_awal"
                        class="form-control {{ $errors->has('metode_bayar_awal') ? 'is-invalid' : '' }}" required>
                    <option value="cash"     {{ old('metode_bayar_awal','cash') === 'cash'     ? 'selected' : '' }}>Cash</option>
                    <option value="transfer" {{ old('metode_bayar_awal')        === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="qris"     {{ old('metode_bayar_awal')        === 'qris'     ? 'selected' : '' }}>QRIS</option>
                </select>
                @error('metode_bayar_awal')<div class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Jumlah Bayar Awal <span class="req">*</span></label>
                <div class="input-prefix-wrap">
                    <span class="input-prefix">Rp</span>
                    <input type="number" name="jumlah_bayar_awal" id="jumlah_bayar_awal"
                           class="form-control {{ $errors->has('jumlah_bayar_awal') ? 'is-invalid' : '' }}"
                           value="{{ old('jumlah_bayar_awal', 0) }}"
                           min="0" placeholder="0" required>
                </div>
                @error('jumlah_bayar_awal')<div class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Bayar <span class="req">*</span></label>
                <input type="date" name="tanggal_bayar_awal"
                       class="form-control {{ $errors->has('tanggal_bayar_awal') ? 'is-invalid' : '' }}"
                       value="{{ old('tanggal_bayar_awal', date('Y-m-d')) }}" required>
                @error('tanggal_bayar_awal')<div class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group full" id="dp-info-box" style="display:none;">
                <div class="pay-info-box">
                    <i class="ri-information-line"></i>
                    <span>Mode <strong>Down Payment</strong>: masukkan nominal DP yang dibayarkan sekarang. Sisa tagihan dapat dilunasi di halaman detail transaksi.</span>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── Action Buttons ── --}}
<div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-bottom:24px;">
    <a href="{{ route('penjualan.index') }}" class="btn btn-ghost">
        <i class="ri-arrow-left-line"></i> Batal
    </a>
    <button type="submit" class="btn btn-primary" id="btnSubmit">
        <i class="ri-save-line"></i> Simpan Penjualan
    </button>
</div>

</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let rowIndex = 0;

/* ════ SELECT2 TEMPLATES ════ */
function templateBarang(item) {
    if (!item.id) return item.text || 'Pilih atau cari nama barang...';
    return $(`
        <div style="padding:3px 0">
            <div style="font-size:13px;font-weight:600;color:var(--text-primary)">${item.text}</div>
            <div style="display:flex;gap:6px;margin-top:3px;align-items:center;flex-wrap:wrap;">
                <span style="font-size:11px;color:var(--text-muted)">${item.kategori || ''}</span>
                <span class="stok-baru-badge">Baru: ${item.stok_baru ?? 0}</span>
                <span class="stok-bekas-badge">Bekas: ${item.stok_bekas ?? 0}</span>
            </div>
        </div>
    `);
}
function templateBarangSelected(item) {
    if (!item.id) return item.text || 'Pilih barang...';
    return $(`<span>${item.text}
        &nbsp;<span class="stok-baru-badge">Baru: ${item.stok_baru ?? 0}</span>
        &nbsp;<span class="stok-bekas-badge">Bekas: ${item.stok_bekas ?? 0}</span>
    </span>`);
}

/* ════ KALKULASI TOTAL ════ */
function recalcTotal() {
    let subtotal = 0;
    document.querySelectorAll('#items-body tr').forEach(tr => {
        const idx      = tr.id.replace('row-', '');
        const harga    = parseFloat(document.getElementById('harga-'   + idx)?.value  || 0);
        const qty      = parseFloat(document.getElementById('qty-'     + idx)?.value  || 0);
        const diskon   = parseFloat(document.getElementById('diskon-'  + idx)?.value  || 0);
        const sub      = harga * qty * (1 - diskon / 100);
        subtotal      += sub;

        const subEl = document.getElementById('subtotal-display-' + idx);
        if (subEl) subEl.textContent = 'Rp ' + formatRp(sub);
    });

    const diskonGlobal = parseFloat(document.getElementById('diskon_global')?.value || 0);
    const total        = Math.max(0, subtotal - diskonGlobal);

    document.getElementById('preview-subtotal').textContent = 'Rp ' + formatRp(subtotal);
    document.getElementById('preview-diskon').textContent   = '- Rp ' + formatRp(diskonGlobal);
    document.getElementById('preview-total').textContent    = 'Rp ' + formatRp(total);

    // Sembunyikan baris diskon jika 0
    document.getElementById('row-diskon-global').style.display =
        diskonGlobal > 0 ? 'flex' : 'none';
}

function formatRp(num) {
    return Math.round(num).toLocaleString('id-ID');
}

/* ════ TAMBAH BARIS ════ */
function addRow(data = {}) {
    rowIndex++;
    const idx   = rowIndex;
    const tbody = document.getElementById('items-body');
    const tr    = document.createElement('tr');
    tr.id = `row-${idx}`;

    const satuanOpts = ['unit','pcs','set','buah','pasang']
        .map(s => `<option value="${s}" ${(data.satuan||'unit')===s?'selected':''}>${s}</option>`)
        .join('');

    const kondisiOpts = [
        `<option value="baru"  ${(data.kondisi||'baru')==='baru' ?'selected':''}>Baru</option>`,
        `<option value="bekas" ${(data.kondisi||'')==='bekas'?'selected':''}>Bekas</option>`,
    ].join('');

    const initOption = data.inventory_id
        ? `<option value="${data.inventory_id}" selected>${data.nama_barang || ''}</option>`
        : '';

    tr.innerHTML = `
        <td style="text-align:center;font-size:12px;color:var(--text-muted)">${idx}</td>

        <td style="min-width:240px">
            <input type="hidden" name="items[${idx}][inventory_id]" id="inv-id-${idx}"  value="${data.inventory_id || ''}">
            <input type="hidden" name="items[${idx}][nama_barang]"  id="nama-${idx}"    value="${data.nama_barang  || ''}">
            <select id="inv-select-${idx}" class="form-control" required>${initOption}</select>
            <div class="stok-info-row" id="stok-info-${idx}">
                ${data.inventory_id && data.stok_baru !== undefined
                    ? `<span class="stok-baru-badge">Baru: ${data.stok_baru ?? 0}</span>
                       <span class="stok-bekas-badge">Bekas: ${data.stok_bekas ?? 0}</span>`
                    : ''}
            </div>
        </td>

        <td>
            <select name="items[${idx}][kondisi]" id="kondisi-${idx}"
                    class="form-control" style="width:100px"
                    onchange="onKondisiChange(${idx})">${kondisiOpts}</select>
        </td>

        <td>
            <input type="number" name="items[${idx}][qty]" id="qty-${idx}"
                   value="${data.qty || 1}" min="1"
                   class="form-control" style="text-align:center;width:60px"
                   required oninput="recalcTotal()">
        </td>

        <td>
            <select name="items[${idx}][satuan]" class="form-control" style="width:78px">
                ${satuanOpts}
            </select>
        </td>

        <td>
            <div class="input-prefix-wrap" style="width:130px">
                <span class="input-prefix">Rp</span>
                <input type="number" name="items[${idx}][harga_satuan]" id="harga-${idx}"
                       value="${data.harga_satuan || 0}" min="0"
                       class="form-control" style="padding-left:34px"
                       oninput="recalcTotal()">
            </div>
        </td>

        <td>
            <div style="display:flex;align-items:center;gap:2px;width:66px">
                <input type="number" name="items[${idx}][diskon]" id="diskon-${idx}"
                       value="${data.diskon || 0}" min="0" max="100"
                       class="form-control" style="width:50px;text-align:center;padding:7px 6px"
                       oninput="recalcTotal()">
                <span style="font-size:12px;color:var(--text-muted)">%</span>
            </div>
        </td>

        <td style="text-align:right;white-space:nowrap">
            <span id="subtotal-display-${idx}" style="font-size:13px;font-weight:600;color:var(--text-primary)">
                Rp 0
            </span>
        </td>

        <td style="text-align:center">
            <button type="button" class="btn-remove-row" onclick="removeRow(${idx})">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);

    /* ── Init Select2 ── */
    $(`#inv-select-${idx}`).select2({
        dropdownParent: $(`#row-${idx}`),
        placeholder:    'Pilih atau cari nama barang...',
        allowClear:     true,
        minimumInputLength: 0,
        language: { inputTooShort: () => '' },
        ajax: {
            url:      '{{ route("api.inventory.index") }}',
            dataType: 'json',
            delay:    250,
            data:     params => ({ q: params.term ?? '', mode: 'jual' }),
            processResults: data => ({ results: data.results }),
            cache: true,
        },
        templateResult:    templateBarang,
        templateSelection: templateBarangSelected,
    })
    .on('select2:open', function () {
        const sf = document.querySelector('.select2-container--open .select2-search__field');
        if (sf) sf.dispatchEvent(new Event('input', { bubbles: true }));
    })
    .on('select2:select', function (e) {
        const item = e.params.data;

        $(`#inv-id-${idx}`).val(item.id);
        $(`#nama-${idx}`).val(item.text);

        $(`#stok-info-${idx}`).html(`
            <span class="stok-baru-badge">Baru: ${item.stok_baru ?? 0}</span>
            <span class="stok-bekas-badge">Bekas: ${item.stok_bekas ?? 0}</span>
            ${item.kategori ? `<span style="font-size:11px;color:var(--text-muted)">| ${item.kategori}</span>` : ''}
        `);

        const $sat = $(`select[name="items[${idx}][satuan]"]`);
        if (item.satuan) {
            $sat.find(`option[value="${item.satuan}"]`).length
                ? $sat.val(item.satuan)
                : $sat.append(new Option(item.satuan, item.satuan, true, true));
        }

        updateHargaField(idx, item);
        recalcTotal();
    })
    .on('select2:clear', function () {
        $(`#inv-id-${idx}`).val('');
        $(`#nama-${idx}`).val('');
        $(`#harga-${idx}`).val(0);
        $(`#stok-info-${idx}`).html('');
        recalcTotal();
    });

    recalcTotal();
}

/* ── Update field harga_satuan sesuai kondisi ── */
function updateHargaField(idx, item) {
    const kondisi = $(`#kondisi-${idx}`).val();
    const harga   = kondisi === 'bekas'
        ? (item?.harga_jual_bekas ?? 0)
        : (item?.harga_jual_baru  ?? 0);
    $(`#harga-${idx}`).val(harga);
}

function onKondisiChange(idx) {
    const data = $(`#inv-select-${idx}`).select2('data')?.[0];
    if (data?.id) { updateHargaField(idx, data); recalcTotal(); }
}

function removeRow(idx) {
    if (document.getElementById('items-body').rows.length <= 1) {
        alert('Minimal harus ada 1 barang.');
        return;
    }
    $(`#inv-select-${idx}`).select2('destroy');
    document.getElementById(`row-${idx}`)?.remove();
    recalcTotal();
}

/* ════ PEMBAYARAN AWAL ════ */
function onMetodePembayaranChange(value) {
    const dpInfoBox   = document.getElementById('dp-info-box');
    const jumlahInput = document.getElementById('jumlah_bayar_awal');
    const viaSelect   = document.getElementById('metode_bayar_awal');

    dpInfoBox.style.display = value === 'dp' ? 'block' : 'none';
    jumlahInput.min         = value === 'dp' ? '1' : '0';
    jumlahInput.placeholder = value === 'dp' ? 'Masukkan nominal DP...' : '0';

    if (value === 'transfer') viaSelect.value = 'transfer';
    else                      viaSelect.value = 'cash';
}

/* ════ SUBMIT ════ */
document.getElementById('formPenjualan').addEventListener('submit', function () {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line" style="animation:spin 1s linear infinite"></i> Menyimpan...';
});

/* ════ INIT ════ */
document.addEventListener('DOMContentLoaded', function () {
    onMetodePembayaranChange(document.getElementById('metode_pembayaran').value);

    @if(old('items'))
        @foreach(old('items') as $i => $oldItem)
            addRow({
                inventory_id: '{{ $oldItem["inventory_id"] ?? "" }}',
                nama_barang:  '{{ addslashes($oldItem["nama_barang"] ?? "") }}',
                kondisi:      '{{ $oldItem["kondisi"] ?? "baru" }}',
                qty:          {{ $oldItem['qty'] ?? 1 }},
                satuan:       '{{ $oldItem["satuan"] ?? "unit" }}',
                harga_satuan: {{ $oldItem['harga_satuan'] ?? 0 }},
                diskon:       {{ $oldItem['diskon'] ?? 0 }},
            });
        @endforeach
    @else
        addRow();
    @endif
});
</script>
<style>
@keyframes spin { from { transform:rotate(0deg) } to { transform:rotate(360deg) } }
</style>
@endpush