{{-- resources/views/admin/pembelian/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Pembelian')
@section('breadcrumb', 'Tambah Pembelian')

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
</style>
@endpush

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
            Tambah Pembelian Barang
        </h1>
        <p style="font-size:13px; color:var(--text-muted);">
            Isi form berikut untuk mencatat pembelian barang baru
        </p>
    </div>
</div>

{{-- Info stok ringkas --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:24px;">
    @foreach([
        ['label' => 'Total Item',    'value' => $summary['total_item'],     'color' => '#3B82F6'],
        ['label' => 'Stok Tersedia', 'value' => $summary['total_tersedia'], 'color' => '#22C55E'],
        ['label' => 'Sedang Disewa', 'value' => $summary['total_disewa'],   'color' => '#F97316'],
        ['label' => 'Stok Bekas',    'value' => $summary['total_bekas'],    'color' => '#A855F7'],
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
    <form action="{{ route('pembelian.store') }}" method="POST"
          enctype="multipart/form-data" id="formPembelian">
        @csrf
        <div style="display:grid; gap:20px;">

            {{-- Tanggal Pembelian --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600;
                              color:var(--text-primary); margin-bottom:6px;">
                    Tanggal Pembelian <span style="color:#EF4444;">*</span>
                </label>
                <input type="date" name="tanggal_pembelian"
                       value="{{ old('tanggal_pembelian', date('Y-m-d')) }}"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'" required>
            </div>

            {{-- Nama Barang + Autocomplete --}}
            <div style="position:relative;">
                <label style="display:block; font-size:13px; font-weight:600;
                              color:var(--text-primary); margin-bottom:6px;">
                    Nama Barang <span style="color:#EF4444;">*</span>
                    <span style="font-weight:400; color:var(--text-muted); font-size:12px;">
                        — ketik untuk saran dari inventory
                    </span>
                </label>
                <input type="text" name="nama_barang" id="inputNamaBarang"
                       value="{{ old('nama_barang') }}"
                       placeholder="Contoh: Tensimeter Digital, Stetoskop, dll."
                       autocomplete="off"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'; showSuggestions()"
                       onblur="this.style.borderColor='var(--border)'; setTimeout(hideSuggestions, 200)"
                       oninput="filterSuggestions()" required>

                {{-- Dropdown suggestions --}}
                <div id="suggestionBox"
                     style="display:none; position:absolute; z-index:100; left:0; right:0;
                            top:100%; margin-top:4px; background:var(--bg-card);
                            border:1px solid var(--border); border-radius:8px;
                            box-shadow:0 8px 24px rgba(0,0,0,0.12);
                            max-height:200px; overflow-y:auto;">
                </div>

                {{-- Link ke inventory jika produk ditemukan --}}
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
                    <label style="display:block; font-size:13px; font-weight:600;
                                  color:var(--text-primary); margin-bottom:6px;">
                        Jumlah (Qty) <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="number" name="jumlah" id="inputJumlah"
                           value="{{ old('jumlah', 1) }}" min="1"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'"
                           oninput="hitungTotal()" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600;
                                  color:var(--text-primary); margin-bottom:6px;">
                        Harga Satuan (Rp) <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="number" name="harga_satuan" id="inputHarga"
                           value="{{ old('harga_satuan', 0) }}" min="0"
                           placeholder="0"
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
                <label style="display:block; font-size:13px; font-weight:600;
                              color:var(--text-primary); margin-bottom:6px;">
                    Kondisi Barang <span style="color:#EF4444;">*</span>
                </label>
                <div style="display:flex; gap:12px;">
                    <label class="kondisi-label">
                        <input type="radio" name="kondisi_barang" value="baru"
                               {{ old('kondisi_barang', 'baru') === 'baru' ? 'checked' : '' }}
                               style="accent-color:var(--brand-500);">
                        <span>
                            <i class="ri-checkbox-blank-circle-fill"
                               style="color:#3B82F6; margin-right:4px; font-size:11px;"></i>
                            Barang Baru
                        </span>
                    </label>
                    <label class="kondisi-label">
                        <input type="radio" name="kondisi_barang" value="bekas"
                               {{ old('kondisi_barang') === 'bekas' ? 'checked' : '' }}
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
                    Kondisi barang akan otomatis memperbarui kolom stok baru/bekas di Inventory.
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
                <label style="display:block; font-size:13px; font-weight:600;
                              color:var(--text-primary); margin-bottom:6px;">
                    Keterangan
                </label>
                <textarea name="keterangan" rows="3"
                          placeholder="Catatan tambahan, nama supplier, nomor faktur, dll. (opsional)"
                          style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                 border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                 color:var(--text-primary); outline:none; resize:vertical;
                                 transition:border-color 0.2s; font-family:inherit;"
                          onfocus="this.style.borderColor='var(--brand-500)'"
                          onblur="this.style.borderColor='var(--border)'">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Bukti Transaksi --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600;
                              color:var(--text-primary); margin-bottom:6px;">
                    Bukti Transaksi
                    <span style="font-weight:400; color:var(--text-muted); font-size:12px;">
                        — foto nota / kwitansi (opsional)
                    </span>
                </label>

                <div id="dropZone"
                     onclick="document.getElementById('inputBukti').click()"
                     style="border:2px dashed var(--border); border-radius:10px;
                            padding:28px 20px; text-align:center; cursor:pointer;
                            background:var(--bg-primary); transition:all 0.2s;"
                     ondragover="event.preventDefault();
                                 this.style.borderColor='var(--brand-500)';
                                 this.style.background='var(--bg-hover)';"
                     ondragleave="this.style.borderColor='var(--border)';
                                  this.style.background='var(--bg-primary)';"
                     ondrop="handleDrop(event)">

                    <div id="dropPlaceholder">
                        <i class="ri-image-add-line"
                           style="font-size:36px; color:var(--text-muted);
                                  display:block; margin-bottom:10px;"></i>
                        <p style="font-size:13px; font-weight:600;
                                  color:var(--text-secondary); margin:0 0 4px;">
                            Klik atau seret gambar ke sini
                        </p>
                        <p style="font-size:12px; color:var(--text-muted); margin:0;">
                            JPG, PNG, WEBP &mdash; maks. 2 MB
                        </p>
                    </div>

                    <div id="previewWrap" style="display:none;">
                        <img id="previewImg" src="" alt="Preview bukti"
                             style="max-height:200px; max-width:100%; border-radius:8px;
                                    object-fit:contain; display:block; margin:0 auto 10px;">
                        <p id="previewFileName"
                           style="font-size:12px; color:var(--text-muted); margin:0;"></p>
                        <button type="button"
                                onclick="event.stopPropagation(); hapusGambar()"
                                style="margin-top:10px; display:inline-flex; align-items:center;
                                       gap:4px; padding:5px 12px; border:1px solid #FCA5A5;
                                       border-radius:7px; background:#FFF1F2; color:#E11D48;
                                       font-size:12px; font-weight:600; cursor:pointer;
                                       font-family:inherit;">
                            <i class="ri-delete-bin-line"></i> Hapus Gambar
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
                        style="padding:10px 24px; background:var(--brand-500); color:white;
                               border:none; border-radius:8px; font-size:13px; font-weight:600;
                               cursor:pointer; transition:background 0.2s; display:inline-flex;
                               align-items:center; gap:6px;"
                        onmouseover="this.style.background='var(--brand-600)'"
                        onmouseout="this.style.background='var(--brand-500)'">
                    <i class="ri-save-line"></i> Simpan & Update Inventory
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

document.addEventListener('DOMContentLoaded', function () {
    hitungTotal();
});

// ── Autocomplete ──
function showSuggestions() { filterSuggestions(); }
function hideSuggestions() { document.getElementById('suggestionBox').style.display = 'none'; }

function filterSuggestions() {
    const val      = document.getElementById('inputNamaBarang').value.toLowerCase().trim();
    const box      = document.getElementById('suggestionBox');
    const filtered = inventoryData.filter(i => i.nama_produk.toLowerCase().includes(val));

    const exact = inventoryData.find(i => i.nama_produk.toLowerCase() === val);
    if (exact) {
        document.getElementById('inventoryLinkAnchor').href        = '/inventory/' + exact.id;
        document.getElementById('inventoryLinkText').textContent   = 'Lihat "' + exact.nama_produk + '" di Inventory';
        document.getElementById('inventoryLink').style.display     = 'block';
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
            document.getElementById('inputNamaBarang').value          = item.nama_produk;
            box.style.display                                          = 'none';
            document.getElementById('inventoryLinkAnchor').href       = '/inventory/' + item.id;
            document.getElementById('inventoryLinkText').textContent  = 'Lihat "' + item.nama_produk + '" di Inventory';
            document.getElementById('inventoryLink').style.display    = 'block';
        });
        box.appendChild(div);
    });

    if (!inventoryData.find(i => i.nama_produk.toLowerCase() === val)) {
        const divNew = document.createElement('div');
        divNew.style.cssText =
            'padding:10px 14px; cursor:pointer; font-size:13px; ' +
            'color:var(--brand-500); display:flex; align-items:center; gap:8px; ' +
            'background:var(--bg-card);';
        const inputVal = document.getElementById('inputNamaBarang').value;
        divNew.innerHTML =
            '<i class="ri-add-circle-line"></i>' +
            '<span>Tambahkan "<strong>' + inputVal + '</strong>" sebagai produk baru</span>';
        divNew.addEventListener('mouseenter', () => divNew.style.background = 'var(--bg-hover)');
        divNew.addEventListener('mouseleave', () => divNew.style.background = 'var(--bg-card)');
        divNew.addEventListener('mousedown', () => { box.style.display = 'none'; });
        box.appendChild(divNew);
    }

    box.style.display = 'block';
}

// ── Preview gambar ──
function previewGambar(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 2 MB.');
        input.value = '';
        return;
    }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src                = e.target.result;
        document.getElementById('previewFileName').textContent   = file.name;
        document.getElementById('dropPlaceholder').style.display = 'none';
        document.getElementById('previewWrap').style.display     = 'block';
        document.getElementById('dropZone').style.borderColor    = 'var(--brand-500)';
        document.getElementById('dropZone').style.background     = 'var(--bg-hover)';
    };
    reader.readAsDataURL(file);
}

function hapusGambar() {
    document.getElementById('inputBukti').value              = '';
    document.getElementById('previewImg').src                = '';
    document.getElementById('previewFileName').textContent   = '';
    document.getElementById('previewWrap').style.display     = 'none';
    document.getElementById('dropPlaceholder').style.display = 'block';
    document.getElementById('dropZone').style.borderColor    = 'var(--border)';
    document.getElementById('dropZone').style.background     = 'var(--bg-primary)';
}

function handleDrop(event) {
    event.preventDefault();
    const dt = event.dataTransfer;
    if (dt.files && dt.files[0]) {
        const input    = document.getElementById('inputBukti');
        const transfer = new DataTransfer();
        transfer.items.add(dt.files[0]);
        input.files = transfer.files;
        previewGambar(input);
    }
    document.getElementById('dropZone').style.borderColor = 'var(--border)';
    document.getElementById('dropZone').style.background  = 'var(--bg-primary)';
}
</script>

@endsection