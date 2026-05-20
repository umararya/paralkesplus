{{-- resources/views/admin/penyewaan/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Penyewaan')
@section('breadcrumb', 'Tambah Penyewaan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; max-width:900px; }
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

    /* Date range */
    .date-range-wrap { display:flex; align-items:center; gap:8px; }
    .date-range-wrap .form-control { flex:1; }
    .date-range-sep { font-size:13px; color:var(--text-muted); white-space:nowrap; }
    .durasi-display { margin-top:6px; font-size:12.5px; color:var(--brand-500); font-weight:600; display:flex; align-items:center; gap:5px; min-height:18px; }
    .pengiriman-note { margin-top:5px; font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:4px; min-height:16px; }
    .metode-info { margin-top:5px; font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:4px; min-height:16px; }

    /* Drag & Drop Upload */
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

    /* ── TABEL DETAIL ITEM ── */
    .items-table { width:100%; border-collapse:collapse; margin-bottom:10px; }
    .items-table thead tr { background:var(--brand-500); color:#fff; }
    .items-table th { padding:9px 10px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; white-space:nowrap; }
    .items-table td { padding:7px 6px; border-bottom:1px solid var(--border); vertical-align:middle; }
    .items-table tbody tr:nth-child(even) td { background:var(--bg-hover); }
    .items-table .form-control { padding:7px 10px; font-size:13px; }
    .subtotal-cell { font-size:13px; font-weight:600; color:var(--brand-500); white-space:nowrap; min-width:100px; text-align:right; }
    .btn-remove-row { width:28px; height:28px; border-radius:6px; background:rgba(239,68,68,0.1); color:#EF4444; border:none; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:14px; transition:background 0.2s; }
    .btn-remove-row:hover { background:rgba(239,68,68,0.2); }
    .btn-add-row { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; border:1.5px dashed var(--brand-500); background:transparent; color:var(--brand-500); font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; font-family:var(--font); }
    .btn-add-row:hover { background:var(--brand-50); }
    html.dark .btn-add-row:hover { background:rgba(29,111,164,0.1); }
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
</style>
@endpush

@section('content')

<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('penyewaan.index') }}"
       style="display:inline-flex; align-items:center; justify-content:center;
              width:36px; height:36px; border-radius:8px; background:var(--bg-card);
              border:1px solid var(--border); color:var(--text-secondary);
              text-decoration:none; transition:all 0.2s;"
       onmouseover="this.style.background='var(--bg-hover)'"
       onmouseout="this.style.background='var(--bg-card)'">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div>
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">Tambah Penyewaan</h1>
        <p style="font-size:13px; color:var(--text-muted);">Isi form berikut untuk menambah data penyewaan baru</p>
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
                    <input type="text" name="nama_penyewa"
                           value="{{ old('nama_penyewa') }}"
                           placeholder="Nama lengkap penyewa"
                           class="form-control {{ $errors->has('nama_penyewa') ? 'is-invalid' : '' }}"
                           required>
                    @error('nama_penyewa')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Telepon/HP <span class="required">*</span></label>
                    <input type="tel" name="nomor_telepon"
                           value="{{ old('nomor_telepon') }}"
                           placeholder="08xxxxxxxxxx"
                           class="form-control {{ $errors->has('nomor_telepon') ? 'is-invalid' : '' }}"
                           required>
                    @error('nomor_telepon')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- ── BARU: Tempat/Tanggal Lahir ── --}}
                <div class="form-group">
                    <label class="form-label">
                        Tempat/Tanggal Lahir
                        <span class="hint">(untuk formulir perjanjian)</span>
                    </label>
                    <input type="text" name="tempat_tanggal_lahir"
                           value="{{ old('tempat_tanggal_lahir') }}"
                           placeholder="Contoh: Semarang, 01 Januari 1990"
                           class="form-control {{ $errors->has('tempat_tanggal_lahir') ? 'is-invalid' : '' }}">
                    @error('tempat_tanggal_lahir')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- ── BARU: Nomor KTP ── --}}
                <div class="form-group">
                    <label class="form-label">
                        Nomor KTP (NIK)
                        <span class="hint">(16 digit, untuk formulir perjanjian)</span>
                    </label>
                    <input type="text" name="nomor_ktp"
                           value="{{ old('nomor_ktp') }}"
                           placeholder="3374xxxxxxxxxxxxxxx"
                           maxlength="16"
                           inputmode="numeric"
                           pattern="[0-9]{16}"
                           class="form-control {{ $errors->has('nomor_ktp') ? 'is-invalid' : '' }}"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    @error('nomor_ktp')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Alamat Penyewa <span class="required">*</span></label>
                    <textarea name="alamat_penyewa" rows="3"
                              placeholder="Alamat lengkap penyewa"
                              class="form-control {{ $errors->has('alamat_penyewa') ? 'is-invalid' : '' }}"
                              required>{{ old('alamat_penyewa') }}</textarea>
                    @error('alamat_penyewa')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ===================== DETAIL ITEM SEWA ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-stethoscope-line"></i> Item Penyewaan</div>

            <div style="overflow-x:auto;">
                <table class="items-table" id="items-table">
                    <thead>
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>Nama Alat</th>
                            <th style="width:70px;">Qty</th>
                            <th style="width:80px;">Satuan</th>
                            <th style="width:130px;">Harga/Satuan (Rp)</th>
                            <th style="width:70px;">Diskon (%)</th>
                            <th style="width:120px; text-align:right;">Subtotal</th>
                            <th style="width:36px;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        {{-- Rows diisi via JS --}}
                    </tbody>
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
                               value="{{ old('tgl_mulai') }}"
                               placeholder="Tanggal Mulai"
                               class="form-control {{ $errors->has('tgl_mulai') ? 'is-invalid' : '' }}"
                               autocomplete="off" readonly required>
                        <span class="date-range-sep"><i class="ri-arrow-right-line"></i></span>
                        <input type="text" id="tgl_selesai" name="tgl_selesai"
                               value="{{ old('tgl_selesai') }}"
                               placeholder="Tanggal Selesai"
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
                    @error('durasi_hari')
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
                            <option value="mandiri" {{ old('pengiriman') == 'mandiri' ? 'selected' : '' }}>Ambil dan Antar kembali sendiri oleh Penyewa</option>
                            <option value="Gosend / GrabExpress" {{ old('pengiriman') == 'Gosend / GrabExpress' ? 'selected' : '' }}>via Gosend / GrabExpress</option>
                            <option value="Rental Mobil Paralkes" {{ old('pengiriman') == 'Rental Mobil Paralkes' ? 'selected' : '' }}>via Rental Mobil Paralkes</option>
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
                               value="{{ old('biaya_ongkir', 0) }}"
                               min="0" placeholder="0"
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
                        <option value="" disabled {{ old('metode_pembayaran') ? '' : 'selected' }}>-- Pilih metode pembayaran --</option>
                        <option value="Tunai / Cash" {{ old('metode_pembayaran') == 'Tunai / Cash' ? 'selected' : '' }}>Tunai / Cash</option>
                        <option value="Transfer via Bank BCA" {{ old('metode_pembayaran') == 'Transfer via Bank BCA' ? 'selected' : '' }}>Transfer via Bank BCA 8030910754 a.n. SURYA DAYYANA</option>
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
                    <label class="form-label">Diskon Global <span class="hint">(Rp, potongan di luar diskon per item)</span></label>
                    <input type="number" name="diskon_global" id="diskon_global"
                           value="{{ old('diskon_global', 0) }}"
                           min="0" placeholder="0"
                           class="form-control {{ $errors->has('diskon_global') ? 'is-invalid' : '' }}"
                           oninput="hitungRingkasan()">
                    @error('diskon_global')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status <span class="required">*</span></label>
                    <select name="status_display" class="form-control" disabled>
                        <option value="berjalan" selected>Berjalan</option>
                    </select>
                    <input type="hidden" name="status" value="berjalan">
                    <span style="font-size:12px; color:var(--text-muted); margin-top:4px; display:flex; align-items:center; gap:4px;">
                        <i class="ri-information-line"></i> Status hanya dapat diubah saat edit
                    </span>
                </div>

                <div class="form-group">
                    <label class="form-label">Bukti Pembayaran <span class="hint">(foto / link drive / bayar ditempat)</span></label>
                    <input type="text" name="bukti_pembayaran"
                           value="{{ old('bukti_pembayaran') }}"
                           placeholder="https://drive.google.com/... atau 'bayar ditempat'"
                           class="form-control {{ $errors->has('bukti_pembayaran') ? 'is-invalid' : '' }}">
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
            <button type="submit" class="btn btn-save">
                <i class="ri-save-line"></i> Simpan Penyewaan
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
// ── Flatpickr date range ──
const fpMulai = flatpickr('#tgl_mulai', {
    locale: 'id',
    dateFormat: 'Y-m-d',
    allowInput: false,
    onChange: function(sel, str) {
        fpSelesai.set('minDate', str);
        hitungDurasi();
    }
});
const fpSelesai = flatpickr('#tgl_selesai', {
    locale: 'id',
    dateFormat: 'Y-m-d',
    allowInput: false,
    onChange: function() { hitungDurasi(); }
});

function hitungDurasi() {
    const mulai   = document.getElementById('tgl_mulai').value;
    const selesai = document.getElementById('tgl_selesai').value;
    const disp    = document.getElementById('durasi-display');
    const hidden  = document.getElementById('durasi_hari');

    if (mulai && selesai) {
        const d1   = new Date(mulai);
        const d2   = new Date(selesai);
        const diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
        if (diff >= 0) {
            hidden.value    = diff;
            disp.innerHTML  = `<i class="ri-calendar-check-line"></i> ${diff} hari`;
        } else {
            hidden.value   = '';
            disp.innerHTML = '';
        }
    } else {
        hidden.value   = '';
        disp.innerHTML = '';
    }
}

// ── Pengiriman note ──
function updatePengirimanNote() {
    const val  = document.getElementById('pengiriman').value;
    const text = document.getElementById('pengiriman-note-text');
    const notes = {
        'mandiri':               'Penyewa mengambil dan mengembalikan sendiri.',
        'Gosend / GrabExpress':  'Pengiriman via ojek online, biaya ditanggung penyewa.',
        'Rental Mobil Paralkes': 'Pengiriman menggunakan Rental Mobil Paralkes.',
    };
    text.textContent = notes[val] || 'Pilih metode pengiriman di atas';
}

// ── Metode info ──
function updateMetodeInfo() {
    const val  = document.getElementById('metode_pembayaran').value;
    const text = document.getElementById('metode-info-text');
    const info = {
        'Tunai / Cash':         'Pembayaran dilakukan secara tunai saat penyerahan alat.',
        'Transfer via Bank BCA': 'Transfer ke BCA 8030910754 a.n. SURYA DAYYANA.',
    };
    text.textContent = info[val] || 'Pilih metode pembayaran';
}

// ── Items table ──
let rowIndex = 0;

function addRow(data = {}) {
    rowIndex++;
    const tbody = document.getElementById('items-body');
    const tr    = document.createElement('tr');
    tr.id       = `row-${rowIndex}`;

    const satuanOpts = ['pcs','unit','set','buah','pasang'].map(s =>
        `<option value="${s}" ${(data.satuan||'pcs')===s?'selected':''}>${s}</option>`
    ).join('');

    tr.innerHTML = `
        <td style="text-align:center; font-size:12px; color:var(--text-muted);">${rowIndex}</td>
        <td><input type="text" name="items[${rowIndex}][nama_alat]"
                   value="${data.nama_alat||''}"
                   placeholder="Nama alat kesehatan"
                   class="form-control" required
                   oninput="hitungSubtotal(${rowIndex})"></td>
        <td><input type="number" name="items[${rowIndex}][qty]"
                   value="${data.qty||1}" min="1"
                   class="form-control" style="text-align:center;"
                   oninput="hitungSubtotal(${rowIndex})"></td>
        <td><select name="items[${rowIndex}][satuan]" class="form-control">${satuanOpts}</select></td>
        <td><input type="number" name="items[${rowIndex}][harga_satuan]"
                   value="${data.harga_satuan||0}" min="0"
                   class="form-control"
                   oninput="hitungSubtotal(${rowIndex})"></td>
        <td><input type="number" name="items[${rowIndex}][diskon]"
                   value="${data.diskon||0}" min="0" max="100"
                   class="form-control" style="text-align:center;"
                   oninput="hitungSubtotal(${rowIndex})"></td>
        <td class="subtotal-cell" id="subtotal-${rowIndex}">Rp 0</td>
        <td style="text-align:center;">
            <button type="button" class="btn-remove-row" onclick="removeRow(${rowIndex})">
                <i class="ri-delete-bin-line"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    hitungSubtotal(rowIndex);
}

function removeRow(idx) {
    const tbody = document.getElementById('items-body');
    if (tbody.rows.length <= 1) return; // minimal 1 row
    document.getElementById(`row-${idx}`)?.remove();
    hitungRingkasan();
}

function hitungSubtotal(idx) {
    const row    = document.getElementById(`row-${idx}`);
    if (!row) return;
    const qty    = parseFloat(row.querySelector(`[name="items[${idx}][qty]"]`)?.value) || 0;
    const harga  = parseFloat(row.querySelector(`[name="items[${idx}][harga_satuan]"]`)?.value) || 0;
    const diskon = parseFloat(row.querySelector(`[name="items[${idx}][diskon]"]`)?.value) || 0;
    const sub    = Math.round(qty * harga * (1 - diskon / 100));

    const cell = document.getElementById(`subtotal-${idx}`);
    if (cell) cell.textContent = 'Rp ' + sub.toLocaleString('id-ID');
    hitungRingkasan();
}

function hitungRingkasan() {
    let total = 0;
    document.querySelectorAll('[id^="subtotal-"]').forEach(cell => {
        const val = parseInt(cell.textContent.replace(/[^0-9]/g, '')) || 0;
        total += val;
    });

    const diskon = parseInt(document.getElementById('diskon_global')?.value) || 0;
    const ongkir = parseInt(document.getElementById('biaya_ongkir')?.value) || 0;
    const grand  = Math.max(0, total - diskon + ongkir);

    document.getElementById('r-subtotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('r-diskon').textContent   = 'Rp ' + diskon.toLocaleString('id-ID');
    document.getElementById('r-ongkir').textContent   = 'Rp ' + ongkir.toLocaleString('id-ID');
    document.getElementById('r-total').textContent    = 'Rp ' + grand.toLocaleString('id-ID');
}

// ── Dropzone ──
function initDropzone(inputId, previewId, zoneId) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const zone    = document.getElementById(zoneId);
    if (!input) return;

    input.addEventListener('change', () => showPreview(input, preview, zone));
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('drag-over');
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            showPreview(input, preview, zone);
        }
    });
}

function showPreview(input, preview, zone) {
    const file = input.files[0];
    if (!file) return;
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

// ── Init ──
document.addEventListener('DOMContentLoaded', function () {
    initDropzone('foto_ktp_sim', 'ktp-preview', 'dropzone-ktp');

    // Restore old items (jika validasi gagal)
    @if(old('items'))
        @foreach(old('items') as $oldItem)
            addRow({
                nama_alat:    '{{ addslashes($oldItem['nama_alat'] ?? '') }}',
                qty:          {{ $oldItem['qty'] ?? 1 }},
                satuan:       '{{ $oldItem['satuan'] ?? 'pcs' }}',
                harga_satuan: {{ $oldItem['harga_satuan'] ?? 0 }},
                diskon:       {{ $oldItem['diskon'] ?? 0 }},
            });
        @endforeach
    @else
        addRow(); // 1 row default
    @endif

    // Restore notes jika ada old value
    if ('{{ old('pengiriman') }}') updatePengirimanNote();
    if ('{{ old('metode_pembayaran') }}') updateMetodeInfo();
});
</script>
@endpush