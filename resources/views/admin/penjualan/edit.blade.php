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
    .page-header-back { display:flex; align-items:center; gap:12px; margin-bottom:24px; }
    .back-btn { display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:8px; background:var(--bg-card); border:1px solid var(--border); color:var(--text-secondary); text-decoration:none; transition:all 0.2s; }
    .back-btn:hover { background:var(--bg-hover); color:var(--text-primary); }
    .page-header-back h1 { font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px; }
    .page-header-back p { font-size:13px; color:var(--text-muted); }
    .total-preview { background:var(--bg-primary); border:1px solid var(--border); border-radius:8px; padding:10px 14px; font-size:13.5px; color:var(--text-muted); display:flex; align-items:center; justify-content:space-between; }
    .total-preview strong { color:#059669; font-size:15px; }
    html.dark .total-preview strong { color:#34D399; }
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
    <form action="{{ route('penjualan.update', $penjualan->id) }}" method="POST" id="formPenjualan">
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
                    @error('qty')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label>Harga Satuan (Rp) <span class="req">*</span></label>
                    <input type="number" name="harga" id="inputHarga"
                           value="{{ old('harga', $penjualan->harga) }}" min="0" placeholder="0"
                           class="form-control @error('harga') is-invalid @enderror"
                           oninput="hitungTotal()" required>
                    @error('harga')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
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
                    @error('jenis_pembayaran')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
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
                @error('alamat_pelanggan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" rows="3"
                          placeholder="Catatan tambahan (opsional)"
                          class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $penjualan->keterangan) }}</textarea>
                @error('keterangan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
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
        const total = qty * harga;
        document.getElementById('previewTotal').textContent =
            'Rp ' + total.toLocaleString('id-ID');
    }
    hitungTotal();
</script>
@endpush