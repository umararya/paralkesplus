{{-- resources/views/admin/penjualan/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Penjualan')
@section('breadcrumb', 'Edit Penjualan')

@push('styles')
<style>
    .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); padding:28px; max-width:760px; }
    .form-grid { display:grid; gap:20px; }
    .form-row-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .form-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
    .form-group label { display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px; }
    .form-group label span.req { color:#EF4444; }
    .form-control { width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13.5px; background:var(--bg-primary); color:var(--text-primary); outline:none; transition:border-color 0.2s, box-shadow 0.2s; font-family:inherit; box-sizing:border-box; }
    .form-control:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .form-control.is-invalid { border-color:#EF4444; }
    .invalid-feedback { font-size:12px; color:#EF4444; margin-top:4px; display:block; }
    .form-footer { display:flex; gap:12px; justify-content:flex-end; padding-top:20px; border-top:1px solid var(--border); margin-top:4px; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 20px; height:40px; border-radius:8px; font-size:13.5px; font-weight:600; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); border-color:var(--brand-600); }
    .btn-secondary { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-secondary:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-danger-sm { background:transparent; color:#EF4444; border:1px solid #FCA5A5; border-radius:7px; padding:0 12px; height:32px; font-size:12.5px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; transition:all 0.2s; font-family:inherit; }
    .btn-danger-sm:hover { background:#FFF1F2; border-color:#EF4444; }
    .page-header-back { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
    .back-btn { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:8px; background:var(--bg-card); border:1px solid var(--border); color:var(--text-secondary); text-decoration:none; transition:all 0.2s; }
    .back-btn:hover { background:var(--bg-hover); color:var(--text-primary); }
    .page-header-back h1 { font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px; }
    .page-header-back p { font-size:13px; color:var(--text-muted); }
    .total-preview { background:var(--bg-primary); border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13.5px; color:var(--text-muted); display:flex; align-items:center; justify-content:space-between; }
    .total-preview strong { color:#059669; font-size:15px; }
    html.dark .total-preview strong { color:#34D399; }

    /* Upload Foto */
    .upload-area { border:2px dashed var(--border); border-radius:10px; padding:24px 16px; text-align:center; cursor:pointer; transition:border-color 0.2s, background 0.2s; background:var(--bg-primary); position:relative; }
    .upload-area:hover, .upload-area.dragover { border-color:var(--brand-500); background:var(--brand-50); }
    html.dark .upload-area:hover, html.dark .upload-area.dragover { background:rgba(29,111,164,0.08); }
    .upload-area input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .upload-icon { font-size:36px; color:var(--brand-500); margin-bottom:8px; display:block; }
    .upload-text { font-size:13.5px; color:var(--text-primary); font-weight:600; margin-bottom:4px; }
    .upload-hint { font-size:12px; color:var(--text-muted); }
    .foto-preview-wrap { margin-top:14px; }
    .foto-existing { display:flex; align-items:center; gap:14px; padding:12px 14px; background:var(--bg-primary); border:1px solid var(--border); border-radius:10px; }
    .foto-existing img { width:70px; height:70px; object-fit:cover; border-radius:8px; border:1px solid var(--border); cursor:pointer; }
    .foto-existing-info { flex:1; }
    .foto-existing-info p { font-size:13px; color:var(--text-primary); font-weight:600; margin-bottom:4px; }
    .foto-existing-info span { font-size:12px; color:var(--text-muted); }
    .foto-new-wrap { display:none; }
    .foto-new-wrap.show { display:block; }
    .foto-preview-inner { position:relative; display:inline-block; margin-top:10px; }
    .foto-preview-img { width:120px; height:120px; object-fit:cover; border-radius:10px; border:2px solid var(--brand-500); display:block; }
    .foto-preview-remove { position:absolute; top:-8px; right:-8px; width:22px; height:22px; border-radius:50%; background:#EF4444; color:#fff; border:none; cursor:pointer; font-size:13px; display:flex; align-items:center; justify-content:center; }
    .foto-preview-name { font-size:12px; color:var(--text-muted); margin-top:6px; text-align:center; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
</style>
@endpush

@section('content')

<div class="page-header-back">
    <a href="{{ route('penjualan.index') }}" class="back-btn">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div>
        <h1>Edit Penjualan</h1>
        <p>Perbarui data transaksi penjualan #{{ $penjualan->id }}</p>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('penjualan.update', $penjualan->id) }}" method="POST"
          enctype="multipart/form-data" id="formPenjualan">
        @csrf
        @method('PUT')
        <div class="form-grid">

            {{-- Baris 1: Tanggal & Nama Barang --}}
            <div class="form-row-2">
                <div class="form-group">
                    <label>Tanggal Penjualan <span class="req">*</span></label>
                    <input type="date" name="tanggal_penjualan"
                           value="{{ old('tanggal_penjualan', \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->format('Y-m-d')) }}"
                           class="form-control @error('tanggal_penjualan') is-invalid @enderror"
                           required>
                    @error('tanggal_penjualan')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Nama Barang <span class="req">*</span></label>
                    <input type="text" name="nama_barang"
                           value="{{ old('nama_barang', $penjualan->nama_barang) }}"
                           placeholder="Nama alat/produk yang dijual"
                           class="form-control @error('nama_barang') is-invalid @enderror"
                           required>
                    @error('nama_barang')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Baris 2: Qty, Harga, Jenis Pembayaran --}}
            <div class="form-row-3">
                <div class="form-group">
                    <label>Qty <span class="req">*</span></label>
                    <input type="number" name="qty" id="inputQty"
                           value="{{ old('qty', $penjualan->qty) }}" min="1"
                           class="form-control @error('qty') is-invalid @enderror"
                           oninput="hitungTotal()" required>
                    @error('qty') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Harga Satuan (Rp) <span class="req">*</span></label>
                    <input type="number" name="harga" id="inputHarga"
                           value="{{ old('harga', $penjualan->harga) }}" min="0" placeholder="0"
                           class="form-control @error('harga') is-invalid @enderror"
                           oninput="hitungTotal()" required>
                    @error('harga') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Jenis Pembayaran <span class="req">*</span></label>
                    <select name="jenis_pembayaran"
                            class="form-control @error('jenis_pembayaran') is-invalid @enderror"
                            required>
                        <option value="">-- Pilih --</option>
                        @foreach(['tunai' => 'Tunai', 'transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'kredit' => 'Kredit'] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('jenis_pembayaran') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Preview Total --}}
            <div class="total-preview">
                <span><i class="ri-calculator-line"></i> Estimasi Total</span>
                <strong id="previewTotal">Rp 0</strong>
            </div>

            {{-- Alamat Pelanggan --}}
            <div class="form-group">
                <label>Alamat Pelanggan <span class="req">*</span></label>
                <textarea name="alamat_pelanggan" rows="2"
                          placeholder="Masukkan alamat lengkap pelanggan"
                          class="form-control @error('alamat_pelanggan') is-invalid @enderror"
                          required>{{ old('alamat_pelanggan', $penjualan->alamat_pelanggan) }}</textarea>
                @error('alamat_pelanggan') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Upload / Ganti Foto Bukti Transfer --}}
            <div class="form-group">
                <label>Foto Bukti Transfer
                    <span style="font-weight:400;color:var(--text-muted);font-size:12px;">(opsional)</span>
                </label>

                @if($penjualan->foto_bukti)
                {{-- Sudah ada foto lama --}}
                <div class="foto-existing" id="fotoExisting">
                    <img src="{{ Storage::url($penjualan->foto_bukti) }}"
                         alt="Bukti Transfer"
                         onclick="window.open('{{ Storage::url($penjualan->foto_bukti) }}', '_blank')"
                         title="Klik untuk lihat penuh">
                    <div class="foto-existing-info">
                        <p><i class="ri-image-line"></i> Foto sudah ada</p>
                        <span>Klik gambar untuk melihat penuh</span>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;">
                        <button type="button" class="btn-danger-sm" onclick="hapusFotoLama()">
                            <i class="ri-delete-bin-line"></i> Hapus
                        </button>
                        <button type="button" class="btn-danger-sm" style="color:var(--brand-500);border-color:var(--brand-100);"
                                onclick="gantiPhoto()">
                            <i class="ri-refresh-line"></i> Ganti
                        </button>
                    </div>
                </div>
                <input type="hidden" name="hapus_foto" id="inputHapusFoto" value="0">
                @endif

                {{-- Area upload baru (muncul jika belum ada foto atau klik ganti) --}}
                <div id="uploadAreaWrap" style="{{ $penjualan->foto_bukti ? 'display:none;' : '' }} margin-top: {{ $penjualan->foto_bukti ? '10px' : '0' }};">
                    <div class="upload-area" id="uploadArea"
                         ondragover="onDragOver(event)" ondragleave="onDragLeave(event)" ondrop="onDrop(event)">
                        <input type="file" name="foto_bukti" id="inputFotoBukti"
                               accept="image/jpg,image/jpeg,image/png,image/webp"
                               onchange="onFileSelected(event)">
                        <i class="ri-upload-cloud-2-line upload-icon"></i>
                        <p class="upload-text">Klik atau seret foto ke sini</p>
                        <p class="upload-hint">JPG, PNG, WEBP — maks. 2 MB</p>
                    </div>
                    @if($penjualan->foto_bukti)
                    <button type="button" onclick="batalGanti()"
                            style="margin-top:8px; font-size:12px; color:var(--text-muted); background:none; border:none; cursor:pointer; text-decoration:underline;">
                        ← Batal ganti foto
                    </button>
                    @endif
                </div>

                {{-- Preview foto baru --}}
                <div class="foto-new-wrap" id="fotoNewWrap">
                    <div class="foto-preview-inner">
                        <img id="fotoPreviewImg" src="" alt="Preview Baru" class="foto-preview-img">
                        <button type="button" class="foto-preview-remove" onclick="removeFoto()">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <p class="foto-preview-name" id="fotoPreviewName"></p>
                </div>

                @error('foto_bukti')
                    <span class="invalid-feedback" style="display:block;">{{ $message }}</span>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" rows="3"
                          placeholder="Catatan tambahan (opsional)"
                          class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $penjualan->keterangan) }}</textarea>
                @error('keterangan') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            {{-- Footer --}}
            <div class="form-footer">
                <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
                    <i class="ri-close-line"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Perbarui Data
                </button>
            </div>

        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    function hitungTotal() {
        const qty   = parseFloat(document.getElementById('inputQty').value)   || 0;
        const harga = parseFloat(document.getElementById('inputHarga').value) || 0;
        document.getElementById('previewTotal').textContent =
            'Rp ' + (qty * harga).toLocaleString('id-ID');
    }
    hitungTotal();

    // Hapus foto lama (set hidden input + sembunyikan kartu foto lama)
    function hapusFotoLama() {
        if (!confirm('Yakin ingin menghapus foto bukti transfer ini?')) return;
        document.getElementById('inputHapusFoto').value = '1';
        document.getElementById('fotoExisting').style.display = 'none';
        document.getElementById('uploadAreaWrap').style.display = 'block';
    }

    // Ganti foto lama — tampilkan area upload tanpa hapus dulu
    function gantiPhoto() {
        document.getElementById('fotoExisting').style.display = 'none';
        document.getElementById('uploadAreaWrap').style.display = 'block';
    }

    // Batal ganti
    function batalGanti() {
        document.getElementById('uploadAreaWrap').style.display = 'none';
        document.getElementById('fotoExisting').style.display  = 'flex';
        document.getElementById('fotoNewWrap').classList.remove('show');
        document.getElementById('inputFotoBukti').value = '';
    }

    // Preview file baru
    function onFileSelected(e) {
        const file = e.target.files[0];
        if (!file) return;
        showPreview(file);
    }

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('fotoPreviewImg').src = ev.target.result;
            document.getElementById('fotoPreviewName').textContent = file.name;
            document.getElementById('fotoNewWrap').classList.add('show');
            document.getElementById('uploadArea').style.display = 'none';
        };
        reader.readAsDataURL(file);
    }

    function removeFoto() {
        document.getElementById('inputFotoBukti').value = '';
        document.getElementById('fotoNewWrap').classList.remove('show');
        document.getElementById('uploadArea').style.display = 'block';
        document.getElementById('fotoPreviewImg').src = '';
    }

    // Drag & Drop
    function onDragOver(e)  { e.preventDefault(); document.getElementById('uploadArea').classList.add('dragover'); }
    function onDragLeave(e) { document.getElementById('uploadArea').classList.remove('dragover'); }
    function onDrop(e) {
        e.preventDefault();
        document.getElementById('uploadArea').classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('inputFotoBukti').files = dt.files;
            showPreview(file);
        }
    }
</script>
@endpush