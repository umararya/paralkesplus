{{-- resources/views/admin/penyewaan/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Penyewaan')
@section('breadcrumb', 'Tambah Penyewaan')

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
    .form-grid-3 { grid-template-columns:1fr 1fr 1fr; }
    @media(max-width:640px) { .form-grid-2,.form-grid-3 { grid-template-columns:1fr; } }
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
    .date-range-wrap { display:flex; align-items:center; gap:8px; }
    .date-range-wrap .form-control { flex:1; }
    .date-range-sep { font-size:13px; color:var(--text-muted); white-space:nowrap; }
    .durasi-display { margin-top:6px; font-size:12.5px; color:var(--brand-500); font-weight:600; display:flex; align-items:center; gap:5px; min-height:18px; }
    .pengiriman-note { margin-top:5px; font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:4px; min-height:16px; }
    .metode-info { margin-top:5px; font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:4px; min-height:16px; }
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
    .dropzone-preview-thumb { width:40px; height:40px; object-fit:cover; border-radius:6px; flex-shrink:0; border:1px solid var(--border); }
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
    .ringkasan-row.total .r-label, .ringkasan-row.total .r-value { color:#fff; font-size:13.5px; }
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
    .stok-info-row { display:flex; align-items:center; gap:6px; margin-top:4px; min-height:18px; }
    .stok-info-row span { font-size:12px; }
    .badge-baru  { background:#DBEAFE; color:#1E40AF; padding:1px 7px; border-radius:99px; font-size:11px; font-weight:600; }
    .badge-bekas { background:#FEF3C7; color:#92400E; padding:1px 7px; border-radius:99px; font-size:11px; font-weight:600; }
    .file-error { font-size:12px; color:#EF4444; display:flex; align-items:center; gap:4px; margin-top:4px; }
</style>
@endpush

@section('content')

<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('penyewaan.index') }}"
       style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;
              border-radius:8px;background:var(--bg-card);border:1px solid var(--border);
              color:var(--text-secondary);text-decoration:none;transition:all 0.2s;"
       onmouseover="this.style.background='var(--bg-hover)'"
       onmouseout="this.style.background='var(--bg-card)'">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:700;color:var(--text-primary);margin-bottom:2px;">Tambah Penyewaan</h1>
        <p style="font-size:13px;color:var(--text-muted);">Isi form berikut untuk menambah data penyewaan baru</p>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('penyewaan.store') }}" method="POST" enctype="multipart/form-data" id="form-penyewaan">
        @csrf

        {{-- ===================== DATA PENYEWA ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-user-line"></i> Data Penyewa</div>
            <div class="form-grid form-grid-2">

                <div class="form-group">
                    <label class="form-label">Nama <span class="required">*</span></label>
                    <input type="text" name="nama_penyewa" value="{{ old('nama_penyewa') }}"
                           placeholder="Nama lengkap penyewa"
                           class="form-control {{ $errors->has('nama_penyewa') ? 'is-invalid' : '' }}" required>
                    @error('nama_penyewa')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Telepon/HP <span class="required">*</span></label>
                    <input type="tel" name="nomor_telepon" value="{{ old('nomor_telepon') }}"
                           placeholder="08xxxxxxxxxx"
                           class="form-control {{ $errors->has('nomor_telepon') ? 'is-invalid' : '' }}" required>
                    @error('nomor_telepon')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tempat/Tanggal Lahir <span class="hint">(untuk formulir perjanjian)</span></label>
                    <input type="text" name="tempat_tanggal_lahir" value="{{ old('tempat_tanggal_lahir') }}"
                           placeholder="Contoh: Semarang, 01 Januari 1990"
                           class="form-control {{ $errors->has('tempat_tanggal_lahir') ? 'is-invalid' : '' }}">
                    @error('tempat_tanggal_lahir')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor KTP (NIK) <span class="hint">(16 digit)</span></label>
                    <input type="text" name="nomor_ktp" value="{{ old('nomor_ktp') }}"
                           placeholder="3374xxxxxxxxxxxxxxx" maxlength="16"
                           inputmode="numeric" pattern="[0-9]{16}"
                           class="form-control {{ $errors->has('nomor_ktp') ? 'is-invalid' : '' }}"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    @error('nomor_ktp')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Alamat Penyewa <span class="required">*</span></label>
                    <textarea name="alamat_penyewa" rows="3" placeholder="Alamat lengkap penyewa"
                              class="form-control {{ $errors->has('alamat_penyewa') ? 'is-invalid' : '' }}"
                              required>{{ old('alamat_penyewa') }}</textarea>
                    @error('alamat_penyewa')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ===================== ITEM PENYEWAAN ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-stethoscope-line"></i> Item Penyewaan</div>

            <div style="overflow-x:auto;">
                <table class="items-table" id="items-table">
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th style="min-width:220px;">Nama Alat</th>
                            <th style="width:90px;">Kondisi</th>
                            <th style="width:70px;">Qty</th>
                            <th style="width:80px;">Satuan</th>
                            <th style="width:140px;">Harga/Satuan (Rp)</th>
                            <th style="width:70px;">Diskon (%)</th>
                            <th style="width:120px;text-align:right;">Subtotal</th>
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
                        <span class="r-label">Subtotal Sewa</span>
                        <span class="r-value" id="r-subtotal">Rp 0</span>
                    </div>
                    <div class="ringkasan-row">
                        <span class="r-label">Diskon Global</span>
                        <span class="r-value" id="r-diskon">Rp 0</span>
                    </div>
                    <div class="ringkasan-row">
                        <span class="r-label">Ongkir</span>
                        <span class="r-value" id="r-ongkir">Rp 0</span>
                    </div>
                    <div class="ringkasan-row total">
                        <span class="r-label">Total Tagihan</span>
                        <span class="r-value" id="r-total">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===================== DURASI & PENGIRIMAN ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-calendar-line"></i> Durasi &amp; Pengiriman</div>
            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Periode Sewa <span class="required">*</span></label>
                    <div class="date-range-wrap">
                        <input type="text" id="tgl_mulai" name="tgl_mulai"
                               value="{{ old('tgl_mulai') }}" placeholder="Tanggal Mulai"
                               class="form-control {{ $errors->has('tgl_mulai') ? 'is-invalid' : '' }}"
                               autocomplete="off" readonly required>
                        <span class="date-range-sep"><i class="ri-arrow-right-line"></i></span>
                        <input type="text" id="tgl_selesai" name="tgl_selesai"
                               value="{{ old('tgl_selesai') }}" placeholder="Tanggal Selesai"
                               class="form-control {{ $errors->has('tgl_selesai') ? 'is-invalid' : '' }}"
                               autocomplete="off" readonly required>
                    </div>
                    <input type="hidden" id="durasi_hari" name="durasi_hari" value="{{ old('durasi_hari') }}">
                    <div class="durasi-display" id="durasi-display">
                        @if(old('durasi_hari'))
                            <i class="ri-calendar-check-line"></i> {{ old('durasi_hari') }} hari
                        @endif
                    </div>
                    @error('tgl_mulai')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                    @error('tgl_selesai')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-grid form-grid-2" style="grid-column:1/-1;">

                    <div class="form-group">
                        <label class="form-label">Pengiriman <span class="required">*</span></label>
                        <select name="pengiriman" id="pengiriman"
                                class="form-control {{ $errors->has('pengiriman') ? 'is-invalid' : '' }}"
                                onchange="updatePengirimanNote()" required>
                            <option value="" disabled {{ old('pengiriman') ? '' : 'selected' }}>-- Pilih metode pengiriman --</option>
                            <option value="mandiri" {{ old('pengiriman')=='mandiri'?'selected':'' }}>Ambil dan Antar kembali sendiri oleh Penyewa</option>
                            <option value="Gosend / GrabExpress" {{ old('pengiriman')=='Gosend / GrabExpress'?'selected':'' }}>via Gosend / GrabExpress</option>
                            <option value="Rental Mobil Paralkes" {{ old('pengiriman')=='Rental Mobil Paralkes'?'selected':'' }}>via Rental Mobil Paralkes</option>
                        </select>
                        <div class="pengiriman-note" id="pengiriman-note">
                            <i class="ri-information-line"></i>
                            <span id="pengiriman-note-text">Pilih metode pengiriman di atas</span>
                        </div>
                        @error('pengiriman')
                            <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Biaya Ongkir <span class="hint">(Rp, isi 0 jika ambil sendiri)</span></label>
                        <input type="number" name="biaya_ongkir" id="biaya_ongkir"
                               value="{{ old('biaya_ongkir', 0) }}" min="0" placeholder="0"
                               class="form-control {{ $errors->has('biaya_ongkir') ? 'is-invalid' : '' }}"
                               oninput="hitungRingkasan()">
                        @error('biaya_ongkir')
                            <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- ===================== PEMBAYARAN ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-bank-card-line"></i> Pembayaran</div>
            <div class="form-grid form-grid-2">

                <div class="form-group">
                    <label class="form-label">Metode Pembayaran <span class="required">*</span></label>
                    <select name="metode_pembayaran" id="metode_pembayaran"
                            class="form-control {{ $errors->has('metode_pembayaran') ? 'is-invalid' : '' }}"
                            onchange="updateMetodeInfo()" required>
                        <option value="" disabled {{ old('metode_pembayaran') ? '' : 'selected' }}>-- Pilih metode --</option>
                        <option value="tunai"    {{ old('metode_pembayaran')=='tunai'   ?'selected':'' }}>Tunai / Cash</option>
                        <option value="transfer" {{ old('metode_pembayaran')=='transfer'?'selected':'' }}>Transfer via Bank BCA</option>
                        <option value="qris"     {{ old('metode_pembayaran')=='qris'   ?'selected':'' }}>QRIS</option>
                    </select>
                    <div class="metode-info" id="metode-info">
                        <i class="ri-information-line"></i>
                        <span id="metode-info-text">Pilih metode pembayaran</span>
                    </div>
                    @error('metode_pembayaran')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Diskon Global <span class="hint">(Rp)</span></label>
                    <input type="number" name="diskon_global" id="diskon_global"
                           value="{{ old('diskon_global', 0) }}" min="0" placeholder="0"
                           class="form-control {{ $errors->has('diskon_global') ? 'is-invalid' : '' }}"
                           oninput="hitungRingkasan()">
                    @error('diskon_global')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select class="form-control" disabled>
                        <option>Berjalan</option>
                    </select>
                    <input type="hidden" name="status" value="berjalan">
                    <span style="font-size:12px;color:var(--text-muted);margin-top:4px;display:flex;align-items:center;gap:4px;">
                        <i class="ri-information-line"></i> Status hanya dapat diubah saat edit
                    </span>
                </div>

                {{-- ===== BUKTI PEMBAYARAN - DRAG & DROP FILE ===== --}}
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">
                        Bukti Pembayaran
                        <span class="hint">(jpg/png, maks 10 MB)</span>
                    </label>
                    <div class="dropzone" id="dropzone-bukti" tabindex="0" role="button"
                         onkeydown="if(event.key==='Enter'||event.key===' ')this.querySelector('input').click()">
                        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran"
                               accept=".jpg,.jpeg,.png">
                        <i class="ri-receipt-line dropzone-icon"></i>
                        <div class="dropzone-title">Klik atau seret file bukti pembayaran ke sini</div>
                        <div class="dropzone-sub">JPG atau PNG &mdash; maks 10 MB</div>
                    </div>
                    <div class="dropzone-preview" id="bukti-preview">
                        <img id="bukti-preview-thumb" class="dropzone-preview-thumb" src="" alt="preview">
                        <span class="dropzone-preview-name" id="bukti-preview-name"></span>
                        <span class="dropzone-preview-size" id="bukti-preview-size"></span>
                        <button type="button" class="dropzone-preview-remove"
                                onclick="removeFile('bukti_pembayaran','bukti-preview','dropzone-bukti')">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <div id="bukti-error" class="file-error" style="display:none;">
                        <i class="ri-error-warning-line"></i>
                        <span id="bukti-error-text"></span>
                    </div>
                    @error('bukti_pembayaran')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Foto KTP / SIM <span class="hint">(jpg/png/pdf, maks 5MB)</span></label>
                    <div class="dropzone" id="dropzone-ktp" tabindex="0" role="button"
                         onkeydown="if(event.key==='Enter'||event.key===' ')this.querySelector('input').click()">
                        <input type="file" id="foto_ktp_sim" name="foto_ktp_sim" accept=".jpg,.jpeg,.png,.pdf">
                        <i class="ri-id-card-line dropzone-icon"></i>
                        <div class="dropzone-title">Klik atau seret file ke sini</div>
                        <div class="dropzone-sub">JPG, PNG, atau PDF &mdash; maks 5 MB</div>
                    </div>
                    <div class="dropzone-preview" id="ktp-preview">
                        <i class="ri-file-check-line"></i>
                        <span class="dropzone-preview-name" id="ktp-preview-name"></span>
                        <span class="dropzone-preview-size" id="ktp-preview-size"></span>
                        <button type="button" class="dropzone-preview-remove"
                                onclick="removeFile('foto_ktp_sim','ktp-preview','dropzone-ktp')">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    @error('foto_ktp_sim')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ===================== KETERANGAN ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-file-text-line"></i> Keterangan</div>
            <div class="form-group">
                <label class="form-label">Keterangan <span class="hint">(opsional)</span></label>
                <textarea name="keterangan" rows="3"
                          placeholder="Catatan tambahan jika ada..."
                          class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- ===================== FOOTER ===================== --}}
        <div class="form-footer">
            <a href="{{ route('penyewaan.index') }}" class="btn btn-cancel">
                <i class="ri-close-line"></i> Batal
            </a>
            <button type="submit" class="btn btn-save" id="btn-submit">
                <i class="ri-save-line"></i> Simpan Penyewaan
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
// ── FLATPICKR ──
const fpMulai = flatpickr('#tgl_mulai', {
    locale:'id', dateFormat:'Y-m-d', allowInput:false,
    onChange: (sel, str) => { fpSelesai.set('minDate', str); hitungDurasi(); }
});
const fpSelesai = flatpickr('#tgl_selesai', {
    locale:'id', dateFormat:'Y-m-d', allowInput:false,
    onChange: () => hitungDurasi()
});

function hitungDurasi() {
    const m = document.getElementById('tgl_mulai').value;
    const s = document.getElementById('tgl_selesai').value;
    const disp   = document.getElementById('durasi-display');
    const hidden = document.getElementById('durasi_hari');
    if (m && s) {
        const [sy, sm, sd] = m.split('-').map(Number);
        const [ey, em, ed] = s.split('-').map(Number);
        const start = new Date(sy, sm - 1, sd);
        const end   = new Date(ey, em - 1, ed);
        const diff  = Math.round((end - start) / 86400000);
        if (diff >= 0) {
            hidden.value   = diff;
            disp.innerHTML = `<i class="ri-calendar-check-line"></i> ${diff} hari`;
        } else { hidden.value = ''; disp.innerHTML = ''; }
    } else { hidden.value = ''; disp.innerHTML = ''; }
}

// ── HELPER NOTES ──
function updatePengirimanNote() {
    const v = document.getElementById('pengiriman').value;
    const n = {
        mandiri:'Penyewa mengambil dan mengembalikan sendiri.',
        'Gosend / GrabExpress':'Pengiriman via ojek online, biaya ditanggung penyewa.',
        'Rental Mobil Paralkes':'Pengiriman menggunakan Rental Mobil Paralkes.'
    };
    document.getElementById('pengiriman-note-text').textContent = n[v] || 'Pilih metode pengiriman di atas';
}
function updateMetodeInfo() {
    const v = document.getElementById('metode_pembayaran').value;
    const i = {
        tunai:'Pembayaran dilakukan secara tunai saat penyerahan alat.',
        transfer:'Transfer ke BCA 8030910754 a.n. SURYA DAYYANA.',
        qris:'Scan QRIS yang tersedia.'
    };
    document.getElementById('metode-info-text').textContent = i[v] || 'Pilih metode pembayaran';
}

// ── SELECT2 TEMPLATE ──
function templateInventory(item) {
    if (!item.id) return item.text || 'Cari nama alat...';
    const badgeClass = { ok:'stok-ok', low:'stok-low', zero:'stok-zero' }[item.stok_status] || 'stok-ok';
    return $(`
        <div style="padding:3px 0">
            <div style="font-size:13px;font-weight:500;color:var(--text-primary)">${item.text}</div>
            <div style="display:flex;gap:6px;margin-top:3px;align-items:center">
                <span style="font-size:11px;color:var(--text-muted)">${item.kategori||''}</span>
                <span class="${badgeClass}">${item.stok_label||''}</span>
            </div>
        </div>
    `);
}
function templateInventorySelection(item) {
    if (!item.id) return item.text || 'Pilih alat...';
    const badgeClass = { ok:'stok-ok', low:'stok-low', zero:'stok-zero' }[item.stok_status] || 'stok-ok';
    const stokLabel  = item.stok_label || '';
    if (!stokLabel) return item.text;
    return $(`<span>${item.text}&nbsp;<span class="${badgeClass}" style="font-size:11px">${stokLabel}</span></span>`);
}

// ── ITEMS TABLE ──
let rowIndex = 0;

function addRow(data = {}) {
    rowIndex++;
    const idx   = rowIndex;
    const tbody = document.getElementById('items-body');
    const tr    = document.createElement('tr');
    tr.id       = `row-${idx}`;

    const satuanOpts = ['unit','pcs','set','buah','pasang']
        .map(s => `<option value="${s}" ${(data.satuan||'unit')===s?'selected':''}>${s}</option>`)
        .join('');

    const kondisiVal = data.kondisi || 'baru';

    tr.innerHTML = `
        <td style="text-align:center;font-size:12px;color:var(--text-muted)">${idx}</td>
        <td style="min-width:220px">
            <input type="hidden"
                   name="items[${idx}][nama_alat]"
                   id="nama-alat-${idx}"
                   value="${data.nama_alat || ''}">
            <select name="items[${idx}][inventory_id]"
                    id="inv-select-${idx}"
                    class="form-control" required>
                ${data.inventory_id
                    ? `<option value="${data.inventory_id}" selected>${data.nama_alat||''}</option>`
                    : ''}
            </select>
            <div class="stok-info-row" id="stok-info-${idx}"></div>
        </td>
        <td>
            <select name="items[${idx}][kondisi]"
                    id="kondisi-${idx}"
                    class="form-control" style="width:90px">
                <option value="baru"  ${kondisiVal==='baru' ?'selected':''}>Baru</option>
                <option value="bekas" ${kondisiVal==='bekas'?'selected':''}>Bekas</option>
            </select>
        </td>
        <td>
            <input type="number" name="items[${idx}][qty]"
                   id="qty-${idx}" value="${data.qty||1}" min="1"
                   class="form-control" style="text-align:center;width:64px"
                   oninput="hitungSubtotal(${idx})">
        </td>
        <td>
            <select name="items[${idx}][satuan]" class="form-control" style="width:80px">${satuanOpts}</select>
        </td>
        <td>
            <input type="number" name="items[${idx}][harga_satuan]"
                   id="harga-${idx}" value="${data.harga_satuan||0}" min="0"
                   class="form-control" style="width:130px"
                   oninput="hitungSubtotal(${idx})">
        </td>
        <td>
            <input type="number" name="items[${idx}][diskon]"
                   id="diskon-${idx}" value="${data.diskon||0}" min="0" max="100"
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
        placeholder: 'Pilih atau cari nama alat...',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: '{{ route("api.inventory.index") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term ?? '', mode: 'sewa' }),
            processResults: data => ({ results: data.results }),
            cache: true,
        },
        templateResult:    templateInventory,
        templateSelection: templateInventorySelection,
    })
    .on('select2:open', function () {
        const searchInput = document.querySelector('.select2-container--open .select2-search__field');
        if (searchInput) searchInput.dispatchEvent(new Event('input', { bubbles: true }));
    })
    .on('select2:select', function (e) {
        const item = e.params.data;

        document.getElementById(`nama-alat-${idx}`).value = item.text || '';
        $(`#harga-${idx}`).val(item.harga_beli_terakhir || 0);
        $(`#qty-${idx}`).attr('max', item.stok_tersedia || 999);

        const kondisiSel = document.getElementById(`kondisi-${idx}`);
        if (item.stok_baru > 0) {
            kondisiSel.value = 'baru';
        } else if (item.stok_bekas > 0) {
            kondisiSel.value = 'bekas';
        }

        const $satSel = $(`#row-${idx} select[name="items[${idx}][satuan]"]`);
        if (item.satuan) {
            if ($satSel.find(`option[value="${item.satuan}"]`).length) {
                $satSel.val(item.satuan);
            } else {
                $satSel.append(new Option(item.satuan, item.satuan, true, true));
            }
        }

        const badgeClass = { ok:'stok-ok', low:'stok-low', zero:'stok-zero' }[item.stok_status] || 'stok-ok';
        $(`#stok-info-${idx}`).html(
            `<i class="ri-stack-line" style="font-size:12px;color:var(--text-muted)"></i>
             <span class="${badgeClass}">${item.stok_label}</span>
             <span style="color:var(--text-muted);font-size:11px">| Kategori: ${item.kategori||'-'}</span>`
        );
        hitungSubtotal(idx);
    })
    .on('select2:clear', function () {
        document.getElementById(`nama-alat-${idx}`).value = '';
        $(`#harga-${idx}`).val(0);
        $(`#stok-info-${idx}`).html('');
        $(`#qty-${idx}`).removeAttr('max');
        hitungSubtotal(idx);
    });

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
    const ongkir = parseInt(document.getElementById('biaya_ongkir')?.value)  || 0;
    const grand  = Math.max(0, total - diskon + ongkir);
    document.getElementById('r-subtotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('r-diskon').textContent   = 'Rp ' + diskon.toLocaleString('id-ID');
    document.getElementById('r-ongkir').textContent   = 'Rp ' + ongkir.toLocaleString('id-ID');
    document.getElementById('r-total').textContent    = 'Rp ' + grand.toLocaleString('id-ID');
}

// ── DROPZONE GENERIC (KTP - tanpa preview gambar) ──
function initDropzone(inputId, previewId, zoneId, maxMB) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const zone    = document.getElementById(zoneId);
    if (!input) return;
    input.addEventListener('change', () => showPreview(input, preview, zone, maxMB));
    zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', ()  => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => {
        e.preventDefault(); zone.classList.remove('drag-over');
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            showPreview(input, preview, zone, maxMB);
        }
    });
}
function showPreview(input, preview, zone, maxMB) {
    const file = input.files[0]; if (!file) return;
    preview.querySelector('.dropzone-preview-name').textContent = file.name;
    preview.querySelector('.dropzone-preview-size').textContent = (file.size / 1024).toFixed(1) + ' KB';
    preview.classList.add('show');
    zone.style.display = 'none';
}
function removeFile(inputId, previewId, zoneId) {
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).classList.remove('show');
    document.getElementById(zoneId).style.display = '';
}

// ── DROPZONE BUKTI PEMBAYARAN (jpg/png, maks 10 MB, dengan thumbnail) ──
const BUKTI_MAX_MB = 10;
const BUKTI_ALLOWED = ['image/jpeg', 'image/png'];

function initDropzoneBukti() {
    const input   = document.getElementById('bukti_pembayaran');
    const preview = document.getElementById('bukti-preview');
    const zone    = document.getElementById('dropzone-bukti');
    const errBox  = document.getElementById('bukti-error');
    const errText = document.getElementById('bukti-error-text');

    if (!input) return;

    function processFile(file) {
        errBox.style.display = 'none';

        if (!BUKTI_ALLOWED.includes(file.type)) {
            errText.textContent = 'Format file tidak didukung. Gunakan JPG atau PNG.';
            errBox.style.display = 'flex';
            input.value = '';
            return;
        }
        if (file.size > BUKTI_MAX_MB * 1024 * 1024) {
            errText.textContent = `Ukuran file melebihi batas maksimal ${BUKTI_MAX_MB} MB.`;
            errBox.style.display = 'flex';
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('bukti-preview-thumb').src = e.target.result;
        };
        reader.readAsDataURL(file);

        document.getElementById('bukti-preview-name').textContent = file.name;
        document.getElementById('bukti-preview-size').textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        preview.classList.add('show');
        zone.style.display = 'none';
    }

    input.addEventListener('change', () => {
        if (input.files[0]) processFile(input.files[0]);
    });

    zone.addEventListener('dragover',  e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('drag-over');
        if (e.dataTransfer.files.length) {
            // Assign ke input via DataTransfer
            const dt = new DataTransfer();
            dt.items.add(e.dataTransfer.files[0]);
            input.files = dt.files;
            processFile(e.dataTransfer.files[0]);
        }
    });
}

// Override removeFile untuk bukti (reset thumb juga)
function removeBuktiFile() {
    document.getElementById('bukti_pembayaran').value = '';
    document.getElementById('bukti-preview').classList.remove('show');
    document.getElementById('dropzone-bukti').style.display = '';
    document.getElementById('bukti-preview-thumb').src = '';
    document.getElementById('bukti-error').style.display = 'none';
}

// ── INIT ──
document.addEventListener('DOMContentLoaded', function () {
    initDropzone('foto_ktp_sim', 'ktp-preview', 'dropzone-ktp', 5);
    initDropzoneBukti();

    // Ganti tombol remove bukti agar pakai fungsi khusus
    document.querySelector('#bukti-preview .dropzone-preview-remove')
        .setAttribute('onclick', 'removeBuktiFile()');

    // Validasi client sebelum submit
    document.getElementById('form-penyewaan').addEventListener('submit', function(e) {
        const input = document.getElementById('bukti_pembayaran');
        if (input.files[0]) {
            const file = input.files[0];
            if (!BUKTI_ALLOWED.includes(file.type)) {
                e.preventDefault();
                document.getElementById('bukti-error-text').textContent = 'Format file tidak didukung. Gunakan JPG atau PNG.';
                document.getElementById('bukti-error').style.display = 'flex';
                return;
            }
            if (file.size > BUKTI_MAX_MB * 1024 * 1024) {
                e.preventDefault();
                document.getElementById('bukti-error-text').textContent = `Ukuran file melebihi batas maksimal ${BUKTI_MAX_MB} MB.`;
                document.getElementById('bukti-error').style.display = 'flex';
                return;
            }
        }
    });

    @if(old('items'))
        @foreach(old('items') as $i => $oldItem)
            addRow({
                inventory_id: '{{ $oldItem["inventory_id"] ?? "" }}',
                nama_alat:    '{{ addslashes($oldItem["nama_alat"] ?? "") }}',
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

    if ('{{ old("pengiriman") }}') updatePengirimanNote();
    if ('{{ old("metode_pembayaran") }}') updateMetodeInfo();
});
</script>
@endpush