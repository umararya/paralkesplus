{{-- resources/views/admin/inventory/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Inventory')
@section('breadcrumb', 'Inventory')

@push('styles')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .page-title { font-size:20px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:10px; line-height:1.2; }
    .page-title i { font-size:22px; color:var(--brand-500); }
    .page-subtitle { font-size:13px; color:var(--text-muted); margin-top:4px; }
    .table-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; transition:background 0.3s, border-color 0.3s; }
    .table-toolbar { padding:14px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .toolbar-left { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .toolbar-right { display:flex; align-items:center; gap:8px; }
    .per-page-wrap { display:flex; align-items:center; gap:7px; font-size:13px; color:var(--text-secondary); }
    .per-page-select { height:36px; padding:0 30px 0 10px; border:1px solid var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-primary); font-size:13px; font-family:var(--font); outline:none; cursor:pointer; transition:border-color 0.2s; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; }
    .per-page-select:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .search-form { display:flex; align-items:center; gap:7px; }
    .search-input-wrap { display:flex; align-items:center; background:var(--bg-primary); border:1px solid var(--border); border-radius:8px; padding:0 11px; height:36px; gap:7px; transition:border-color 0.2s, box-shadow 0.2s; }
    .search-input-wrap:focus-within { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .search-input-wrap i { color:var(--text-muted); font-size:14px; flex-shrink:0; }
    .search-input-wrap input { border:none; background:transparent; outline:none; font-size:13px; color:var(--text-primary); font-family:var(--font); width:220px; }
    .search-input-wrap input::placeholder { color:var(--text-muted); }
    .toolbar-divider { width:1px; height:24px; background:var(--border); flex-shrink:0; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 14px; height:36px; border-radius:8px; font-size:13px; font-weight:500; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-search { background:var(--brand-50); color:var(--brand-500); border:1px solid var(--brand-100); }
    .btn-search:hover { background:var(--brand-100); color:var(--brand-600); }
    html.dark .btn-search { background:rgba(29,111,164,0.12); color:#60A5FA; border-color:rgba(29,111,164,0.25); }
    .btn-reset { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-reset:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); border-color:var(--brand-600); }
    .btn-ghost { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-danger { background:#EF4444; color:#fff; border:1px solid #EF4444; }
    .btn-danger:hover { background:#DC2626; border-color:#DC2626; }
    .info-bar { padding:9px 18px; border-bottom:1px solid var(--border); background:var(--bg-primary); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px; }
    .info-bar-text { font-size:12.5px; color:var(--text-muted); display:flex; align-items:center; gap:6px; }
    .info-bar-text strong { color:var(--text-primary); }
    .badge-count { display:inline-flex; align-items:center; background:var(--brand-50); color:var(--brand-500); border:1px solid var(--brand-100); border-radius:99px; padding:1px 9px; font-size:11.5px; font-weight:600; }
    html.dark .badge-count { background:rgba(29,111,164,0.12); color:#60A5FA; border-color:rgba(29,111,164,0.25); }
    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead tr { background:var(--bg-primary); border-bottom:2px solid var(--border); }
    .data-table th { padding:10px 16px; text-align:left; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--text-muted); white-space:nowrap; }
    .data-table td { padding:13px 16px; font-size:13.5px; color:var(--text-primary); border-bottom:1px solid var(--border); vertical-align:middle; }
    .data-table tbody tr:last-child td { border-bottom:none; }
    .data-table tbody tr { transition:background 0.15s; }
    .data-table tbody tr:hover td { background:var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align:center; }
    .action-group { display:flex; align-items:center; gap:4px; justify-content:center; }
    .btn-action { width:30px; height:30px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:15px; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); transition:all 0.2s; }
    .btn-action.edit:hover { background:#EFF6FF; color:var(--brand-500); border-color:var(--brand-100); }
    .btn-action.delete:hover { background:#FFF1F2; color:#E11D48; border-color:#FFE4E6; }
    html.dark .btn-action.edit:hover { background:rgba(29,111,164,0.15); color:#60A5FA; border-color:rgba(29,111,164,0.3); }
    html.dark .btn-action.delete:hover { background:rgba(225,29,72,0.12); color:#FB7185; border-color:rgba(225,29,72,0.25); }
    .table-footer { padding:12px 18px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
    .pagination-meta { font-size:12.5px; color:var(--text-muted); }
    .pagination-meta strong { color:var(--text-primary); }
    .pagination-nav { display:flex; align-items:center; gap:3px; }
    .page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 6px; border-radius:7px; font-size:13px; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); text-decoration:none; cursor:pointer; transition:all 0.18s; font-family:var(--font); }
    .page-btn:hover { background:var(--bg-hover); color:var(--text-primary); }
    .page-btn.active { background:var(--brand-500); color:#fff; border-color:var(--brand-500); font-weight:700; }
    .page-btn.disabled { opacity:0.35; cursor:not-allowed; pointer-events:none; }
    .page-ellipsis { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; font-size:13px; color:var(--text-muted); }
    .empty-state { text-align:center; padding:56px 24px; }
    .empty-state i { font-size:48px; color:var(--border); display:block; margin-bottom:12px; }
    .empty-state h3 { font-size:15px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; }
    .empty-state p { font-size:13px; color:var(--text-muted); }
    .alert { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:500; margin-bottom:18px; border:1px solid transparent; }
    .alert-success { background:#F0FDF4; color:#15803D; border-color:#BBF7D0; }
    html.dark .alert-success { background:rgba(21,128,61,0.12); color:#4ADE80; border-color:rgba(21,128,61,0.25); }
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from { opacity:0; } to { opacity:1; } }
    .modal { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.2); width:100%; max-width:400px; animation:slideUp 0.2s ease; }
    @keyframes slideUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
    .modal-header { padding:18px 22px 14px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .modal-title { font-size:15px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .modal-close { width:28px; height:28px; border:none; background:none; cursor:pointer; color:var(--text-muted); font-size:19px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:all 0.2s; }
    .modal-close:hover { background:var(--bg-hover); color:var(--text-primary); }
    .modal-body { padding:18px 22px; }
    .modal-footer { padding:14px 22px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:flex-end; gap:8px; }
    .delete-warning { text-align:center; padding:6px 0; }
    .delete-warning i { font-size:42px; color:#EF4444; display:block; margin-bottom:10px; }
    .delete-warning h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:7px; }
    .delete-warning p { font-size:13px; color:var(--text-muted); line-height:1.6; }
    .delete-warning strong { color:var(--text-primary); }
    .kode-mono { font-family:'Courier New',monospace; font-size:12.5px; color:var(--text-secondary); background:var(--bg-primary); padding:3px 8px; border-radius:6px; border:1px solid var(--border); display:inline-block; }
    .stok-badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:20px; font-size:12.5px; font-weight:700; }
    .stok-ok      { background:#F0FDF4; color:#16A34A; }
    .stok-low     { background:#FFF7ED; color:#C2410C; }
    .stok-empty   { background:#FFF1F2; color:#BE123C; }
    html.dark .stok-ok    { background:rgba(22,163,74,0.12); color:#4ADE80; }
    html.dark .stok-low   { background:rgba(194,65,12,0.12); color:#FB923C; }
    html.dark .stok-empty { background:rgba(190,18,60,0.12); color:#FB7185; }
    .kondisi-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:12px; font-weight:600; }
    .kondisi-baik   { background:#F0FDF4; color:#16A34A; }
    .kondisi-rusak  { background:#FFF1F2; color:#BE123C; }
    .kondisi-sedang { background:#FFF7ED; color:#C2410C; }
    html.dark .kondisi-baik   { background:rgba(22,163,74,0.12); color:#4ADE80; }
    html.dark .kondisi-rusak  { background:rgba(190,18,60,0.12); color:#FB7185; }
    html.dark .kondisi-sedang { background:rgba(194,65,12,0.12); color:#FB923C; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success">
    <i class="ri-checkbox-circle-fill"></i> {{ session('success') }}
</div>
@endif

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-box-3-line"></i> Inventory
        </h1>
        <p class="page-subtitle">Kelola stok dan data barang alat kesehatan</p>
    </div>
</div>

{{-- Table Card --}}
<div class="table-card">

    {{-- ── TOOLBAR ── --}}
    <div class="table-toolbar">
        <div class="toolbar-left">

            <form method="GET" action="{{ route('inventory.index') }}" id="perPageForm">
                <input type="hidden" name="search" value="{{ $search }}">
                <div class="per-page-wrap">
                    <span>Tampilkan</span>
                    <select name="per_page" class="per-page-select"
                            onchange="document.getElementById('perPageForm').submit()">
                        @foreach([5, 10, 25, 50] as $pp)
                        <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                    <span>data</span>
                </div>
            </form>

            <div class="toolbar-divider"></div>

            <form method="GET" action="{{ route('inventory.index') }}" class="search-form" id="searchForm">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search" id="searchInput"
                           value="{{ $search }}"
                           placeholder="Cari kode, nama barang, kategori..."
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                @if($search)
                <a href="{{ route('inventory.index', ['per_page' => $perPage]) }}" class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                @endif
            </form>
        </div>

        <div class="toolbar-right">
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Barang
            </a>
        </div>
    </div>

    {{-- ── INFO BAR ── --}}
    <div class="info-bar">
        <div class="info-bar-text">
            @if($search)
                <i class="ri-filter-3-line"></i>
                Hasil pencarian: <strong>"{{ $search }}"</strong>
                &nbsp;<span class="badge-count">{{ $inventories->total() }} barang</span>
            @else
                <i class="ri-box-3-line"></i>
                Total <span class="badge-count">{{ $inventories->total() }} barang</span>
            @endif
        </div>
        @if($inventories->total() > 0)
        <div class="info-bar-text">
            Halaman <strong>{{ $inventories->currentPage() }}</strong> dari <strong>{{ $inventories->lastPage() }}</strong>
        </div>
        @endif
    </div>

    {{-- ── TABLE ── --}}
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:46px;">#</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th class="center">Stok</th>
                    <th>Satuan</th>
                    <th>Kondisi</th>
                    <th class="center" style="width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventories as $item)
                <tr>
                    <td style="color:var(--text-muted);font-size:12.5px;font-weight:500;">
                        {{ $inventories->firstItem() + $loop->index }}
                    </td>
                    <td>
                        <span class="kode-mono">{{ $item->kode_barang ?? '—' }}</span>
                    </td>
                    <td style="font-weight:600;">
                        @if($search)
                            {!! preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                '<mark style="background:#FEF08A;border-radius:3px;padding:0 2px;">$1</mark>',
                                e($item->nama_barang)) !!}
                        @else
                            {{ $item->nama_barang }}
                        @endif
                    </td>
                    <td style="font-size:13px;color:var(--text-muted);">{{ $item->kategori ?? '—' }}</td>
                    <td class="center">
                        @php
                            $stok = $item->stok ?? 0;
                            $stokClass = $stok == 0 ? 'stok-empty' : ($stok <= 5 ? 'stok-low' : 'stok-ok');
                        @endphp
                        <span class="stok-badge {{ $stokClass }}">{{ number_format($stok) }}</span>
                    </td>
                    <td style="font-size:13px;color:var(--text-muted);">{{ $item->satuan ?? '—' }}</td>
                    <td>
                        @php
                            $kondisiClass = match(strtolower($item->kondisi ?? '')) {
                                'baik'  => 'kondisi-baik',
                                'rusak' => 'kondisi-rusak',
                                default => 'kondisi-sedang',
                            };
                        @endphp
                        <span class="kondisi-badge {{ $kondisiClass }}">
                            {{ ucfirst($item->kondisi ?? 'baik') }}
                        </span>
                    </td>
                    <td class="center">
                        <div class="action-group">
                            <a href="{{ route('inventory.edit', $item->id) }}"
                               class="btn-action edit" title="Edit">
                                <i class="ri-edit-line"></i>
                            </a>
                            <button type="button" class="btn-action delete" title="Hapus"
                                    onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_barang) }}')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            <form id="formHapus-{{ $item->id }}"
                                  action="{{ route('inventory.destroy', $item->id) }}"
                                  method="POST" style="display:none;">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="ri-box-3-line"></i>
                            <h3>{{ $search ? 'Tidak ditemukan' : 'Belum ada data inventory' }}</h3>
                            <p>{{ $search ? 'Coba kata kunci lain atau klik Reset.' : 'Klik "Tambah Barang" untuk menambahkan barang ke inventory.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── PAGINATION ── --}}
    @if($inventories->total() > 0)
    <div class="table-footer">
        <div class="pagination-meta">
            Menampilkan
            <strong>{{ $inventories->firstItem() }} – {{ $inventories->lastItem() }}</strong>
            dari <strong>{{ $inventories->total() }}</strong> barang
        </div>
        @if($inventories->hasPages())
        <nav class="pagination-nav">
            @if($inventories->onFirstPage())
                <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
            @else
                <a class="page-btn" href="{{ $inventories->previousPageUrl() }}"><i class="ri-arrow-left-s-line"></i></a>
            @endif
            @php
                $current = $inventories->currentPage(); $last = $inventories->lastPage();
                $pages = []; for ($p=1;$p<=$last;$p++) { if($p===1||$p===$last||($p>=$current-2&&$p<=$current+2)) $pages[]=$p; }
                $rendered=[]; $prev=null; foreach($pages as $p) { if($prev!==null&&$p-$prev>1) $rendered[]='...'; $rendered[]=$p; $prev=$p; }
            @endphp
            @foreach($rendered as $item)
                @if($item==='...') <span class="page-ellipsis">…</span>
                @elseif($item==$current) <span class="page-btn active">{{ $item }}</span>
                @else <a class="page-btn" href="{{ $inventories->url($item) }}">{{ $item }}</a>
                @endif
            @endforeach
            @if($inventories->hasMorePages())
                <a class="page-btn" href="{{ $inventories->nextPageUrl() }}"><i class="ri-arrow-right-s-line"></i></a>
            @else
                <span class="page-btn disabled"><i class="ri-arrow-right-s-line"></i></span>
            @endif
        </nav>
        @endif
    </div>
    @endif

</div>

{{-- MODAL HAPUS --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-delete-bin-line" style="color:#EF4444;"></i> Konfirmasi Hapus
            </span>
            <button class="modal-close" onclick="closeModal('modalHapus')"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="ri-alert-fill"></i>
                <h3>Hapus Data Barang?</h3>
                <p>Kamu akan menghapus barang:<br><strong id="deleteTarget"></strong><br><br>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalHapus')">Batal</button>
            <form id="formDeleteSubmit" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="ri-delete-bin-line"></i> Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
    document.querySelectorAll('.modal-overlay').forEach(el => {
        el.addEventListener('click', function(e) { if(e.target===this) closeModal(this.id); });
    });
    document.addEventListener('keydown', e => {
        if(e.key==='Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
    });
    function openDeleteModal(id, nama) {
        document.getElementById('deleteTarget').textContent = nama;
        document.getElementById('formDeleteSubmit').action = '/inventory/' + id;
        openModal('modalHapus');
    }
</script>
@endpush