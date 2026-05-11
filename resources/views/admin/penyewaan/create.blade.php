@extends('admin.layouts.app')

@section('title', 'Tambah Penyewaan')
@section('breadcrumb', 'Tambah Penyewaan')

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
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">Tambah Penyewaan</h1>
        <p style="font-size:13px; color:var(--text-muted);">Isi form berikut untuk menambah data penyewaan baru</p>
    </div>
</div>

<div class="card" style="max-width:720px;">
    <form action="{{ route('penyewaan.store') }}" method="POST">
        @csrf
        <div style="display:grid; gap:20px;">

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Nama Penyewa <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="nama_penyewa" value="{{ old('nama_penyewa') }}"
                       placeholder="Masukkan nama penyewa"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'" required>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Alat Kesehatan <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="alat_kesehatan" value="{{ old('alat_kesehatan') }}"
                       placeholder="Nama alat kesehatan yang disewa"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'" required>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Tanggal Mulai Sewa <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="date" name="tanggal_sewa" value="{{ old('tanggal_sewa') }}"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'" required>
                </div>
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Tanggal Kembali <span style="color:#EF4444;">*</span>
                    </label>
                    <input type="date" name="tanggal_kembali" value="{{ old('tanggal_kembali') }}"
                           style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                  border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                  color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                           onfocus="this.style.borderColor='var(--brand-500)'"
                           onblur="this.style.borderColor='var(--border)'" required>
                </div>
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Biaya Sewa (Rp)
                </label>
                <input type="number" name="biaya_sewa" value="{{ old('biaya_sewa') }}"
                       placeholder="0"
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'">
            </div>

            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Status
                </label>
                <select name="status"
                        style="width:100%; padding:10px 14px; border:1px solid var(--border);
                               border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                               color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='var(--brand-500)'"
                        onblur="this.style.borderColor='var(--border)'">
                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="terlambat" {{ old('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                </select>
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
                <a href="{{ route('penyewaan.index') }}"
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