{{-- resources/views/admin/penyewaan/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Penyewaan')
@section('breadcrumb', 'Tambah Penyewaan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; max-width:860px; }
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

    /* Checkbox alkes */
    .checkbox-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:8px; }
    .checkbox-item { display:flex; align-items:center; gap:8px; padding:8px 12px; border:1px solid var(--border); border-radius:8px; cursor:pointer; transition:all 0.2s; background:var(--bg-primary); }
    .checkbox-item:has(input:checked) { border-color:var(--brand-500); background:var(--brand-50); }
    html.dark .checkbox-item:has(input:checked) { background:rgba(29,111,164,0.12); }
    .checkbox-item input[type="checkbox"] { accent-color:var(--brand-500); width:15px; height:15px; flex-shrink:0; }
    .checkbox-item-label { font-size:13px; font-weight:500; color:var(--text-primary); cursor:pointer; line-height:1.3; }

    /* Date range */
    .date-range-wrap { display:flex; align-items:center; gap:8px; }
    .date-range-wrap .form-control { flex:1; }
    .date-range-sep { font-size:13px; color:var(--text-muted); white-space:nowrap; }
    .durasi-display { margin-top:6px; font-size:12.5px; color:var(--brand-500); font-weight:600; display:flex; align-items:center; gap:5px; min-height:18px; }

    /* Pengiriman note */
    .pengiriman-note { margin-top:5px; font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:4px; min-height:16px; }

    /* Metode pembayaran info */
    .metode-info { margin-top:5px; font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:4px; min-height:16px; }

    /* === Drag & Drop Upload === */
    .dropzone {
        position: relative;
        border: 2px dashed var(--border);
        border-radius: 8px;
        padding: 24px 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--bg-primary);
        outline: none;
    }
    .dropzone:hover,
    .dropzone.drag-over {
        border-color: var(--brand-500);
        background: var(--brand-50);
    }
    html.dark .dropzone:hover,
    html.dark .dropzone.drag-over {
        background: rgba(29,111,164,0.08);
    }
    .dropzone input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }
    .dropzone-icon { font-size: 28px; color: var(--brand-500); margin-bottom: 8px; display: block; }
    .dropzone-title { font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 2px; }
    .dropzone-sub { font-size: 12px; color: var(--text-muted); }
    .dropzone-preview {
        display: none;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: var(--brand-50);
        border: 1px solid var(--brand-100);
        border-radius: 8px;
        margin-top: 8px;
    }
    html.dark .dropzone-preview {
        background: rgba(29,111,164,0.1);
        border-color: rgba(29,111,164,0.25);
    }
    .dropzone-preview.show { display: flex; }
    .dropzone-preview i { font-size: 18px; color: var(--brand-500); flex-shrink: 0; }
    .dropzone-preview-name { font-size: 12.5px; color: var(--text-primary); font-weight: 500; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dropzone-preview-size { font-size: 11.5px; color: var(--text-muted); white-space: nowrap; }
    .dropzone-preview-remove {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: rgba(239,68,68,0.1);
        color: #EF4444;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 13px; cursor: pointer; flex-shrink: 0;
        border: none;
        transition: background 0.2s;
    }
    .dropzone-preview-remove:hover { background: rgba(239,68,68,0.2); }

    .form-footer { padding:16px 24px; display:flex; gap:12px; justify-content:flex-end; background:var(--bg-primary); border-top:1px solid var(--border); }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 18px; height:40px; border-radius:8px; font-size:13.5px; font-weight:600; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; }
    .btn-cancel { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-cancel:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-save { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-save:hover { background:var(--brand-600); border-color:var(--brand-600); }
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

        {{-- ===================== SECTION: DATA PENYEWA ===================== --}}
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

        {{-- ===================== SECTION: DETAIL SEWA ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-stethoscope-line"></i> Detail Sewa</div>
            <div class="form-grid">

                {{-- Produk Alkes (Checkbox) --}}
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Produk Alat Kesehatan <span class="required">*</span></label>
                    @php
                        $daftarAlkes = [
                            'Nebulizer',
                            'Kursi Roda Travel',
                            'Kursi Roda Standar',
                            'Bed Pasien',
                            'Tabung Oksigen Kecil',
                            'Tabung Oksigen Besar',
                            'Regulator Oksigen',
                            'Walker / Alat Bantu Jalan',
                            'Tongkat Ketiak (Kruk)',
                            'Pompa ASI',
                            'Breast Pump Elektrik',
                            'Tens / Stimulator Otot',
                            'Commode Chair',
                            'Trapeze Bar',
                            'Lainnya',
                        ];
                        $oldAlkes = old('produk_alkes', []);
                    @endphp
                    <div class="checkbox-grid">
                        @foreach($daftarAlkes as $alkes)
                        <label class="checkbox-item">
                            <input type="checkbox" name="produk_alkes[]" value="{{ $alkes }}"
                                   {{ in_array($alkes, $oldAlkes) ? 'checked' : '' }}>
                            <span class="checkbox-item-label">{{ $alkes }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('produk_alkes')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                    @error('produk_alkes.*')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Durasi (Date Range) --}}
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Durasi <span class="required">*</span> <span class="hint">(Pilih tanggal mulai &amp; selesai)</span></label>
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

                    {{-- Pengiriman --}}
                    <div class="form-group">
                        <label class="form-label">Pengiriman <span class="required">*</span> <span class="hint">(ditanggung Pelanggan)</span></label>
                        <select name="pengiriman" id="pengiriman"
                                class="form-control {{ $errors->has('pengiriman') ? 'is-invalid' : '' }}"
                                onchange="updatePengirimanNote()" required>
                            <option value="" disabled {{ old('pengiriman') ? '' : 'selected' }}>-- Pilih metode pengiriman --</option>
                            <option value="mandiri" {{ old('pengiriman') == 'mandiri' ? 'selected' : '' }}>
                                Ambil dan Antar kembali sendiri oleh Penyewa
                            </option>
                            <option value="Gosend / GrabExpress" {{ old('pengiriman') == 'Gosend / GrabExpress' ? 'selected' : '' }}>
                                via Gosend / GrabExpress (barang dibawah 2 Kg, ex. Nebulizer / Kursi Roda Travel / sejenisnya)
                            </option>
                            <option value="Rental Mobil Paralkes" {{ old('pengiriman') == 'Rental Mobil Paralkes' ? 'selected' : '' }}>
                                via Rental Mobil Paralkes (disarankan untuk Bed Pasien / Tabung Oksigen Besar / sejenisnya)
                            </option>
                        </select>
                        <div class="pengiriman-note" id="pengiriman-note">
                            <i class="ri-information-line"></i>
                            <span id="pengiriman-note-text">Pilih metode pengiriman di atas</span>
                        </div>
                        @error('pengiriman')
                            <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Biaya Ongkir --}}
                    <div class="form-group">
                        <label class="form-label">Biaya Ongkir <span class="hint">(Rp, isi 0 jika Ambil Sendiri oleh Penyewa)</span></label>
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
        </div>

        {{-- ===================== SECTION: PEMBAYARAN ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-bank-card-line"></i> Pembayaran</div>
            <div class="form-grid form-grid-2">

                {{-- Metode Pembayaran --}}
                <div class="form-group">
                    <label class="form-label">Metode Pembayaran <span class="required">*</span></label>
                    <select name="metode_pembayaran" id="metode_pembayaran"
                            class="form-control {{ $errors->has('metode_pembayaran') ? 'is-invalid' : '' }}"
                            onchange="updateMetodeInfo()" required>
                        <option value="" disabled {{ old('metode_pembayaran') ? '' : 'selected' }}>-- Pilih metode pembayaran --</option>
                        <option value="Tunai / Cash" {{ old('metode_pembayaran') == 'Tunai / Cash' ? 'selected' : '' }}>
                            Tunai / Cash
                        </option>
                        <option value="Transfer via Bank BCA" {{ old('metode_pembayaran') == 'Transfer via Bank BCA' ? 'selected' : '' }}>
                            Transfer via Bank BCA 8030910754 a.n. SURYA DAYYANA
                        </option>
                    </select>
                    <div class="metode-info" id="metode-info">
                        <i class="ri-information-line"></i>
                        <span id="metode-info-text">Pilih metode pembayaran</span>
                    </div>
                    @error('metode_pembayaran')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Status (locked on create) --}}
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

                {{-- Bukti Pembayaran (teks/link) --}}
                <div class="form-group">
                    <label class="form-label">Bukti Pembayaran <span class="hint">(foto / link drive / bayar ditempat)</span></label>
                    <input type="text" name="bukti_pembayaran"
                           value="{{ old('bukti_pembayaran') }}"
                           placeholder="Contoh: https://drive.google.com/... atau 'bayar ditempat'"
                           class="form-control {{ $errors->has('bukti_pembayaran') ? 'is-invalid' : '' }}">
                    @error('bukti_pembayaran')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Foto KTP / SIM (Drag & Drop) --}}
                <div class="form-group">
                    <label class="form-label">Foto KTP / SIM <span class="hint">(jpg/png/pdf, maks 5MB)</span></label>

                    <div class="dropzone" id="dropzone-ktp"
                         tabindex="0"
                         role="button"
                         aria-label="Upload Foto KTP atau SIM"
                         onkeydown="if(event.key==='Enter'||event.key===' ')this.querySelector('input').click()">
                        <input type="file"
                               id="foto_ktp_sim"
                               name="foto_ktp_sim"
                               accept=".jpg,.jpeg,.png,.pdf"
                               aria-hidden="true">
                        <i class="ri-id-card-line dropzone-icon"></i>
                        <div class="dropzone-title">Klik atau seret file ke sini</div>
                        <div class="dropzone-sub">JPG, PNG, atau PDF &mdash; maks 5 MB</div>
                    </div>

                    <div class="dropzone-preview" id="ktp-preview">
                        <i class="ri-file-check-line"></i>
                        <span class="dropzone-preview-name" id="ktp-preview-name"></span>
                        <span class="dropzone-preview-size" id="ktp-preview-size"></span>
                        <button type="button" class="dropzone-preview-remove"
                                onclick="removeFile('foto_ktp_sim', 'ktp-preview', 'dropzone-ktp')"
                                title="Hapus file">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>

                    @error('foto_ktp_sim')
                        <span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        {{-- ===================== SECTION: KETERANGAN ===================== --}}
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

        {{-- FOOTER --}}
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    // ============================================================
    // DATE RANGE PICKER
    // ============================================================
    const fpMulai = flatpickr("#tgl_mulai", {
        locale: "id",
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function(selectedDates, dateStr) {
            fpSelesai.set('minDate', dateStr);
            hitungDurasi();
        }
    });

    const fpSelesai = flatpickr("#tgl_selesai", {
        locale: "id",
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function() {
            hitungDurasi();
        }
    });

    function hitungDurasi() {
        const mulai      = document.getElementById('tgl_mulai').value;
        const selesai    = document.getElementById('tgl_selesai').value;
        const display    = document.getElementById('durasi-display');
        const hiddenInput = document.getElementById('durasi_hari');

        if (mulai && selesai) {
            const d1   = new Date(mulai);
            const d2   = new Date(selesai);
            const diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
            if (diff > 0) {
                hiddenInput.value = diff;
                display.innerHTML = '<i class="ri-calendar-check-line"></i> ' + diff + ' hari';
            } else if (diff === 0) {
                hiddenInput.value = 1;
                display.innerHTML = '<i class="ri-calendar-check-line"></i> 1 hari (same day)';
            } else {
                hiddenInput.value = '';
                display.innerHTML = '<span style="color:#EF4444;"><i class="ri-error-warning-line"></i> Tanggal selesai harus setelah tanggal mulai</span>';
            }
        } else {
            hiddenInput.value = '';
            display.innerHTML = '';
        }
    }

    // ============================================================
    // PENGIRIMAN NOTE
    // ============================================================
    const pengirimanNotes = {
        'mandiri'               : 'Penyewa mengambil & mengembalikan sendiri — tidak ada ongkir',
        'Gosend / GrabExpress'  : 'Maks berat 2 Kg — cocok untuk Nebulizer, Kursi Roda Travel, dsb',
        'Rental Mobil Paralkes' : 'Disarankan untuk Bed Pasien, Tabung Oksigen Besar, dsb',
    };

    function updatePengirimanNote() {
        const val    = document.getElementById('pengiriman').value;
        const noteEl = document.getElementById('pengiriman-note-text');
        noteEl.textContent = pengirimanNotes[val] || 'Pilih metode pengiriman di atas';
        if (val === 'mandiri') {
            document.querySelector('input[name="biaya_ongkir"]').value = 0;
        }
    }

    // ============================================================
    // METODE PEMBAYARAN INFO
    // ============================================================
    const metodeInfos = {
        'Tunai / Cash'         : 'Pembayaran tunai di tempat',
        'Transfer via Bank BCA': 'Transfer ke BCA 8030910754 a.n. SURYA DAYYANA',
    };

    function updateMetodeInfo() {
        const val    = document.getElementById('metode_pembayaran').value;
        const infoEl = document.getElementById('metode-info-text');
        infoEl.textContent = metodeInfos[val] || 'Pilih metode pembayaran';
    }

    // ============================================================
    // DRAG & DROP FILE UPLOAD
    // ============================================================
    function formatBytes(bytes) {
        if (bytes < 1024)            return bytes + ' B';
        if (bytes < 1024 * 1024)     return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function initDropzone(inputId, dropzoneId, previewId, previewNameId, previewSizeId, allowedTypes, maxBytes) {
        const input    = document.getElementById(inputId);
        const dropzone = document.getElementById(dropzoneId);
        const preview  = document.getElementById(previewId);
        const nameEl   = document.getElementById(previewNameId);
        const sizeEl   = document.getElementById(previewSizeId);

        if (!input || !dropzone) return;

        function showPreview(file) {
            nameEl.textContent = file.name;
            sizeEl.textContent = formatBytes(file.size);
            preview.classList.add('show');
            dropzone.style.display = 'none';
        }

        function validateAndShow(file) {
            if (!allowedTypes.includes(file.type)) {
                alert('File tidak valid. Gunakan JPG, PNG, atau PDF.');
                return false;
            }
            if (file.size > maxBytes) {
                alert('Ukuran file melebihi ' + formatBytes(maxBytes) + '.');
                return false;
            }
            return true;
        }

        // Via file picker
        input.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                if (validateAndShow(this.files[0])) showPreview(this.files[0]);
                else this.value = '';
            }
        });

        // Drag events
        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        dropzone.addEventListener('dragleave', function (e) {
            if (!this.contains(e.relatedTarget)) this.classList.remove('drag-over');
        });

        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            const file = e.dataTransfer.files[0];
            if (!file) return;
            if (!validateAndShow(file)) return;

            // Assign ke input via DataTransfer
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showPreview(file);
        });
    }

    function removeFile(inputId, previewId, dropzoneId) {
        const input    = document.getElementById(inputId);
        const preview  = document.getElementById(previewId);
        const dropzone = document.getElementById(dropzoneId);
        input.value = '';
        preview.classList.remove('show');
        dropzone.style.display = '';
    }

    // Init dropzone KTP/SIM — JPG, PNG, PDF, maks 5 MB
    initDropzone(
        'foto_ktp_sim',
        'dropzone-ktp',
        'ktp-preview',
        'ktp-preview-name',
        'ktp-preview-size',
        ['image/jpeg', 'image/png', 'application/pdf'],
        5 * 1024 * 1024
    );

    // ============================================================
    // INIT ON DOM READY
    // ============================================================
    window.addEventListener('DOMContentLoaded', function () {
        updatePengirimanNote();
        updateMetodeInfo();
        hitungDurasi();
    });
</script>
@endpush