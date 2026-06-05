<?php $__env->startSection('title', 'Input Data Inventory'); ?>
<?php $__env->startSection('breadcrumb', 'Input Data Manual Inventory'); ?>

<?php $__env->startSection('content'); ?>
<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="<?php echo e(route('inventory.index')); ?>"
       style="display:inline-flex; align-items:center; justify-content:center;
              width:36px; height:36px; border-radius:8px; background:var(--bg-card);
              border:1px solid var(--border); color:var(--text-secondary);
              text-decoration:none; transition:all 0.2s;"
       onmouseover="this.style.background='var(--bg-hover)'"
       onmouseout="this.style.background='var(--bg-card)'">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div>
        <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin-bottom:2px;">Input Data Manual Inventory</h1>
        <p style="font-size:13px; color:var(--text-muted);">Tambah atau perbarui data stok secara manual</p>
    </div>
</div>


<div style="background:#EFF6FF; border:1px solid #BFDBFE; border-radius:8px;
            padding:12px 16px; margin-bottom:20px; display:flex; align-items:flex-start; gap:10px;">
    <i class="ri-information-line" style="color:#3B82F6; font-size:18px; flex-shrink:0; margin-top:1px;"></i>
    <div style="font-size:13px; color:#1D4ED8; line-height:1.6;">
        <strong>Catatan:</strong> Jika nama produk sudah ada di inventory (case-insensitive),
        sistem akan <strong>menambahkan stok</strong> ke data yang sudah ada, bukan membuat entri baru.
    </div>
</div>

<?php if($errors->any()): ?>
<div style="background:#FEF2F2; border:1px solid #FCA5A5; color:#991B1B;
            padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px;">
    <div style="display:flex; align-items:center; gap:8px; font-weight:600; margin-bottom:8px;">
        <i class="ri-error-warning-line"></i> Terdapat kesalahan input:
    </div>
    <ul style="margin:0; padding-left:20px;">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>

<div class="card" style="max-width:720px;">
    <form action="<?php echo e(route('inventory.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div style="display:grid; gap:20px;">

            
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                    Nama Produk <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="nama_produk" value="<?php echo e(old('nama_produk')); ?>"
                       placeholder="Contoh: Tensimeter Digital, Kursi Roda, dll."
                       style="width:100%; padding:10px 14px; border:1px solid var(--border);
                              border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                              color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                       onfocus="this.style.borderColor='var(--brand-500)'"
                       onblur="this.style.borderColor='var(--border)'" required>
            </div>

            
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
                        <option value="alat_diagnostik" <?php echo e(old('kategori') == 'alat_diagnostik' ? 'selected' : ''); ?>>Alat Diagnostik</option>
                        <option value="alat_terapi"     <?php echo e(old('kategori') == 'alat_terapi'     ? 'selected' : ''); ?>>Alat Terapi</option>
                        <option value="alat_bedah"      <?php echo e(old('kategori') == 'alat_bedah'      ? 'selected' : ''); ?>>Alat Bedah</option>
                        <option value="alat_bantu"      <?php echo e(old('kategori') == 'alat_bantu'      ? 'selected' : ''); ?>>Alat Bantu</option>
                        <option value="konsumabel"      <?php echo e(old('kategori') == 'konsumabel'      ? 'selected' : ''); ?>>Konsumabel</option>
                        <option value="lainnya"         <?php echo e(old('kategori') == 'lainnya'         ? 'selected' : ''); ?>>Lainnya</option>
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
                        <option value="unit"  <?php echo e(old('satuan', 'unit') == 'unit'  ? 'selected' : ''); ?>>Unit</option>
                        <option value="pcs"   <?php echo e(old('satuan') == 'pcs'   ? 'selected' : ''); ?>>Pcs</option>
                        <option value="box"   <?php echo e(old('satuan') == 'box'   ? 'selected' : ''); ?>>Box</option>
                        <option value="set"   <?php echo e(old('satuan') == 'set'   ? 'selected' : ''); ?>>Set</option>
                        <option value="lusin" <?php echo e(old('satuan') == 'lusin' ? 'selected' : ''); ?>>Lusin</option>
                    </select>
                </div>
            </div>

            
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:10px;">
                    Jumlah Stok <span style="color:#EF4444;">*</span>
                </label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#1D4ED8; margin-bottom:6px;">
                            <i class="ri-checkbox-blank-circle-fill" style="color:#3B82F6;"></i> Stok Baru
                        </label>
                        <input type="number" name="stok_baru" id="inputStokBaru"
                               value="<?php echo e(old('stok_baru', 0)); ?>" min="0"
                               style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                      border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                      color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                               onfocus="this.style.borderColor='var(--brand-500)'"
                               onblur="this.style.borderColor='var(--border)'"
                               oninput="hitungTotal()" required>
                    </div>
                    <div>
                        <label style="display:block; font-size:12px; font-weight:500; color:#7C3AED; margin-bottom:6px;">
                            <i class="ri-checkbox-blank-circle-fill" style="color:#A855F7;"></i> Stok Bekas
                        </label>
                        <input type="number" name="stok_bekas" id="inputStokBekas"
                               value="<?php echo e(old('stok_bekas', 0)); ?>" min="0"
                               style="width:100%; padding:10px 14px; border:1px solid var(--border);
                                      border-radius:8px; font-size:13.5px; background:var(--bg-primary);
                                      color:var(--text-primary); outline:none; transition:border-color 0.2s;"
                               onfocus="this.style.borderColor='var(--brand-500)'"
                               onblur="this.style.borderColor='var(--border)'"
                               oninput="hitungTotal()" required>
                    </div>
                </div>

                
                <div style="background:var(--bg-hover); border:1px solid var(--border);
                            border-radius:8px; padding:12px 16px; margin-top:12px;
                            display:flex; align-items:center; justify-content:space-between;">
                    <span style="font-size:13px; font-weight:600; color:var(--text-secondary);">
                        <i class="ri-stack-line" style="margin-right:6px;"></i>Total Stok Tersedia
                    </span>
                    <span id="previewTotalStok"
                          style="font-size:16px; font-weight:700; color:#059669;">0 unit</span>
                </div>
            </div>

            
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
                          onblur="this.style.borderColor='var(--border)'"><?php echo e(old('keterangan')); ?></textarea>
            </div>

            
            <div style="display:flex; gap:12px; justify-content:flex-end;
                        padding-top:8px; border-top:1px solid var(--border);">
                <a href="<?php echo e(route('inventory.index')); ?>"
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
                    <i class="ri-save-line"></i> Simpan Data
                </button>
            </div>

        </div>
    </form>
</div>

<script>
function hitungTotal() {
    const baru  = parseInt(document.getElementById('inputStokBaru').value)  || 0;
    const bekas = parseInt(document.getElementById('inputStokBekas').value) || 0;
    const total = baru + bekas;
    document.getElementById('previewTotalStok').textContent = total + ' unit';
    document.getElementById('previewTotalStok').style.color = total > 0 ? '#059669' : '#DC2626';
}
document.addEventListener('DOMContentLoaded', hitungTotal);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\paralkesplus\resources\views/admin/inventory/create.blade.php ENDPATH**/ ?>