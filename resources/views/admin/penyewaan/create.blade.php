{{-- resources/views/admin/penyewaan/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Penyewaan')
@section('breadcrumb', 'Tambah Penyewaan')

@push('styles')
<style>
    .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; max-width:820px; }
    .form-section { padding:20px 24px; border-bottom:1px solid var(--border); }
    .form-section:last-child { border-bottom:none; }
    .section-title { font-size:13px; font-weight:700; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.6px; margin-bottom:16px; display:flex; align-items:center; gap:8px; }
    .section-title i { font-size:16px; color:var(--brand-500); }
    .form-grid { display:grid; gap:16px; }
    .form-grid-2 { grid-template-columns:1fr 1fr; }
    .form-grid-3 { grid-template-columns:1fr 1fr 1fr; }
    @media(max-width:640px) { .form-grid-2, .form-grid-3 { grid-template-columns:1fr; } }
    .form-group { display:flex; flex-direction:column; gap:6px; }
    .form-label { font-size:13px; font-weight:600; color:var(--text-primary); }
    .form-label .required { color:#EF4444; margin-left:2px; }
    .form-label .hint { font-weight:400; color:var(--text-muted); font-size:12px; margin-left:4px; }
    .form-control { width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13.5px; background:var(--bg-primary); color:var(--text-primary); outline:none; transition:border-color 0.2s, box-shadow 0.2s; font-family:var(--font); box-sizing:border-box; }
    .form-control:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .form-control.is-invalid { border-color:#EF4444; }
    .form-control.is-invalid:focus { box-shadow:0 0 0 3px rgba(239,68,68,0.1); }
    textarea.form-control { resize:vertical; min-height:80px; }
    select.form-control { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 12px center; padding-right:36px; }
    .invalid-feedback { font-size:12px; color:#EF4444; display:flex; align-items:center; gap:4px; }
    .file-upload-area { display:flex; flex-direction:column; gap:6px; }
    .file-upload-label { display:flex; align-items:center; gap:10px; padding:10px 14px; border:1.5px dashed var(--border); border-radius:8px; cursor:pointer; transition:all 0.2s; background:var(--bg-primary); }
    .file-upload-label:hover { border-color:var(--brand-500); background:var(--brand-50); }
    .file-upload-label i { font-size:20px; color:var(--brand-500); flex-shrink:0; }
    .file-upload-text { font-size:13px; color:var(--text-secondary); }
    .file-upload-text strong { color:var(--text-primary); display:block; font-size:13px; }
    .file-name-preview { font-size:12px; color:var(--brand-500); display:flex; align-items:center; gap:4px; display:none; }
    .form-footer { padding:16px 24px; display:flex; gap:12px; justify-content:flex-end; background:var(--bg-primary); border-top:1px solid var(--border); }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 18px; height:40px; border-radius:8px; font-size:13.5px; font-weight:600; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; }
    .btn-cancel { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-cancel:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-save { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-save:hover { background:var(--brand-600); border-color:var(--brand-600); }

    /* Radio group untuk pengiriman */
    .radio-group { display:flex; gap:10px; }
    .radio-card { flex:1; display:flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid var(--border); border-radius:8px; cursor:pointer; transition:all 0.2s; background:var(--bg-primary); }
    .radio-card:has(input:checked) { border-color:var(--brand-500); background:var(--brand-50); }
    html.dark .radio-card:has(input:checked) { background:rgba(29,111,164,0.12); }
    .radio-card input[type="radio"] { accent-color:var(--brand-500); width:15px; height:15px; flex-shrink:0; }
    .radio-card-label { font-size:13px; font-weight:500; color:var(--text-primary); cursor:pointer; }
    .radio-card-sub { font-size:11.5px; color:var(--text-muted); }
</style>
@endpush

@section('content')

{{-- Back header --}}
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
    <form action="{{ route('penyewaan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- SECTION: Data Penyewa --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-user-line"></i> Data Penyewa</div>
            <div class="form-grid form-grid-2">

                {{-- Nama --}}
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

                {{-- Nomor Telepon --}}
                <div class="form-group">
                    <label class="form-label">Nomor Telepon/HP <span class="required">*</span></label>
                    <input type="tel" name="nomor_telepon"
                           value="{{ old('nomor_telepon') }}"
                           placeholder="Contoh: 08123456789"
                           class="form-control {{ $errors->has('nomor_telepon') ? 'is-invalid' : '' }}"
                           required>
                    @error('nomor_telepon')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Alamat --}}
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

        {{-- SECTION: Detail Sewa --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-stethoscope-line"></i> Detail Sewa</div>
            <div class="form-grid form-grid-2">

                {{-- Produk Alkes --}}
                <div class="form-group">
                    <label class="form-label">Produk Alat Kesehatan <span class="required">*</span></label>
                    <input type="text" name="produk_alkes"
                           value="{{ old('produk_alkes') }}"
                           placeholder="Nama alat kesehatan yang disewa"
                           class="form-control {{ $errors->has('produk_alkes') ? 'is-invalid' : '' }}"
                           required>
                    @error('produk_alkes')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Durasi --}}
                <div class="form-group">
                    <label class="form-label">Durasi <span class="required">*</span> <span class="hint">(dalam Hari)</span></label>
                    <input type="number" name="durasi_hari"
                           value="{{ old('durasi_hari', 1) }}"
                           min="1" placeholder="Contoh: 7"
                           class="form-control {{ $errors->has('durasi_hari') ? 'is-invalid' : '' }}"
                           required>
                    @error('durasi_hari')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Pengiriman --}}
                <div class="form-group">
                    <label class="form-label">Pengiriman <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-card">
                            <input type="radio" name="pengiriman_ditanggung_pelanggan" value="1"
                                   {{ old('pengiriman_ditanggung_pelanggan', '1') == '1' ? 'checked' : '' }}>
                            <div>
                                <div class="radio-card-label">Ditanggung Pelanggan</div>
                                <div class="radio-card-sub">Ongkir bayar penyewa</div>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="pengiriman_ditanggung_pelanggan" value="0"
                                   {{ old('pengiriman_ditanggung_pelanggan') == '0' ? 'checked' : '' }}>
                            <div>
                                <div class="radio-card-label">Ditanggung Toko</div>
                                <div class="radio-card-sub">Ongkir gratis</div>
                            </div>
                        </label>
                    </div>
                    @error('pengiriman_ditanggung_pelanggan')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Biaya Ongkir --}}
                <div class="form-group">
                    <label class="form-label">Biaya Ongkir <span class="hint">(Rp, isi 0 jika gratis)</span></label>
                    <input type="number" name="biaya_ongkir"
                           value="{{ old('biaya_ongkir', 0) }}"
                           min="0" placeholder="0"
                           class="form-control {{ $errors->has('biaya_ongkir') ? 'is-invalid' : '' }}">
                    @error('biaya_ongkir')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        {{-- SECTION: Pembayaran --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-bank-card-line"></i> Pembayaran</div>
            <div class="form-grid form-grid-2">

                {{-- Metode Pembayaran --}}
                <div class="form-group">
                    <label class="form-label">Metode Pembayaran <span class="required">*</span></label>
                    <select name="metode_pembayaran"
                            class="form-control {{ $errors->has('metode_pembayaran') ? 'is-invalid' : '' }}"
                            required>
                        <option value="" disabled {{ old('metode_pembayaran') ? '' : 'selected' }}>-- Pilih metode --</option>
                        @foreach(['Tunai', 'Transfer Bank', 'QRIS', 'OVO', 'GoPay', 'Dana', 'ShopeePay', 'Lainnya'] as $metode)
                            <option value="{{ $metode }}" {{ old('metode_pembayaran') == $metode ? 'selected' : '' }}>
                                {{ $metode }}
                            </option>
                        @endforeach
                    </select>
                    @error('metode_pembayaran')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Status (hanya Berjalan saat create) --}}
                <div class="form-group">
                    <label class="form-label">Status <span class="required">*</span></label>
                    <select name="status"
                            class="form-control"
                            disabled>
                        <option value="berjalan" selected>Berjalan</option>
                    </select>
                    {{-- Hidden karena disabled tidak terkirim --}}
                    <input type="hidden" name="status" value="berjalan">
                    <span style="font-size:12px; color:var(--text-muted); margin-top:4px; display:flex; align-items:center; gap:4px;">
                        <i class="ri-information-line"></i> Status hanya dapat diubah saat edit
                    </span>
                </div>

                {{-- Bukti Pembayaran --}}
                <div class="form-group">
                    <label class="form-label">Bukti Pembayaran <span class="hint">(jpg/png/pdf, maks 2MB)</span></label>
                    <div class="file-upload-area">
                        <label class="file-upload-label" for="bukti_pembayaran">
                            <i class="ri-image-add-line"></i>
                            <div class="file-upload-text">
                                <strong>Klik untuk upload</strong>
                                JPG, PNG, atau PDF
                            </div>
                        </label>
                        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran"
                               accept=".jpg,.jpeg,.png,.pdf" style="display:none;"
                               onchange="showFileName(this, 'bukti-name')">
                        <span class="file-name-preview" id="bukti-name">
                            <i class="ri-file-check-line"></i> <span></span>
                        </span>
                    </div>
                    @error('bukti_pembayaran')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Foto KTP/SIM --}}
                <div class="form-group">
                    <label class="form-label">Foto KTP / SIM <span class="hint">(jpg/png, maks 2MB)</span></label>
                    <div class="file-upload-area">
                        <label class="file-upload-label" for="foto_ktp_sim">
                            <i class="ri-id-card-line"></i>
                            <div class="file-upload-text">
                                <strong>Klik untuk upload</strong>
                                JPG atau PNG
                            </div>
                        </label>
                        <input type="file" id="foto_ktp_sim" name="foto_ktp_sim"
                               accept=".jpg,.jpeg,.png" style="display:none;"
                               onchange="showFileName(this, 'ktp-name')">
                        <span class="file-name-preview" id="ktp-name">
                            <i class="ri-file-check-line"></i> <span></span>
                        </span>
                    </div>
                    @error('foto_ktp_sim')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        {{-- SECTION: Keterangan --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-file-text-line"></i> Keterangan</div>
            <div class="form-group">
                <label class="form-label">Keterangan <span class="hint">(opsional)</span></label>
                <textarea name="keterangan" rows="3"
                          placeholder="Catatan tambahan mengenai penyewaan ini"
                          class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- FOOTER BUTTONS --}}
        <div class="form-footer">
            <a href="{{ route('penyewaan.index') }}" class="btn btn-cancel">
                <i class="ri-close-line"></i> Batal
            </a>
            <button type="submit" class="btn btn-save">
                <i class="ri-save-line"></i> Simpan Data
            </button>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
    function showFileName(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            preview.style.display = 'flex';
            preview.querySelector('span').textContent = input.files[0].name;
        } else {
            preview.style.display = 'none';
        }
    }
</script>
@endpush