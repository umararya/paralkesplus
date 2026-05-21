{{-- resources/views/admin/penjualan/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Penjualan #' . $penjualan->id)
@section('breadcrumb', 'Edit Penjualan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<style>
    .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; max-width:960px; }
    .form-section { padding:20px 24px; border-bottom:1px solid var(--border); }
    .form-section:last-child { border-bottom:none; }
    .section-title { font-size:13px; font-weight:700; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.6px; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
    .section-title i { font-size:16px; color:var(--brand-500); }
    .form-grid { display:grid; gap:16px; }
    .form-grid-2 { grid-template-columns:1fr 1fr; }
    @media(max-width:640px) { .form-grid-2 { grid-template-columns:1fr; } }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-label { font-size:13px; font-weight:600; color:var(--text-primary); }
    .form-label .required { color:#EF4444; margin-left:2px; }
    .form-label .hint { font-weight:400; color:var(--text-muted); font-size:12px; margin-left:4px; }
    .form-control { width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13.5px; background:var(--bg-primary); color:var(--text-primary); outline:none; transition:border-color 0.2s,box-shadow 0.2s; font-family:var(--font); box-sizing:border-box; }
    .form-control:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .form-control.is-invalid { border-color:#EF4444; }
    textarea.form-control { resize:vertical; min-height:80px; }
    select.form-control { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:36px; }
    .invalid-feedback { font-size:12px; color:#EF4444; display:flex; align-items:center; gap:4px; }
    .dropzone { position:relative; border:2px dashed var(--border); border-radius:8px; padding:24px 16px; text-align:center; cursor:pointer; transition:all 0.2s; background:var(--bg-primary); outline:none; }
    .dropzone:hover,.dropzone.drag-over { border-color:var(--brand-500); background:var(--brand-50); }
    html.dark .dropzone:hover,html.dark .dropzone.drag-over { background:rgba(29,111,164,0.08); }
    .dropzone input[type="file"] { position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer; z-index:2; }
    .dropzone-icon { font-size:28px; color:var(--brand-500); margin-bottom:8px; display:block; }
    .dropzone-title { font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:2px; }
    .dropzone-sub { font-size:12px; color:var(--text-muted); }
    .dropzone-preview { display:none; align-items:center; gap:10px; padding:10px 14px; background:var(--brand-50); border:1px solid var(--brand-100); border-radius:8px; margin-top:8px; }
    html.dark .dropzone-preview { background:rgba(29,111,164,0.1); border-color:rgba(29,111,164,0.25); }
    .dropzone-preview.show { display:flex; }
    .dropzone-preview i { font-size:18px; color:var(--brand-500); flex-shrink:0; }
    .dropzone-preview-name { font-size:12.5px; color:var(--text-primary); font-weight:500; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .dropzone-preview-size { font-size:11.5px; color:var(--text-muted); white-space:nowrap; }
    .dropzone-preview-remove { width:22px; height:22px; border-radius:50%; background:rgba(239,68,68,0.1); color:#EF4444; display:inline-flex; align-items:center; justify-content:center; font-size:13px; cursor:pointer; flex-shrink:0; border:none; transition:background 0.2s; }
    .dropzone-preview-remove:hover { background:rgba(239,68,68,0.2); }
    .existing-file { display:inline-flex; align-items:center; gap:8px; padding:6px 12px; background:var(--brand-50); border:1px solid var(--brand-100); border-radius:8px; font-size:12.5px; color:var(--brand-600); font-weight:500; margin-bottom:8px; text-decoration:none; }
    html.dark .existing-file { background:rgba(29,111,164,0.1); border-color:rgba(29,111,164,0.25); color:var(--brand-400); }
    .existing-file i { font-size:15px; }
    .items-table { width:100%; border-collapse:collapse; margin-bottom:10px; }
    .items-table thead tr { background:var(--brand-500); color:#fff; }
    .items-table th { padding:9px 10px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; white-space:nowrap; }
    .items-table td { padding:7px 6px; border-bottom:1px solid var(--border); vertical-align:middle; }
    .items-table tbody tr:nth-child(even) td { background:var(--bg-hover); }
    .items-table .form-control { padding:7px 10px; font-size:13px; }
    .subtotal-cell { font-size:13px; font-weight:600; color:var(--brand-500); white-space:nowrap; min-width:110px; text-align:right; }
    .btn-remove-row { width:28px; height:28px; border-radius:6px; background:rgba(239,68,68,0.1); color:#EF4444; border:none; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:14px; transition:background 0.2s; }
    .btn-remove-row:hover { background:rgba(239,68,68,0.2); }
    .btn-add-row { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; border:1.5px dashed var(--brand-500); background:transparent; color:var(--brand-500); font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; font-family:var(--font); }
    .btn-add-row:hover { background:var(--brand-50); }
    html.dark .btn-add-row:hover { background:rgba(29,111,164,0.1); }
    .stok-ok   { background:#D1FAE5; color:#065F46; padding:1px 7px; border-radius:99px; font-size:11px; font-weight:600; }
    .stok-low  { background:#FEF3C7; color:#92400E; padding:1px 7px; border-radius:99px; font-size:11px; font-weight:600; }
    .stok-zero { background:#FEE2E2; color:#991B1B; padding:1px 7px; border-radius:99px; font-size:11px; font-weight:600; }
    .ringkasan-box { display:flex; justify-content:flex-end; margin-top:14px; }
    .ringkasan-inner { width:280px; border:1px solid var(--border); border-radius:8px; overflow:hidden; }
    .ringkasan-row { display:flex; justify-content:space-between; padding:8px 12px; font-size:13px; border-bottom:1px solid var(--border); }
    .ringkasan-row:last-child { border-bottom:none; }
    .ringkasan-row .r-label { color:var(--text-muted); }
    .ringkasan-row .r-value { font-weight:700; color:var(--text-primary); }
    .ringkasan-row.total { background:var(--brand-500); }
    .ringkasan-row.total .r-label,
    .ringkasan-row.total .r-value { color:#fff; font-size:13.5px; }
    .form-footer { padding:16px 24px; display:flex; gap:12px; justify-content:flex-end; background:var(--bg-primary); border-top:1px solid var(--border); }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 18px; height:40px; border-radius:8px; font-size:13.5px; font-weight:600; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; }
    .btn-cancel { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-cancel:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-save { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-save:hover { background:var(--brand-600); border-color:var(--brand-600); }
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single { height:36px; border:1px solid var(--border); border-radius:7px; background:var(--bg-primary); display:flex; align-items:center; padding:0 10px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color:var(--text-primary); font-size:13px; line-height:1; padding:0; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; right:8px; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single { border-color:var(--brand-500); outline:none; box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .select2-dropdown { border:1px solid var(--border); border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,0.1); background:var(--bg-card); z-index:9999; }
    .select2-container--default .select2-results__option { font-size:13px; color:var(--text-primary); padding:8px 12px; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background:var(--brand-500); color:#fff; }
    .select2-search--dropdown .select2-search__field { border:1px solid var(--border); border-radius:6px; padding:6px 10px; font-size:13px; background:var(--bg-primary); color:var(--text-primary); width:100%; box-sizing:border-box; }
</style>
@endpush

@section('content')

<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('penjualan.index') }}"
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
            Edit Penjualan <span style="color:var(--text-muted); font-weight:500;">#{{ $penjualan->id }}</span>
        </h1>
        <p style="font-size:13px; color:var(--text-muted);">Ubah data penjualan a.n. {{ $penjualan->nama_pelanggan }}</p>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('penjualan.update', $penjualan->id) }}" method="POST"
          enctype="multipart/form-data" id="form-penjualan">
        @csrf
        @method('PUT')

        {{-- ===================== DATA PELANGGAN ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-user-line"></i> Data Pelanggan</div>
            <div class="form-grid form-grid-2">

                <div class="form-group">
                    <label class="form-label">Nama Pelanggan <span class="required">*</span></label>
                    <input type="text" name="nama_pelanggan"
                           value="{{ old('nama_pelanggan', $penjualan->nama_pelanggan) }}"
                           placeholder="Nama lengkap pelanggan"
                           class="form-control {{ $errors->has('nama_pelanggan') ? 'is-invalid' : '' }}"
                           required>
                    @error('nama_pelanggan')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Telepon <span class="hint">(opsional)</span></label>
                    <input type="tel" name="nomor_telepon"
                           value="{{ old('nomor_telepon', $penjualan->nomor_telepon) }}"
                           placeholder="08xxxxxxxxxx"
                           class="form-control {{ $errors->has('nomor_telepon') ? 'is-invalid' : '' }}">
                    @error('nomor_telepon')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Alamat Pelanggan <span class="required">*</span></label>
                    <textarea name="alamat_pelanggan" rows="2"
                              placeholder="Alamat lengkap pelanggan"
                              class="form-control {{ $errors->has('alamat_pelanggan') ? 'is-invalid' : '' }}"
                              required>{{ old('alamat_pelanggan', $penjualan->alamat_pelanggan) }}</textarea>
                    @error('alamat_pelanggan')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ===================== ITEM PENJUALAN ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-shopping-bag-line"></i> Item Penjualan</div>

            <div style="overflow-x:auto;">
                <table class="items-table" id="items-table">
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th style="min-width:200px;">Nama Barang</th>
                            <th style="width:90px;">Kondisi</th>
                            <th style="width:70px;">Qty</th>
                            <th style="width:80px;">Satuan</th>
                            <th style="width:140px;">Harga/Satuan (Rp)</th>
                            <th style="width:70px;">Diskon (%)</th>
                            <th style="width:120px; text-align:right;">Subtotal</th>
                            <th style="width:36px;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body"></tbody>
                </table>
            </div>

            <button type="button" class="btn-add-row" onclick="addRow()">
                <i class="ri-add-line"></i> Tambah Item
            </button>

            <div class="ringkasan-box">
                <div class="ringkasan-inner">
                    <div class="ringkasan-row">
                        <span class="r-label">Subtotal</span>
                        <span class="r-value" id="r-subtotal">Rp 0</span>
                    </div>
                    <div class="ringkasan-row">
                        <span class="r-label">Diskon Global</span>
                        <span class="r-value" id="r-diskon">Rp 0</span>
                    </div>
                    <div class="ringkasan-row total">
                        <span class="r-label">Total Tagihan</span>
                        <span class="r-value" id="r-total">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== DETAIL TRANSAKSI ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-bank-card-line"></i> Detail Transaksi</div>
            <div class="form-grid form-grid-2">

                <div class="form-group">
                    <label class="form-label">Tanggal Penjualan <span class="required">*</span></label>
                    <input type="text" id="tanggal_penjualan" name="tanggal_penjualan"
                           value="{{ old('tanggal_penjualan', $penjualan->tanggal_penjualan?->format('Y-m-d')) }}"
                           class="form-control {{ $errors->has('tanggal_penjualan') ? 'is-invalid' : '' }}"
                           autocomplete="off" readonly required>
                    @error('tanggal_penjualan')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Pembayaran <span class="required">*</span></label>
                    <select name="jenis_pembayaran"
                            class="form-control {{ $errors->has('jenis_pembayaran') ? 'is-invalid' : '' }}"
                            required>
                        @foreach(['tunai' => 'Tunai / Cash', 'transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'kredit' => 'Kredit / Cicilan'] as $val => $label)
                            <option value="{{ $val }}"
                                {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran) == $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_pembayaran')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Diskon Global <span class="hint">(Rp)</span></label>
                    <input type="number" name="diskon_global" id="diskon_global"
                           value="{{ old('diskon_global', $penjualan->diskon_global ?? 0) }}"
                           min="0" placeholder="0"
                           class="form-control {{ $errors->has('diskon_global') ? 'is-invalid' : '' }}"
                           oninput="hitungRingkasan()">
                    @error('diskon_global')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Bukti Pembayaran <span class="hint">(kosongkan jika tidak diganti)</span></label>
                    @if($penjualan->foto_bukti)
                        <a href="{{ asset('storage/' . $penjualan->foto_bukti) }}"
                           target="_blank" class="existing-file">
                            <i class="ri-image-line"></i> Foto saat ini: {{ basename($penjualan->foto_bukti) }}
                            <i class="ri-external-link-line" style="font-size:12px;"></i>
                        </a>
                    @endif
                    <div class="dropzone" id="dropzone-bukti" tabindex="0" role="button"
                         onkeydown="if(event.key==='Enter'||event.key===' ')this.querySelector('input').click()">
                        <input type="file" id="foto_bukti" name="foto_bukti" accept=".jpg,.jpeg,.png,.webp">
                        <i class="ri-image-line dropzone-icon"></i>
                        <div class="dropzone-title">Klik atau seret file baru ke sini</div>
                        <div class="dropzone-sub">JPG, PNG, WEBP &mdash; maks 2 MB</div>
                    </div>
                    <div class="dropzone-preview" id="bukti-preview">
                        <i class="ri-file-check-line"></i>
                        <span class="dropzone-preview-name" id="bukti-preview-name"></span>
                        <span class="dropzone-preview-size" id="bukti-preview-size"></span>
                        <button type="button" class="dropzone-preview-remove"
                                onclick="removeFile('foto_bukti','bukti-preview','dropzone-bukti')">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    @error('foto_bukti')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Keterangan <span class="hint">(opsional)</span></label>
                    <textarea name="keterangan" rows="2"
                              placeholder="Catatan tambahan jika ada..."
                              class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}">{{ old('keterangan', $penjualan->keterangan) }}</textarea>
                    @error('keterangan')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ===================== FOOTER ===================== --}}
        <div class="form-footer">
            <a href="{{ route('penjualan.index') }}" class="btn btn-cancel">
                <i class="ri-close-line"></i> Batal
            </a>
            <button type="submit" class="btn btn-save">
                <i class="ri-save-line"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// ─────────────────────────────────────────────
//  FLATPICKR
// ─────────────────────────────────────────────
flatpickr('#tanggal_penjualan', {
    locale: 'id', dateFormat: 'Y-m-d', allowInput: false,
    defaultDate: '{{ old("tanggal_penjualan", $penjualan->tanggal_penjualan?->format("Y-m-d")) }}',
});

// ─────────────────────────────────────────────
//  SELECT2 TEMPLATE
// ─────────────────────────────────────────────
function templateBarang(item) {
    if (!item.id) return item.text || 'Cari nama barang...';
    const badge = { ok:'stok-ok', low:'stok-low', zero:'stok-zero' };
    return $(`
        <div style="padding:3px 0">
            <div style="font-size:13px;font-weight:500;color:var(--text-primary)">${item.text}</div>
            <div style="display:flex;gap:6px;margin-top:3px;align-items:center">
                <span style="font-size:11px;color:var(--text-muted)">${item.kategori||''}</span>
                <span class="${badge[item.stok_status]||'stok-ok'}">${item.stok_label||''}</span>
            </div>
        </div>
    `);
}

// ─────────────────────────────────────────────
//  ITEMS TABLE
// ─────────────────────────────────────────────
let rowIndex = 0;

function addRow(data = {}) {
    rowIndex++;
    const idx   = rowIndex;
    const tbody = document.getElementById('items-body');
    const tr    = document.createElement('tr');
    tr.id       = `row-${idx}`;

    const satuanList = ['unit','pcs','set','buah','pasang','botol','box'];
    if (data.satuan && !satuanList.includes(data.satuan)) satuanList.push(data.satuan);
    const satuanOpts = satuanList.map(s =>
        `<option value="${s}" ${(data.satuan||'unit')===s?'selected':''}>${s}</option>`
    ).join('');

    // FIX: Pastikan detail_id dan inventory_id tidak pernah bernilai string "null"
    const detailIdVal    = (data.detail_id    && data.detail_id    !== 'null' && data.detail_id    !== '') ? data.detail_id    : null;
    const inventoryIdVal = (data.inventory_id && data.inventory_id !== 'null' && data.inventory_id !== '') ? data.inventory_id : null;

    const detailIdField = detailIdVal
        ? `<input type="hidden" name="items[${idx}][detail_id]" value="${detailIdVal}">`
        : '';

    // FIX: Pre-selected option yang valid dengan nama barang
    const preSelectedOption = inventoryIdVal && data.nama_barang
        ? `<option value="${inventoryIdVal}" selected>${data.nama_barang}</option>`
        : '';

    tr.innerHTML = `
        <td style="text-align:center;font-size:12px;color:var(--text-muted)">${idx}</td>
        <td style="min-width:200px">
            ${detailIdField}
            <input type="hidden" name="items[${idx}][nama_barang]" id="nama-barang-${idx}" value="${(data.nama_barang||'').replace(/"/g,'&quot;')}">
            <select name="items[${idx}][inventory_id]"
                    id="inv-select-${idx}"
                    class="form-control">
                ${preSelectedOption}
            </select>
        </td>
        <td>
            <select name="items[${idx}][kondisi]"
                    id="kondisi-${idx}"
                    class="form-control" style="width:90px"
                    onchange="onKondisiChange(${idx})">
                <option value="baru"  ${(data.kondisi||'baru')==='baru' ?'selected':''}>Baru</option>
                <option value="bekas" ${(data.kondisi||'')==='bekas'?'selected':''}>Bekas</option>
            </select>
        </td>
        <td>
            <input type="number" name="items[${idx}][qty]"
                   id="qty-${idx}"
                   value="${data.qty||1}" min="1"
                   class="form-control" style="text-align:center;width:64px"
                   oninput="hitungSubtotal(${idx})">
        </td>
        <td>
            <select name="items[${idx}][satuan]" class="form-control" style="width:80px">${satuanOpts}</select>
        </td>
        <td>
            <input type="number" name="items[${idx}][harga_satuan]"
                   id="harga-${idx}"
                   value="${data.harga_satuan||0}" min="0"
                   class="form-control" style="width:130px"
                   oninput="hitungSubtotal(${idx})">
        </td>
        <td>
            <input type="number" name="items[${idx}][diskon]"
                   id="diskon-${idx}"
                   value="${data.diskon||0}" min="0" max="100"
                   class="form-control" style="text-align:center;width:64px"
                   oninput="hitungSubtotal(${idx})">
        </td>
        <td class="subtotal-cell" id="subtotal-${idx}">Rp 0</td>
        <td style="text-align:center">
            <button type="button" class="btn-remove-row" onclick="removeRow(${idx})">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);

    const $sel = $(`#inv-select-${idx}`);

    $sel.select2({
        dropdownParent: $(`#row-${idx}`),
        placeholder:    'Cari nama barang...',
        minimumInputLength: 0,
        allowClear:     true,
        ajax: {
            url:      '{{ route("api.inventory.index") }}',
            dataType: 'json',
            delay:    200,
            data:     params => ({
                q:       params.term || '',
                mode:    'jual',
                kondisi: $(`#kondisi-${idx}`).val(),
            }),
            processResults: data => ({ results: data.results }),
            cache: false,
        },
        templateResult:    templateBarang,
        // FIX: templateSelection menggunakan teks option yang sudah ada
        templateSelection: function(d) {
            return d.text || data.nama_barang || 'Pilih barang...';
        },
    });

    // FIX: Trigger change agar Select2 render label dari pre-selected option
    if (inventoryIdVal) {
        $sel.trigger('change');
    }

    // FIX: Update hidden nama_barang dan field lain saat memilih item baru
    $sel.on('select2:select', function(e) {
        const item = e.params.data;
        $(`#nama-barang-${idx}`).val(item.text);
        $(`#harga-${idx}`).val(item.harga_beli_terakhir || 0);
        const kondisi = $(`#kondisi-${idx}`).val();
        $(`#qty-${idx}`).attr('max', kondisi === 'baru' ? item.stok_baru : item.stok_bekas);
        const satSel = $(`#row-${idx} select[name="items[${idx}][satuan]"]`);
        if (item.satuan) {
            if (!satSel.find(`option[value="${item.satuan}"]`).length)
                satSel.append(new Option(item.satuan, item.satuan));
            satSel.val(item.satuan);
        }
        hitungSubtotal(idx);
    }).on('select2:clear', function() {
        $(`#nama-barang-${idx}`).val('');
        $(`#harga-${idx}`).val(0);
        hitungSubtotal(idx);
    });

    if (data.harga_satuan) {
        hitungSubtotal(idx);
    }
}

function onKondisiChange(idx) {
    $(`#inv-select-${idx}`).val(null).trigger('change');
    $(`#nama-barang-${idx}`).val('');
    $(`#harga-${idx}`).val(0);
    hitungSubtotal(idx);
}

function removeRow(idx) {
    if (document.getElementById('items-body').rows.length <= 1) return;
    $(`#inv-select-${idx}`).select2('destroy');
    document.getElementById(`row-${idx}`)?.remove();
    hitungRingkasan();
}

function hitungSubtotal(idx) {
    const qty    = parseFloat(document.getElementById(`qty-${idx}`)?.value)    || 0;
    const harga  = parseFloat(document.getElementById(`harga-${idx}`)?.value)  || 0;
    const diskon = parseFloat(document.getElementById(`diskon-${idx}`)?.value) || 0;
    const sub    = Math.round(qty * harga * (1 - diskon / 100));
    const cell   = document.getElementById(`subtotal-${idx}`);
    if (cell) cell.textContent = 'Rp ' + sub.toLocaleString('id-ID');
    hitungRingkasan();
}

function hitungRingkasan() {
    let total = 0;
    document.querySelectorAll('[id^="subtotal-"]').forEach(c => {
        total += parseInt(c.textContent.replace(/[^0-9]/g,'')) || 0;
    });
    const diskon = parseInt(document.getElementById('diskon_global')?.value) || 0;
    const grand  = Math.max(0, total - diskon);
    document.getElementById('r-subtotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('r-diskon').textContent   = 'Rp ' + diskon.toLocaleString('id-ID');
    document.getElementById('r-total').textContent    = 'Rp ' + grand.toLocaleString('id-ID');
}

// ─────────────────────────────────────────────
//  DROPZONE
// ─────────────────────────────────────────────
function initDropzone(inputId, previewId, zoneId) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const zone    = document.getElementById(zoneId);
    if (!input) return;
    input.addEventListener('change', () => showPreview(input, preview, zone));
    zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => {
        e.preventDefault(); zone.classList.remove('drag-over');
        if (e.dataTransfer.files.length) { input.files = e.dataTransfer.files; showPreview(input, preview, zone); }
    });
}
function showPreview(input, preview, zone) {
    const file = input.files[0]; if (!file) return;
    preview.querySelector('.dropzone-preview-name').textContent = file.name;
    preview.querySelector('.dropzone-preview-size').textContent = (file.size/1024).toFixed(1)+' KB';
    preview.classList.add('show'); zone.style.display = 'none';
}
function removeFile(inputId, previewId, zoneId) {
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).classList.remove('show');
    document.getElementById(zoneId).style.display = '';
}

// ─────────────────────────────────────────────
//  INIT — load existing items dari DB
// ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    initDropzone('foto_bukti', 'bukti-preview', 'dropzone-bukti');

    @if(old('items'))
        {{-- Saat validasi gagal, reload dari old() --}}
        @foreach(old('items') as $i => $oldItem)
            addRow({
                detail_id:    '{{ $oldItem["detail_id"] ?? "" }}',
                inventory_id: '{{ $oldItem["inventory_id"] ?? "" }}',
                nama_barang:  '{{ addslashes($oldItem["nama_barang"] ?? "") }}',
                kondisi:      '{{ $oldItem["kondisi"] ?? "baru" }}',
                qty:          {{ (int)($oldItem['qty'] ?? 1) }},
                satuan:       '{{ $oldItem["satuan"] ?? "unit" }}',
                harga_satuan: {{ (int)($oldItem['harga_satuan'] ?? 0) }},
                diskon:       {{ (int)($oldItem['diskon'] ?? 0) }},
            });
        @endforeach
    @else
        {{-- Load dari relasi details DB --}}
        @forelse($penjualan->details as $detail)
            addRow({
                detail_id:    {{ $detail->id }},
                {{-- FIX: Gunakan JS null (bukan string "null") jika inventory_id kosong --}}
                inventory_id: {{ $detail->inventory_id !== null ? $detail->inventory_id : 'null' }},
                nama_barang:  '{{ addslashes($detail->nama_barang) }}',
                kondisi:      '{{ $detail->kondisi ?? "baru" }}',
                qty:          {{ (int)$detail->qty }},
                satuan:       '{{ $detail->satuan }}',
                harga_satuan: {{ (int)$detail->harga_satuan }},
                diskon:       {{ (int)($detail->diskon ?? 0) }},
            });
        @empty
            addRow();
        @endforelse
    @endif
});
</script>
@endpush