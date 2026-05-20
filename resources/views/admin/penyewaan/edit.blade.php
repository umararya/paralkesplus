{{-- resources/views/admin/penyewaan/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Penyewaan')
@section('breadcrumb', 'Edit Penyewaan')

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

    .date-range-wrap { display:flex; align-items:center; gap:8px; }
    .date-range-wrap .form-control { flex:1; }
    .date-range-sep { font-size:13px; color:var(--text-muted); white-space:nowrap; }
    .durasi-display { margin-top:6px; font-size:12.5px; color:var(--brand-500); font-weight:600; display:flex; align-items:center; gap:5px; min-height:18px; }
    .pengiriman-note { margin-top:5px; font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:4px; }
    .metode-info { margin-top:5px; font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:4px; }

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

    /* Tabel item */
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
    .ringkasan-row.total .r-label,.ringkasan-row.total .r-value { color:#fff; font-size:13.5px; }

    /* KTP existing */
    .ktp-existing { display:flex; align-items:center; gap:10px; padding:10px 14px; background:var(--brand-50); border:1px solid var(--brand-100); border-radius:8px; margin-bottom:8px; }
    html.dark .ktp-existing { background:rgba(29,111,164,0.1); border-color:rgba(29,111,164,0.25); }
    .ktp-existing i { font-size:18px; color:var(--brand-500); }

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
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">Edit Penyewaan</h1>
        <p style="font-size:13px; color:var(--text-muted);">INV-{{ str_pad($penyewaan->id, 5, '0', STR_PAD_LEFT) }} &mdash; {{ $penyewaan->nama_penyewa }}</p>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('penyewaan.update', $penyewaan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ===================== DATA PENYEWA ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-user-line"></i> Data Penyewa</div>
            <div class="form-grid form-grid-2">

                <div class="form-group">
                    <label class="form-label">Nama <span class="required">*</span></label>
                    <input type="text" name="nama_penyewa"
                           value="{{ old('nama_penyewa', $penyewaan->nama_penyewa) }}"
                           class="form-control {{ $errors->has('nama_penyewa') ? 'is-invalid' : '' }}" required>
                    @error('nama_penyewa')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor Telepon/HP <span class="required">*</span></label>
                    <input type="tel" name="nomor_telepon"
                           value="{{ old('nomor_telepon', $penyewaan->nomor_telepon) }}"
                           class="form-control {{ $errors->has('nomor_telepon') ? 'is-invalid' : '' }}" required>
                    @error('nomor_telepon')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

                {{-- ── BARU: Tempat/Tanggal Lahir ── --}}
                <div class="form-group">
                    <label class="form-label">
                        Tempat/Tanggal Lahir
                        <span class="hint">(untuk formulir perjanjian)</span>
                    </label>
                    <input type="text" name="tempat_tanggal_lahir"
                           value="{{ old('tempat_tanggal_lahir', $penyewaan->tempat_tanggal_lahir) }}"
                           placeholder="Contoh: Semarang, 01 Januari 1990"
                           class="form-control {{ $errors->has('tempat_tanggal_lahir') ? 'is-invalid' : '' }}">
                    @error('tempat_tanggal_lahir')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

                {{-- ── BARU: Nomor KTP ── --}}
                <div class="form-group">
                    <label class="form-label">
                        Nomor KTP (NIK)
                        <span class="hint">(16 digit, untuk formulir perjanjian)</span>
                    </label>
                    <input type="text" name="nomor_ktp"
                           value="{{ old('nomor_ktp', $penyewaan->nomor_ktp) }}"
                           placeholder="3374xxxxxxxxxxxxxxx"
                           maxlength="16"
                           inputmode="numeric"
                           pattern="[0-9]{16}"
                           class="form-control {{ $errors->has('nomor_ktp') ? 'is-invalid' : '' }}"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                    @error('nomor_ktp')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Alamat Penyewa <span class="required">*</span></label>
                    <textarea name="alamat_penyewa" rows="3"
                              class="form-control {{ $errors->has('alamat_penyewa') ? 'is-invalid' : '' }}"
                              required>{{ old('alamat_penyewa', $penyewaan->alamat_penyewa) }}</textarea>
                    @error('alamat_penyewa')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

            </div>
        </div>

        {{-- ===================== DETAIL ITEM SEWA ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-stethoscope-line"></i> Item Penyewaan</div>

            @error('items')
                <div style="margin-bottom:10px;">
                    <span class="invalid-feedback" style="display:flex;"><i class="ri-error-warning-line"></i> {{ $message }}</span>
                </div>
            @enderror

            @php
                /*
                | Priority:
                | 1. old() jika ada (setelah error validasi)
                | 2. $penyewaan->details jika sudah pakai sistem baru
                | 3. legacyList dari produk_alkes CSV (data lama)
                */
                if (old('items')) {
                    $editItems = old('items');
                } elseif ($penyewaan->has_detail) {
                    $editItems = $penyewaan->details->map(fn($d) => [
                        'nama_alat'    => $d->nama_alat,
                        'qty'          => $d->qty,
                        'satuan'       => $d->satuan,
                        'harga_satuan' => $d->harga_satuan,
                        'diskon'       => $d->diskon,
                        'subtotal'     => $d->subtotal,
                    ])->toArray();
                } else {
                    // Migrasi data lama: satu baris per produk CSV
                    $legacy = $penyewaan->produk_alkes
                        ? array_map('trim', explode(',', $penyewaan->produk_alkes))
                        : [''];
                    $editItems = array_map(fn($nama) => [
                        'nama_alat'    => $nama,
                        'qty'          => 1,
                        'satuan'       => 'pcs',
                        'harga_satuan' => 0,
                        'diskon'       => 0,
                        'subtotal'     => 0,
                    ], $legacy);
                }
            @endphp

            <table class="items-table" id="items-table">
                <thead>
                    <tr>
                        <th style="width:32px;">#</th>
                        <th>Nama Alat Kesehatan</th>
                        <th style="width:65px;">Qty</th>
                        <th style="width:80px;">Satuan</th>
                        <th style="width:130px;">Harga / Satuan (Rp)</th>
                        <th style="width:75px;">Diskon (%)</th>
                        <th style="width:120px; text-align:right;">Subtotal</th>
                        <th style="width:36px;"></th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    @foreach($editItems as $idx => $ei)
                    @php
                        $sub = isset($ei['subtotal'])
                            ? (int)$ei['subtotal']
                            : (int)round(($ei['qty']??1) * ($ei['harga_satuan']??0) * (1 - ($ei['diskon']??0) / 100));
                    @endphp
                    <tr class="item-row">
                        <td style="text-align:center; color:var(--text-muted); font-size:13px;" class="row-num">{{ $idx+1 }}</td>
                        <td>
                            <input type="text" name="items[{{ $idx }}][nama_alat]"
                                   value="{{ $ei['nama_alat'] ?? '' }}"
                                   placeholder="Nama alat kesehatan"
                                   class="form-control item-nama" required>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $idx }}][qty]"
                                   value="{{ $ei['qty'] ?? 1 }}"
                                   min="1" class="form-control item-qty" required>
                        </td>
                        <td>
                            <select name="items[{{ $idx }}][satuan]" class="form-control item-satuan">
                                @foreach(['pcs','unit','set','buah','pasang'] as $sat)
                                <option value="{{ $sat }}" {{ ($ei['satuan'] ?? 'pcs') == $sat ? 'selected' : '' }}>{{ $sat }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $idx }}][harga_satuan]"
                                   value="{{ $ei['harga_satuan'] ?? 0 }}"
                                   min="0" class="form-control item-harga" required>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $idx }}][diskon]"
                                   value="{{ $ei['diskon'] ?? 0 }}"
                                   min="0" max="100" class="form-control item-diskon">
                        </td>
                        <td class="subtotal-cell" data-subtotal="{{ $sub }}">
                            Rp {{ number_format($sub, 0, ',', '.') }}
                        </td>
                        <td>
                            <button type="button" class="btn-remove-row" onclick="removeRow(this)" title="Hapus baris">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="button" class="btn-add-row" onclick="addRow()">
                <i class="ri-add-line"></i> Tambah Item
            </button>

            <div class="ringkasan-box">
                <div class="ringkasan-inner">
                    <div class="ringkasan-row">
                        <span class="r-label">Subtotal Sewa</span>
                        <span class="r-value" id="ringkasan-subtotal">Rp 0</span>
                    </div>
                    <div class="ringkasan-row">
                        <span class="r-label">Diskon Global (Rp)</span>
                        <span class="r-value" id="ringkasan-diskon-label">Rp 0</span>
                    </div>
                    <div class="ringkasan-row">
                        <span class="r-label">Ongkos Kirim</span>
                        <span class="r-value" id="ringkasan-ongkir">Rp 0</span>
                    </div>
                    <div class="ringkasan-row total">
                        <span class="r-label">Total Tagihan</span>
                        <span class="r-value" id="ringkasan-total">Rp 0</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===================== DURASI & PENGIRIMAN ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-calendar-line"></i> Durasi &amp; Pengiriman</div>
            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Durasi Sewa <span class="required">*</span></label>
                    <div class="date-range-wrap">
                        <input type="text" id="tgl_mulai" name="tgl_mulai"
                               value="{{ old('tgl_mulai', $penyewaan->tgl_mulai?->format('Y-m-d')) }}"
                               placeholder="Tanggal Mulai"
                               class="form-control {{ $errors->has('tgl_mulai') ? 'is-invalid' : '' }}"
                               autocomplete="off" readonly required>
                        <span class="date-range-sep"><i class="ri-arrow-right-line"></i></span>
                        <input type="text" id="tgl_selesai" name="tgl_selesai"
                               value="{{ old('tgl_selesai', $penyewaan->tgl_selesai?->format('Y-m-d')) }}"
                               placeholder="Tanggal Selesai"
                               class="form-control {{ $errors->has('tgl_selesai') ? 'is-invalid' : '' }}"
                               autocomplete="off" readonly required>
                    </div>
                    <input type="hidden" id="durasi_hari" name="durasi_hari"
                           value="{{ old('durasi_hari', $penyewaan->durasi_hari) }}">
                    <div class="durasi-display" id="durasi-display">
                        @if(old('durasi_hari', $penyewaan->durasi_hari))
                            <i class="ri-calendar-check-line"></i> {{ old('durasi_hari', $penyewaan->durasi_hari) }} hari
                        @endif
                    </div>
                    @error('tgl_mulai')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                    @error('tgl_selesai')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                    @error('durasi_hari')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

                <div class="form-grid form-grid-2" style="grid-column:1/-1;">

                    <div class="form-group">
                        <label class="form-label">Pengiriman <span class="required">*</span></label>
                        <select name="pengiriman" id="pengiriman"
                                class="form-control {{ $errors->has('pengiriman') ? 'is-invalid' : '' }}"
                                onchange="updatePengirimanNote()" required>
                            <option value="" disabled>-- Pilih metode pengiriman --</option>
                            <option value="mandiri" {{ old('pengiriman', $penyewaan->pengiriman) == 'mandiri' ? 'selected' : '' }}>Ambil dan Antar kembali sendiri oleh Penyewa</option>
                            <option value="Gosend / GrabExpress" {{ old('pengiriman', $penyewaan->pengiriman) == 'Gosend / GrabExpress' ? 'selected' : '' }}>via Gosend / GrabExpress</option>
                            <option value="Rental Mobil Paralkes" {{ old('pengiriman', $penyewaan->pengiriman) == 'Rental Mobil Paralkes' ? 'selected' : '' }}>via Rental Mobil Paralkes</option>
                        </select>
                        <div class="pengiriman-note" id="pengiriman-note">
                            <i class="ri-information-line"></i>
                            <span id="pengiriman-note-text"></span>
                        </div>
                        @error('pengiriman')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Biaya Ongkir <span class="hint">(Rp)</span></label>
                        <input type="number" name="biaya_ongkir" id="biaya_ongkir"
                               value="{{ old('biaya_ongkir', $penyewaan->biaya_ongkir) }}"
                               min="0" class="form-control {{ $errors->has('biaya_ongkir') ? 'is-invalid' : '' }}"
                               oninput="hitungRingkasan()">
                        @error('biaya_ongkir')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
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
                        <option value="" disabled>-- Pilih --</option>
                        <option value="Tunai / Cash" {{ old('metode_pembayaran', $penyewaan->metode_pembayaran) == 'Tunai / Cash' ? 'selected' : '' }}>Tunai / Cash</option>
                        <option value="Transfer via Bank BCA" {{ old('metode_pembayaran', $penyewaan->metode_pembayaran) == 'Transfer via Bank BCA' ? 'selected' : '' }}>Transfer via Bank BCA 8030910754 a.n. SURYA DAYYANA</option>
                    </select>
                    <div class="metode-info" id="metode-info">
                        <i class="ri-information-line"></i>
                        <span id="metode-info-text"></span>
                    </div>
                    @error('metode_pembayaran')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Diskon Global <span class="hint">(Rp, potongan di luar diskon per item)</span></label>
                    <input type="number" name="diskon_global" id="diskon_global"
                           value="{{ old('diskon_global', $penyewaan->diskon_global ?? 0) }}"
                           min="0" class="form-control {{ $errors->has('diskon_global') ? 'is-invalid' : '' }}"
                           oninput="hitungRingkasan()">
                    @error('diskon_global')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status <span class="required">*</span></label>
                    <select name="status"
                            class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" required>
                        <option value="berjalan" {{ old('status', $penyewaan->status) == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                        <option value="segera_konfirmasi" {{ old('status', $penyewaan->status) == 'segera_konfirmasi' ? 'selected' : '' }}>Segera Konfirmasi</option>
                        <option value="selesai" {{ old('status', $penyewaan->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Bukti Pembayaran</label>
                    <input type="text" name="bukti_pembayaran"
                           value="{{ old('bukti_pembayaran', $penyewaan->bukti_pembayaran) }}"
                           placeholder="https://drive.google.com/... atau 'bayar ditempat'"
                           class="form-control {{ $errors->has('bukti_pembayaran') ? 'is-invalid' : '' }}">
                    @error('bukti_pembayaran')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Foto KTP / SIM <span class="hint">(kosongkan jika tidak ingin mengganti)</span></label>

                    @if($penyewaan->foto_ktp_sim)
                    <div class="ktp-existing">
                        <i class="ri-id-card-line"></i>
                        <span style="font-size:12.5px; color:var(--text-primary); flex:1;">
                            File KTP/SIM sudah ada &mdash;
                            <a href="{{ asset('storage/' . $penyewaan->foto_ktp_sim) }}"
                               target="_blank" style="color:var(--brand-500); text-decoration:none;">
                                Lihat file lama
                            </a>
                        </span>
                    </div>
                    @endif

                    <div class="dropzone" id="dropzone-ktp" tabindex="0" role="button"
                         onkeydown="if(event.key==='Enter'||event.key===' ')this.querySelector('input').click()">
                        <input type="file" id="foto_ktp_sim" name="foto_ktp_sim" accept=".jpg,.jpeg,.png,.pdf">
                        <i class="ri-upload-cloud-line dropzone-icon"></i>
                        <div class="dropzone-title">{{ $penyewaan->foto_ktp_sim ? 'Klik untuk mengganti file' : 'Klik atau seret file ke sini' }}</div>
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
                    @error('foto_ktp_sim')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
                </div>

            </div>
        </div>

        {{-- ===================== KETERANGAN ===================== --}}
        <div class="form-section">
            <div class="section-title"><i class="ri-file-text-line"></i> Keterangan</div>
            <div class="form-group">
                <label class="form-label">Keterangan <span class="hint">(opsional)</span></label>
                <textarea name="keterangan" rows="3"
                          class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}">{{ old('keterangan', $penyewaan->keterangan) }}</textarea>
                @error('keterangan')<span class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</span>@enderror
            </div>
        </div>

        <div class="form-footer">
            <a href="{{ route('penyewaan.index') }}" class="btn btn-cancel">
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
<script>
// ============================================================
// DATE RANGE PICKER
// ============================================================
const fpMulai = flatpickr("#tgl_mulai", {
    locale: "id", dateFormat: "Y-m-d",
    onChange: function(_, dateStr) { fpSelesai.set('minDate', dateStr); hitungDurasi(); }
});
const fpSelesai = flatpickr("#tgl_selesai", {
    locale: "id", dateFormat: "Y-m-d",
    onChange: function() { hitungDurasi(); }
});

function hitungDurasi() {
    const mulai   = document.getElementById('tgl_mulai').value;
    const selesai = document.getElementById('tgl_selesai').value;
    const display = document.getElementById('durasi-display');
    const hidden  = document.getElementById('durasi_hari');
    if (mulai && selesai) {
        const diff = Math.round((new Date(selesai) - new Date(mulai)) / 86400000);
        if (diff > 0) {
            hidden.value = diff;
            display.innerHTML = '<i class="ri-calendar-check-line"></i> ' + diff + ' hari';
        } else if (diff === 0) {
            hidden.value = 1;
            display.innerHTML = '<i class="ri-calendar-check-line"></i> 1 hari (same day)';
        } else {
            hidden.value = '';
            display.innerHTML = '<span style="color:#EF4444;"><i class="ri-error-warning-line"></i> Tanggal selesai harus setelah tanggal mulai</span>';
        }
    }
}

// ============================================================
// PENGIRIMAN & METODE INFO
// ============================================================
const pengirimanNotes = {
    'mandiri'              : 'Penyewa mengambil & mengembalikan sendiri — tidak ada ongkir',
    'Gosend / GrabExpress' : 'Maks berat 2 Kg — cocok untuk Nebulizer, Kursi Roda Travel, dsb',
    'Rental Mobil Paralkes': 'Disarankan untuk Bed Pasien, Tabung Oksigen Besar, dsb',
};
function updatePengirimanNote() {
    const val = document.getElementById('pengiriman').value;
    document.getElementById('pengiriman-note-text').textContent = pengirimanNotes[val] || '';
    if (val === 'mandiri') { document.getElementById('biaya_ongkir').value = 0; hitungRingkasan(); }
}

const metodeInfos = {
    'Tunai / Cash'         : 'Pembayaran tunai di tempat',
    'Transfer via Bank BCA': 'Transfer ke BCA 8030910754 a.n. SURYA DAYYANA',
};
function updateMetodeInfo() {
    const val = document.getElementById('metode_pembayaran').value;
    document.getElementById('metode-info-text').textContent = metodeInfos[val] || '';
}

// ============================================================
// DYNAMIC TABLE — ITEM ROWS
// ============================================================
let rowIndex = {{ count($editItems) }};
const satuanOptions = ['pcs','unit','set','buah','pasang'];

function buildSatuanOptions(selected) {
    return satuanOptions.map(s =>
        `<option value="${s}" ${s === selected ? 'selected' : ''}>${s}</option>`
    ).join('');
}

function addRow() {
    const tbody = document.getElementById('items-body');
    const idx   = rowIndex++;
    const tr    = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td style="text-align:center; color:var(--text-muted); font-size:13px;" class="row-num">${tbody.rows.length + 1}</td>
        <td><input type="text"   name="items[${idx}][nama_alat]"    placeholder="Nama alat" class="form-control item-nama" required></td>
        <td><input type="number" name="items[${idx}][qty]"          value="1" min="1" class="form-control item-qty" required></td>
        <td><select name="items[${idx}][satuan]" class="form-control item-satuan">${buildSatuanOptions('pcs')}</select></td>
        <td><input type="number" name="items[${idx}][harga_satuan]" value="0" min="0" class="form-control item-harga" required></td>
        <td><input type="number" name="items[${idx}][diskon]"       value="0" min="0" max="100" class="form-control item-diskon"></td>
        <td class="subtotal-cell" data-subtotal="0">Rp 0</td>
        <td><button type="button" class="btn-remove-row" onclick="removeRow(this)"><i class="ri-delete-bin-line"></i></button></td>
    `;
    tbody.appendChild(tr);
    bindRowEvents(tr);
    updateRowNumbers();
}

function removeRow(btn) {
    const tbody = document.getElementById('items-body');
    if (tbody.rows.length <= 1) { alert('Minimal harus ada 1 item penyewaan.'); return; }
    btn.closest('tr').remove();
    updateRowNumbers();
    hitungRingkasan();
}

function updateRowNumbers() {
    document.querySelectorAll('#items-body .row-num').forEach((el, i) => { el.textContent = i + 1; });
}

function hitungSubtotal(row) {
    const qty    = parseFloat(row.querySelector('.item-qty').value)    || 0;
    const harga  = parseFloat(row.querySelector('.item-harga').value)  || 0;
    const diskon = parseFloat(row.querySelector('.item-diskon').value) || 0;
    const sub    = Math.round(qty * harga * (1 - diskon / 100));
    const cell   = row.querySelector('.subtotal-cell');
    cell.dataset.subtotal = sub;
    cell.textContent = 'Rp ' + sub.toLocaleString('id-ID');
    hitungRingkasan();
}

function hitungRingkasan() {
    let subtotalSewa = 0;
    document.querySelectorAll('#items-body .subtotal-cell').forEach(cell => {
        subtotalSewa += parseFloat(cell.dataset.subtotal) || 0;
    });
    const diskonGlobal = parseFloat(document.getElementById('diskon_global').value) || 0;
    const ongkir       = parseFloat(document.getElementById('biaya_ongkir').value)  || 0;
    const total        = Math.max(0, subtotalSewa - diskonGlobal + ongkir);
    const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
    document.getElementById('ringkasan-subtotal').textContent     = fmt(subtotalSewa);
    document.getElementById('ringkasan-diskon-label').textContent = fmt(diskonGlobal);
    document.getElementById('ringkasan-ongkir').textContent       = fmt(ongkir);
    document.getElementById('ringkasan-total').textContent        = fmt(total);
}

function bindRowEvents(row) {
    row.querySelectorAll('.item-qty, .item-harga, .item-diskon').forEach(input => {
        input.addEventListener('input', () => hitungSubtotal(row));
    });
    hitungSubtotal(row);
}

document.querySelectorAll('#items-body .item-row').forEach(bindRowEvents);

// ============================================================
// DRAG & DROP FILE UPLOAD
// ============================================================
function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024*1024) return (bytes/1024).toFixed(1) + ' KB';
    return (bytes/(1024*1024)).toFixed(1) + ' MB';
}

function initDropzone(inputId, dropzoneId, previewId, previewNameId, previewSizeId, allowedTypes, maxBytes) {
    const input    = document.getElementById(inputId);
    const dropzone = document.getElementById(dropzoneId);
    const preview  = document.getElementById(previewId);
    const nameEl   = document.getElementById(previewNameId);
    const sizeEl   = document.getElementById(previewSizeId);
    if (!input || !dropzone) return;

    function showPreview(file) {
        nameEl.textContent = file.name; sizeEl.textContent = formatBytes(file.size);
        preview.classList.add('show'); dropzone.style.display = 'none';
    }
    function validateAndShow(file) {
        if (!allowedTypes.includes(file.type)) { alert('File tidak valid.'); return false; }
        if (file.size > maxBytes) { alert('File terlalu besar.'); return false; }
        return true;
    }
    input.addEventListener('change', function () {
        if (this.files && this.files[0] && validateAndShow(this.files[0])) showPreview(this.files[0]);
        else this.value = '';
    });
    dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
    dropzone.addEventListener('dragleave', e => { if (!dropzone.contains(e.relatedTarget)) dropzone.classList.remove('drag-over'); });
    dropzone.addEventListener('drop', e => {
        e.preventDefault(); dropzone.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (!file || !validateAndShow(file)) return;
        const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files;
        showPreview(file);
    });
}

function removeFile(inputId, previewId, dropzoneId) {
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).classList.remove('show');
    document.getElementById(dropzoneId).style.display = '';
}

initDropzone('foto_ktp_sim','dropzone-ktp','ktp-preview','ktp-preview-name','ktp-preview-size',
    ['image/jpeg','image/png','application/pdf'], 5*1024*1024);

// ============================================================
// INIT
// ============================================================
window.addEventListener('DOMContentLoaded', function () {
    updatePengirimanNote();
    updateMetodeInfo();
    hitungDurasi();
    hitungRingkasan();
});
</script>
@endpush