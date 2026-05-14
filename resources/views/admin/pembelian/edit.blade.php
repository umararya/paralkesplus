{{-- resources/views/admin/pembelian/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Pembelian')
@section('breadcrumb', 'Edit Pembelian')

@section('content')
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
            Mengedit data: <strong>{{ $pembelian->nama_barang }}</strong>
        </p>
    </div>
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
    {{-- enctype wajib untuk upload file --}}
    <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST"
          enctype="multipart/form-data" id="formEditPembelian">
        @csrf
        @method('PUT')
        <div style="display:grid; gap:20px;">

            {{-- Tanggal Pembelian --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
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

            {{-- Nama Barang --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Nama Barang <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="nama_barang"
                       value="{{ old('nama_barang', $pembelian->nama_barang) }}"
                       placeholder="Contoh: Tensimeter Digital, Stetoskop, dll."
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'" required>
            </div>

            {{-- Jumlah & Harga Satuan --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
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
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
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
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Keterangan
                </label>
                <textarea name="keterangan" rows="3"
                          placeholder="Catatan tambahan, nama supplier, nomor faktur, dll. (opsional)"
                          style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                 border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                 color:var(--text-primary); outline:none; resize:vertical;
                                 transition:border-color 0.2s; font-family:inherit;"
                          onfocus="this.style.borderColor='var(--brand-500)'"
                          onblur="this.style.borderColor='var(--border)'">{{ old('keterangan', $pembelian->keterangan) }}</textarea>
            </div>

            {{-- ══════════════ BUKTI TRANSAKSI ══════════════ --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Bukti Transaksi
                    <span style="font-weight:400; color:var(--text-muted); font-size:12px;">
                        — foto nota / kwitansi (opsional)
                    </span>
                </label>

                {{-- Tampilkan gambar existing jika ada --}}
                @if($pembelian->bukti_transaksi)
                <div id="existingBukti"
                     style="display:flex; align-items:center; gap:14px; padding:12px 14px;
                            background:var(--bg-hover); border:1px solid var(--border);
                            border-radius:10px; margin-bottom:12px; transition:opacity 0.2s;">
                    <img src="{{ asset('storage/' . $pembelian->bukti_transaksi) }}"
                         alt="Bukti Transaksi"
                         style="width:64px; height:64px; border-radius:8px; object-fit:cover;
                                border:1px solid var(--border); cursor:pointer; flex-shrink:0;"
                         onclick="window.open('{{ asset('storage/' . $pembelian->bukti_transaksi) }}', '_blank')">
                    <div style="flex:1; min-width:0;">
                        <p style="font-size:13px; font-weight:600; color:var(--text-primary); margin:0 0 4px;">
                            <i class="ri-image-line" style="color:var(--brand-500);"></i> Bukti tersedia
                        </p>
                        <p style="font-size:12px; color:var(--text-muted); margin:0;">
                            Klik gambar untuk lihat penuh. Upload baru untuk mengganti.
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

                {{-- Drop zone upload baru --}}
                <div id="dropZone"
                     onclick="document.getElementById('inputBukti').click()"
                     style="border:2px dashed var(--border); border-radius:10px;
                            padding:22px 20px; text-align:center; cursor:pointer;
                            background:var(--bg-primary); transition:all 0.2s;"
                     ondragover="event.preventDefault();
                                 this.style.borderColor='var(--brand-500)';
                                 this.style.background='var(--brand-50)';"
                     ondragleave="this.style.borderColor='var(--border)';
                                  this.style.background='var(--bg-primary)';"
                     ondrop="handleDrop(event)">

                    <div id="dropPlaceholder">
                        <i class="ri-image-add-line"
                           style="font-size:32px; color:var(--text-muted); display:block; margin-bottom:8px;"></i>
                        <p style="font-size:13px; font-weight:600; color:var(--text-secondary); margin:0 0 3px;">
                            @if($pembelian->bukti_transaksi)
                                Upload gambar baru untuk mengganti
                            @else
                                Klik atau seret gambar ke sini
                            @endif
                        </p>
                        <p style="font-size:12px; color:var(--text-muted); margin:0;">
                            JPG, PNG, WEBP &mdash; maks. 2 MB
                        </p>
                    </div>

                    <div id="previewWrap" style="display:none;">
                        <img id="previewImg" src="" alt="Preview bukti"
                             style="max-height:180px; max-width:100%; border-radius:8px;
                                    object-fit:contain; display:block; margin:0 auto 10px;">
                        <p id="previewFileName"
                           style="font-size:12px; color:var(--text-muted); margin:0;"></p>
                        <button type="button"
                                onclick="event.stopPropagation(); hapusGambar()"
                                style="margin-top:8px; display:inline-flex; align-items:center; gap:4px;
                                       padding:4px 12px; border:1px solid #FCA5A5; border-radius:7px;
                                       background:#FFF1F2; color:#E11D48; font-size:12px;
                                       font-weight:600; cursor:pointer; font-family:inherit;">
                            <i class="ri-delete-bin-line"></i> Batal pilih
                        </button>
                    </div>
                </div>

                <input type="file" id="inputBukti" name="bukti_transaksi"
                       accept="image/jpeg,image/png,image/webp"
                       style="display:none;" onchange="previewGambar(this)">

                @error('bukti_transaksi')
                <p style="font-size:12px; color:#E11D48; margin-top:6px;">
                    <i class="ri-error-warning-line"></i> {{ $message }}
                </p>
                @enderror
            </div>
            {{-- ══════════════════════════════════════════════ --}}

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
// ── Hitung total ──
function hitungTotal() {
    const qty   = parseFloat(document.getElementById('inputJumlah').value) || 0;
    const harga = parseFloat(document.getElementById('inputHarga').value)  || 0;
    document.getElementById('previewTotal').textContent =
        'Rp ' + (qty * harga).toLocaleString('id-ID');
}
document.addEventListener('DOMContentLoaded', hitungTotal);

// ── Preview gambar dari input file ──
function previewGambar(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2 MB.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src              = e.target.result;
            document.getElementById('previewFileName').textContent = file.name;
            document.getElementById('dropPlaceholder').style.display = 'none';
            document.getElementById('previewWrap').style.display     = 'block';
            document.getElementById('dropZone').style.borderColor    = 'var(--brand-500)';
            document.getElementById('dropZone').style.background     = 'var(--brand-50)';
        };
        reader.readAsDataURL(file);
    }
}

// ── Batal pilih gambar baru ──
function hapusGambar() {
    document.getElementById('inputBukti').value                   = '';
    document.getElementById('previewImg').src                     = '';
    document.getElementById('previewFileName').textContent        = '';
    document.getElementById('previewWrap').style.display          = 'none';
    document.getElementById('dropPlaceholder').style.display      = 'block';
    document.getElementById('dropZone').style.borderColor         = 'var(--border)';
    document.getElementById('dropZone').style.background          = 'var(--bg-primary)';
}

// ── Drag & drop ──
function handleDrop(event) {
    event.preventDefault();
    if (event.dataTransfer.files && event.dataTransfer.files[0]) {
        const input    = document.getElementById('inputBukti');
        const transfer = new DataTransfer();
        transfer.items.add(event.dataTransfer.files[0]);
        input.files = transfer.files;
        previewGambar(input);
    }
    const zone = document.getElementById('dropZone');
    zone.style.borderColor = 'var(--border)';
    zone.style.background  = 'var(--bg-primary)';
}

// ── Toggle opacity saat centang hapus bukti ──
function toggleHapusBukti(checkbox) {
    const existing = document.getElementById('existingBukti');
    if (existing) {
        existing.style.opacity = checkbox.checked ? '0.4' : '1';
    }
}
</script>
@endsection