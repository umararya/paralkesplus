@extends('admin.layouts.app')

@section('title', 'Tambah Barang')
@section('breadcrumb', 'Tambah Barang Inventory')

@section('content')
<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="{{ route('inventory.index') }}"
       style="display:inline-flex; align-items:center; justify-content:center;
              width:36px; height:36px; border-radius:8px; background:var(--bg-card);
              border:1px solid var(--border); color:var(--text-secondary);
              text-decoration:none; transition:all 0.2s;"
       onmouseover="this.style.background='var(--bg-hover)'"
       onmouseout="this.style.background='var(--bg-card)'">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div>
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">Tambah Barang Inventory</h1>
        <p style="font-size:13px; color:var(--text-muted);">Isi form berikut untuk menambah barang baru ke inventory</p>
    </div>
</div>

<div class="card" style="max-width:720px;">
    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        <div style="display:grid; gap:20px;">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Kode Barang <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="text" name="kode_barang" value="{{ old('kode_barang') }}"
                           placeholder="Contoh: BRG-001"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Nama Barang <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"
                           placeholder="Nama alat/barang kesehatan"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'" required>
                </div>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Kategori
                </label>
                <select name="kategori"
                        style="width:100%; padding:10px 14px; border:1px solid var(--border);
                               border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                               color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='var(--brand-500)'"
                        onblur="this.style.borderColor='var(--border)'">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="alat_diagnostik" {{ old('kategori') == 'alat_diagnostik' ? 'selected' : '' }}>Alat Diagnostik</option>
                    <option value="alat_terapi" {{ old('kategori') == 'alat_terapi' ? 'selected' : '' }}>Alat Terapi</option>
                    <option value="alat_bedah" {{ old('kategori') == 'alat_bedah' ? 'selected' : '' }}>Alat Bedah</option>
                    <option value="konsumabel" {{ old('kategori') == 'konsumabel' ? 'selected' : '' }}>Konsumabel</option>
                    <option value="lainnya" {{ old('kategori') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Jumlah Stok <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="number" name="stok" value="{{ old('stok', 0) }}" min="0"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Satuan
                    </label>
                    <select name="satuan"
                            style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                   border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                   color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='var(--brand-500)'"
                            onblur="this.style.borderColor='var(--border)'">
                        <option value="unit" {{ old('satuan') == 'unit' ? 'selected' : '' }}>Unit</option>
                        <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                        <option value="box" {{ old('satuan') == 'box' ? 'selected' : '' }}>Box</option>
                        <option value="set" {{ old('satuan') == 'set' ? 'selected' : '' }}>Set</option>
                        <option value="lusin" {{ old('satuan') == 'lusin' ? 'selected' : '' }}>Lusin</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Kondisi Barang
                </label>
                <select name="kondisi"
                        style="width:100%; padding:10px 14px; border:1px solid var(--border);
                               border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                               color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='var(--brand-500)'"
                        onblur="this.style.borderColor='var(--border)'">
                    <option value="baik" {{ old('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak_ringan" {{ old('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ old('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                    <option value="dalam_perbaikan" {{ old('kondisi') == 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                </select>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Lokasi Penyimpanan
                </label>
                <input type="text" name="lokasi" value="{{ old('lokasi') }}"
                       placeholder="Contoh: Gudang A, Rak 3"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'">
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Keterangan
                </label>
                <textarea name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"
                          style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                 border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                 color:var(--text-primary); outline:none; resize:vertical;
                                 transition:border-color 0.2s; font-family:inherit;"
                          onfocus="this.style.borderColor='var(--brand-500)'"
                          onblur="this.style.borderColor='var(--border)'">{{ old('keterangan') }}</textarea>
            </div>

            <div style="display:flex; gap:12px; justify-content:flex-end; padding-top:8px; border-top:1px solid var(--border);">
                <a href="{{ route('inventory.index') }}"
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