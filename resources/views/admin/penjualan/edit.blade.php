{{-- resources/views/admin/penjualan/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Penjualan')
@section('breadcrumb', 'Edit Penjualan')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
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
    .form-label .req { color:#EF4444; margin-left:2px; }
    .form-control {
        width:100%; padding:10px 14px; border:1px solid var(--border);
        border-radius:8px; font-size:13.5px; background:var(--bg-primary);
        color:var(--text-primary); outline:none;
        transition:border-color 0.2s,box-shadow 0.2s;
        font-family:var(--font); box-sizing:border-box;
    }
    .form-control:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .form-control.is-invalid { border-color:#EF4444; }
    textarea.form-control { resize:vertical; min-height:80px; }
    select.form-control {
        appearance:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 12px center; padding-right:36px;
    }
    .invalid-feedback { font-size:12px; color:#EF4444; display:flex; align-items:center; gap:4px; }
    .alert { display:flex; align-items:flex-start; gap:10px; padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:500; margin-bottom:18px; border:1px solid transparent; }
    .alert-error { background:#FFF1F2; color:#BE123C; border-color:#FECDD3; }
    html.dark .alert-error { background:rgba(190,18,60,0.12); color:#FB7185; border-color:rgba(190,18,60,0.25); }
    .current-foto { display:flex; align-items:center; gap:10px; padding:8px 12px; background:var(--bg-hover); border:1px solid var(--border); border-radius:8px; margin-bottom:8px; }
    .current-foto img { width:44px; height:44px; border-radius:6px; object-fit:cover; }
    .current-foto-info { font-size:12px; color:var(--text-muted); }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 18px; height:40px; border-radius:8px; font-size:13px; font-weight:600; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); }
    .btn-ghost { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover { background:var(--bg-hover); color:var(--text-primary); }

    /* ── Items Table ── */
    .items-table { width:100%; border-collapse:collapse; margin-bottom:10px; }
    .items-table thead tr { background:var(--brand-500); color:#fff; }
    .items-table th { padding:9px 10px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:left; white-space:nowrap; }
    .items-table td { padding:7px 6px; border-bottom:1px solid var(--border); vertical-align:middle; }
    .items-table tbody tr:nth-child(even) td { background:var(--bg-hover); }
    .items-table .form-control { padding:7px 10px; font-size:13px; }

    .btn-remove-row { width:28px; height:28px; border-radius:6px; background:rgba(239,68,68,0.1); color:#EF4444; border:none; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; font-size:14px; transition:background 0.2s; }
    .btn-remove-row:hover { background:rgba(239,68,68,0.2); }
    .btn-add-row { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; border:1.5px dashed var(--brand-500); background:transparent; color:var(--brand-500); font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s; font-family:var(--font); }
    .btn-add-row:hover { background:var(--brand-50); }
    html.dark .btn-add-row:hover { background:rgba(29,111,164,0.1); }

    /* ── Stok Badge ── */
    .stok-baru-badge  { background:#DBEAFE; color:#1D4ED8; padding:1px 7px; border-radius:99px; font-size:11px; font-weight:600; }
    .stok-bekas-badge { background:#F3E8FF; color:#7C3AED; padding:1px 7px; border-radius:99px; font-size:11px; font-weight:600; }

    .stok-info-row { display:flex; align-items:center; gap:6px; margin-top:5px; min-height:20px; flex-wrap:wrap; }
    .stok-info-row span { font-size:12px; }

    /* ── Select2 Overrides ── */
    .select2-container { width:100% !important; }
    .select2-container--default .select2-selection--single {
        height:36px; border:1px solid var(--border); border-radius:7px;
        background:var(--bg-primary); display:flex; align-items:center; padding:0 10px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color:var(--text-primary); font-size:13px; line-height:1; padding:0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height:36px; right:8px; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open  .select2-selection--single {
        border-color:var(--brand-500); outline:none; box-shadow:0 0 0 3px rgba(29,111,164,0.1);
    }
    .select2-dropdown { border:1px solid var(--border); border-radius:8px; box-shadow:0 4px 16px rgba(0,0,0,0.1); background:var(--bg-card); z-index:9999; }
    .select2-container--default .select2-results__option { font-size:13px; color:var(--text-primary); padding:8px 12px; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background:var(--brand-500); color:#fff; }
    .select2-search--dropdown .select2-search__field { border:1px solid var(--border); border-radius:6px; padding:6px 10px; font-size:13px; background:var(--bg-primary); color:var(--text-primary); width:100%; box-sizing:border-box; }
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

<form method="POST" action="{{ route('penjualan.update', $penjualan->id) }}"
      enctype="multipart/form-data" id="formPenjualan">
@csrf @method('PUT')

{{-- ── INFO PELANGGAN ── --}}
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-title"><i class="ri-user-line"></i> Informasi Pelanggan & Transaksi</div>
    </div>
    <div class="form-card-body">
        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Nama Pelanggan <span class="req">*</span></label>
                <input type="text" name="nama_pelanggan"
                       class="form-control {{ $errors->has('nama_pelanggan') ? 'is-invalid' : '' }}"
                       value="{{ old('nama_pelanggan', $penjualan->nama_pelanggan) }}" required>
                @error('nama_pelanggan')<div class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="nomor_telepon" class="form-control"
                       value="{{ old('nomor_telepon', $penjualan->nomor_telepon) }}">
            </div>

            <div class="form-group full">
                <label class="form-label">Alamat Pelanggan <span class="req">*</span></label>
                <textarea name="alamat_pelanggan" class="form-control" required>{{ old('alamat_pelanggan', $penjualan->alamat_pelanggan) }}</textarea>
                @error('alamat_pelanggan')<div class="invalid-feedback"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Penjualan <span class="req">*</span></label>
                <input type="date" name="tanggal_penjualan" class="form-control"
                       value="{{ old('tanggal_penjualan', $penjualan->tanggal_penjualan?->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Pembayaran <span class="req">*</span></label>
                <select name="jenis_pembayaran" class="form-control" required>
                    @foreach(['tunai'=>'Tunai','transfer'=>'Transfer Bank','qris'=>'QRIS','kredit'=>'Kredit'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('jenis_pembayaran', $penjualan->jenis_pembayaran)==$v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Foto Bukti Pembayaran</label>
                @if($penjualan->foto_bukti)
                <div class="current-foto">
                    <img src="{{ Storage::url($penjualan->foto_bukti) }}" alt="Bukti">
                    <div class="current-foto-info">Foto tersimpan. Upload baru untuk mengganti.</div>
                </div>
                @endif
                <input type="file" name="foto_bukti" class="form-control" accept="image/*">
            </div>

            <div class="form-group full">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control">{{ old('keterangan', $penjualan->keterangan) }}</textarea>
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
        <div style="overflow-x:auto;">
            <table class="items-table" id="items-table">
                <thead>
                    <tr>
                        <th style="width:32px;">#</th>
                        <th style="min-width:260px;">Nama Barang <span style="color:#fde68a">*</span></th>
                        <th style="width:110px;">Kondisi <span style="color:#fde68a">*</span></th>
                        <th style="width:70px;">Qty</th>
                        <th style="width:90px;">Satuan</th>
                        <th style="width:36px;"></th>
                    </tr>
                </thead>
                <tbody id="items-body"></tbody>
            </table>
        </div>
        <button type="button" class="btn-add-row" onclick="addRow()">
            <i class="ri-add-line"></i> Tambah Barang
        </button>
    </div>
</div>

<div style="display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-bottom:24px;">
    <a href="{{ route('penjualan.index') }}" class="btn btn-ghost">
        <i class="ri-arrow-left-line"></i> Batal
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="ri-save-line"></i> Simpan Perubahan
    </button>
</div>

</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
/* ── Data existing dari controller (aman, sudah di-encode PHP) ── */
const existingItems = @json($existingItems);

let rowIndex = 0;

/* ── Template Select2 dropdown ── */
function templateBarang(item) {
    if (!item.id) return item.text || 'Pilih atau cari nama barang...';
    return $(
        '<div style="padding:3px 0">' +
            '<div style="font-size:13px;font-weight:600;color:var(--text-primary)">' + item.text + '</div>' +
            '<div style="display:flex;gap:6px;margin-top:3px;align-items:center;flex-wrap:wrap;">' +
                '<span style="font-size:11px;color:var(--text-muted)">' + (item.kategori || '') + '</span>' +
                '<span class="stok-baru-badge">Baru: ' + (item.stok_baru || 0) + '</span>' +
                '<span class="stok-bekas-badge">Bekas: ' + (item.stok_bekas || 0) + '</span>' +
            '</div>' +
        '</div>'
    );
}

function templateBarangSelected(item) {
    if (!item.id) return item.text || 'Pilih barang...';
    return $(
        '<span>' + item.text +
        ' <span class="stok-baru-badge">Baru: ' + (item.stok_baru || 0) + '</span>' +
        ' <span class="stok-bekas-badge">Bekas: ' + (item.stok_bekas || 0) + '</span>' +
        '</span>'
    );
}

/* ── Tambah baris ── */
function addRow(data) {
    data = data || {};
    rowIndex++;
    var idx   = rowIndex;
    var tbody = document.getElementById('items-body');
    var tr    = document.createElement('tr');
    tr.id = 'row-' + idx;

    var satuanList = ['unit','pcs','set','buah','pasang'];
    var satuanOpts = satuanList.map(function(s) {
        return '<option value="' + s + '"' + ((data.satuan || 'unit') === s ? ' selected' : '') + '>' + s + '</option>';
    }).join('');

    var kondisiSelected = data.kondisi || 'baru';
    var kondisiOpts =
        '<option value="baru"'  + (kondisiSelected === 'baru'  ? ' selected' : '') + '>Baru</option>' +
        '<option value="bekas"' + (kondisiSelected === 'bekas' ? ' selected' : '') + '>Bekas</option>';

    var stokInfoHtml = '';
    if (data.inventory_id && data.stok_baru !== undefined) {
        stokInfoHtml =
            '<span class="stok-baru-badge">Baru: ' + (data.stok_baru || 0) + '</span>' +
            '<span class="stok-bekas-badge">Bekas: ' + (data.stok_bekas || 0) + '</span>';
    }

    var initOption = data.inventory_id
        ? '<option value="' + data.inventory_id + '" selected>' + (data.nama_barang || '') + '</option>'
        : '';

    tr.innerHTML =
        '<td style="text-align:center;font-size:12px;color:var(--text-muted)">' + idx + '</td>' +

        '<td style="min-width:260px">' +
            '<input type="hidden" name="items[' + idx + '][detail_id]"    id="detail-id-' + idx + '" value="' + (data.detail_id    || '') + '">' +
            '<input type="hidden" name="items[' + idx + '][inventory_id]" id="inv-id-'    + idx + '" value="' + (data.inventory_id || '') + '">' +
            '<input type="hidden" name="items[' + idx + '][nama_barang]"  id="nama-'      + idx + '" value="' + (data.nama_barang  || '') + '">' +
            '<input type="hidden" name="items[' + idx + '][harga_satuan]" id="harga-'     + idx + '" value="' + (data.harga_satuan || 0)  + '">' +
            '<input type="hidden" name="items[' + idx + '][diskon]"       id="diskon-'    + idx + '" value="' + (data.diskon       || 0)  + '">' +
            '<select id="inv-select-' + idx + '" class="form-control" required>' + initOption + '</select>' +
            '<div class="stok-info-row" id="stok-info-' + idx + '">' + stokInfoHtml + '</div>' +
        '</td>' +

        '<td>' +
            '<select name="items[' + idx + '][kondisi]" id="kondisi-' + idx + '" class="form-control" style="width:100px" onchange="onKondisiChange(' + idx + ')">' +
                kondisiOpts +
            '</select>' +
        '</td>' +
        '<td>' +
            '<input type="number" name="items[' + idx + '][qty]" id="qty-' + idx + '" value="' + (data.qty || 1) + '" min="1" class="form-control" style="text-align:center;width:64px" required>' +
        '</td>' +
        '<td>' +
            '<select name="items[' + idx + '][satuan]" class="form-control" style="width:80px">' + satuanOpts + '</select>' +
        '</td>' +
        '<td style="text-align:center">' +
            '<button type="button" class="btn-remove-row" onclick="removeRow(' + idx + ')"><i class="ri-delete-bin-line"></i></button>' +
        '</td>';

    tbody.appendChild(tr);

    /* ── Init Select2 ── */
    $('#inv-select-' + idx).select2({
        dropdownParent: $('#row-' + idx),
        placeholder:    'Pilih atau cari nama barang...',
        allowClear:     true,
        minimumInputLength: 0,
        language: { inputTooShort: function() { return ''; } },
        ajax: {
            url:      '{{ route("api.inventory.index") }}',
            dataType: 'json',
            delay:    250,
            data:     function(params) { return { q: params.term || '', mode: 'jual' }; },
            processResults: function(data) { return { results: data.results }; },
            cache: true,
        },
        templateResult:    templateBarang,
        templateSelection: templateBarangSelected,
    })
    .on('select2:open', function() {
        var sf = document.querySelector('.select2-container--open .select2-search__field');
        if (sf) sf.dispatchEvent(new Event('input', { bubbles: true }));
    })
    .on('select2:select', function(e) {
        var item = e.params.data;

        document.getElementById('inv-id-' + idx).value   = item.id;
        document.getElementById('nama-'    + idx).value  = item.text;

        var infoHtml =
            '<span class="stok-baru-badge">Baru: '   + (item.stok_baru  || 0) + '</span>' +
            '<span class="stok-bekas-badge">Bekas: ' + (item.stok_bekas || 0) + '</span>';
        if (item.kategori) {
            infoHtml += '<span style="font-size:11px;color:var(--text-muted)">| ' + item.kategori + '</span>';
        }
        document.getElementById('stok-info-' + idx).innerHTML = infoHtml;

        /* satuan otomatis */
        var $sat = $('select[name="items[' + idx + '][satuan]"]');
        if (item.satuan) {
            if ($sat.find('option[value="' + item.satuan + '"]').length) {
                $sat.val(item.satuan);
            } else {
                $sat.append(new Option(item.satuan, item.satuan, true, true));
            }
        }

        updateHargaHidden(idx, item);
    })
    .on('select2:clear', function() {
        document.getElementById('inv-id-' + idx).value  = '';
        document.getElementById('nama-'   + idx).value  = '';
        document.getElementById('harga-'  + idx).value  = 0;
        document.getElementById('stok-info-' + idx).innerHTML = '';
    });
}

function updateHargaHidden(idx, item) {
    var kondisi = document.getElementById('kondisi-' + idx).value;
    var harga   = kondisi === 'bekas'
        ? (item.harga_jual_bekas || 0)
        : (item.harga_jual_baru  || 0);
    document.getElementById('harga-' + idx).value = harga;
}

function onKondisiChange(idx) {
    var sel  = $('#inv-select-' + idx);
    var data = sel.select2('data');
    if (data && data[0] && data[0].id) {
        updateHargaHidden(idx, data[0]);
    }
}

function removeRow(idx) {
    if (document.getElementById('items-body').rows.length <= 1) {
        alert('Minimal harus ada 1 barang.');
        return;
    }
    $('#inv-select-' + idx).select2('destroy');
    var row = document.getElementById('row-' + idx);
    if (row) row.parentNode.removeChild(row);
}

/* ── INIT: load existing items dari controller ── */
document.addEventListener('DOMContentLoaded', function() {
    if (existingItems && existingItems.length > 0) {
        existingItems.forEach(function(item) { addRow(item); });
    } else {
        addRow();
    }
});
</script>
@endpush