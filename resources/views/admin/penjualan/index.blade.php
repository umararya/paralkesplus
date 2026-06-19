{{-- resources/views/admin/penjualan/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Penjualan')
@section('breadcrumb', 'Penjualan')

@push('styles')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .page-title { font-size:20px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:10px; line-height:1.2; }
    .page-title i { font-size:22px; color:var(--brand-500); }
    .page-subtitle { font-size:13px; color:var(--text-muted); margin-top:4px; }

    .table-card { background:var(--bg-card); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow); overflow:hidden; transition:background 0.3s,border-color 0.3s; }
    .table-toolbar { padding:14px 18px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .toolbar-left { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .toolbar-right { display:flex; align-items:center; gap:8px; }
    .per-page-wrap { display:flex; align-items:center; gap:7px; font-size:13px; color:var(--text-secondary); }
    .per-page-select { height:36px; padding:0 30px 0 10px; border:1px solid var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-primary); font-size:13px; font-family:var(--font); outline:none; cursor:pointer; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; }
    .per-page-select:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .search-form { display:flex; align-items:center; gap:7px; }
    .search-input-wrap { display:flex; align-items:center; background:var(--bg-primary); border:1px solid var(--border); border-radius:8px; padding:0 11px; height:36px; gap:7px; transition:border-color 0.2s,box-shadow 0.2s; }
    .search-input-wrap:focus-within { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .search-input-wrap i { color:var(--text-muted); font-size:14px; flex-shrink:0; }
    .search-input-wrap input { border:none; background:transparent; outline:none; font-size:13px; color:var(--text-primary); font-family:var(--font); width:220px; }
    .search-input-wrap input::placeholder { color:var(--text-muted); }
    .toolbar-divider { width:1px; height:24px; background:var(--border); flex-shrink:0; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:0 14px; height:36px; border-radius:8px; font-size:13px; font-weight:500; font-family:var(--font); cursor:pointer; border:none; transition:all 0.2s; text-decoration:none; white-space:nowrap; }
    .btn i { font-size:15px; }
    .btn-search  { background:var(--brand-50); color:var(--brand-500); border:1px solid var(--brand-100); }
    .btn-search:hover { background:var(--brand-100); color:var(--brand-600); }
    html.dark .btn-search { background:rgba(29,111,164,0.12); color:#60A5FA; border-color:rgba(29,111,164,0.25); }
    .btn-reset   { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-reset:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-primary { background:var(--brand-500); color:#fff; border:1px solid var(--brand-500); }
    .btn-primary:hover { background:var(--brand-600); border-color:var(--brand-600); }
    .btn-ghost   { background:transparent; color:var(--text-secondary); border:1px solid var(--border); }
    .btn-ghost:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-danger  { background:#EF4444; color:#fff; border:1px solid #EF4444; }
    .btn-danger:hover { background:#DC2626; border-color:#DC2626; }
    .btn-export  { background:#10B981; color:#fff; border:1px solid #10B981; }
    .btn-export:hover { background:#059669; border-color:#059669; }
    html.dark .btn-export { background:rgba(16,185,129,0.2); color:#34D399; border-color:rgba(16,185,129,0.3); }

    /* ── Date Filter Bar ── */
    .date-filter-bar { padding:10px 18px; border-bottom:1px solid var(--border); background:var(--bg-primary); display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .date-filter-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); display:flex; align-items:center; gap:5px; white-space:nowrap; flex-shrink:0; }
    .date-filter-label i { font-size:13px; color:var(--brand-500); }
    .date-inputs-wrap { display:flex; align-items:center; gap:6px; }
    .date-input { height:34px; padding:0 10px; border:1px solid var(--border); border-radius:8px; background:var(--bg-card); color:var(--text-primary); font-size:13px; font-family:var(--font); outline:none; transition:border-color 0.2s,box-shadow 0.2s; width:150px; }
    .date-input:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .date-input.active { border-color:var(--brand-500); background:var(--brand-50); }
    html.dark .date-input.active { background:rgba(29,111,164,0.1); }
    .date-sep { font-size:12px; color:var(--text-muted); font-weight:500; padding:0 2px; }
    .date-filter-active-badge { display:inline-flex; align-items:center; gap:4px; background:var(--brand-500); color:#fff; border-radius:99px; padding:2px 9px; font-size:11px; font-weight:700; }
    .date-shortcuts { display:flex; align-items:center; gap:4px; margin-left:2px; }
    .date-shortcuts span { font-size:11px; color:var(--text-muted); margin-right:2px; }
    .shortcut-btn { height:26px; padding:0 9px; border-radius:6px; font-size:11.5px; font-weight:500; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); font-family:var(--font); transition:all 0.15s; white-space:nowrap; }
    .shortcut-btn:hover { background:var(--brand-50); color:var(--brand-500); border-color:var(--brand-200); }
    html.dark .shortcut-btn:hover { background:rgba(29,111,164,0.15); color:#60A5FA; border-color:rgba(29,111,164,0.3); }

    /* ── Export Filter Panel ── */
    .export-panel { padding:12px 18px; border-bottom:1px solid var(--border); background:var(--bg-primary); display:none; }
    .export-panel.open { display:block; animation:fadePanel 0.15s ease; }
    @keyframes fadePanel { from{opacity:0;transform:translateY(-6px);}to{opacity:1;transform:translateY(0);} }
    .export-panel-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--text-muted); margin-bottom:10px; display:flex; align-items:center; gap:6px; }
    .export-panel-title i { font-size:13px; color:#10B981; }
    .export-filter-row { display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; }
    .export-filter-group { display:flex; flex-direction:column; gap:4px; }
    .export-filter-label { font-size:11.5px; font-weight:600; color:var(--text-secondary); }
    .export-filter-input { height:34px; padding:0 10px; border:1px solid var(--border); border-radius:8px; background:var(--bg-card); color:var(--text-primary); font-size:13px; font-family:var(--font); outline:none; transition:border-color 0.2s; }
    .export-filter-input:focus { border-color:#10B981; box-shadow:0 0 0 3px rgba(16,185,129,0.1); }
    select.export-filter-input { padding-right:28px; cursor:pointer; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; }
    .export-filter-hint { font-size:11px; color:var(--text-muted); margin-top:6px; }
    .export-filter-hint strong { color:#10B981; }

    .info-bar { padding:9px 18px; border-bottom:1px solid var(--border); background:var(--bg-primary); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px; }
    .info-bar-text { font-size:12.5px; color:var(--text-muted); display:flex; align-items:center; gap:6px; }
    .info-bar-text strong { color:var(--text-primary); }
    .badge-count { display:inline-flex; align-items:center; background:var(--brand-50); color:var(--brand-500); border:1px solid var(--brand-100); border-radius:99px; padding:1px 9px; font-size:11.5px; font-weight:600; }
    html.dark .badge-count { background:rgba(29,111,164,0.12); color:#60A5FA; border-color:rgba(29,111,164,0.25); }

    .data-table { width:100%; border-collapse:collapse; }
    .data-table thead tr { background:var(--bg-primary); border-bottom:2px solid var(--border); }
    .data-table th { padding:10px 14px; text-align:left; font-size:10.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:var(--text-muted); white-space:nowrap; }
    .data-table td { padding:12px 14px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); vertical-align:middle; }
    .data-table tbody tr:last-child td { border-bottom:none; }
    .data-table tbody tr { transition:background 0.15s; }
    .data-table tbody tr:hover td { background:var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align:center; }
    .data-table th.right,  .data-table td.right  { text-align:right; }

    .items-list { display:flex; flex-direction:column; gap:4px; }
    .item-pill { display:inline-flex; align-items:center; gap:5px; background:var(--bg-hover); border:1px solid var(--border); border-radius:6px; padding:3px 8px; font-size:12px; white-space:nowrap; }
    .item-pill .item-name { font-weight:600; color:var(--text-primary); }
    .item-pill .item-qty  { background:var(--brand-50); color:var(--brand-500); border-radius:4px; padding:1px 5px; font-size:11px; font-weight:700; }
    html.dark .item-pill .item-qty { background:rgba(29,111,164,0.15); color:#60A5FA; }
    .item-pill .item-sub  { color:var(--text-muted); font-size:11px; }
    .items-more { font-size:11.5px; color:var(--brand-500); cursor:pointer; margin-top:2px; }

    /* ── Kolom Pengiriman ── */
    .kirim-wrap { display:flex; flex-direction:column; gap:4px; }
    .kirim-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:8px; font-size:11.5px; font-weight:600; border:1px solid transparent; width:fit-content; }
    .kirim-ambil  { background:#FEF3C7; color:#92400E; border-color:#FDE68A; }
    .kirim-gosend { background:#D1FAE5; color:#065F46; border-color:#A7F3D0; }
    .kirim-rental { background:#DBEAFE; color:#1E40AF; border-color:#BFDBFE; }
    html.dark .kirim-ambil  { background:rgba(146,64,14,.2);   color:#FCD34D; border-color:rgba(146,64,14,.35); }
    html.dark .kirim-gosend { background:rgba(6,95,70,.2);     color:#6EE7B7; border-color:rgba(6,95,70,.35); }
    html.dark .kirim-rental { background:rgba(30,64,175,.2);   color:#93C5FD; border-color:rgba(30,64,175,.35); }
    .kirim-badge i { font-size:13px; }
    .kirim-ongkir { font-size:11px; color:var(--text-muted); padding-left:2px; }
    .kirim-free   { color:#059669; font-weight:600; }
    .kirim-instalasi { font-size:11px; color:#7C3AED; padding-left:2px; display:flex; align-items:center; gap:4px; }

    /* ── Dropdown Aksi ── */
    .action-wrap { position:relative; display:inline-flex; }
    .btn-action-menu { width:30px; height:30px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:17px; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); transition:all 0.2s; }
    .btn-action-menu:hover { background:var(--bg-hover); color:var(--text-primary); }
    .action-dropdown { position:absolute; right:0; top:calc(100% + 5px); background:var(--bg-card); border:1px solid var(--border); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.14); min-width:178px; z-index:500; display:none; overflow:hidden; animation:fadeMenu 0.12s ease; }
    .action-dropdown.open { display:block; }
    @keyframes fadeMenu { from{opacity:0;transform:translateY(-4px);}to{opacity:1;transform:translateY(0);} }
    .action-dropdown-item { display:flex; align-items:center; gap:9px; padding:10px 14px; font-size:13px; color:var(--text-primary); cursor:pointer; transition:background 0.12s; background:none; border:none; width:100%; font-family:var(--font); text-decoration:none; }
    .action-dropdown-item:hover { background:var(--bg-hover); }
    .action-dropdown-item i { font-size:15px; width:18px; text-align:center; flex-shrink:0; }
    .action-dropdown-item.item-view    i { color:var(--brand-500); }
    .action-dropdown-item.item-edit    i { color:#2563EB; }
    .action-dropdown-item.item-invoice i { color:#7C3AED; }
    .action-dropdown-item.item-delete  i { color:#EF4444; }
    .action-dropdown-item.item-delete:hover { background:#FFF1F2; color:#E11D48; }
    html.dark .action-dropdown-item.item-delete:hover { background:rgba(225,29,72,0.12); color:#FB7185; }
    .dropdown-divider { height:1px; background:var(--border); margin:3px 0; }

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

    .tanggal-badge { display:inline-flex; align-items:center; gap:5px; background:var(--bg-hover); padding:3px 10px; border-radius:6px; font-size:12.5px; font-weight:500; color:var(--text-primary); }
    .total-value { font-weight:700; color:#059669; }
    html.dark .total-value { color:#34D399; }

    .pay-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11.5px; font-weight:600; white-space:nowrap; }
    .pay-cash     { background:#F0FDF4; color:#16A34A; }
    .pay-tunai    { background:#F0FDF4; color:#16A34A; }
    .pay-transfer { background:#EFF6FF; color:#1D4ED8; }
    .pay-qris     { background:#FFF7ED; color:#C2410C; }
    .pay-kredit   { background:#FDF4FF; color:#7C3AED; }
    .pay-dp       { background:#FEF3C7; color:#92400E; }
    html.dark .pay-cash     { background:rgba(22,163,74,0.12);  color:#4ADE80; }
    html.dark .pay-tunai    { background:rgba(22,163,74,0.12);  color:#4ADE80; }
    html.dark .pay-transfer { background:rgba(29,78,216,0.12);  color:#60A5FA; }
    html.dark .pay-qris     { background:rgba(194,65,12,0.12);  color:#FB923C; }
    html.dark .pay-kredit   { background:rgba(124,58,237,0.12); color:#C084FC; }
    html.dark .pay-dp       { background:rgba(146,64,14,0.12);  color:#FCD34D; }

    .status-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11px; font-weight:700; white-space:nowrap; margin-top:4px; }
    .status-lunas       { background:#D1FAE5; color:#065F46; }
    .status-dp          { background:#FEF3C7; color:#92400E; }
    .status-belum-lunas { background:#FEE2E2; color:#991B1B; }
    .status-batal       { background:#F1F5F9; color:#64748B; }
    html.dark .status-lunas       { background:rgba(6,95,70,.25);    color:#6EE7B7; }
    html.dark .status-dp          { background:rgba(146,64,14,.25);  color:#FCD34D; }
    html.dark .status-belum-lunas { background:rgba(153,27,27,.25);  color:#FCA5A5; }
    html.dark .status-batal       { background:rgba(100,116,139,.2); color:#94A3B8; }

    .bukti-thumb { width:44px; height:44px; border-radius:8px; object-fit:cover; cursor:pointer; border:1px solid var(--border); transition:transform 0.15s,box-shadow 0.15s; display:block; margin:0 auto; }
    .bukti-thumb:hover { transform:scale(1.08); box-shadow:0 4px 12px rgba(0,0,0,0.15); }
    .bukti-empty { width:44px; height:44px; border-radius:8px; background:var(--bg-primary); border:1px dashed var(--border); display:inline-flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:18px; }
    .file-btn { display:inline-flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:8px; cursor:pointer; border:1px solid #FECACA; background:#FEF2F2; color:#EF4444; font-size:22px; margin:0 auto; transition:transform 0.15s,box-shadow 0.15s; }
    .file-btn:hover { transform:scale(1.08); box-shadow:0 4px 12px rgba(0,0,0,0.15); }

    .tfoot-total td { padding:12px 14px; font-size:13px; font-weight:700; color:var(--text-primary); background:var(--bg-hover); border-top:2px solid var(--border); }

    /* ── Modal ── */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from{opacity:0;}to{opacity:1;} }
    .modal { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.2); width:100%; max-width:460px; animation:slideUp 0.2s ease; }
    .modal-xl  { max-width:720px; }
    @keyframes slideUp { from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);} }
    .modal-header { padding:18px 22px 14px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .modal-title { font-size:15px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .modal-close { width:28px; height:28px; border:none; background:none; cursor:pointer; color:var(--text-muted); font-size:19px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:all 0.2s; }
    .modal-close:hover { background:var(--bg-hover); color:var(--text-primary); }
    .modal-body { padding:18px 22px; max-height:75vh; overflow-y:auto; }
    .modal-footer { padding:14px 22px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:flex-end; gap:8px; }
    .delete-warning { text-align:center; padding:6px 0; }
    .delete-warning i { font-size:42px; color:#EF4444; display:block; margin-bottom:10px; }
    .delete-warning h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:7px; }
    .delete-warning p { font-size:13px; color:var(--text-muted); line-height:1.6; }
    .delete-warning strong { color:var(--text-primary); }

    /* ── Lightbox ── */
    .lightbox-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.88); z-index:2000; align-items:center; justify-content:center; padding:20px; cursor:zoom-out; }
    .lightbox-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    .lightbox-overlay img { max-width:90vw; max-height:88vh; border-radius:10px; object-fit:contain; box-shadow:0 20px 60px rgba(0,0,0,0.5); cursor:default; }
    .lightbox-close { position:fixed; top:16px; right:20px; width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .lightbox-close:hover { background:rgba(255,255,255,0.28); }
    .lightbox-caption { position:fixed; bottom:18px; left:50%; transform:translateX(-50%); font-size:13px; color:rgba(255,255,255,0.75); background:rgba(0,0,0,0.5); padding:6px 16px; border-radius:20px; white-space:nowrap; max-width:80vw; overflow:hidden; text-overflow:ellipsis; }

    /* ── File Viewer Modal PDF ── */
    .file-modal { max-width:860px; width:100%; }
    .file-modal .modal-body { padding:0; max-height:none; overflow-y:visible; }
    .pdf-frame { width:100%; height:72vh; border:none; border-radius:0 0 16px 16px; display:block; }
    .pdf-fallback { padding:28px 24px; text-align:center; }
    .pdf-fallback i { font-size:52px; color:#EF4444; display:block; margin-bottom:12px; }
    .pdf-fallback p { font-size:13px; color:var(--text-muted); margin-bottom:16px; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success">
    <i class="ri-checkbox-circle-fill"></i> {{ session('success') }}
</div>
@endif

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-exchange-dollar-line"></i> Penjualan
        </h1>
        <p class="page-subtitle">Kelola data transaksi penjualan alat kesehatan</p>
    </div>
</div>

<div class="table-card">

    {{-- ══ TOOLBAR ══ --}}
    <div class="table-toolbar">
        <div class="toolbar-left">

            {{-- Per Page --}}
            <form method="GET" action="{{ route('penjualan.index') }}" id="perPageForm">
                <input type="hidden" name="search"    value="{{ $search }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to"   value="{{ $dateTo }}">
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

            {{-- Search --}}
            <form method="GET" action="{{ route('penjualan.index') }}" class="search-form" id="searchForm">
                <input type="hidden" name="per_page"  value="{{ $perPage }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to"   value="{{ $dateTo }}">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search"
                           value="{{ $search }}"
                           placeholder="Cari pelanggan, barang..."
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                @if($search)
                <a href="{{ route('penjualan.index', ['per_page' => $perPage, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                   class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                @endif
            </form>

        </div>

        <div class="toolbar-right">
            <button type="button" class="btn btn-export" id="btnToggleExport"
                    onclick="toggleExportPanel()" title="Export ke Excel">
                <i class="ri-file-excel-2-line"></i> Export XLSX
                <i class="ri-arrow-down-s-line" id="exportArrow" style="font-size:14px;transition:transform 0.2s;"></i>
            </button>
            <a href="{{ route('penjualan.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Penjualan
            </a>
        </div>
    </div>

    {{-- ══ DATE FILTER BAR ══ --}}
    <div class="date-filter-bar">
        <span class="date-filter-label">
            <i class="ri-calendar-2-line"></i> Filter Tanggal
        </span>

        <form method="GET" action="{{ route('penjualan.index') }}" id="dateFilterForm"
              style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <input type="hidden" name="search"   value="{{ $search }}">
            <input type="hidden" name="per_page" value="{{ $perPage }}">

            <div class="date-inputs-wrap">
                <input type="date" name="date_from" id="inputDateFrom"
                       class="date-input {{ $dateFrom ? 'active' : '' }}"
                       value="{{ $dateFrom }}"
                       title="Dari tanggal"
                       onchange="document.getElementById('dateFilterForm').submit()">
                <span class="date-sep">—</span>
                <input type="date" name="date_to" id="inputDateTo"
                       class="date-input {{ $dateTo ? 'active' : '' }}"
                       value="{{ $dateTo }}"
                       title="Sampai tanggal"
                       onchange="document.getElementById('dateFilterForm').submit()">
            </div>

            @if($dateFrom || $dateTo)
            <span class="date-filter-active-badge">
                <i class="ri-filter-fill" style="font-size:10px;"></i>
                Aktif{{ $dateFrom && $dateTo ? ': ' . \Carbon\Carbon::parse($dateFrom)->format('d M') . ' – ' . \Carbon\Carbon::parse($dateTo)->format('d M Y') : '' }}
            </span>
            <a href="{{ route('penjualan.index', ['search' => $search, 'per_page' => $perPage]) }}"
               class="btn btn-ghost" style="height:28px;font-size:12px;padding:0 10px;">
                <i class="ri-close-line"></i> Hapus Filter
            </a>
            @endif
        </form>

        <div class="date-shortcuts">
            <span>Cepat:</span>
            <button type="button" class="shortcut-btn" onclick="setDateRange('today')">Hari ini</button>
            <button type="button" class="shortcut-btn" onclick="setDateRange('week')">7 hari</button>
            <button type="button" class="shortcut-btn" onclick="setDateRange('month')">Bulan ini</button>
            <button type="button" class="shortcut-btn" onclick="setDateRange('year')">Tahun ini</button>
        </div>
    </div>

    {{-- ══ EXPORT FILTER PANEL ══ --}}
    <div class="export-panel" id="exportPanel">
        <div class="export-panel-title">
            <i class="ri-file-excel-2-line"></i>
            Filter Export XLSX
        </div>
        <form method="GET" action="{{ route('penjualan.export') }}" id="formExport">
            <input type="hidden" name="search" value="{{ $search }}">
            <div class="export-filter-row">

                <div class="export-filter-group">
                    <label class="export-filter-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="export-filter-input"
                           value="{{ $dateFrom ?: request('date_from') }}"
                           style="width:150px;">
                </div>

                <div class="export-filter-group">
                    <label class="export-filter-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="export-filter-input"
                           value="{{ $dateTo ?: request('date_to') }}"
                           style="width:150px;">
                </div>

                <div class="export-filter-group">
                    <label class="export-filter-label">Status Pembayaran</label>
                    <select name="status_pembayaran" class="export-filter-input" style="width:155px;">
                        <option value="semua">Semua Status</option>
                        <option value="lunas"       {{ request('status_pembayaran') === 'lunas'       ? 'selected' : '' }}>Lunas</option>
                        <option value="dp"          {{ request('status_pembayaran') === 'dp'          ? 'selected' : '' }}>DP / Sebagian</option>
                        <option value="belum_lunas" {{ request('status_pembayaran') === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>

                <div class="export-filter-group">
                    <label class="export-filter-label">Status Transaksi</label>
                    <select name="status_transaksi" class="export-filter-input" style="width:140px;">
                        <option value="semua">Semua Transaksi</option>
                        <option value="aktif"   {{ request('status_transaksi') === 'aktif'   ? 'selected' : '' }}>Aktif</option>
                        <option value="selesai" {{ request('status_transaksi') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal"   {{ request('status_transaksi') === 'batal'   ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>

                <div class="export-filter-group">
                    <label class="export-filter-label" style="visibility:hidden;">-</label>
                    <button type="submit" class="btn btn-export" style="height:34px;padding:0 16px;">
                        <i class="ri-download-2-line"></i> Download
                    </button>
                </div>

                <div class="export-filter-group">
                    <label class="export-filter-label" style="visibility:hidden;">-</label>
                    <button type="button" class="btn btn-ghost" style="height:34px;padding:0 12px;"
                            onclick="resetExportFilter()">
                        <i class="ri-refresh-line"></i> Reset
                    </button>
                </div>

            </div>
            <div class="export-filter-hint" style="margin-top:8px;">
                <i class="ri-information-line" style="font-size:12px;"></i>
                File yang dihasilkan memiliki <strong>2 sheet</strong>: <strong>Rekap Transaksi</strong> &amp; <strong>Detail Barang</strong>.
                Kosongkan tanggal untuk export semua data.
            </div>
        </form>
    </div>

    {{-- ══ INFO BAR ══ --}}
    <div class="info-bar">
        <div class="info-bar-text">
            @if($search || $dateFrom || $dateTo)
                <i class="ri-filter-3-line"></i>
                @if($search)
                    Pencarian: <strong>"{{ $search }}"</strong>
                @endif
                @if($dateFrom || $dateTo)
                    @if($search) &nbsp;·&nbsp; @endif
                    <i class="ri-calendar-line" style="font-size:12px;"></i>
                    @if($dateFrom && $dateTo)
                        {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                    @elseif($dateFrom)
                        Dari {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
                    @else
                        S/d {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                    @endif
                @endif
                &nbsp;<span class="badge-count">{{ $penjualans->total() }} transaksi</span>
            @else
                <i class="ri-exchange-dollar-line"></i>
                Total <span class="badge-count">{{ $penjualans->total() }} transaksi</span>
            @endif
        </div>
        @if($penjualans->total() > 0)
        <div class="info-bar-text">
            Halaman <strong>{{ $penjualans->currentPage() }}</strong>
            dari <strong>{{ $penjualans->lastPage() }}</strong>
        </div>
        @endif
    </div>

    {{-- ══ TABLE ══ --}}
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:42px;">#</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Barang (Detail)</th>
                    <th>Metode &amp; Status</th>
                    <th>Pengiriman</th>
                    <th class="right">Total Tagihan</th>
                    <th>Keterangan</th>
                    <th class="center" style="width:70px;">Bukti</th>
                    <th class="center" style="width:54px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjualans as $item)
                @php
                    $kirimKey   = $item->jasa_pengiriman ?? 'ambil_sendiri';
                    $kirimClass = match($kirimKey) {
                        'gosend_grab'  => 'kirim-gosend',
                        'rental_mobil' => 'kirim-rental',
                        default        => 'kirim-ambil',
                    };
                @endphp
                <tr>
                    <td style="color:var(--text-muted);font-size:12.5px;font-weight:500;">
                        {{ $penjualans->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <span class="tanggal-badge">
                            <i class="ri-calendar-line" style="font-size:12px;color:var(--text-muted);"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal_penjualan)->format('d M Y') }}
                        </span>
                    </td>

                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $item->nama_pelanggan }}</div>
                        @if($item->nomor_telepon)
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">
                            <i class="ri-phone-line" style="font-size:11px;"></i> {{ $item->nomor_telepon }}
                        </div>
                        @endif
                        @if($item->alamat_pelanggan)
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:1px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            <i class="ri-map-pin-line" style="font-size:11px;"></i> {{ $item->alamat_pelanggan }}
                        </div>
                        @endif
                    </td>

                    <td style="min-width:220px;">
                        @if($item->details->count() > 0)
                        <div class="items-list">
                            @foreach($item->details->take(2) as $d)
                            <div class="item-pill">
                                <span class="item-name">{{ $d->nama_barang }}</span>
                                <span class="item-qty">×{{ $d->qty }}</span>
                                <span class="item-sub">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                            @if($item->details->count() > 2)
                            <span class="items-more"
                                  onclick="window.location='{{ route('penjualan.show', $item->id) }}'">
                                <i class="ri-add-circle-line"></i>
                                +{{ $item->details->count() - 2 }} barang lainnya
                            </span>
                            @endif
                        </div>
                        @else
                        <span style="color:var(--text-muted);font-size:12.5px;font-style:italic;">—</span>
                        @endif
                    </td>

                    <td>
                        @php
                            $metode = $item->jenis_pembayaran ?? 'cash';
                            $payClass = match(strtolower($metode)) {
                                'transfer' => 'pay-transfer',
                                'qris'     => 'pay-qris',
                                'kredit'   => 'pay-kredit',
                                'dp'       => 'pay-dp',
                                default    => 'pay-cash',
                            };
                            $payIcon = match(strtolower($metode)) {
                                'transfer' => 'ri-bank-line',
                                'qris'     => 'ri-qr-code-line',
                                'kredit'   => 'ri-bank-card-line',
                                default    => 'ri-money-dollar-circle-line',
                            };
                            $statusClass = match($item->status_pembayaran ?? 'belum_lunas') {
                                'lunas'       => 'status-lunas',
                                'dp'          => 'status-dp',
                                'belum_lunas' => 'status-belum-lunas',
                                default       => 'status-belum-lunas',
                            };
                            if ($item->isBatal()) $statusClass = 'status-batal';
                            $statusLabel = $item->isBatal() ? 'Dibatalkan' : $item->status_pembayaran_label;
                            $statusIcon  = match($item->status_pembayaran ?? 'belum_lunas') {
                                'lunas'       => 'ri-checkbox-circle-fill',
                                'dp'          => 'ri-time-line',
                                'belum_lunas' => 'ri-close-circle-line',
                                default       => 'ri-close-circle-line',
                            };
                            if ($item->isBatal()) $statusIcon = 'ri-forbid-line';
                        @endphp
                        <span class="pay-badge {{ $payClass }}">
                            <i class="{{ $payIcon }}"></i>
                            {{ ucfirst($metode) }}
                        </span>
                        <br>
                        <span class="status-badge {{ $statusClass }}">
                            <i class="{{ $statusIcon }}" style="font-size:10px;"></i>
                            {{ $statusLabel }}
                        </span>
                    </td>

                    <td style="min-width:170px;">
                        <div class="kirim-wrap">
                            <span class="kirim-badge {{ $kirimClass }}">
                                <i class="{{ $item->jasa_pengiriman_icon }}"></i>
                                {{ $item->jasa_pengiriman_label }}
                            </span>
                            @if(($item->harga_pengiriman ?? 0) > 0)
                                <span class="kirim-ongkir">
                                    <i class="ri-price-tag-3-line" style="font-size:10px;"></i>
                                    Ongkir: Rp {{ number_format($item->harga_pengiriman, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="kirim-ongkir kirim-free">
                                    <i class="ri-check-double-line" style="font-size:10px;"></i> Gratis
                                </span>
                            @endif
                            @if(($item->jasa_instalasi ?? 0) > 0)
                                <span class="kirim-instalasi">
                                    <i class="ri-tools-line"></i>
                                    Instalasi: Rp {{ number_format($item->jasa_instalasi, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </td>

                    <td class="right total-value" style="font-size:13px;white-space:nowrap;">
                        Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                        @if(($item->total_terbayar ?? 0) > 0 && !$item->isLunas())
                        <div style="font-size:11px;color:var(--text-muted);font-weight:400;margin-top:2px;">
                            Terbayar: Rp {{ number_format($item->total_terbayar, 0, ',', '.') }}
                        </div>
                        @endif
                    </td>

                    <td style="max-width:130px;">
                        @if($item->keterangan)
                            <span title="{{ $item->keterangan }}"
                                  style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12.5px;color:var(--text-muted);">
                                {{ $item->keterangan }}
                            </span>
                        @else
                            <span style="color:var(--text-muted);font-size:12px;">—</span>
                        @endif
                    </td>

                    <td class="center">
                        @if($item->foto_bukti)
                            @php $extBukti = strtolower(pathinfo($item->foto_bukti, PATHINFO_EXTENSION)); @endphp
                            @if($extBukti === 'pdf')
                                <button type="button" class="file-btn"
                                        title="Lihat Bukti Pembayaran (PDF)"
                                        onclick="openFileModal(
                                            '{{ Storage::url($item->foto_bukti) }}',
                                            'Bukti — {{ addslashes($item->nama_pelanggan) }}',
                                            'pdf'
                                        )">
                                    <i class="ri-file-pdf-2-line"></i>
                                </button>
                            @else
                                <img src="{{ Storage::url($item->foto_bukti) }}"
                                     alt="Bukti {{ $item->nama_pelanggan }}"
                                     class="bukti-thumb"
                                     onclick="openLightbox(
                                         '{{ Storage::url($item->foto_bukti) }}',
                                         'Bukti — {{ addslashes($item->nama_pelanggan) }}'
                                     )"
                                     title="Klik untuk perbesar">
                            @endif
                        @else
                            <span class="bukti-empty" title="Tidak ada bukti">
                                <i class="ri-image-line"></i>
                            </span>
                        @endif
                    </td>

                    {{-- ══ Kolom Aksi (tanpa Buy Back) ══ --}}
                    <td class="center">
                        <div class="action-wrap">
                            <button type="button"
                                    class="btn-action-menu"
                                    title="Aksi"
                                    onclick="toggleDropdown(this, event)">
                                <i class="ri-more-2-fill"></i>
                            </button>
                            <div class="action-dropdown">
                                <a href="{{ route('penjualan.show', $item->id) }}"
                                   class="action-dropdown-item item-view">
                                    <i class="ri-eye-line"></i> Lihat Detail
                                </a>
                                @if(!$item->isBatal())
                                <a href="{{ route('penjualan.edit', $item->id) }}"
                                   class="action-dropdown-item item-edit">
                                    <i class="ri-edit-line"></i> Edit Penjualan
                                </a>
                                @endif
                                <a href="{{ route('penjualan.invoice', $item->id) }}"
                                   class="action-dropdown-item item-invoice"
                                   target="_blank">
                                    <i class="ri-file-text-line"></i> Cetak Invoice
                                </a>
                                <div class="dropdown-divider"></div>
                                <button type="button"
                                        class="action-dropdown-item item-delete"
                                        onclick="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_pelanggan) }}');closeAllDropdowns();">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10">
                        <div class="empty-state">
                            <i class="ri-exchange-dollar-line"></i>
                            <h3>
                                @if($search || $dateFrom || $dateTo)
                                    Tidak ada data yang cocok
                                @else
                                    Belum ada data penjualan
                                @endif
                            </h3>
                            <p>
                                @if($search || $dateFrom || $dateTo)
                                    Coba ubah kata kunci atau filter tanggal.
                                @else
                                    Klik "Tambah Penjualan" untuk mencatat transaksi baru.
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>

            @if($penjualans->count() > 0)
            <tfoot>
                <tr class="tfoot-total">
                    <td colspan="6" class="right">Total keseluruhan halaman ini:</td>
                    <td class="right total-value" style="font-size:14px;white-space:nowrap;">
                        Rp {{ number_format($penjualans->sum('total_tagihan'), 0, ',', '.') }}
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- ══ PAGINATION ══ --}}
    @if($penjualans->total() > 0)
    <div class="table-footer">
        <div class="pagination-meta">
            Menampilkan
            <strong>{{ $penjualans->firstItem() }} – {{ $penjualans->lastItem() }}</strong>
            dari <strong>{{ $penjualans->total() }}</strong> transaksi
        </div>

        @if($penjualans->hasPages())
        <nav class="pagination-nav" aria-label="Pagination">
            @if($penjualans->onFirstPage())
                <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
            @else
                <a class="page-btn" href="{{ $penjualans->previousPageUrl() }}">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            @endif

            @php
                $current = $penjualans->currentPage();
                $last    = $penjualans->lastPage();
                $pages   = [];
                for ($p = 1; $p <= $last; $p++) {
                    if ($p === 1 || $p === $last || ($p >= $current - 2 && $p <= $current + 2))
                        $pages[] = $p;
                }
                $rendered = []; $prev = null;
                foreach ($pages as $p) {
                    if ($prev !== null && $p - $prev > 1) $rendered[] = '...';
                    $rendered[] = $p;
                    $prev = $p;
                }
            @endphp

            @foreach($rendered as $pageItem)
                @if($pageItem === '...')
                    <span class="page-ellipsis">…</span>
                @elseif($pageItem == $current)
                    <span class="page-btn active">{{ $pageItem }}</span>
                @else
                    <a class="page-btn" href="{{ $penjualans->url($pageItem) }}">{{ $pageItem }}</a>
                @endif
            @endforeach

            @if($penjualans->hasMorePages())
                <a class="page-btn" href="{{ $penjualans->nextPageUrl() }}">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            @else
                <span class="page-btn disabled"><i class="ri-arrow-right-s-line"></i></span>
            @endif
        </nav>
        @endif
    </div>
    @endif

</div>{{-- /table-card --}}


{{-- ══ MODAL: KONFIRMASI HAPUS ══ --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-delete-bin-line" style="color:#EF4444;"></i> Konfirmasi Hapus
            </span>
            <button class="modal-close" onclick="closeModal('modalHapus')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="ri-alert-fill"></i>
                <h3>Hapus Data Penjualan?</h3>
                <p>Kamu akan menghapus transaksi atas nama:<br>
                   <strong id="deleteNamaPelanggan"></strong><br><br>
                   Stok barang akan dikembalikan otomatis. Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalHapus')">Batal</button>
            <form id="formDeleteSubmit" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>


{{-- ══ LIGHTBOX ══ --}}
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="event.stopPropagation();closeLightbox()">
        <i class="ri-close-line"></i>
    </button>
    <img id="lightboxImg" src="" alt="Bukti" onclick="event.stopPropagation()">
    <div class="lightbox-caption" id="lightboxCaption"></div>
</div>


{{-- ══ MODAL FILE VIEWER PDF ══ --}}
<div class="modal-overlay" id="modalFileViewer">
    <div class="modal file-modal">
        <div class="modal-header">
            <span class="modal-title" id="fileViewerTitle">
                <i class="ri-file-pdf-2-line" style="color:#EF4444;"></i>
                <span id="fileViewerTitleText">Lihat File</span>
            </span>
            <div style="display:flex;align-items:center;gap:8px;">
                <a id="fileViewerOpenLink" href="#" target="_blank"
                   class="btn btn-ghost" style="height:30px;font-size:12px;padding:0 10px;"
                   title="Buka di tab baru">
                    <i class="ri-external-link-line"></i> Tab baru
                </a>
                <button class="modal-close" onclick="closeFileModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
        <div class="modal-body">
            <div id="pdfViewerWrap">
                <iframe id="pdfFrame" class="pdf-frame" src="" title="PDF Viewer"></iframe>
            </div>
            <div id="pdfFallback" class="pdf-fallback" style="display:none;">
                <i class="ri-file-pdf-2-line"></i>
                <p>Browser kamu tidak dapat menampilkan PDF secara langsung.</p>
                <a id="pdfFallbackLink" href="#" target="_blank" class="btn btn-primary">
                    <i class="ri-download-line"></i> Buka / Download File
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Date Filter Shortcuts ──
function setDateRange(range) {
    const today = new Date();
    const pad   = n => String(n).padStart(2, '0');
    const ymd   = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
    let from, to;
    to = ymd(today);
    if (range === 'today') {
        from = to;
    } else if (range === 'week') {
        const w = new Date(today);
        w.setDate(today.getDate() - 6);
        from = ymd(w);
    } else if (range === 'month') {
        from = `${today.getFullYear()}-${pad(today.getMonth()+1)}-01`;
    } else if (range === 'year') {
        from = `${today.getFullYear()}-01-01`;
    }
    document.getElementById('inputDateFrom').value = from;
    document.getElementById('inputDateTo').value   = to;
    document.getElementById('dateFilterForm').submit();
}

// ── Export Panel Toggle ──
function toggleExportPanel() {
    const panel = document.getElementById('exportPanel');
    const arrow = document.getElementById('exportArrow');
    const isOpen = panel.classList.contains('open');
    panel.classList.toggle('open');
    arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

function resetExportFilter() {
    const form = document.getElementById('formExport');
    form.querySelector('[name="date_from"]').value         = '';
    form.querySelector('[name="date_to"]').value           = '';
    form.querySelector('[name="status_pembayaran"]').value = 'semua';
    form.querySelector('[name="status_transaksi"]').value  = 'semua';
}

// ── Modal ──
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) {
            if (this.id === 'modalFileViewer') closeFileModal();
            else closeModal(this.id);
        }
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeLightbox();
        closeFileModal();
        document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
        closeAllDropdowns();
    }
});

// ── Dropdown Aksi ──
function toggleDropdown(btn, e) {
    e.stopPropagation();
    const dropdown = btn.nextElementSibling;
    const isOpen   = dropdown.classList.contains('open');
    closeAllDropdowns();
    if (!isOpen) {
        dropdown.classList.add('open');
        requestAnimationFrame(() => {
            const rect = dropdown.getBoundingClientRect();
            if (rect.bottom > window.innerHeight - 8) {
                dropdown.style.top    = 'auto';
                dropdown.style.bottom = 'calc(100% + 5px)';
            }
        });
    }
}
function closeAllDropdowns() {
    document.querySelectorAll('.action-dropdown.open').forEach(d => {
        d.classList.remove('open');
        d.style.top    = '';
        d.style.bottom = '';
    });
}
document.addEventListener('click', function(e) {
    closeAllDropdowns();
    const panel  = document.getElementById('exportPanel');
    const btnExp = document.getElementById('btnToggleExport');
    if (panel && panel.classList.contains('open') &&
        !panel.contains(e.target) && !btnExp.contains(e.target)) {
        panel.classList.remove('open');
        document.getElementById('exportArrow').style.transform = 'rotate(0deg)';
    }
});

// ── Delete Modal ──
function openDeleteModal(id, nama) {
    document.getElementById('deleteNamaPelanggan').textContent = nama;
    document.getElementById('formDeleteSubmit').action = '/penjualan/' + id;
    openModal('modalHapus');
}

// ── Lightbox ──
function openLightbox(src, caption) {
    document.getElementById('lightboxImg').src             = src;
    document.getElementById('lightboxCaption').textContent = caption;
    document.getElementById('lightboxOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightboxOverlay').classList.remove('open');
    document.getElementById('lightboxImg').src = '';
    document.body.style.overflow = '';
}

// ── File Viewer Modal PDF ──
function openFileModal(url, title, type) {
    document.getElementById('fileViewerTitleText').textContent = title;
    document.getElementById('fileViewerOpenLink').href         = url;
    document.getElementById('pdfFallbackLink').href            = url;
    const frame    = document.getElementById('pdfFrame');
    const wrap     = document.getElementById('pdfViewerWrap');
    const fallback = document.getElementById('pdfFallback');
    if (type === 'pdf') {
        frame.src              = url;
        wrap.style.display     = 'block';
        fallback.style.display = 'none';
        frame.onerror = function() {
            wrap.style.display     = 'none';
            fallback.style.display = 'block';
        };
    }
    openModal('modalFileViewer');
}
function closeFileModal() {
    closeModal('modalFileViewer');
    setTimeout(() => {
        document.getElementById('pdfFrame').src                = '';
        document.getElementById('pdfViewerWrap').style.display = 'block';
        document.getElementById('pdfFallback').style.display   = 'none';
    }, 200);
}
</script>
@endpush