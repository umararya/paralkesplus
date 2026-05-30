{{-- resources/views/admin/inventory/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Inventory')
@section('breadcrumb', 'Edit Data Inventory')

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
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">
            Edit Data Inventory
        </h1>
        <p style="font-size:13px; color:var(--text-muted);">
            Mengedit: <strong>{{ $inventory->nama_produk }}</strong>
        </p>
    </div>
</div>

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
    <form action="{{ route('inventory.update', $inventory->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="display:grid; gap:20px;">

            {{-- Nama Produk --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Nama Produk <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="nama_produk"
                       value="{{ old('nama_produk', $inventory->nama_produk) }}"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'" required>
            </div>

            {{-- Kategori & Satuan --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
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
                        @foreach([
                            'alat_diagnostik' => 'Alat Diagnostik',
                            'alat_terapi'     => 'Alat Terapi',
                            'alat_bedah'      => 'Alat Bedah',
                            'alat_bantu'      => 'Alat Bantu',
                            'konsumabel'      => 'Konsumabel',
                            'lainnya'         => 'Lainnya',
                        ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('kategori', $inventory->kategori) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Satuan <span style="color:#EF4444;">*</span>
                    </label>
                    <select name="satuan"
                            style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                   border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                   color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='var(--brand-500)'"
                            onblur="this.style.borderColor='var(--border)'" required>
                        @foreach([
                            'unit'  => 'Unit',
                            'pcs'   => 'Pcs',
                            'box'   => 'Box',
                            'set'   => 'Set',
                            'lusin' => 'Lusin',
                        ] as $val => $label)
                        <option value="{{ $val }}"
                            {{ old('satuan', $inventory->satuan) == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Stok (3 kolom) --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:10px;">
                    Data Stok <span style="color:#EF4444;">*</span>
                    <span style="font-weight:400; color:var(--text-muted); font-size:12px; margin-left:4px;">
                        — edit langsung nilai stok
                    </span>
                </label>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#1D4ED8; margin-bottom:6px;">
                            <i class="ri-checkbox-blank-circle-fill" style="color:#3B82F6; font-size:10px;"></i>
                            Stok Baru
                        </label>
                        <input type="number" name="stok_baru" id="editStokBaru"
                               value="{{ old('stok_baru', $inventory->stok_baru) }}" min="0"
                               style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                      border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                      color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                               onfocus="this.style.borderColor='var(--brand-500)'"
                               onblur="this.style.borderColor='var(--border)'"
                               oninput="updatePreview()" required>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#7C3AED; margin-bottom:6px;">
                            <i class="ri-checkbox-blank-circle-fill" style="color:#A855F7; font-size:10px;"></i>
                            Stok Bekas
                        </label>
                        <input type="number" name="stok_bekas" id="editStokBekas"
                               value="{{ old('stok_bekas', $inventory->stok_bekas) }}" min="0"
                               style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                      border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                      color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                               onfocus="this.style.borderColor='var(--brand-500)'"
                               onblur="this.style.borderColor='var(--border)'"
                               oninput="updatePreview()" required>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#B45309; margin-bottom:6px;">
                            <i class="ri-checkbox-blank-circle-fill" style="color:#F97316; font-size:10px;"></i>
                            Sedang Disewa
                        </label>
                        <input type="number" name="stok_disewa"
                               value="{{ old('stok_disewa', $inventory->stok_disewa) }}" min="0"
                               style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                      border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                      color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                               onfocus="this.style.borderColor='var(--brand-500)'"
                               onblur="this.style.borderColor='var(--border)'" required>
                    </div>
                </div>

                {{-- Preview total stok --}}
                <div style="background:var(--bg-hover); border:1px solid var(--border);
                            border-radius:8px; padding:12px 16px; margin-top:12px;
                            display:flex; align-items:center; justify-content:space-between;">
                    <span style="font-size:13px; font-weight:600; color:var(--text-secondary);">
                        <i class="ri-stack-line" style="margin-right:6px;"></i>Total Stok Tersedia (Baru + Bekas)
                    </span>
                    <span id="previewEditTotal"
                          style="font-size:16px; font-weight:700; color:#059669;">
                        {{ ($inventory->stok_baru ?? 0) + ($inventory->stok_bekas ?? 0) }} unit
                    </span>
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Keterangan
                    <span style="font-weight:400; color:var(--text-muted); font-size:12px;">(opsional)</span>
                </label>
                <textarea name="keterangan" rows="3"
                          placeholder="Deskripsi barang, lokasi penyimpanan, dll."
                          style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                 border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                 color:var(--text-primary); outline:none; resize:vertical;
                                 transition:border-color 0.2s; font-family:inherit;"
                          onfocus="this.style.borderColor='var(--brand-500)'"
                          onblur="this.style.borderColor='var(--border)'">{{ old('keterangan', $inventory->keterangan) }}</textarea>
            </div>

            {{-- Tombol Aksi --}}
            <div style="display:flex; gap:12px; justify-content:flex-end;
                        padding-top:8px; border-top:1px solid var(--border);">
                <a href="{{ route('inventory.index') }}"
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
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
            </div>

        </div>
    </form>
</div>

<script>
function updatePreview() {
    const baru  = parseInt(document.getElementById('editStokBaru').value)  || 0;
    const bekas = parseInt(document.getElementById('editStokBekas').value) || 0;
    const total = baru + bekas;
    const el    = document.getElementById('previewEditTotal');
    el.textContent  = total + ' unit';
    el.style.color  = total > 0 ? '#059669' : '#DC2626';
}
</script>
@endsection