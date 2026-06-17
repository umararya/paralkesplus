{{-- resources/views/admin/pembelian/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Pembelian')
@section('breadcrumb', 'Edit Pembelian')

@push('styles')
<style>
.kondisi-label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: 8px;
    cursor: pointer;
    flex: 1;
    transition: all 0.2s;
    background: var(--bg-card);
}
.kondisi-label:has(input:checked) {
    border-color: var(--brand-500);
    background: var(--bg-hover);
}
.kondisi-label span {
    font-size: 13.5px;
    font-weight: 500;
    color: var(--text-primary);
}
.invoice-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    background: #EFF6FF;
    border: 1px solid #BFDBFE;
    border-radius: 99px;
    font-size: 11.5px;
    font-weight: 600;
    color: #1D4ED8;
    margin-top: 6px;
    font-family: monospace;
}
html.dark .invoice-badge {
    background: rgba(29, 78, 216, 0.15);
    border-color: rgba(29, 78, 216, 0.3);
    color: #93C5FD;
}
.upload-section-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 6px;
}
.drop-zone {
    border: 2px dashed var(--border);
    border-radius: 10px;
    padding: 22px 20px;
    text-align: center;
    cursor: pointer;
    background: var(--bg-primary);
    transition: all 0.2s;
}
.drop-zone:hover {
    border-color: var(--brand-500);
    background: var(--bg-hover);
}
.drop-zone-placeholder p { margin: 0; }
.drop-zone-placeholder .icon {
    font-size: 32px;
    color: var(--text-muted);
    display: block;
    margin-bottom: 8px;
}
.drop-zone-placeholder .title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 3px !important;
}
.drop-zone-placeholder .subtitle {
    font-size: 12px;
    color: var(--text-muted);
}
.btn-hapus-file {
    margin-top: 8px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border: 1px solid #FCA5A5;
    border-radius: 7px;
    background: #FFF1F2;
    color: #E11D48;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
}
.existing-file-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 14px;
    background: var(--bg-hover);
    border: 1px solid var(--border);
    border-radius: 10px;
    margin-bottom: 12px;
    transition: opacity 0.3s;
}
</style>
@endpush

@section('content')

{{-- Header --}}
<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('pembelian.index') }}"
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
            Edit Pembelian Barang
        </h1>
        <p style="font-size:13px; color:var(--text-muted);">
            Mengedit data: <strong style="color:var(--text-primary);">{{ $pembelian->nama_barang }}</strong>
            @if($pembelian->no_invoice)
                &mdash; <span style="font-family:monospace; color:var(--brand-500); font-size:12px;">
                    {{ $pembelian->no_invoice }}
                </span>
            @endif
        </p>
    </div>
</div>

{{-- Info stok ringkas --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:24px;">
    @foreach([
        ['label' => 'Total Item',    'value' => Illuminate\Support\Facades\DB::table('inventories')->count(),            'color' => '#3B82F6'],
        ['label' => 'Stok Tersedia', 'value' => Illuminate\Support\Facades\DB::table('inventories')->sum('stok_tersedia'), 'color' => '#22C55E'],
        ['label' => 'Sedang Disewa', 'value' => Illuminate\Support\Facades\DB::table('inventories')->sum('stok_disewa'),   'color' => '#F97316'],
        ['label' => 'Stok Bekas',    'value' => Illuminate\Support\Facades\DB::table('inventories')->sum('stok_bekas'),    'color' => '#A855F7'],
    ] as $card)
    <div style="background:var(--bg-card); border:1px solid var(--border);
                border-radius:12px; padding:14px 16px; box-shadow:var(--shadow);
                border-left:4px solid {{ $card['color'] }};">
        <div style="font-size:11px; font-weight:600; text-transform:uppercase;
                    letter-spacing:0.6px; color:var(--text-muted);">
            {{ $card['label'] }}
        </div>
        <div style="font-size:24px; font-weight:700; color:var(--text-primary); margin-top:4px;">
            {{ $card['value'] }}
        </div>
    </div>
    @endforeach
</div>

{{-- Validation Errors --}}
@if($errors->any())
<div style="background:#FEF2F2; border:1px solid #FCA5A5; color:#991B1B;
            padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px;">
    <div style="display:flex; align-items:center; gap:8px; font-weight:600; margin-bottom:8px;">
        <i class="ri-error-warning-line"></i> Terdapat kesalahan input:
    </div>
    <ul style="margin:0; padding-left:20px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card" style="max-width:720px;">
    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST"
          enctype="multipart/form-data" id="formEditPembelian">
        @csrf
        @method('PUT')
        <div style="display:grid; gap:20px;">

            {{-- Tanggal Pembelian --}}
            <div>
                <label class="upload-section-label">
                    Tanggal Pembelian <span style="color:#EF4444;">*</span>
                </label>
                <input type="date" name="tanggal_pembelian"
                       value="{{ old('tanggal_pembelian', \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->format('Y-m-d')) }}"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'" required>
            </div>

            {{-- No. Invoice --}}
            <div>
                <label class="upload-section-label">
                    No. Invoice / Faktur
                    <span style="font-weight:400; color:var(--text-muted); font-size:12px;">
                        &mdash; nomor dari supplier (opsional)
                    </span>
                </label>
                <div style="position:relative;">
                    <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                                 color:var(--text-muted); font-size:15px; pointer-events:none;">
                        <i class="ri-file-list-3-line"></i>
                    </span>
                    <input type="text" name="no_invoice" id="inputNoInvoice"
                           value="{{ old('no_invoice', $pembelian->no_invoice) }}"
                           placeholder="Contoh: INV-2025-001, FKT/2025/06/001"
                           maxlength="100"
                           style="width:100%; padding:10px 14px 10px 38px;
                                  border:1px solid var(--border); border-radius:8px;
                                  font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none;
                                  transition:border-color 0.2s; font-family:monospace;
                                  box-sizing:border-box;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'"
                           oninput="updateInvoiceBadge(this.value)">
                </div>
                <div id="invoiceBadgeWrap" style="display:none; margin-top:6px;">
                    <span class="invoice-badge">
                        <i class="ri-price-tag-3-line"></i>
                        <span id="invoiceBadgeText"></span>
                    </span>
                </div>
                <p style="font-size:12px; color:var(--text-muted); margin-top:5px;">
                    <i class="ri-information-line"></i>
                    Nomor invoice akan ditampilkan di tabel daftar pembelian untuk kemudahan pelacakan.
                </p>
                @error('no_invoice')
                <p style="font-size:12px; color:#E11D48; margin-top:6px;">
                    <i class="ri-error-warning-line"></i> {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Nama Barang + Autocomplete --}}
            <div style="position:relative;">
                <label class="upload-section-label">
                    Nama Barang <span style="color:#EF4444;">*</span>
                    <span style="font-weight:400; color:var(--text-muted); font-size:12px;">
                        &mdash; ketik untuk saran dari inventory
                    </span>
                </label>
                <input type="text" name="nama_barang" id="inputNamaBarang"
                       value="{{ old('nama_barang', $pembelian->nama_barang) }}"
                       placeholder="Contoh: Tensimeter Digital, Stetoskop, dll."
                       autocomplete="off"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'; showSuggestions()"
                       onblur="this.style.borderColor='var(--border)'; setTimeout(hideSuggestions, 200)"
                       oninput="filterSuggestions()" required>

                <div id="suggestionBox"
                     style="display:none; position:absolute; z-index:100; left:0; right:0;
                            top:100%; margin-top:4px; background:var(--bg-card);
                            border:1px solid var(--border); border-radius:8px;
                            box-shadow:0 8px 24px rgba(0,0,0,0.12);
                            max-height:200px; overflow-y:auto;">
                </div>

                <div id="inventoryLink" style="display:none; margin-top:6px;">
                    <a id="inventoryLinkAnchor" href="#" target="_blank"
                       style="font-size:12px; color:var(--brand-500); text-decoration:none;
                              display:inline-flex; align-items:center; gap:4px;">
                        <i class="ri-archive-drawer-line"></i>
                        <span id="inventoryLinkText">Lihat di Inventory</span>
                    </a>
                </div>
            </div>

            {{-- Jumlah & Harga Satuan --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label class="upload-section-label">
                        Jumlah (Qty) <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="number" name="jumlah" id="inputJumlah"
                           value="{{ old('jumlah', $pembelian->jumlah) }}" min="1"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'"
                           oninput="hitungTotal()" required>
                </div>
                <div>
                    <label class="upload-section-label">
                        Harga Satuan (Rp) <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="number" name="harga_satuan" id="inputHarga"
                           value="{{ old('harga_satuan', $pembelian->harga_satuan) }}" min="0"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'"
                           oninput="hitungTotal()" required>
                </div>
            </div>

            {{-- Kondisi Barang --}}
            <div>
                <label class="upload-section-label">
                    Kondisi Barang <span style="color:#EF4444;">*</span>
                </label>
                <div style="display:flex; gap:12px;">
                    <label class="kondisi-label">
                        <input type="radio" name="kondisi_barang" value="baru"
                               {{ old('kondisi_barang', $pembelian->kondisi_barang) === 'baru' ? 'checked' : '' }}
                               style="accent-color:var(--brand-500);">
                        <span>
                            <i class="ri-checkbox-blank-circle-fill"
                               style="color:#3B82F6; margin-right:4px; font-size:11px;"></i>
                            Barang Baru
                        </span>
                    </label>
                    <label class="kondisi-label">
                        <input type="radio" name="kondisi_barang" value="bekas"
                               {{ old('kondisi_barang', $pembelian->kondisi_barang) === 'bekas' ? 'checked' : '' }}
                               style="accent-color:var(--brand-500);">
                        <span>
                            <i class="ri-checkbox-blank-circle-fill"
                               style="color:#A855F7; margin-right:4px; font-size:11px;"></i>
                            Barang Bekas
                        </span>
                    </label>
                </div>
                <p style="font-size:12px; color:var(--text-muted); margin-top:6px;">
                    <i class="ri-information-line"></i>
                    Perubahan kondisi tidak otomatis mengubah stok inventory &mdash; hanya data catatan yang diperbarui.
                </p>
            </div>

            {{-- Preview Total --}}
            <div style="background:var(--bg-hover); border:1px solid var(--border);
                        border-radius:8px; padding:14px 16px;
                        display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:13px; font-weight:600; color:var(--text-secondary);">
                    <i class="ri-calculator-line" style="margin-right:6px;"></i>Total Harga
                </span>
                <span id="previewTotal" style="font-size:16px; font-weight:700; color:#059669;">
                    Rp 0
                </span>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="upload-section-label">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          placeholder="Catatan tambahan, nama supplier, dll. (opsional)"
                          style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                 border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                 color:var(--text-primary); outline:none; resize:vertical;
                                 transition:border-color 0.2s; font-family:inherit;"
                          onfocus="this.style.borderColor='var(--brand-500)'"
                          onblur="this.style.borderColor='var(--border)'">{{ old('keterangan', $pembelian->keterangan) }}</textarea>
            </div>

            {{-- ===== FILE INVOICE / FAKTUR SUPPLIER ===== --}}
            <div>
                <label class="upload-section-label">
                    File Invoice / Faktur Supplier
                    <span style="font-weight:400; color:var(--text-muted); font-size:12px;">
                        &mdash; PDF, JPG, PNG (opsional)
                    </span>
                </label>

                {{-- Tampilkan file invoice yang sudah ada --}}
                @if($pembelian->file_invoice)
                <div id="existingInvoice" class="existing-file-card">
                    @php $extInv = strtolower(pathinfo($pembelian->file_invoice, PATHINFO_EXTENSION)); @endphp
                    @if(in_array($extInv, ['jpg', 'jpeg', 'png']))
                        <img src="{{ asset('storage/' . $pembelian->file_invoice) }}"
                             alt="File Invoice"
                             style="width:64px; height:64px; border-radius:8px; object-fit:cover;
                                    border:1px solid var(--border); cursor:pointer; flex-shrink:0;"
                             onclick="window.open('{{ asset('storage/' . $pembelian->file_invoice) }}', '_blank')">
                    @else
                        <div style="width:64px; height:64px; border-radius:8px; background:#FEF2F2;
                                    border:1px solid #FECACA; display:flex; align-items:center;
                                    justify-content:center; flex-shrink:0; cursor:pointer;"
                             onclick="window.open('{{ asset('storage/' . $pembelian->file_invoice) }}', '_blank')">
                            <i class="ri-file-pdf-2-line" style="font-size:28px; color:#EF4444;"></i>
                        </div>
                    @endif
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:13px; font-weight:600; color:var(--text-primary); margin:0 0 4px;">
                            <i class="ri-file-text-line" style="color:var(--brand-500);"></i>
                            File invoice tersedia
                            <span style="font-size:11px; text-transform:uppercase; color:var(--text-muted);
                                         background:var(--bg-card); border:1px solid var(--border);
                                         border-radius:4px; padding:1px 6px; margin-left:4px;">
                                {{ strtoupper($extInv) }}
                            </span>
                        </p>
                        <p style="font-size:12px; color:var(--text-muted); margin:0;">
                            Klik ikon untuk buka. Upload baru untuk mengganti.
                        </p>
                    </div>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;
                                  font-size:12.5px; color:#E11D48; font-weight:600;
                                  flex-shrink:0; white-space:nowrap;">
                        <input type="checkbox" name="hapus_file_invoice" value="1"
                               onchange="toggleHapusInvoice(this)"
                               style="accent-color:#E11D48; width:14px; height:14px;">
                        Hapus file
                    </label>
                </div>
                @endif

                <div id="dropZoneInvoice" class="drop-zone"
                     onclick="document.getElementById('inputFileInvoice').click()"
                     ondragover="event.preventDefault();
                                 this.style.borderColor='var(--brand-500)';
                                 this.style.background='var(--bg-hover)';"
                     ondragleave="this.style.borderColor='var(--border)';
                                  this.style.background='var(--bg-primary)';"
                     ondrop="handleDropInvoice(event)">

                    <div id="dropPlaceholderInvoice" class="drop-zone-placeholder">
                        <i class="ri-file-text-line icon"></i>
                        <p class="title">
                            @if($pembelian->file_invoice) Upload file baru untuk mengganti
                            @else Klik atau seret file invoice ke sini
                            @endif
                        </p>
                        <p class="subtitle">PDF, JPG, PNG &mdash; maks. 10 MB</p>
                    </div>

                    <div id="previewInvoiceWrap" style="display:none;">
                        <div id="previewInvoiceImg" style="display:none; margin-bottom:10px;">
                            <img id="invoiceImgEl" src="" alt="Preview invoice"
                                 style="max-height:180px; max-width:100%; border-radius:8px;
                                        object-fit:contain; display:block; margin:0 auto;">
                        </div>
                        <div id="previewInvoicePdf" style="display:none; margin-bottom:10px;">
                            <i class="ri-file-pdf-2-line" style="font-size:48px; color:#EF4444;"></i>
                        </div>
                        <p id="invoiceFileName" style="font-size:12px; color:var(--text-muted); margin:0;"></p>
                        <button type="button" onclick="event.stopPropagation(); hapusInvoice()"
                                class="btn-hapus-file">
                            <i class="ri-delete-bin-line"></i> Batal pilih
                        </button>
                    </div>
                </div>

                <input type="file" id="inputFileInvoice" name="file_invoice"
                       accept="image/jpeg,image/png,application/pdf"
                       style="display:none;" onchange="previewInvoice(this)">

                @error('file_invoice')
                <p style="font-size:12px; color:#E11D48; margin-top:6px;">
                    <i class="ri-error-warning-line"></i> {{ $message }}
                </p>
                @enderror
            </div>

            {{-- ===== BUKTI PEMBAYARAN (REVISI: + support PDF) ===== --}}
            <div>
                <label class="upload-section-label">
                    Bukti Pembayaran
                    <span style="font-weight:400; color:var(--text-muted); font-size:12px;">
                        &mdash; foto nota / kwitansi / PDF (opsional)
                    </span>
                </label>

                {{-- REVISI: existing card handle PDF juga --}}
                @if($pembelian->bukti_transaksi)
                <div id="existingBukti" class="existing-file-card">
                    @php $extBukti = strtolower(pathinfo($pembelian->bukti_transaksi, PATHINFO_EXTENSION)); @endphp
                    @if($extBukti === 'pdf')
                        {{-- ← REVISI: tampilkan icon PDF jika file adalah PDF --}}
                        <div style="width:64px; height:64px; border-radius:8px; background:#FEF2F2;
                                    border:1px solid #FECACA; display:flex; align-items:center;
                                    justify-content:center; flex-shrink:0; cursor:pointer;"
                             onclick="window.open('{{ asset('storage/' . $pembelian->bukti_transaksi) }}', '_blank')">
                            <i class="ri-file-pdf-2-line" style="font-size:28px; color:#EF4444;"></i>
                        </div>
                    @else
                        <img src="{{ asset('storage/' . $pembelian->bukti_transaksi) }}"
                             alt="Bukti Pembayaran"
                             style="width:64px; height:64px; border-radius:8px; object-fit:cover;
                                    border:1px solid var(--border); cursor:pointer; flex-shrink:0;"
                             onclick="window.open('{{ asset('storage/' . $pembelian->bukti_transaksi) }}', '_blank')">
                    @endif
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:13px; font-weight:600; color:var(--text-primary); margin:0 0 4px;">
                            <i class="ri-{{ $extBukti === 'pdf' ? 'file-pdf-2' : 'image' }}-line"
                               style="color:var(--brand-500);"></i>
                            Bukti tersedia
                            <span style="font-size:11px; text-transform:uppercase; color:var(--text-muted);
                                         background:var(--bg-card); border:1px solid var(--border);
                                         border-radius:4px; padding:1px 6px; margin-left:4px;">
                                {{ strtoupper($extBukti) }}
                            </span>
                        </p>
                        <p style="font-size:12px; color:var(--text-muted); margin:0;">
                            Klik untuk lihat. Upload baru untuk mengganti.
                        </p>
                    </div>
                    <label style="display:flex; align-items:center; gap:6px; cursor:pointer;
                                  font-size:12.5px; color:#E11D48; font-weight:600;
                                  flex-shrink:0; white-space:nowrap;">
                        <input type="checkbox" name="hapus_bukti" value="1"
                               onchange="toggleHapusBukti(this)"
                               style="accent-color:#E11D48; width:14px; height:14px;">
                        Hapus bukti
                    </label>
                </div>
                @endif

                <div id="dropZone" class="drop-zone"
                     onclick="document.getElementById('inputBukti').click()"
                     ondragover="event.preventDefault();
                                 this.style.borderColor='var(--brand-500)';
                                 this.style.background='var(--bg-hover)';"
                     ondragleave="this.style.borderColor='var(--border)';
                                  this.style.background='var(--bg-primary)';"
                     ondrop="handleDrop(event)">

                    <div id="dropPlaceholder" class="drop-zone-placeholder">
                        {{-- ← REVISI: icon & teks --}}
                        <i class="ri-file-upload-line icon"></i>
                        <p class="title">
                            @if($pembelian->bukti_transaksi) Upload file baru untuk mengganti
                            @else Klik atau seret file ke sini
                            @endif
                        </p>
                        <p class="subtitle">JPG, PNG, PDF &mdash; maks. 10 MB</p>
                    </div>

                    <div id="previewWrap" style="display:none;">
                        {{-- ← REVISI: pisah wrap image & PDF --}}
                        <div id="previewImgWrap" style="display:none; margin-bottom:10px;">
                            <img id="previewImg" src="" alt="Preview bukti"
                                 style="max-height:180px; max-width:100%; border-radius:8px;
                                        object-fit:contain; display:block; margin:0 auto;">
                        </div>
                        <div id="previewPdfWrap" style="display:none; margin-bottom:10px;">
                            <i class="ri-file-pdf-2-line" style="font-size:48px; color:#EF4444;"></i>
                        </div>
                        <p id="previewFileName" style="font-size:12px; color:var(--text-muted); margin:0;"></p>
                        <button type="button" onclick="event.stopPropagation(); hapusGambar()"
                                class="btn-hapus-file">
                            <i class="ri-delete-bin-line"></i> Batal pilih
                        </button>
                    </div>
                </div>

                {{-- ← REVISI: accept tambah PDF --}}
                <input type="file" id="inputBukti" name="bukti_transaksi"
                       accept="image/jpeg,image/png,application/pdf"
                       style="display:none;" onchange="previewGambar(this)">

                @error('bukti_transaksi')
                <p style="font-size:12px; color:#E11D48; margin-top:6px;">
                    <i class="ri-error-warning-line"></i> {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Tombol Aksi --}}
            <div style="display:flex; gap:12px; justify-content:flex-end;
                        padding-top:8px; border-top:1px solid var(--border);">
                <a href="{{ route('pembelian.index') }}"
                   style="padding:10px 20px; border:1px solid var(--border); border-radius:8px;
                          font-size:13px; font-weight:600; color:var(--text-secondary);
                          text-decoration:none; transition:all 0.2s;"
                   onmouseover="this.style.background='var(--bg-hover)'"
                   onmouseout="this.style.background='transparent'">
                    <i class="ri-close-line"></i> Batal
                </a>
                <button type="submit"
                        style="padding:10px 24px; background:#F59E0B; color:white;
                               border:none; border-radius:8px; font-size:13px; font-weight:600;
                               cursor:pointer; transition:background 0.2s; display:inline-flex;
                               align-items:center; gap:6px;"
                        onmouseover="this.style.background='#D97706'"
                        onmouseout="this.style.background='#F59E0B'">
                    <i class="ri-save-line"></i> Update Data
                </button>
            </div>

        </div>
    </form>
</div>

<script>
const inventoryData = @json($inventoryItems);

function hitungTotal() {
    const qty   = parseFloat(document.getElementById('inputJumlah').value) || 0;
    const harga = parseFloat(document.getElementById('inputHarga').value)  || 0;
    document.getElementById('previewTotal').textContent =
        'Rp ' + (qty * harga).toLocaleString('id-ID');
}

function updateInvoiceBadge(val) {
    const wrap = document.getElementById('invoiceBadgeWrap');
    const text = document.getElementById('invoiceBadgeText');
    if (val.trim()) {
        text.textContent   = val.trim();
        wrap.style.display = 'block';
    } else {
        wrap.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    hitungTotal();
    const existingInvoice = document.getElementById('inputNoInvoice').value;
    if (existingInvoice) updateInvoiceBadge(existingInvoice);
});

function showSuggestions() { filterSuggestions(); }
function hideSuggestions() { document.getElementById('suggestionBox').style.display = 'none'; }

function filterSuggestions() {
    const val      = document.getElementById('inputNamaBarang').value.toLowerCase().trim();
    const box      = document.getElementById('suggestionBox');
    const filtered = inventoryData.filter(i => i.nama_produk.toLowerCase().includes(val));

    const exact = inventoryData.find(i => i.nama_produk.toLowerCase() === val);
    if (exact) {
        document.getElementById('inventoryLinkAnchor').href      = '/inventory/' + exact.id;
        document.getElementById('inventoryLinkText').textContent = 'Lihat "' + exact.nama_produk + '" di Inventory';
        document.getElementById('inventoryLink').style.display   = 'block';
    } else {
        document.getElementById('inventoryLink').style.display = 'none';
    }

    if (!val || filtered.length === 0) { box.style.display = 'none'; return; }

    box.innerHTML = '';
    filtered.slice(0, 8).forEach(item => {
        const div = document.createElement('div');
        div.style.cssText =
            'padding:10px 14px; cursor:pointer; font-size:13.5px; ' +
            'color:var(--text-primary); border-bottom:1px solid var(--border); ' +
            'display:flex; align-items:center; gap:8px; background:var(--bg-card);';
        div.innerHTML =
            '<i class="ri-archive-drawer-line" style="color:var(--brand-500);"></i>' +
            '<span>' + item.nama_produk + '</span>';
        div.addEventListener('mouseenter', () => div.style.background = 'var(--bg-hover)');
        div.addEventListener('mouseleave', () => div.style.background = 'var(--bg-card)');
        div.addEventListener('mousedown', function () {
            document.getElementById('inputNamaBarang').value         = item.nama_produk;
            box.style.display                                         = 'none';
            document.getElementById('inventoryLinkAnchor').href      = '/inventory/' + item.id;
            document.getElementById('inventoryLinkText').textContent = 'Lihat "' + item.nama_produk + '" di Inventory';
            document.getElementById('inventoryLink').style.display   = 'block';
        });
        box.appendChild(div);
    });

    box.style.display = filtered.length > 0 ? 'block' : 'none';
}

// ── REVISI: Preview Bukti Pembayaran (support JPG/PNG/PDF) ──
function previewGambar(input) {
    if (!input.files || !input.files[0]) return;
    const file    = input.files[0];
    const allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
    if (!allowed.includes(file.type)) {
        alert('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
        input.value = '';
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 10 MB.');
        input.value = '';
        return;
    }

    document.getElementById('dropPlaceholder').style.display = 'none';
    document.getElementById('previewWrap').style.display     = 'block';
    document.getElementById('dropZone').style.borderColor    = 'var(--brand-500)';
    document.getElementById('dropZone').style.background     = 'var(--bg-hover)';
    document.getElementById('previewFileName').textContent   =
        file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';

    if (file.type === 'application/pdf') {
        document.getElementById('previewImgWrap').style.display = 'none';
        document.getElementById('previewPdfWrap').style.display = 'block';
    } else {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('previewImg').src               = e.target.result;
            document.getElementById('previewPdfWrap').style.display = 'none';
            document.getElementById('previewImgWrap').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function hapusGambar() {
    document.getElementById('inputBukti').value              = '';
    document.getElementById('previewImg').src                = '';
    document.getElementById('previewFileName').textContent   = '';
    document.getElementById('previewWrap').style.display     = 'none';
    document.getElementById('previewImgWrap').style.display  = 'none';
    document.getElementById('previewPdfWrap').style.display  = 'none';
    document.getElementById('dropPlaceholder').style.display = 'block';
    document.getElementById('dropZone').style.borderColor    = 'var(--border)';
    document.getElementById('dropZone').style.background     = 'var(--bg-primary)';
}

function handleDrop(event) {
    event.preventDefault();
    if (event.dataTransfer.files && event.dataTransfer.files[0]) {
        const input    = document.getElementById('inputBukti');
        const transfer = new DataTransfer();
        transfer.items.add(event.dataTransfer.files[0]);
        input.files = transfer.files;
        previewGambar(input);
    }
    document.getElementById('dropZone').style.borderColor = 'var(--border)';
    document.getElementById('dropZone').style.background  = 'var(--bg-primary)';
}

function toggleHapusBukti(checkbox) {
    const existing = document.getElementById('existingBukti');
    if (existing) existing.style.opacity = checkbox.checked ? '0.4' : '1';
}

// -- Preview File Invoice --
function previewInvoice(input) {
    if (!input.files || !input.files[0]) return;
    const file    = input.files[0];
    const allowed = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!allowed.includes(file.type)) {
        alert('Format tidak didukung. Gunakan PDF, JPG, atau PNG.');
        input.value = '';
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 10 MB.');
        input.value = '';
        return;
    }
    document.getElementById('dropPlaceholderInvoice').style.display = 'none';
    document.getElementById('previewInvoiceWrap').style.display     = 'block';
    document.getElementById('dropZoneInvoice').style.borderColor    = 'var(--brand-500)';
    document.getElementById('dropZoneInvoice').style.background     = 'var(--bg-hover)';
    document.getElementById('invoiceFileName').textContent =
        file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';

    if (file.type === 'application/pdf') {
        document.getElementById('previewInvoiceImg').style.display = 'none';
        document.getElementById('previewInvoicePdf').style.display = 'block';
    } else {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('invoiceImgEl').src                = e.target.result;
            document.getElementById('previewInvoicePdf').style.display = 'none';
            document.getElementById('previewInvoiceImg').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

function hapusInvoice() {
    document.getElementById('inputFileInvoice').value               = '';
    document.getElementById('invoiceImgEl').src                     = '';
    document.getElementById('invoiceFileName').textContent          = '';
    document.getElementById('previewInvoiceWrap').style.display     = 'none';
    document.getElementById('dropPlaceholderInvoice').style.display = 'block';
    document.getElementById('dropZoneInvoice').style.borderColor    = 'var(--border)';
    document.getElementById('dropZoneInvoice').style.background     = 'var(--bg-primary)';
    document.getElementById('previewInvoiceImg').style.display      = 'none';
    document.getElementById('previewInvoicePdf').style.display      = 'none';
}

function handleDropInvoice(event) {
    event.preventDefault();
    const dt = event.dataTransfer;
    if (dt.files && dt.files[0]) {
        const input    = document.getElementById('inputFileInvoice');
        const transfer = new DataTransfer();
        transfer.items.add(dt.files[0]);
        input.files = transfer.files;
        previewInvoice(input);
    }
    document.getElementById('dropZoneInvoice').style.borderColor = 'var(--border)';
    document.getElementById('dropZoneInvoice').style.background  = 'var(--bg-primary)';
}

function toggleHapusInvoice(checkbox) {
    const existing = document.getElementById('existingInvoice');
    if (existing) existing.style.opacity = checkbox.checked ? '0.4' : '1';
}
</script>

@endsection