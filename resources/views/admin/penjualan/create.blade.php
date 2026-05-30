{{-- resources/views/admin/penjualan/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Tambah Penjualan')
@section('breadcrumb', 'Tambah Penjualan')

@push('styles')
<style>
    .form-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; margin-bottom:20px; }
    .form-card-header { padding:16px 22px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; background:var(--bg-primary); }
    .form-card-title { font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .form-card-title i { color:var(--brand-500); font-size:16px; }
    .form-card-body { padding:22px; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .form-group { display:flex; flex-direction:column; gap:5px; }
    .form-group.full { grid-column:1/-1; }
    .form-label { font-size:12.5px; font-weight:600; color:var(--text-secondary); }
    .form-label .required { color:#EF4444; margin-left:2px; }
    .form-control { height:40px; padding:0 12px; border:1px solid var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-primary); font-size:13px; font-family:var(--font); outline:none; transition:border-color 0.2s,box-shadow 0.2s; width:100%; box-sizing:border-box; }
    .form-control:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .form-control.is-invalid { border-color:#EF4444; }
    textarea.form-control { height:80px; padding:10px 12px; resize:vertical; }
    select.form-control { cursor:pointer; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 10px center; padding-right:32px; }
    .invalid-feedback { font-size:11.5px; color:#EF4444; margin-top:3px; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 18px; height:40px; border-radius:8px; font-size:13px; font-weight:500; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); }
    .btn-ghost { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover { background:var(--bg-hover); color:var(--text-primary); }
    .alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:500; margin-bottom:18px; border:1px solid transparent; }
    .alert-error { background:#FFF1F2; color:#BE123C; border-color:#FECDD3; }
    html.dark .alert-error { background:rgba(190,18,60,0.12); color:#FB7185; border-color:rgba(190,18,60,0.25); }

    /* ── Items Table ── */
    .items-table-wrap { width:100%; overflow-x:auto; }
    .items-table { width:100%; border-collapse:collapse; border:1px solid var(--border); border-radius:10px; overflow:hidden; min-width:780px; }
    .items-table th { padding:9px 10px; background:var(--bg-primary); font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); border-bottom:1px solid var(--border); text-align:left; white-space:nowrap; }
    .items-table td { padding:7px 8px; border-bottom:1px solid var(--border); vertical-align:top; }
    .items-table tr:last-child td { border-bottom:none; }
    .item-input { height:36px; padding:0 10px; border:1px solid var(--border); border-radius:7px; background:var(--bg-primary); color:var(--text-primary); font-size:12.5px; font-family:var(--font); outline:none; width:100%; box-sizing:border-box; transition:border-color 0.2s; }
    .item-input:focus { border-color:var(--brand-500); box-shadow:0 0 0 2px rgba(29,111,164,0.1); }
    .item-select { height:36px; padding:0 28px 0 9px; border:1px solid var(--border); border-radius:7px; background:var(--bg-primary); color:var(--text-primary); font-size:12.5px; font-family:var(--font); outline:none; width:100%; cursor:pointer; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 7px center; box-sizing:border-box; }
    .item-select:focus { border-color:var(--brand-500); box-shadow:0 0 0 2px rgba(29,111,164,0.1); }
    .subtotal-cell { font-size:12.5px; font-weight:700; color:#059669; white-space:nowrap; min-width:110px; padding:8px 10px; vertical-align:middle; }
    html.dark .subtotal-cell { color:#34D399; }
    .btn-remove-item { width:30px; height:30px; border:1px solid #FECDD3; border-radius:7px; background:#FFF1F2; color:#E11D48; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:14px; transition:all 0.2s; margin-top:3px; }
    .btn-remove-item:hover { background:#FECDD3; }
    html.dark .btn-remove-item { background:rgba(225,29,72,0.12); border-color:rgba(225,29,72,0.25); color:#FB7185; }
    .btn-add-item { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border:1px dashed var(--border); border-radius:8px; background:transparent; color:var(--text-secondary); font-size:12.5px; cursor:pointer; transition:all 0.2s; font-family:var(--font); margin-top:10px; }
    .btn-add-item:hover { border-color:var(--brand-500); color:var(--brand-500); background:var(--brand-50); }
    .grand-total-box { display:flex; align-items:center; justify-content:flex-end; gap:12px; padding:12px 14px; background:var(--bg-hover); border:1px solid var(--border); border-radius:8px; margin-top:10px; }
    .grand-total-label { font-size:13px; color:var(--text-secondary); font-weight:600; }
    .grand-total-value { font-size:16px; font-weight:800; color:#059669; }
    html.dark .grand-total-value { color:#34D399; }

    /* ── Autocomplete Dropdown ── */
    .inv-wrap { position:relative; }
    .inv-dropdown { position:absolute; top:calc(100% + 4px); left:0; right:0; background:var(--bg-card); border:1px solid var(--border); border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,0.14); z-index:999; max-height:240px; overflow-y:auto; display:none; min-width:260px; }
    .inv-dropdown.show { display:block; animation:fadeIn 0.12s ease; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(-4px);}to{opacity:1;transform:translateY(0);} }
    .inv-opt { padding:9px 12px; cursor:pointer; font-size:12.5px; border-bottom:1px solid var(--border); transition:background 0.12s; }
    .inv-opt:last-child { border-bottom:none; }
    .inv-opt:hover { background:var(--bg-hover); }
    .inv-opt-name { font-weight:600; color:var(--text-primary); }
    .inv-opt-meta { font-size:11px; color:var(--text-muted); margin-top:3px; display:flex; flex-wrap:wrap; gap:8px; align-items:center; }

    /* ── Stok Badge ── */
    .stok-badge { display:inline-flex; align-items:center; padding:1px 8px; border-radius:99px; font-size:10.5px; font-weight:700; white-space:nowrap; }
    .stok-ok   { background:#F0FDF4; color:#16A34A; border:1px solid #BBF7D0; }
    .stok-low  { background:#FFF7ED; color:#C2410C; border:1px solid #FED7AA; }
    .stok-zero { background:#FFF1F2; color:#DC2626; border:1px solid #FECDD3; }
    html.dark .stok-ok   { background:rgba(22,163,74,0.12);  color:#4ADE80; border-color:rgba(22,163,74,0.25); }
    html.dark .stok-low  { background:rgba(194,65,12,0.12);  color:#FB923C; border-color:rgba(194,65,12,0.25); }
    html.dark .stok-zero { background:rgba(220,38,38,0.12);  color:#FCA5A5; border-color:rgba(220,38,38,0.25); }

    /* ── Stok Realtime (bawah input) ── */
    .stok-realtime { display:inline-flex; align-items:center; gap:4px; margin-top:4px; font-size:11px; font-weight:600; padding:2px 8px; border-radius:99px; }
</style>
@endpush

@section('content')

@if($errors->any())
<div class="alert alert-error">
    <i class="ri-error-warning-fill" style="font-size:17px;flex-shrink:0;margin-top:1px;"></i>
    <div>
        <strong>Terjadi kesalahan:</strong>
        <ul style="margin:4px 0 0 16px;padding:0;">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

<form method="POST" action="{{ route('penjualan.store') }}" enctype="multipart/form-data" id="formPenjualan">
@csrf

{{-- ── INFO PELANGGAN ── --}}
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-title"><i class="ri-user-line"></i> Informasi Pelanggan & Transaksi</div>
    </div>
    <div class="form-card-body">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nama Pelanggan <span class="required">*</span></label>
                <input type="text" name="nama_pelanggan"
                       class="form-control {{ $errors->has('nama_pelanggan') ? 'is-invalid' : '' }}"
                       value="{{ old('nama_pelanggan') }}"
                       placeholder="Nama lengkap pelanggan" required autofocus>
                @error('nama_pelanggan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="nomor_telepon" class="form-control"
                       value="{{ old('nomor_telepon') }}" placeholder="08xx-xxxx-xxxx">
            </div>

            <div class="form-group full">
                <label class="form-label">Alamat Pelanggan <span class="required">*</span></label>
                <textarea name="alamat_pelanggan" class="form-control"
                          placeholder="Alamat lengkap pelanggan" required>{{ old('alamat_pelanggan') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Penjualan <span class="required">*</span></label>
                <input type="date" name="tanggal_penjualan" class="form-control"
                       value="{{ old('tanggal_penjualan', date('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Pembayaran <span class="required">*</span></label>
                <select name="jenis_pembayaran" class="form-control" required>
                    @foreach(['tunai' => 'Tunai', 'transfer' => 'Transfer Bank', 'qris' => 'QRIS', 'kredit' => 'Kredit'] as $v => $l)
                    <option value="{{ $v }}" {{ old('jenis_pembayaran') == $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Diskon Global (Rp)</label>
                <input type="number" name="diskon_global" class="form-control" id="diskonGlobal"
                       value="{{ old('diskon_global', 0) }}" min="0" placeholder="0">
            </div>

            <div class="form-group">
                <label class="form-label">Foto Bukti Pembayaran</label>
                <input type="file" name="foto_bukti" class="form-control" accept="image/*">
            </div>

            <div class="form-group full">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control"
                          placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
            </div>
        </div>
    </div>
</div>

{{-- ── DETAIL BARANG ── --}}
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-title"><i class="ri-shopping-bag-line"></i> Detail Barang</div>
    </div>
    <div class="form-card-body">
        <div class="items-table-wrap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="min-width:230px;">Nama Barang <span style="color:#EF4444">*</span></th>
                        <th style="width:95px;">Kondisi</th>
                        <th style="width:68px;">Qty <span style="color:#EF4444">*</span></th>
                        <th style="width:78px;">Satuan</th>
                        <th style="width:135px;">Harga Satuan <span style="color:#EF4444">*</span></th>
                        <th style="width:72px;">Diskon %</th>
                        <th style="width:115px;">Subtotal</th>
                        <th style="width:38px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="item-row" data-index="0"
                        data-inv-id="" data-harga-baru="0" data-harga-bekas="0"
                        data-stok-baru="0" data-stok-bekas="0">
                        <td>
                            <div class="inv-wrap">
                                <input type="hidden" name="items[0][inventory_id]" class="inv-id-input">
                                <input type="text"   name="items[0][nama_barang]"
                                       class="item-input inv-search"
                                       placeholder="Ketik nama barang..."
                                       value="{{ old('items.0.nama_barang') }}"
                                       autocomplete="off" required>
                                <div class="inv-dropdown"></div>
                            </div>
                        </td>
                        <td>
                            <select name="items[0][kondisi]" class="item-select kondisi-select">
                                <option value="baru">Baru</option>
                                <option value="bekas">Bekas</option>
                            </select>
                        </td>
                        <td><input type="number" name="items[0][qty]"          class="item-input qty-input"    value="{{ old('items.0.qty', 1) }}"    min="1"   required></td>
                        <td><input type="text"   name="items[0][satuan]"       class="item-input satuan-input" value="{{ old('items.0.satuan', 'unit') }}"></td>
                        <td><input type="number" name="items[0][harga_satuan]" class="item-input harga-input"  value="{{ old('items.0.harga_satuan', 0) }}" min="0" required></td>
                        <td><input type="number" name="items[0][diskon]"       class="item-input diskon-input" value="{{ old('items.0.diskon', 0) }}"   min="0" max="100"></td>
                        <td class="subtotal-cell">Rp 0</td>
                        <td>
                            <button type="button" class="btn-remove-item" onclick="removeItem(this)" title="Hapus baris">
                                <i class="ri-close-line"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button type="button" class="btn-add-item" onclick="addItem()">
            <i class="ri-add-line"></i> Tambah Barang
        </button>

        <div class="grand-total-box">
            <span class="grand-total-label">Total Tagihan:</span>
            <span class="grand-total-value" id="grandTotalDisplay">Rp 0</span>
        </div>
    </div>
</div>

<div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-bottom:24px;">
    <a href="{{ route('penjualan.index') }}" class="btn btn-ghost">
        <i class="ri-arrow-left-line"></i> Batal
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="ri-save-line"></i> Simpan Penjualan
    </button>
</div>

</form>
@endsection

@push('scripts')
<script>
let itemIndex    = 1;
const SEARCH_URL = '{{ route("penjualan.search-inventory") }}';

/* ── Format Rupiah ── */
function formatRp(n) {
    return 'Rp ' + Number(n).toLocaleString('id-ID');
}

/* ── Hitung subtotal 1 row ── */
function calcSubtotal(row) {
    const qty    = parseFloat(row.querySelector('.qty-input').value)    || 0;
    const harga  = parseFloat(row.querySelector('.harga-input').value)  || 0;
    const diskon = parseFloat(row.querySelector('.diskon-input').value) || 0;
    const sub    = Math.round(qty * harga * (1 - diskon / 100));
    row.querySelector('.subtotal-cell').textContent = formatRp(sub);
    return sub;
}

/* ── Hitung grand total ── */
function calcGrandTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(r => { total += calcSubtotal(r); });
    const diskon = parseFloat(document.getElementById('diskonGlobal')?.value) || 0;
    document.getElementById('grandTotalDisplay').textContent = formatRp(Math.max(0, total - diskon));
}

/* ── Badge stok realtime di bawah input nama ── */
function updateStokBadge(row, kondisi) {
    const old = row.querySelector('.stok-realtime');
    if (old) old.remove();

    const stokBaru  = parseInt(row.dataset.stokBaru  ?? '0') || 0;
    const stokBekas = parseInt(row.dataset.stokBekas ?? '0') || 0;
    const stok      = kondisi === 'bekas' ? stokBekas : stokBaru;

    const cls  = stok <= 0 ? 'stok-zero' : stok <= 3 ? 'stok-low' : 'stok-ok';
    const icon = stok <= 0 ? '⚠' : '✓';
    const lbl  = stok <= 0
        ? 'Stok habis'
        : `${icon} Tersedia: ${stok} ${kondisi}`;

    const badge       = document.createElement('div');
    badge.className   = `stok-realtime stok-badge ${cls}`;
    badge.textContent = lbl;

    const invWrap = row.querySelector('.inv-wrap');
    invWrap.after(badge);
}

/* ── Bind autocomplete & events ke 1 row ── */
function bindAutocomplete(row) {
    const searchInput = row.querySelector('.inv-search');
    const idInput     = row.querySelector('.inv-id-input');
    const dropdown    = row.querySelector('.inv-dropdown');
    const kondisiSel  = row.querySelector('.kondisi-select');
    const hargaInput  = row.querySelector('.harga-input');
    const satuanInput = row.querySelector('.satuan-input');

    if (!searchInput) return;

    let timer = null;

    /* Ketik di input nama → fetch inventory */
    searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();

        // Reset saat ketik ulang
        idInput.value          = '';
        row.dataset.invId      = '';
        row.dataset.hargaBaru  = '0';
        row.dataset.hargaBekas = '0';
        row.dataset.stokBaru   = '0';
        row.dataset.stokBekas  = '0';
        const oldBadge = row.querySelector('.stok-realtime');
        if (oldBadge) oldBadge.remove();

        if (q.length < 1) {
            dropdown.classList.remove('show');
            dropdown.innerHTML = '';
            return;
        }

        timer = setTimeout(() => {
            fetch(`${SEARCH_URL}?q=${encodeURIComponent(q)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                }
            })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(items => {
                if (!items.length) {
                    dropdown.innerHTML = `<div class="inv-opt" style="color:var(--text-muted);cursor:default;font-style:italic;">
                        Produk tidak ditemukan
                    </div>`;
                    dropdown.classList.add('show');
                    return;
                }

                dropdown.innerHTML = items.map(i => {
                    const stok = parseInt(i.stok_tersedia) || 0;
                    const cls  = stok <= 0 ? 'stok-zero' : stok <= 3 ? 'stok-low' : 'stok-ok';
                    const lbl  = stok <= 0 ? 'Stok Habis' : `Stok: ${stok}`;
                    return `
                    <div class="inv-opt"
                         data-id="${i.id}"
                         data-nama="${escHtml(i.nama_barang)}"
                         data-satuan="${escHtml(i.satuan ?? 'unit')}"
                         data-harga-baru="${parseInt(i.harga_jual_baru) || 0}"
                         data-harga-bekas="${parseInt(i.harga_jual_bekas) || 0}"
                         data-stok-baru="${parseInt(i.stok_baru) || 0}"
                         data-stok-bekas="${parseInt(i.stok_bekas) || 0}"
                         data-stok="${stok}">
                        <div class="inv-opt-name">${escHtml(i.nama_barang)}</div>
                        <div class="inv-opt-meta">
                            <span class="stok-badge ${cls}">${lbl}</span>
                            <span>Baru: ${formatRp(i.harga_jual_baru ?? 0)}</span>
                            <span>Bekas: ${formatRp(i.harga_jual_bekas ?? 0)}</span>
                        </div>
                    </div>`;
                }).join('');

                dropdown.classList.add('show');

                dropdown.querySelectorAll('.inv-opt[data-id]').forEach(opt => {
                    opt.addEventListener('mousedown', function (e) {
                        e.preventDefault(); // cegah blur dulu
                        const kondisi = kondisiSel.value;

                        // Simpan ke dataset row
                        idInput.value          = this.dataset.id;
                        row.dataset.invId      = this.dataset.id;
                        row.dataset.hargaBaru  = this.dataset.hargaBaru;
                        row.dataset.hargaBekas = this.dataset.hargaBekas;
                        row.dataset.stokBaru   = this.dataset.stokBaru;
                        row.dataset.stokBekas  = this.dataset.stokBekas;

                        // Isi input
                        searchInput.value = this.dataset.nama;
                        satuanInput.value = this.dataset.satuan;
                        hargaInput.value  = kondisi === 'bekas'
                            ? this.dataset.hargaBekas
                            : this.dataset.hargaBaru;

                        updateStokBadge(row, kondisi);

                        dropdown.classList.remove('show');
                        dropdown.innerHTML = '';
                        calcGrandTotal();
                    });
                });
            })
            .catch(err => {
                console.error('[SearchInventory]', err);
                dropdown.innerHTML = `<div class="inv-opt" style="color:#EF4444;cursor:default;">
                    <i class="ri-error-warning-line"></i> Gagal memuat data (${err.message})
                </div>`;
                dropdown.classList.add('show');
            });
        }, 280);
    });

    /* Blur → tutup dropdown */
    searchInput.addEventListener('blur', () => {
        setTimeout(() => dropdown.classList.remove('show'), 150);
    });

    /* Ganti kondisi → update harga & badge stok */
    kondisiSel?.addEventListener('change', function () {
        if (!idInput.value) return;
        hargaInput.value = this.value === 'bekas'
            ? (row.dataset.hargaBekas ?? '0')
            : (row.dataset.hargaBaru  ?? '0');
        updateStokBadge(row, this.value);
        calcGrandTotal();
    });
}

/* ── Bind semua event ke 1 row ── */
function bindRowEvents(row) {
    row.querySelectorAll('.qty-input, .harga-input, .diskon-input').forEach(el => {
        el.addEventListener('input', calcGrandTotal);
    });
    bindAutocomplete(row);
}

/* ── Tambah baris baru ── */
function addItem() {
    const idx = itemIndex++;
    const tr  = document.createElement('tr');
    tr.className     = 'item-row';
    tr.dataset.index = idx;
    tr.dataset.invId      = '';
    tr.dataset.hargaBaru  = '0';
    tr.dataset.hargaBekas = '0';
    tr.dataset.stokBaru   = '0';
    tr.dataset.stokBekas  = '0';

    tr.innerHTML = `
        <td>
            <div class="inv-wrap">
                <input type="hidden" name="items[${idx}][inventory_id]" class="inv-id-input">
                <input type="text"   name="items[${idx}][nama_barang]"
                       class="item-input inv-search"
                       placeholder="Ketik nama barang..."
                       autocomplete="off" required>
                <div class="inv-dropdown"></div>
            </div>
        </td>
        <td>
            <select name="items[${idx}][kondisi]" class="item-select kondisi-select">
                <option value="baru">Baru</option>
                <option value="bekas">Bekas</option>
            </select>
        </td>
        <td><input type="number" name="items[${idx}][qty]"          class="item-input qty-input"    value="1"    min="1"   required></td>
        <td><input type="text"   name="items[${idx}][satuan]"       class="item-input satuan-input" value="unit"></td>
        <td><input type="number" name="items[${idx}][harga_satuan]" class="item-input harga-input"  value="0"    min="0"   required></td>
        <td><input type="number" name="items[${idx}][diskon]"       class="item-input diskon-input" value="0"    min="0" max="100"></td>
        <td class="subtotal-cell">Rp 0</td>
        <td>
            <button type="button" class="btn-remove-item" onclick="removeItem(this)" title="Hapus baris">
                <i class="ri-close-line"></i>
            </button>
        </td>
    `;

    document.getElementById('itemsBody').appendChild(tr);
    bindRowEvents(tr);
    tr.querySelector('.inv-search').focus();
}

/* ── Hapus baris ── */
function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length <= 1) {
        alert('Minimal harus ada 1 barang.');
        return;
    }
    btn.closest('.item-row').remove();
    calcGrandTotal();
}

/* ── Escape HTML untuk safety ── */
function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

/* ── Init ── */
document.querySelectorAll('.item-row').forEach(bindRowEvents);
document.getElementById('diskonGlobal')?.addEventListener('input', calcGrandTotal);
calcGrandTotal();
</script>
@endpush