@extends('admin.layouts.app')

@section('title', 'Tambah Pembelian')
@section('breadcrumb', 'Tambah Pembelian')

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
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">Tambah Pembelian Barang</h1>
        <p style="font-size:13px; color:var(--text-muted);">Isi form berikut untuk mencatat pembelian barang baru</p>
    </div>
</div>

<div class="card" style="max-width:720px;">
    <form action="{{ route('pembelian.store') }}" method="POST">
        @csrf
        <div style="display:grid; gap:20px;">

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Nama Barang <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"
                       placeholder="Masukkan nama barang"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'" required>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Supplier / Pemasok
                </label>
                <input type="text" name="supplier" value="{{ old('supplier') }}"
                       placeholder="Nama supplier atau toko"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Jumlah <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="number" name="jumlah" value="{{ old('jumlah', 1) }}" min="1"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Harga Satuan (Rp) <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="number" name="harga_satuan" value="{{ old('harga_satuan') }}" min="0"
                           placeholder="0"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'" required>
                </div>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Tanggal Pembelian <span style="color:#EF4444;">*</span>
                </label>
                <input type="date" name="tanggal_pembelian" value="{{ old('tanggal_pembelian', date('Y-m-d')) }}"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'" required>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Catatan
                </label>
                <textarea name="catatan" rows="3" placeholder="Catatan tambahan (opsional)"
                          style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                 border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                 color:var(--text-primary); outline:none; resize:vertical;
                                 transition:border-color 0.2s; font-family:inherit;"
                          onfocus="this.style.borderColor='var(--brand-500)'"
                          onblur="this.style.borderColor='var(--border)'">{{ old('catatan') }}</textarea>
            </div>

            <div style="display:flex; gap:12px; justify-content:flex-end; padding-top:8px; border-top:1px solid var(--border);">
                <a href="{{ route('pembelian.index') }}"
                   style="padding:10px 20px; border:1px solid var(--border); border-radius:8px;
                          font-size:13px; font-weight:600; color:var(--text-secondary);
                          text-decoration:none; transition:all 0.2s;"
                   onmouseover="this.style.background='var(--bg-hover)'"
                   onmouseout="this.style.background='transparent'">
                    Batal
                </a>
                <button type="submit"
                        style="padding:10px 24px; background:var(--brand-500); color:white;
                               border:none; border-radius:8px; font-size:13px; font-weight:600;
                               cursor:pointer; transition:background 0.2s;"
                        onmouseover="this.style.background='var(--brand-600)'"
                        onmouseout="this.style.background='var(--brand-500)'">
                    <i class="ri-save-line"></i> Simpan Data
                </button>
            </div>

        </div>
    </form>
</div>
@endsection