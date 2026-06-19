{{-- resources/views/admin/pembelian/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Pembelian Barang')
@section('breadcrumb', 'Pembelian Barang')

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
    .btn-search:hover  { background:var(--brand-100); color:var(--brand-600); }
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

    .date-filter-bar { padding:10px 18px; border-bottom:1px solid var(--border); background:var(--bg-primary); display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .date-filter-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); display:flex; align-items:center; gap:5px; white-space:nowrap; }
    .date-filter-label i { font-size:13px; color:var(--brand-500); }
    .date-input-wrap { display:flex; align-items:center; gap:6px; }
    .date-input { height:34px; padding:0 10px; border:1px solid var(--border); border-radius:8px; background:var(--bg-card); color:var(--text-primary); font-size:13px; font-family:var(--font); outline:none; transition:border-color 0.2s,box-shadow 0.2s; width:148px; }
    .date-input:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.1); }
    .date-separator { font-size:12px; color:var(--text-muted); font-weight:500; }
    .date-filter-active { background:var(--brand-50); border-color:var(--brand-200) !important; }
    html.dark .date-filter-active { background:rgba(29,111,164,0.1); }
    .date-filter-badge { display:inline-flex; align-items:center; gap:4px; background:var(--brand-500); color:#fff; border-radius:99px; padding:2px 9px; font-size:11px; font-weight:700; margin-left:4px; }

    .filter-tabs { display:flex; align-items:center; gap:4px; padding:12px 18px; border-bottom:1px solid var(--border); background:var(--bg-primary); flex-wrap:wrap; }
    .tab-btn { display:inline-flex; align-items:center; gap:6px; padding:5px 14px; height:32px; border-radius:8px; font-size:12.5px; font-weight:500; font-family:var(--font); cursor:pointer; text-decoration:none; transition:all 0.18s; border:1px solid transparent; color:var(--text-secondary); background:transparent; }
    .tab-btn:hover { background:var(--bg-hover); color:var(--text-primary); border-color:var(--border); }
    .tab-btn.active         { background:var(--brand-500); color:#fff; border-color:var(--brand-500); font-weight:600; }
    .tab-btn.active-buyback { background:#F59E0B; color:#fff; border-color:#F59E0B; font-weight:600; }
    .tab-count { display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; border-radius:99px; font-size:11px; font-weight:700; padding:0 5px; }
    .tab-btn.active .tab-count,
    .tab-btn.active-buyback .tab-count { background:rgba(255,255,255,0.25); color:#fff; }
    .tab-btn:not(.active):not(.active-buyback) .tab-count { background:var(--bg-hover); color:var(--text-muted); }

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
    .data-table th.right,  .data-table td.right  { text-align:right; }

    .action-wrap { position:relative; display:inline-block; }
    .btn-aksi-toggle { width:32px; height:32px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:18px; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); transition:all 0.2s; }
    .btn-aksi-toggle:hover { background:var(--brand-500); color:#fff; border-color:var(--brand-500); }
    .dropdown-menu-aksi { display:none; position:absolute; right:0; top:calc(100% + 6px); background:var(--bg-card); border:1px solid var(--border); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.15); z-index:200; min-width:185px; padding:5px; }
    .dropdown-menu-aksi.open { display:block; animation:fadeDropdown 0.15s ease; }
    @keyframes fadeDropdown { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
    .dropdown-item { display:flex; align-items:center; gap:9px; padding:8px 12px; border-radius:7px; font-size:13px; font-weight:500; color:var(--text-primary); text-decoration:none; cursor:pointer; border:none; background:none; width:100%; font-family:var(--font); transition:background 0.15s; }
    .dropdown-item:hover { background:var(--bg-hover); }
    .dropdown-item i { font-size:16px; width:18px; text-align:center; }
    .dropdown-item.item-show i    { color:#F59E0B; }
    .dropdown-item.item-edit i    { color:var(--brand-500); }
    .dropdown-item.item-invoice i { color:#16A34A; }
    .dropdown-item.item-delete i  { color:#EF4444; }
    .dropdown-item.item-delete:hover { background:#FFF1F2; color:#DC2626; }
    html.dark .dropdown-item.item-delete:hover { background:rgba(225,29,72,0.1); color:#FB7185; }
    .dropdown-divider { height:1px; background:var(--border); margin:4px 0; }

    .table-footer { padding:12px 18px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
    .pagination-meta { font-size:12.5px; color:var(--text-muted); }
    .pagination-meta strong { color:var(--text-primary); }
    .pagination-nav { display:flex; align-items:center; gap:3px; }
    .page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 6px; border-radius:7px; font-size:13px; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); text-decoration:none; cursor:pointer; transition:all 0.18s; font-family:var(--font); }
    .page-btn:hover  { background:var(--bg-hover); color:var(--text-primary); }
    .page-btn.active { background:var(--brand-500); color:#fff; border-color:var(--brand-500); font-weight:700; }
    .page-btn.disabled { opacity:0.35; cursor:not-allowed; pointer-events:none; }
    .page-ellipsis { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; font-size:13px; color:var(--text-muted); }

    .empty-state { text-align:center; padding:56px 24px; }
    .empty-state i { font-size:48px; color:var(--border); display:block; margin-bottom:12px; }
    .empty-state h3 { font-size:15px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; }
    .empty-state p  { font-size:13px; color:var(--text-muted); }

    .alert { display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; font-size:13.5px; font-weight:500; margin-bottom:18px; border:1px solid transparent; }
    .alert-success { background:#F0FDF4; color:#15803D; border-color:#BBF7D0; }
    .alert-error   { background:#FFF1F2; color:#BE123C; border-color:#FECDD3; }
    html.dark .alert-success { background:rgba(21,128,61,0.12); color:#4ADE80; border-color:rgba(21,128,61,0.25); }
    html.dark .alert-error   { background:rgba(190,18,60,0.12); color:#FB7185; border-color:rgba(190,18,60,0.25); }

    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from{opacity:0;}to{opacity:1;} }
    .modal { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.2); width:100%; max-width:460px; animation:slideUp 0.2s ease; }
    @keyframes slideUp { from{opacity:0;transform:translateY(18px);}to{opacity:1;transform:translateY(0);} }
    .modal-header { padding:18px 22px 14px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }
    .modal-title  { font-size:15px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
    .modal-close  { width:28px; height:28px; border:none; background:none; cursor:pointer; color:var(--text-muted); font-size:19px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:all 0.2s; }
    .modal-close:hover { background:var(--bg-hover); color:var(--text-primary); }
    .modal-body   { padding:18px 22px; }
    .modal-footer { padding:14px 22px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:flex-end; gap:8px; }
    .delete-warning { text-align:center; padding:6px 0; }
    .delete-warning i  { font-size:42px; color:#EF4444; display:block; margin-bottom:10px; }
    .delete-warning h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:7px; }
    .delete-warning p  { font-size:13px; color:var(--text-muted); line-height:1.6; }
    .delete-warning strong { color:var(--text-primary); }
    .delete-impact-box { display:flex; align-items:center; gap:10px; background:#FFF7ED; border:1px solid #FED7AA; border-radius:10px; padding:10px 14px; margin-top:12px; font-size:12.5px; color:#C2410C; text-align:left; }
    .delete-impact-box i { font-size:18px; flex-shrink:0; }
    html.dark .delete-impact-box { background:rgba(194,65,12,0.1); border-color:rgba(194,65,12,0.3); color:#FB923C; }

    .qty-badge { display:inline-flex; align-items:center; background:#EFF6FF; color:#1D4ED8; padding:2px 10px; border-radius:20px; font-size:12.5px; font-weight:700; }
    html.dark .qty-badge { background:rgba(29,78,216,0.15); color:#60A5FA; }
    .total-value { font-weight:700; color:#059669; }
    html.dark .total-value { color:#34D399; }
    .tanggal-badge { display:inline-flex; align-items:center; gap:5px; background:var(--bg-hover); padding:3px 10px; border-radius:6px; font-size:12.5px; font-weight:500; color:var(--text-primary); }
    .status-badge    { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11.5px; font-weight:600; white-space:nowrap; }
    .status-normal   { background:#EFF6FF; color:#1D4ED8; }
    .status-buy-back { background:#FFFBEB; color:#D97706; }
    html.dark .status-normal   { background:rgba(29,78,216,0.12); color:#60A5FA; }
    html.dark .status-buy-back { background:rgba(217,119,6,0.12);  color:#FBBF24; }
    .kondisi-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:11.5px; font-weight:600; white-space:nowrap; }
    .kondisi-baru  { background:#EFF6FF; color:#1D6FA4; border:1px solid #BFDBFE; }
    .kondisi-bekas { background:#F5F3FF; color:#7C3AED; border:1px solid #DDD6FE; }
    .kondisi-baik  { background:#F0FDF4; color:#15803D; border:1px solid #BBF7D0; }
    .kondisi-rusak { background:#FFF1F2; color:#BE123C; border:1px solid #FECDD3; }
    html.dark .kondisi-baru  { background:rgba(29,111,164,0.12);  color:#60A5FA;  border-color:rgba(29,111,164,0.25); }
    html.dark .kondisi-bekas { background:rgba(124,58,237,0.12);  color:#A78BFA;  border-color:rgba(124,58,237,0.25); }
    html.dark .kondisi-baik  { background:rgba(21,128,61,0.12);   color:#4ADE80;  border-color:rgba(21,128,61,0.25); }
    html.dark .kondisi-rusak { background:rgba(190,18,60,0.12);   color:#FB7185;  border-color:rgba(190,18,60,0.25); }
    .invoice-pill { display:inline-flex; align-items:center; gap:4px; font-family:monospace; font-size:11.5px; font-weight:700; color:#1D4ED8; background:#EFF6FF; padding:2px 9px; border-radius:99px; border:1px solid #BFDBFE; white-space:nowrap; }
    html.dark .invoice-pill { background:rgba(29,78,216,0.15); color:#93C5FD; border-color:rgba(29,78,216,0.3); }

    .tfoot-total td { padding:12px 16px; font-size:13px; font-weight:700; color:var(--text-primary); background:var(--bg-hover); border-top:2px solid var(--border); }

    .bukti-thumb { width:44px; height:44px; border-radius:8px; object-fit:cover; cursor:pointer; border:1px solid var(--border); transition:transform 0.15s,box-shadow 0.15s; display:block; margin:0 auto; }
    .bukti-thumb:hover { transform:scale(1.08); box-shadow:0 4px 12px rgba(0,0,0,0.15); }
    .bukti-empty { width:44px; height:44px; border-radius:8px; background:var(--bg-primary); border:1px dashed var(--border); display:inline-flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:18px; }
    .file-btn { display:inline-flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:8px; cursor:pointer; border:1px solid #FECACA; background:#FEF2F2; color:#EF4444; font-size:22px; margin:0 auto; transition:transform 0.15s, box-shadow 0.15s; }
    .file-btn:hover { transform:scale(1.08); box-shadow:0 4px 12px rgba(0,0,0,0.15); }
    .file-btn.invoice-file { border-color:#BFDBFE; background:#EFF6FF; color:#2563EB; }
    .file-btn.invoice-file:hover { box-shadow:0 4px 12px rgba(37,99,235,0.2); }

    .lightbox-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.88); z-index:2000; align-items:center; justify-content:center; padding:20px; cursor:zoom-out; }
    .lightbox-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    .lightbox-overlay img { max-width:90vw; max-height:88vh; border-radius:10px; object-fit:contain; box-shadow:0 20px 60px rgba(0,0,0,0.5); cursor:default; }
    .lightbox-close { position:fixed; top:16px; right:20px; width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .lightbox-close:hover { background:rgba(255,255,255,0.28); }
    .lightbox-caption { position:fixed; bottom:18px; left:50%; transform:translateX(-50%); font-size:13px; color:rgba(255,255,255,0.75); background:rgba(0,0,0,0.5); padding:6px 16px; border-radius:20px; white-space:nowrap; max-width:80vw; overflow:hidden; text-overflow:ellipsis; }

    .file-modal { max-width:860px; width:100%; }
    .file-modal .modal-body { padding:0; }
    .pdf-frame { width:100%; height:72vh; border:none; border-radius:0 0 16px 16px; display:block; }
    .pdf-fallback { padding:28px 24px; text-align:center; }
    .pdf-fallback i { font-size:52px; color:#EF4444; display:block; margin-bottom:12px; }
    .pdf-fallback p { font-size:13px; color:var(--text-muted); margin-bottom:16px; }

    /* ── Export Panel Dropdown ── */
    .dropdown-export-panel {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 10px 32px rgba(0,0,0,0.15);
        z-index: 300;
        width: 300px;
        overflow: hidden;
    }
    .dropdown-export-panel.open {
        display: block;
        animation: fadeDropdown 0.15s ease;
    }
    .export-panel-header {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 12px 16px 10px;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .export-field-group {
        padding: 10px 16px 0;
    }
    .export-field-label {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 5px;
    }
    .export-date-input,
    .export-select {
        width: 100%;
        height: 34px;
        padding: 0 10px;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: var(--bg-primary);
        color: var(--text-primary);
        font-size: 13px;
        font-family: var(--font);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }
    .export-date-input { width: calc(50% - 6px); }
    .export-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 9px center;
        padding-right: 28px;
    }
    .export-date-input:focus,
    .export-select:focus {
        border-color: var(--brand-500);
        box-shadow: 0 0 0 3px rgba(29,111,164,0.1);
    }
    .export-date-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .export-date-sep {
        font-size: 11px;
        color: var(--text-muted);
        flex-shrink: 0;
    }
    .export-panel-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        padding: 12px 16px;
        margin-top: 10px;
        border-top: 1px solid var(--border);
        background: var(--bg-primary);
    }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success">
    <i class="ri-checkbox-circle-fill"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-error">
    <i class="ri-error-warning-fill"></i> {{ session('error') }}
</div>
@endif

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-shopping-cart-2-line"></i> Pembelian Barang
        </h1>
        <p class="page-subtitle">Kelola data pembelian barang dan alat kesehatan</p>
    </div>
</div>

<div class="table-card">

    {{-- TOOLBAR --}}
    <div class="table-toolbar">
        <div class="toolbar-left">
            <form method="GET" action="{{ route('pembelian.index') }}" id="perPageForm">
                <input type="hidden" name="search"    value="{{ $search }}">
                <input type="hidden" name="filter"    value="{{ $filter }}">
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

            <form method="GET" action="{{ route('pembelian.index') }}" class="search-form" id="searchForm">
                <input type="hidden" name="per_page"  value="{{ $perPage }}">
                <input type="hidden" name="filter"    value="{{ $filter }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to"   value="{{ $dateTo }}">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama barang, no. invoice, pelanggan..."
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                @if($search)
                <a href="{{ route('pembelian.index', ['per_page' => $perPage, 'filter' => $filter, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
                   class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                @endif
            </form>
        </div>

        <div class="toolbar-right">

            {{-- ══ EXPORT PANEL ══ --}}
            <div class="action-wrap" id="exportWrap">
                <button type="button" class="btn btn-export" id="btnToggleExport"
                        onclick="toggleExportPanel()">
                    <i class="ri-file-excel-2-line"></i> Export XLSX
                    <i class="ri-arrow-down-s-line" id="exportArrow"
                       style="font-size:16px;transition:transform 0.2s;margin-left:2px;"></i>
                </button>

                <div class="dropdown-export-panel" id="exportPanel">
                    <div class="export-panel-header">
                        <i class="ri-filter-3-line" style="color:#10B981;"></i>
                        <span>Filter Export</span>
                    </div>

                    <form method="GET" action="{{ route('pembelian.export') }}" id="exportForm">
                        <input type="hidden" name="search" value="{{ $search }}">

                        {{-- Filter Tanggal --}}
                        <div class="export-field-group">
                            <label class="export-field-label">
                                <i class="ri-calendar-line"></i> Rentang Tanggal
                            </label>
                            <div class="export-date-row">
                                <input type="date" name="date_from"
                                       class="export-date-input"
                                       value="{{ $dateFrom }}">
                                <span class="export-date-sep">s/d</span>
                                <input type="date" name="date_to"
                                       class="export-date-input"
                                       value="{{ $dateTo }}">
                            </div>
                        </div>

                        {{-- Filter Status --}}
                        <div class="export-field-group">
                            <label class="export-field-label">
                                <i class="ri-list-check"></i> Status Pembelian
                            </label>
                            <select name="filter" class="export-select">
                                <option value="semua"    {{ ($filter ?? 'semua') === 'semua'    ? 'selected' : '' }}>Semua Status</option>
                                <option value="normal"   {{ ($filter ?? '') === 'normal'        ? 'selected' : '' }}>Normal</option>
                                <option value="buy_back" {{ ($filter ?? '') === 'buy_back'      ? 'selected' : '' }}>Buy Back</option>
                            </select>
                        </div>

                        {{-- Filter Kondisi Barang --}}
                        <div class="export-field-group">
                            <label class="export-field-label">
                                <i class="ri-checkbox-blank-circle-line"></i> Kondisi Barang
                            </label>
                            <select name="kondisi_barang" class="export-select">
                                <option value="semua">Semua Kondisi</option>
                                <option value="baru">Baru</option>
                                <option value="bekas">Bekas</option>
                                <option value="baik">Baik</option>
                                <option value="rusak">Rusak</option>
                            </select>
                        </div>

                        <div class="export-panel-footer">
                            <button type="button" class="btn btn-ghost"
                                    style="height:34px;font-size:12.5px;"
                                    onclick="resetExportForm()">
                                <i class="ri-refresh-line"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-export"
                                    style="height:34px;font-size:12.5px;">
                                <i class="ri-download-2-line"></i> Download
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- ══ /EXPORT PANEL ══ --}}

            <a href="{{ route('pembelian.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Pembelian
            </a>
        </div>
    </div>

    {{-- FILTER TANGGAL --}}
    <div class="date-filter-bar">
        <span class="date-filter-label">
            <i class="ri-calendar-2-line"></i> Filter Tanggal:
        </span>
        <form method="GET" action="{{ route('pembelian.index') }}"
              id="dateFilterForm" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <input type="hidden" name="search"   value="{{ $search }}">
            <input type="hidden" name="filter"   value="{{ $filter }}">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
            <div class="date-input-wrap">
                <input type="date" name="date_from" id="dateFrom"
                       class="date-input {{ $dateFrom ? 'date-filter-active' : '' }}"
                       value="{{ $dateFrom }}" title="Dari tanggal"
                       onchange="this.form.submit()">
                <span class="date-separator">s/d</span>
                <input type="date" name="date_to" id="dateTo"
                       class="date-input {{ $dateTo ? 'date-filter-active' : '' }}"
                       value="{{ $dateTo }}" title="Sampai tanggal"
                       onchange="this.form.submit()">
            </div>
            @if($dateFrom || $dateTo)
            <span class="date-filter-badge">
                <i class="ri-filter-fill" style="font-size:10px;"></i> Filter aktif
            </span>
            <a href="{{ route('pembelian.index', ['search' => $search, 'filter' => $filter, 'per_page' => $perPage]) }}"
               class="btn btn-ghost" style="height:30px;font-size:12px;padding:0 10px;">
                <i class="ri-close-line"></i> Reset
            </a>
            @endif
        </form>
        <div style="display:flex;align-items:center;gap:5px;margin-left:4px;">
            <span style="font-size:11px;color:var(--text-muted);">Cepat:</span>
            <button type="button" class="btn btn-ghost" style="height:28px;font-size:11.5px;padding:0 9px;" onclick="setDateRange('today')">Hari ini</button>
            <button type="button" class="btn btn-ghost" style="height:28px;font-size:11.5px;padding:0 9px;" onclick="setDateRange('week')">7 hari</button>
            <button type="button" class="btn btn-ghost" style="height:28px;font-size:11.5px;padding:0 9px;" onclick="setDateRange('month')">Bulan ini</button>
            <button type="button" class="btn btn-ghost" style="height:28px;font-size:11.5px;padding:0 9px;" onclick="setDateRange('year')">Tahun ini</button>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="filter-tabs">
        @php $dq = ['per_page' => $perPage, 'search' => $search, 'date_from' => $dateFrom, 'date_to' => $dateTo]; @endphp
        <a href="{{ route('pembelian.index', array_merge($dq, ['filter' => 'semua'])) }}"
           class="tab-btn {{ $filter === 'semua' ? 'active' : '' }}">
            <i class="ri-list-check"></i> Semua
            <span class="tab-count">{{ $countSemua }}</span>
        </a>
        <a href="{{ route('pembelian.index', array_merge($dq, ['filter' => 'normal'])) }}"
           class="tab-btn {{ $filter === 'normal' ? 'active' : '' }}">
            <i class="ri-shopping-cart-line"></i> Pembelian Normal
            <span class="tab-count">{{ $countNormal }}</span>
        </a>
        <a href="{{ route('pembelian.index', array_merge($dq, ['filter' => 'buy_back'])) }}"
           class="tab-btn {{ $filter === 'buy_back' ? 'active-buyback' : '' }}">
            <i class="ri-loop-left-line"></i> Buy Back
            <span class="tab-count">{{ $countBuyBack }}</span>
        </a>
    </div>

    {{-- INFO BAR --}}
    <div class="info-bar">
        <div class="info-bar-text">
            @if($dateFrom || $dateTo)
                <i class="ri-calendar-line"></i>
                @if($dateFrom && $dateTo)
                    <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong> s/d
                    <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
                @elseif($dateFrom)
                    Dari <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</strong>
                @else
                    Sampai <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</strong>
                @endif
                &nbsp;
            @endif
            @if($search)
                <i class="ri-filter-3-line"></i>
                "<strong>{{ $search }}</strong>"
            @endif
            <span class="badge-count">{{ $pembelians->total() }} data</span>
        </div>
        @if($pembelians->total() > 0)
        <div class="info-bar-text">
            Halaman <strong>{{ $pembelians->currentPage() }}</strong>
            dari <strong>{{ $pembelians->lastPage() }}</strong>
        </div>
        @endif
    </div>

    {{-- TABLE --}}
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:46px;">#</th>
                    <th>Tanggal</th>
                    <th>No. Invoice</th>
                    <th>Nama Barang</th>
                    <th class="center">Status</th>
                    <th class="center">Kondisi</th>
                    <th class="center">Qty</th>
                    <th class="right">Harga Satuan</th>
                    <th class="right">Total</th>
                    <th>Keterangan</th>
                    <th class="center" style="width:70px;">Bukti Bayar</th>
                    <th class="center" style="width:80px;">File Invoice</th>
                    <th class="center" style="width:60px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembelians as $item)
                <tr>
                    <td style="color:var(--text-muted);font-size:12.5px;font-weight:500;">
                        {{ $pembelians->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <span class="tanggal-badge">
                            <i class="ri-calendar-line" style="font-size:12px;color:var(--text-muted);"></i>
                            {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y') }}
                        </span>
                    </td>

                    <td>
                        @if($item->no_invoice)
                            <span class="invoice-pill">
                                <i class="ri-price-tag-3-line" style="font-size:11px;"></i>
                                @if($search)
                                    {!! preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                        '<mark style="background:#FEF08A;border-radius:3px;padding:0 2px;">$1</mark>',
                                        e($item->no_invoice)) !!}
                                @else
                                    {{ $item->no_invoice }}
                                @endif
                            </span>
                        @else
                            <span style="font-size:12px;color:var(--text-muted);">-</span>
                        @endif
                    </td>

                    <td>
                        <div style="font-weight:600;">
                            @if($search)
                                {!! preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                    '<mark style="background:#FEF08A;border-radius:3px;padding:0 2px;">$1</mark>',
                                    e($item->nama_barang)) !!}
                            @else
                                {{ $item->nama_barang }}
                            @endif
                        </div>
                        @if($item->status === 'buy_back' && $item->nama_pelanggan)
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">
                            <i class="ri-user-line" style="font-size:11px;"></i> {{ $item->nama_pelanggan }}
                        </div>
                        @endif
                    </td>

                    <td class="center">
                        @if($item->status === 'buy_back')
                            <span class="status-badge status-buy-back">
                                <i class="ri-loop-left-line"></i> Buy Back
                            </span>
                        @else
                            <span class="status-badge status-normal">
                                <i class="ri-shopping-cart-line"></i> Normal
                            </span>
                        @endif
                    </td>

                    <td class="center">
                        @php $k = $item->kondisi_barang; @endphp
                        @if($k === 'baru')
                            <span class="kondisi-badge kondisi-baru"><i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Baru</span>
                        @elseif($k === 'bekas')
                            <span class="kondisi-badge kondisi-bekas"><i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Bekas</span>
                        @elseif($k === 'baik')
                            <span class="kondisi-badge kondisi-baik"><i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Baik</span>
                        @elseif($k === 'rusak')
                            <span class="kondisi-badge kondisi-rusak"><i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Rusak</span>
                        @else
                            <span style="color:var(--text-muted);font-size:13px;">-</span>
                        @endif
                    </td>

                    <td class="center">
                        <span class="qty-badge">{{ number_format($item->jumlah) }}</span>
                    </td>

                    <td class="right" style="font-size:13px;white-space:nowrap;">
                        {{ $item->harga_formatted }}
                    </td>

                    <td class="right total-value" style="font-size:13px;white-space:nowrap;">
                        {{ $item->total_formatted }}
                    </td>

                    <td style="max-width:160px;">
                        @if($item->keterangan)
                            <span title="{{ $item->keterangan }}"
                                  style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;color:var(--text-muted);">
                                @if($search)
                                    {!! preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                        '<mark style="background:#FEF08A;border-radius:3px;padding:0 2px;">$1</mark>',
                                        e($item->keterangan)) !!}
                                @else
                                    {{ $item->keterangan }}
                                @endif
                            </span>
                        @else
                            <span style="color:var(--text-muted);font-style:italic;font-size:13px;">-</span>
                        @endif
                    </td>

                    {{-- Kolom Bukti Pembayaran --}}
                    <td class="center">
                        @if($item->bukti_transaksi)
                            @php $extBukti = strtolower(pathinfo($item->bukti_transaksi, PATHINFO_EXTENSION)); @endphp
                            @if($extBukti === 'pdf')
                                <button type="button" class="file-btn" title="Lihat Bukti Pembayaran (PDF)"
                                        onclick="openFileModal(
                                            '{{ asset('storage/' . $item->bukti_transaksi) }}',
                                            'Bukti Pembayaran - {{ addslashes($item->nama_barang) }}',
                                            'pdf'
                                        )">
                                    <i class="ri-file-pdf-2-line"></i>
                                </button>
                            @else
                                <img src="{{ asset('storage/' . $item->bukti_transaksi) }}"
                                     alt="Bukti {{ $item->nama_barang }}"
                                     class="bukti-thumb"
                                     onclick="openLightbox(
                                         '{{ asset('storage/' . $item->bukti_transaksi) }}',
                                         'Bukti Pembayaran - {{ addslashes($item->nama_barang) }}'
                                     )">
                            @endif
                        @else
                            <span class="bukti-empty" title="Tidak ada bukti">
                                <i class="ri-image-line"></i>
                            </span>
                        @endif
                    </td>

                    {{-- Kolom File Invoice --}}
                    <td class="center">
                        @if($item->file_invoice)
                            @php $extInv = strtolower(pathinfo($item->file_invoice, PATHINFO_EXTENSION)); @endphp
                            @if($extInv === 'pdf')
                                <button type="button" class="file-btn invoice-file" title="Lihat File Invoice (PDF)"
                                        onclick="openFileModal(
                                            '{{ asset('storage/' . $item->file_invoice) }}',
                                            'File Invoice - {{ addslashes($item->nama_barang) }}',
                                            'pdf'
                                        )">
                                    <i class="ri-file-pdf-2-line"></i>
                                </button>
                            @else
                                <img src="{{ asset('storage/' . $item->file_invoice) }}"
                                     alt="Invoice {{ $item->nama_barang }}"
                                     class="bukti-thumb"
                                     style="border-color:#BFDBFE;"
                                     onclick="openLightbox(
                                         '{{ asset('storage/' . $item->file_invoice) }}',
                                         'File Invoice - {{ addslashes($item->nama_barang) }}'
                                     )">
                            @endif
                        @else
                            <span class="bukti-empty" title="Tidak ada file invoice"
                                  style="border-color:#BFDBFE;">
                                <i class="ri-file-text-line" style="color:#93C5FD;"></i>
                            </span>
                        @endif
                    </td>

                    {{-- AKSI DROPDOWN --}}
                    <td class="center">
                        <div class="action-wrap">
                            <button class="btn-aksi-toggle"
                                    onclick="toggleDropdown(this)"
                                    title="Aksi">
                                <i class="ri-more-2-fill"></i>
                            </button>
                            <div class="dropdown-menu-aksi">
                                <a href="{{ route('pembelian.show', $item->id) }}"
                                   class="dropdown-item item-show">
                                    <i class="ri-eye-line"></i> Lihat Detail
                                </a>
                                <a href="{{ route('pembelian.edit', $item->id) }}"
                                   class="dropdown-item item-edit">
                                    <i class="ri-edit-line"></i> Edit Data
                                </a>
                                @if($item->status === 'buy_back')
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('pembelian.invoice', $item->id) }}"
                                   target="_blank"
                                   class="dropdown-item item-invoice">
                                    <i class="ri-printer-line"></i> Cetak Invoice
                                </a>
                                @endif
                                <div class="dropdown-divider"></div>
                                <button type="button"
                                        class="dropdown-item item-delete"
                                        onclick="closeAllDropdowns(); openDeleteModal(
                                            '{{ addslashes($item->nama_barang) }}',
                                            {{ $item->jumlah }},
                                            '{{ route('pembelian.destroy', $item->id) }}'
                                        )">
                                    <i class="ri-delete-bin-line"></i> Hapus Data
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13">
                        <div class="empty-state">
                            <i class="ri-shopping-cart-2-line"></i>
                            <h3>
                                @if($dateFrom || $dateTo)
                                    Tidak ada data pada rentang tanggal ini
                                @elseif($search)
                                    Tidak ditemukan
                                @else
                                    Belum ada data pembelian
                                @endif
                            </h3>
                            <p>
                                @if($dateFrom || $dateTo)
                                    Coba ubah rentang tanggal atau klik Reset.
                                @elseif($search)
                                    Coba kata kunci lain atau klik Reset.
                                @else
                                    Klik "Tambah Pembelian" untuk mencatat pembelian baru.
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>

            @if($pembelians->count() > 0)
            <tfoot>
                <tr class="tfoot-total">
                    <td colspan="8" class="right">
                        Total {{ ($dateFrom || $dateTo) ? 'periode ini' : ($search ? 'hasil filter' : 'keseluruhan') }}:
                    </td>
                    <td class="right total-value" style="font-size:14px;white-space:nowrap;">
                        Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}
                    </td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($pembelians->total() > 0)
    <div class="table-footer">
        <div class="pagination-meta">
            Menampilkan
            <strong>{{ $pembelians->firstItem() }} - {{ $pembelians->lastItem() }}</strong>
            dari <strong>{{ $pembelians->total() }}</strong> data
        </div>

        @if($pembelians->hasPages())
        <nav class="pagination-nav" aria-label="Pagination">
            @if($pembelians->onFirstPage())
                <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
            @else
                <a class="page-btn" href="{{ $pembelians->previousPageUrl() }}">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            @endif

            @php
                $current = $pembelians->currentPage();
                $last    = $pembelians->lastPage();
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
                    <span class="page-ellipsis">...</span>
                @elseif($pageItem == $current)
                    <span class="page-btn active">{{ $pageItem }}</span>
                @else
                    <a class="page-btn" href="{{ $pembelians->url($pageItem) }}">{{ $pageItem }}</a>
                @endif
            @endforeach

            @if($pembelians->hasMorePages())
                <a class="page-btn" href="{{ $pembelians->nextPageUrl() }}">
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


{{-- MODAL: KONFIRMASI HAPUS --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal" style="max-width:420px;">
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
                <h3>Hapus Data Pembelian?</h3>
                <p>
                    Kamu akan menghapus data pembelian:<br>
                    <strong id="deleteNamaBarang"></strong>
                </p>
                <div class="delete-impact-box">
                    <i class="ri-archive-line"></i>
                    <span>
                        Stok inventory akan berkurang
                        <strong id="deleteJumlah"></strong>.
                        Pastikan stok ini belum dipakai di transaksi lain.
                    </span>
                </div>
                <p style="margin-top:12px;color:#EF4444;font-weight:600;font-size:12.5px;">
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalHapus')">
                Batal
            </button>
            <form id="formDeleteSubmit" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- LIGHTBOX --}}
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="event.stopPropagation();closeLightbox()">
        <i class="ri-close-line"></i>
    </button>
    <img id="lightboxImg" src="" alt="Preview File" onclick="event.stopPropagation()">
    <div class="lightbox-caption" id="lightboxCaption"></div>
</div>

{{-- MODAL FILE VIEWER PDF --}}
<div class="modal-overlay" id="modalFileViewer">
    <div class="modal file-modal">
        <div class="modal-header">
            <span class="modal-title" id="fileViewerTitle">
                <i class="ri-file-line"></i> <span id="fileViewerTitleText">Lihat File</span>
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
/* ── Modal helpers ── */
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
        closeAllDropdowns();
        closeExportPanel();
        document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
    }
});

/* ── Dropdown Aksi (titik tiga) ── */
function toggleDropdown(btn) {
    const menu   = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');
    closeAllDropdowns();
    closeExportPanel();
    if (!isOpen) menu.classList.add('open');
}
function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu-aksi.open').forEach(m => m.classList.remove('open'));
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-wrap')) closeAllDropdowns();
    if (!e.target.closest('#exportWrap'))  closeExportPanel();
});

/* ── Export Panel ── */
function toggleExportPanel() {
    const panel  = document.getElementById('exportPanel');
    const arrow  = document.getElementById('exportArrow');
    const isOpen = panel.classList.contains('open');
    closeAllDropdowns();
    if (!isOpen) {
        panel.classList.add('open');
        arrow.style.transform = 'rotate(180deg)';
    } else {
        panel.classList.remove('open');
        arrow.style.transform = 'rotate(0deg)';
    }
}
function closeExportPanel() {
    const panel = document.getElementById('exportPanel');
    const arrow = document.getElementById('exportArrow');
    if (panel) panel.classList.remove('open');
    if (arrow) arrow.style.transform = 'rotate(0deg)';
}
function resetExportForm() {
    const form = document.getElementById('exportForm');
    form.querySelector('[name="date_from"]').value      = '';
    form.querySelector('[name="date_to"]').value        = '';
    form.querySelector('[name="filter"]').value         = 'semua';
    form.querySelector('[name="kondisi_barang"]').value = 'semua';
}

/* ── Delete Modal ── */
function openDeleteModal(namaBarang, jumlah, actionUrl) {
    document.getElementById('deleteNamaBarang').textContent = namaBarang;
    document.getElementById('deleteJumlah').textContent     = jumlah + ' unit';
    document.getElementById('formDeleteSubmit').action      = actionUrl;
    openModal('modalHapus');
}

/* ── Lightbox ── */
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

/* ── File Viewer Modal (PDF) ── */
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

/* ── Filter tanggal cepat ── */
function setDateRange(type) {
    const today = new Date();
    const fmt   = d => d.toISOString().split('T')[0];
    let from, to = fmt(today);

    if (type === 'today') {
        from = fmt(today);
    } else if (type === 'week') {
        const d = new Date(today); d.setDate(d.getDate() - 6);
        from = fmt(d);
    } else if (type === 'month') {
        from = fmt(new Date(today.getFullYear(), today.getMonth(), 1));
    } else if (type === 'year') {
        from = fmt(new Date(today.getFullYear(), 0, 1));
    }

    document.getElementById('dateFrom').value = from;
    document.getElementById('dateTo').value   = to;
    document.getElementById('dateFilterForm').submit();
}
</script>
@endpush