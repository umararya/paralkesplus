{{-- resources/views/admin/penyewaan/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Penyewaan')
@section('breadcrumb', 'Penyewaan')

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
    .per-page-select { height:36px; padding:0 30px 0 10px; border:1px solid var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-primary); font-size:13px; font-family:var(--font); outline:none; cursor:pointer; transition:border-color 0.2s, box-shadow 0.2s; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 8px center; }
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
    .btn-warning { background:#F59E0B; color:#fff; border:1px solid #F59E0B; }
    .btn-warning:hover { background:#D97706; border-color:#D97706; }
    .btn-success { background:#16A34A; color:#fff; border:1px solid #16A34A; }
    .btn-success:hover { background:#15803D; border-color:#15803D; }
    .btn-secondary { background:var(--bg-hover); color:var(--text-secondary); border:1px solid var(--border); }
    .btn-secondary:hover { background:var(--border); color:var(--text-primary); }
    .btn-monitoring { background:#7C3AED; color:#fff; border:1px solid #7C3AED; }
    .btn-monitoring:hover { background:#6D28D9; border-color:#6D28D9; }
    html.dark .btn-monitoring { background:#7C3AED; color:#fff; }
    .btn-export { background:#10B981; color:#fff; border:1px solid #10B981; }
    .btn-export:hover { background:#059669; border-color:#059669; }
    html.dark .btn-export { background:rgba(16,185,129,0.2); color:#34D399; border-color:rgba(16,185,129,0.3); }

    /* ── Status Filter Chips ── */
    .status-filter-bar { padding:10px 18px; border-bottom:1px solid var(--border); background:var(--bg-primary); display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .status-filter-label { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); white-space:nowrap; flex-shrink:0; display:flex; align-items:center; gap:5px; }
    .status-filter-label i { font-size:13px; color:var(--brand-500); }
    .status-chips { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
    .chip { display:inline-flex; align-items:center; gap:5px; height:28px; padding:0 12px; border-radius:99px; font-size:12px; font-weight:600; cursor:pointer; border:1.5px solid var(--border); background:var(--bg-card); color:var(--text-secondary); text-decoration:none; transition:all 0.18s; white-space:nowrap; font-family:var(--font); }
    .chip:hover { background:var(--bg-hover); color:var(--text-primary); }
    .chip .chip-count { background:var(--bg-hover); border-radius:99px; padding:0 6px; font-size:11px; font-weight:700; min-width:18px; text-align:center; transition:background 0.18s; }
    .chip.active-semua { background:var(--brand-500); color:#fff; border-color:var(--brand-500); }
    .chip.active-semua .chip-count { background:rgba(255,255,255,0.25); color:#fff; }
    .chip.active-berjalan { background:#F0FDF4; color:#16A34A; border-color:#86EFAC; }
    .chip.active-berjalan .chip-count { background:#DCFCE7; color:#16A34A; }
    html.dark .chip.active-berjalan { background:rgba(22,163,74,0.15); color:#4ADE80; border-color:rgba(22,163,74,0.35); }
    html.dark .chip.active-berjalan .chip-count { background:rgba(22,163,74,0.25); color:#4ADE80; }
    .chip.active-segera_konfirmasi { background:#FFFBEB; color:#B45309; border-color:#FCD34D; }
    .chip.active-segera_konfirmasi .chip-count { background:#FEF3C7; color:#B45309; }
    html.dark .chip.active-segera_konfirmasi { background:rgba(180,83,9,0.15); color:#FCD34D; border-color:rgba(180,83,9,0.35); }
    html.dark .chip.active-segera_konfirmasi .chip-count { background:rgba(180,83,9,0.25); color:#FCD34D; }
    .chip.active-selesai { background:#F0F9FF; color:#0369A1; border-color:#7DD3FC; }
    .chip.active-selesai .chip-count { background:#E0F2FE; color:#0369A1; }
    html.dark .chip.active-selesai { background:rgba(3,105,161,0.15); color:#38BDF8; border-color:rgba(3,105,161,0.35); }
    html.dark .chip.active-selesai .chip-count { background:rgba(3,105,161,0.25); color:#38BDF8; }
    .chip.active-dibatalkan { background:#FFF1F2; color:#BE123C; border-color:#FDA4AF; }
    .chip.active-dibatalkan .chip-count { background:#FFE4E6; color:#BE123C; }
    html.dark .chip.active-dibatalkan { background:rgba(190,18,60,0.15); color:#FB7185; border-color:rgba(190,18,60,0.35); }
    html.dark .chip.active-dibatalkan .chip-count { background:rgba(190,18,60,0.25); color:#FB7185; }

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
    .data-table td { padding:11px 14px; font-size:13px; color:var(--text-primary); border-bottom:1px solid var(--border); vertical-align:middle; }
    .data-table tbody tr:last-child td { border-bottom:none; }
    .data-table tbody tr { transition:background 0.15s; }
    .data-table tbody tr:hover td { background:var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align:center; }

    /* ── Row dibatalkan: visual redup ── */
    .data-table tbody tr.row-dibatalkan td { opacity:0.65; }
    .data-table tbody tr.row-dibatalkan:hover td { opacity:1; background:var(--bg-hover); }

    /* ── DROPDOWN AKSI ── */
    .action-wrap { position:relative; display:inline-block; }
    .btn-aksi-toggle { width:32px; height:32px; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; font-size:18px; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); transition:all 0.2s; }
    .btn-aksi-toggle:hover { background:var(--brand-500); color:#fff; border-color:var(--brand-500); }
    .dropdown-menu-aksi { display:none; position:absolute; right:0; top:calc(100% + 6px); background:var(--bg-card); border:1px solid var(--border); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.15); z-index:200; min-width:185px; padding:5px; }
    .dropdown-menu-aksi.open { display:block; animation:fadeDropdown 0.15s ease; }
    @keyframes fadeDropdown { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
    .dropdown-item { display:flex; align-items:center; gap:9px; padding:8px 12px; border-radius:7px; font-size:13px; font-weight:500; color:var(--text-primary); text-decoration:none; cursor:pointer; border:none; background:none; width:100%; font-family:var(--font); transition:background 0.15s; }
    .dropdown-item:hover { background:var(--bg-hover); }
    .dropdown-item i { font-size:16px; width:18px; text-align:center; }
    .dropdown-item.item-edit i       { color:var(--brand-500); }
    .dropdown-item.item-invoice i    { color:#16A34A; }
    .dropdown-item.item-perjanjian i { color:#7C3AED; }
    .dropdown-item.item-show i       { color:#F59E0B; }
    .dropdown-item.item-delete i     { color:#EF4444; }
    .dropdown-item.item-delete:hover { background:#FFF1F2; color:#DC2626; }
    html.dark .dropdown-item.item-delete:hover { background:rgba(225,29,72,0.1); color:#FB7185; }
    .dropdown-item.item-batal i      { color:#BE123C; }
    .dropdown-item.item-batal:hover  { background:#FFF1F2; color:#BE123C; }
    html.dark .dropdown-item.item-batal:hover { background:rgba(190,18,60,0.1); color:#FB7185; }
    .dropdown-item.item-restore i    { color:#B45309; }
    .dropdown-item.item-restore:hover { background:#FFFBEB; color:#B45309; }
    html.dark .dropdown-item.item-restore:hover { background:rgba(180,83,9,0.1); color:#FCD34D; }
    .dropdown-divider { height:1px; background:var(--border); margin:4px 0; }

    .table-footer { padding:12px 18px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
    .pagination-meta { font-size:12.5px; color:var(--text-muted); }
    .pagination-meta strong { color:var(--text-primary); }
    .pagination-nav { display:flex; align-items:center; gap:3px; }
    .page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 6px; border-radius:7px; font-size:13px; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); text-decoration:none; cursor:pointer; transition:all 0.18s; font-family:var(--font); }
    .page-btn:hover { background:var(--bg-hover); color:var(--text-primary); border-color:var(--text-muted); }
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

    /* ═══ MODAL BASE ═══ */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center; padding:16px; backdrop-filter:blur(2px); }
    .modal-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from{opacity:0}to{opacity:1} }
    .modal { background:var(--bg-card); border:1px solid var(--border); border-radius:16px; box-shadow:0 20px 60px rgba(0,0,0,0.2); width:100%; animation:slideUp 0.2s ease; }
    .modal-sm { max-width:420px; }
    .modal-md { max-width:600px; }
    .modal-lg { max-width:860px; }
    @keyframes slideUp { from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)} }
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

    /* ── Status badge ── */
    .status-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:99px; font-size:12px; font-weight:600; white-space:nowrap; }
    .status-berjalan    { background:#F0FDF4; color:#16A34A; }
    .status-konfirmasi  { background:#FFFBEB; color:#B45309; }
    .status-selesai     { background:#F0F9FF; color:#0369A1; }
    .status-dibatalkan  { background:#FFF1F2; color:#BE123C; border:1px solid #FECDD3; }
    html.dark .status-berjalan   { background:rgba(22,163,74,0.12); color:#4ADE80; }
    html.dark .status-konfirmasi { background:rgba(180,83,9,0.12); color:#FCD34D; }
    html.dark .status-selesai    { background:rgba(3,105,161,0.12); color:#38BDF8; }
    html.dark .status-dibatalkan { background:rgba(190,18,60,0.12); color:#FB7185; border-color:rgba(190,18,60,0.25); }

    /* ── File badge ── */
    .link-badge { display:inline-flex; align-items:center; gap:5px; background:var(--bg-hover); border:1px solid var(--border); border-radius:6px; padding:4px 10px; font-size:12px; font-weight:500; color:var(--text-secondary); text-decoration:none; cursor:pointer; transition:all 0.2s; white-space:nowrap; font-family:var(--font); }
    .link-badge:hover { color:var(--brand-500); background:var(--brand-50); border-color:var(--brand-200); }
    .link-badge i { font-size:13px; }
    html.dark .link-badge:hover { background:rgba(29,111,164,0.1); color:#60A5FA; border-color:rgba(29,111,164,0.3); }
    .no-file { color:var(--text-muted); font-style:italic; font-size:12.5px; }

    /* ── Modal Preview File ── */
    .file-preview-wrap { display:flex; flex-direction:column; align-items:center; gap:12px; }
    .file-preview-img  { max-width:100%; max-height:70vh; border-radius:8px; border:1px solid var(--border); object-fit:contain; }
    .file-preview-pdf  { width:100%; height:70vh; border:1px solid var(--border); border-radius:8px; }
    .file-preview-info { font-size:12px; color:var(--text-muted); display:flex; align-items:center; gap:5px; }

    .badge-mandiri { background:#F0FDF4; color:#16A34A; border-radius:6px; padding:2px 8px; font-size:12px; font-weight:600; }
    .badge-gosend  { background:#FFF7ED; color:#C2410C; border-radius:6px; padding:2px 8px; font-size:12px; font-weight:600; }
    .badge-rental  { background:#EFF6FF; color:#1D6FA4; border-radius:6px; padding:2px 8px; font-size:12px; font-weight:600; }
    html.dark .badge-mandiri { background:rgba(22,163,74,0.12); color:#4ADE80; }
    html.dark .badge-gosend  { background:rgba(194,65,12,0.12); color:#FB923C; }
    html.dark .badge-rental  { background:rgba(29,111,164,0.12); color:#38BDF8; }

    /* ── Produk item list ── */
    .produk-list { display:flex; flex-direction:column; gap:3px; min-width:160px; }
    .produk-list-item { font-size:12.5px; color:var(--text-primary); white-space:nowrap; display:flex; align-items:center; gap:5px; }
    .produk-list-item .qty-badge { display:inline-flex; align-items:center; background:var(--brand-50); color:var(--brand-500); border:1px solid var(--brand-100); border-radius:5px; padding:0px 6px; font-size:11.5px; font-weight:700; white-space:nowrap; flex-shrink:0; }
    html.dark .produk-list-item .qty-badge { background:rgba(29,111,164,0.12); color:#60A5FA; border-color:rgba(29,111,164,0.25); }
    .produk-list-fallback { font-size:13px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px; }

    /* ═══ MONITORING MODAL ═══ */
    .monitoring-table { width:100%; border-collapse:collapse; font-size:13px; }
    .monitoring-table th { padding:9px 12px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:var(--text-muted); background:var(--bg-primary); border-bottom:2px solid var(--border); white-space:nowrap; }
    .monitoring-table td { padding:10px 12px; border-bottom:1px solid var(--border); color:var(--text-primary); vertical-align:middle; }
    .monitoring-table tbody tr:last-child td { border-bottom:none; }
    .monitoring-table tbody tr:hover td { background:var(--bg-hover); }
    .sisa-hari-normal  { font-weight:700; color:#16A34A; }
    .sisa-hari-warning { font-weight:700; color:#B45309; }
    .sisa-hari-danger  { font-weight:700; color:#DC2626; }

    /* ═══ SELESAIKAN MODAL ═══ */
    .confirm-box { text-align:center; padding:10px 0 6px; }
    .confirm-box i { font-size:44px; color:#7C3AED; display:block; margin-bottom:10px; }
    .confirm-box h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:8px; }
    .confirm-box p { font-size:13px; color:var(--text-muted); line-height:1.6; }
    .confirm-box .durasi-info { display:inline-block; margin-top:8px; background:#FEF3C7; color:#92400E; border-radius:8px; padding:6px 16px; font-size:13px; font-weight:600; }
    html.dark .confirm-box .durasi-info { background:rgba(146,64,14,0.18); color:#FCD34D; }
    .action-buttons { display:flex; gap:8px; justify-content:center; flex-wrap:wrap; margin-top:14px; }
    .btn-full { width:100%; justify-content:center; height:40px; font-size:14px; }

    .konfirmasi-box { text-align:center; padding:8px 0; }
    .konfirmasi-box i { font-size:44px; color:#F59E0B; display:block; margin-bottom:10px; }
    .konfirmasi-box h3 { font-size:15px; font-weight:700; color:var(--text-primary); margin-bottom:6px; }
    .konfirmasi-box p { font-size:13px; color:var(--text-muted); line-height:1.6; }

    .monitoring-loading { text-align:center; padding:40px; color:var(--text-muted); font-size:14px; }
    .monitoring-loading i { font-size:32px; display:block; margin-bottom:8px; animation:spin 1s linear infinite; }
    @keyframes spin { from{transform:rotate(0deg)}to{transform:rotate(360deg)} }

    /* ═══ EXTEND MODAL ═══ */
    .extend-form-group { margin-bottom:14px; }
    .extend-form-group:last-child { margin-bottom:0; }
    .extend-form-group label { display:block; font-size:12.5px; font-weight:600; color:var(--text-secondary); margin-bottom:5px; }
    .extend-form-group input, .extend-form-group select { width:100%; height:40px; border:1px solid var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-primary); font-size:13.5px; padding:0 12px; outline:none; font-family:var(--font); transition:border-color 0.2s, box-shadow 0.2s; box-sizing:border-box; }
    .extend-form-group input:focus, .extend-form-group select:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.12); }
    .extend-form-group textarea { width:100%; height:76px; border:1px solid var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-primary); font-size:13px; padding:10px 12px; outline:none; resize:none; font-family:var(--font); transition:border-color 0.2s, box-shadow 0.2s; box-sizing:border-box; }
    .extend-form-group textarea:focus { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.12); }
    .extend-form-group .input-prefix-wrap { display:flex; align-items:center; border:1px solid var(--border); border-radius:8px; overflow:hidden; background:var(--bg-primary); transition:border-color 0.2s, box-shadow 0.2s; }
    .extend-form-group .input-prefix-wrap:focus-within { border-color:var(--brand-500); box-shadow:0 0 0 3px rgba(29,111,164,0.12); }
    .extend-form-group .input-prefix-wrap span { padding:0 10px; font-size:13px; font-weight:600; color:var(--text-muted); background:var(--bg-hover); border-right:1px solid var(--border); white-space:nowrap; height:40px; display:flex; align-items:center; }
    .extend-form-group .input-prefix-wrap input { border:none; box-shadow:none; border-radius:0; flex:1; height:40px; width:auto; }
    .extend-form-group .input-prefix-wrap input:focus { border:none; box-shadow:none; }
    .extend-upload-label { display:flex; align-items:center; justify-content:center; gap:6px; width:100%; height:40px; border:1.5px dashed var(--border); border-radius:8px; background:var(--bg-primary); color:var(--text-muted); font-size:13px; font-weight:500; cursor:pointer; transition:all 0.2s; font-family:var(--font); box-sizing:border-box; }
    .extend-upload-label:hover { border-color:var(--brand-500); color:var(--brand-500); background:var(--brand-50); }
    .extend-upload-label i { font-size:16px; }
    #extendBuktiName { font-size:11.5px; color:var(--text-muted); margin-top:4px; text-align:center; }
    .extend-info-bar { background:var(--bg-hover); border:1px solid var(--border); border-radius:8px; padding:9px 14px; margin-bottom:16px; font-size:12.5px; color:var(--text-secondary); display:flex; align-items:center; gap:7px; }
    .extend-info-bar i { color:#F59E0B; font-size:15px; flex-shrink:0; }

    /* ═══ MODAL SUKSES EXTEND ═══ */
    .extend-sukses-box { text-align:center; padding:8px 0 4px; }
    .extend-sukses-box .sukses-icon { width:60px; height:60px; border-radius:50%; background:#F0FDF4; border:2px solid #BBF7D0; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px; }
    .extend-sukses-box .sukses-icon i { font-size:30px; color:#16A34A; }
    .extend-sukses-box h3 { font-size:16px; font-weight:700; color:var(--text-primary); margin-bottom:6px; }
    .extend-sukses-box p { font-size:13px; color:var(--text-muted); line-height:1.6; }
    .extend-sukses-info { display:flex; flex-direction:column; gap:6px; background:var(--bg-hover); border:1px solid var(--border); border-radius:10px; padding:12px 16px; margin:14px 0; text-align:left; }
    .extend-sukses-info-row { display:flex; justify-content:space-between; align-items:center; font-size:13px; }
    .extend-sukses-info-row .label { color:var(--text-muted); }
    .extend-sukses-info-row .value { font-weight:700; color:var(--text-primary); }
    .extend-sukses-info-row .value.highlight { color:#F59E0B; }
    .extend-cetak-row { display:flex; gap:8px; margin-top:4px; }
    .extend-cetak-row a { flex:1; display:inline-flex; align-items:center; justify-content:center; gap:6px; height:40px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; transition:all 0.2s; }
    .btn-cetak-invoice { background:#F0FDF4; color:#16A34A; border:1.5px solid #BBF7D0; }
    .btn-cetak-invoice:hover { background:#DCFCE7; border-color:#86EFAC; }
    .btn-cetak-perjanjian { background:#F5F3FF; color:#7C3AED; border:1.5px solid #DDD6FE; }
    .btn-cetak-perjanjian:hover { background:#EDE9FE; border-color:#C4B5FD; }
    html.dark .btn-cetak-invoice { background:rgba(22,163,74,0.12); color:#4ADE80; border-color:rgba(22,163,74,0.25); }
    html.dark .btn-cetak-perjanjian { background:rgba(124,58,237,0.12); color:#A78BFA; border-color:rgba(124,58,237,0.25); }

    /* ── Modal Batalkan/Restore ── */
    .batal-icon-wrap { width:48px; height:48px; border-radius:50%; background:#FFF1F2; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .restore-icon-wrap { width:48px; height:48px; border-radius:50%; background:#FFFBEB; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .form-group-modal { display:flex; flex-direction:column; gap:6px; margin-top:14px; }
    .form-label-modal { font-size:13px; font-weight:600; color:var(--text-primary); }
    .form-label-modal .req { color:#EF4444; margin-left:2px; }
    .textarea-modal { width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:13px; background:var(--bg-primary); color:var(--text-primary); outline:none; resize:vertical; min-height:80px; font-family:var(--font); box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s; }
    .textarea-modal:focus { border-color:#BE123C; box-shadow:0 0 0 3px rgba(190,18,60,0.08); }
    .field-error { font-size:12px; color:#EF4444; display:none; align-items:center; gap:4px; margin-top:3px; }
    .field-error.show { display:flex; }
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
            <i class="ri-store-2-line"></i> Penyewaan
        </h1>
        <p class="page-subtitle">Kelola data penyewaan alat kesehatan</p>
    </div>
</div>

{{-- Table Card --}}
<div class="table-card">

    {{-- ══ TOOLBAR ══ --}}
    <div class="table-toolbar">
        <div class="toolbar-left">

            {{-- Per Page --}}
            <form method="GET" action="{{ route('penyewaan.index') }}" id="perPageForm">
                <input type="hidden" name="search"    value="{{ $search }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to"   value="{{ $dateTo }}">
                <input type="hidden" name="status"    value="{{ $status }}">
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
            <form method="GET" action="{{ route('penyewaan.index') }}" class="search-form" id="searchForm">
                <input type="hidden" name="per_page"  value="{{ $perPage }}">
                <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                <input type="hidden" name="date_to"   value="{{ $dateTo }}">
                <input type="hidden" name="status"    value="{{ $status }}">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama, telepon, produk, status..."
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                @if($search)
                <a href="{{ route('penyewaan.index', ['per_page' => $perPage, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'status' => $status]) }}"
                   class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                @endif
            </form>
        </div>

        {{-- TOOLBAR RIGHT --}}
        <div class="toolbar-right">
            <button type="button" class="btn btn-export" id="btnToggleExport"
                    onclick="toggleExportPanel()" title="Export ke Excel">
                <i class="ri-file-excel-2-line"></i> Export XLSX
                <i class="ri-arrow-down-s-line" id="exportArrow" style="font-size:14px;transition:transform 0.2s;"></i>
            </button>
            <button type="button" class="btn btn-monitoring" onclick="openMonitoring()">
                <i class="ri-radar-line"></i> Monitoring
            </button>
            <a href="{{ route('penyewaan.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Input Data
            </a>
        </div>
    </div>

    {{-- ══ STATUS FILTER CHIPS ══ --}}
    <div class="status-filter-bar">
        <span class="status-filter-label">
            <i class="ri-filter-3-line"></i> Status
        </span>
        <div class="status-chips">

            <a href="{{ route('penyewaan.index', array_merge(request()->except(['status','page']), ['status' => ''])) }}"
               class="chip {{ $status === '' ? 'active-semua' : '' }}">
                <i class="ri-list-unordered"></i>
                Semua
                <span class="chip-count">{{ $statusCounts['semua'] }}</span>
            </a>

            <a href="{{ route('penyewaan.index', array_merge(request()->except(['status','page']), ['status' => 'berjalan'])) }}"
               class="chip {{ $status === 'berjalan' ? 'active-berjalan' : '' }}">
                <i class="ri-play-circle-line"></i>
                Berjalan
                <span class="chip-count">{{ $statusCounts['berjalan'] }}</span>
            </a>

            <a href="{{ route('penyewaan.index', array_merge(request()->except(['status','page']), ['status' => 'segera_konfirmasi'])) }}"
               class="chip {{ $status === 'segera_konfirmasi' ? 'active-segera_konfirmasi' : '' }}">
                <i class="ri-alarm-warning-line"></i>
                Segera Konfirmasi
                <span class="chip-count">{{ $statusCounts['segera_konfirmasi'] }}</span>
            </a>

            <a href="{{ route('penyewaan.index', array_merge(request()->except(['status','page']), ['status' => 'selesai'])) }}"
               class="chip {{ $status === 'selesai' ? 'active-selesai' : '' }}">
                <i class="ri-checkbox-circle-line"></i>
                Selesai
                <span class="chip-count">{{ $statusCounts['selesai'] }}</span>
            </a>

            <a href="{{ route('penyewaan.index', array_merge(request()->except(['status','page']), ['status' => 'dibatalkan'])) }}"
               class="chip {{ $status === 'dibatalkan' ? 'active-dibatalkan' : '' }}">
                <i class="ri-close-circle-line"></i>
                Dibatalkan
                <span class="chip-count">{{ $statusCounts['dibatalkan'] }}</span>
            </a>

        </div>
    </div>

    {{-- ══ DATE FILTER BAR ══ --}}
    <div class="date-filter-bar">
        <span class="date-filter-label">
            <i class="ri-calendar-2-line"></i> Filter Tanggal
        </span>

        <form method="GET" action="{{ route('penyewaan.index') }}" id="dateFilterForm"
              style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <input type="hidden" name="search"   value="{{ $search }}">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
            <input type="hidden" name="status"   value="{{ $status }}">

            <div class="date-inputs-wrap">
                <input type="date" name="date_from" id="inputDateFrom"
                       class="date-input {{ $dateFrom ? 'active' : '' }}"
                       value="{{ $dateFrom }}"
                       onchange="document.getElementById('dateFilterForm').submit()">
                <span class="date-sep">—</span>
                <input type="date" name="date_to" id="inputDateTo"
                       class="date-input {{ $dateTo ? 'active' : '' }}"
                       value="{{ $dateTo }}"
                       onchange="document.getElementById('dateFilterForm').submit()">
            </div>

            @if($dateFrom || $dateTo)
            <span class="date-filter-active-badge">
                <i class="ri-filter-fill" style="font-size:10px;"></i>
                Aktif{{ $dateFrom && $dateTo ? ': ' . \Carbon\Carbon::parse($dateFrom)->format('d M') . ' – ' . \Carbon\Carbon::parse($dateTo)->format('d M Y') : '' }}
            </span>
            <a href="{{ route('penyewaan.index', ['search' => $search, 'per_page' => $perPage, 'status' => $status]) }}"
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
        <form method="GET" action="{{ route('penyewaan.export') }}" id="formExport">
            <input type="hidden" name="search" value="{{ $search }}">
            <div class="export-filter-row">

                <div class="export-filter-group">
                    <label class="export-filter-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="export-filter-input"
                           value="{{ $dateFrom }}" style="width:150px;">
                </div>

                <div class="export-filter-group">
                    <label class="export-filter-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="export-filter-input"
                           value="{{ $dateTo }}" style="width:150px;">
                </div>

                <div class="export-filter-group">
                    <label class="export-filter-label">Status</label>
                    <select name="status" class="export-filter-input" style="width:180px;">
                        <option value="semua">Semua Status</option>
                        <option value="berjalan"          {{ $status === 'berjalan'          ? 'selected' : '' }}>Berjalan</option>
                        <option value="segera_konfirmasi" {{ $status === 'segera_konfirmasi' ? 'selected' : '' }}>Segera Konfirmasi</option>
                        <option value="selesai"           {{ $status === 'selesai'           ? 'selected' : '' }}>Selesai</option>
                        <option value="dibatalkan"        {{ $status === 'dibatalkan'        ? 'selected' : '' }}>Dibatalkan</option>
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
                Kosongkan tanggal untuk export <strong>semua data</strong> penyewaan.
            </div>
        </form>
    </div>

    {{-- ══ INFO BAR ══ --}}
    <div class="info-bar">
        <div class="info-bar-text">
            @if($search || $dateFrom || $dateTo || $status)
                <i class="ri-filter-3-line"></i>
                @if($search)
                    Pencarian: <strong>"{{ $search }}"</strong>
                @endif
                @if($status)
                    @if($search) &nbsp;·&nbsp; @endif
                    Status: <strong>{{ ['berjalan'=>'Berjalan','segera_konfirmasi'=>'Segera Konfirmasi','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'][$status] ?? $status }}</strong>
                @endif
                @if($dateFrom || $dateTo)
                    @if($search || $status) &nbsp;·&nbsp; @endif
                    <i class="ri-calendar-line" style="font-size:12px;"></i>
                    @if($dateFrom && $dateTo)
                        {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} — {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                    @elseif($dateFrom)
                        Dari {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
                    @else
                        S/d {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
                    @endif
                @endif
                &nbsp;<span class="badge-count">{{ $penyewaans->total() }} data</span>
            @else
                <i class="ri-store-2-line"></i>
                Total <span class="badge-count">{{ $penyewaans->total() }} data</span>
            @endif
        </div>
        @if($penyewaans->total() > 0)
        <div class="info-bar-text">
            Halaman <strong>{{ $penyewaans->currentPage() }}</strong> dari <strong>{{ $penyewaans->lastPage() }}</strong>
        </div>
        @endif
    </div>

    {{-- ══ TABLE ══ --}}
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:42px;">#</th>
                    <th>Nama</th>
                    <th>No. Telepon/HP</th>
                    <th>Produk Alat Kesehatan</th>
                    <th class="center">Durasi (Hari)</th>
                    <th class="center">Pengiriman</th>
                    <th>Biaya Ongkir</th>
                    <th>Alamat Penyewa</th>
                    <th>Metode Pembayaran</th>
                    <th class="center">Bukti Pembayaran</th>
                    <th class="center">Foto KTP/SIM</th>
                    <th class="center">Status</th>
                    <th>Keterangan</th>
                    <th class="center" style="width:60px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penyewaans as $item)
                <tr class="{{ $item->status === 'dibatalkan' ? 'row-dibatalkan' : '' }}">
                    <td style="color:var(--text-muted);font-size:12px;font-weight:500;">
                        {{ $penyewaans->firstItem() + $loop->index }}
                    </td>
                    <td style="font-weight:600; white-space:nowrap;">
                        @if($search)
                            {!! preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                '<mark style="background:#FEF08A;border-radius:3px;padding:0 2px;">$1</mark>',
                                e($item->nama_penyewa)) !!}
                        @else
                            {{ $item->nama_penyewa }}
                        @endif
                    </td>
                    <td style="white-space:nowrap;">
                        <a href="tel:{{ $item->nomor_telepon }}"
                           style="color:var(--text-primary); text-decoration:none; display:inline-flex; align-items:center; gap:5px;">
                            <i class="ri-phone-line" style="color:var(--brand-500); font-size:13px;"></i>
                            {{ $item->nomor_telepon }}
                        </a>
                    </td>

                    {{-- ══ PRODUK ══ --}}
                    <td>
                        @php $details = $item->details; @endphp
                        @if($details->isNotEmpty())
                            <div class="produk-list">
                                @foreach($details as $d)
                                <div class="produk-list-item">
                                    <span>{{ $d->nama_alat }}</span>
                                    <span class="qty-badge">× {{ $d->qty }}</span>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <span class="produk-list-fallback" title="{{ $item->nama_alat ?? '—' }}">
                                {{ $item->nama_alat ?? '—' }}
                            </span>
                        @endif
                    </td>

                    <td class="center">
                        <span style="font-weight:600;">{{ $item->durasi_hari }}</span>
                        <span style="font-size:11px; color:var(--text-muted);"> hari</span>
                    </td>
                    <td class="center">
                        @php
                            $badgeClass = match($item->pengiriman) {
                                'mandiri'               => 'badge-mandiri',
                                'Gosend / GrabExpress'  => 'badge-gosend',
                                'Rental Mobil Paralkes' => 'badge-rental',
                                default                 => 'badge-mandiri',
                            };
                        @endphp
                        <span class="{{ $badgeClass }}">{{ $item->pengiriman_label }}</span>
                    </td>
                    <td style="white-space:nowrap;">
                        {{ $item->biaya_ongkir > 0 ? $item->biaya_ongkir_formatted : '—' }}
                    </td>
                    <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                        title="{{ $item->alamat_penyewa }}">
                        {{ $item->alamat_penyewa }}
                    </td>
                    <td style="white-space:nowrap;">
                        <span style="display:inline-flex; align-items:center; gap:5px;">
                            <i class="ri-bank-card-line" style="color:var(--brand-500); font-size:13px;"></i>
                            {{ $item->metode_pembayaran }}
                        </span>
                    </td>

                    {{-- ══ BUKTI PEMBAYARAN ══ --}}
                    <td class="center">
                        @if($item->bukti_pembayaran)
                            @php
                                $ext   = strtolower(pathinfo($item->bukti_pembayaran, PATHINFO_EXTENSION));
                                $isPdf = $ext === 'pdf';
                            @endphp
                            <button type="button" class="link-badge"
                                    onclick="previewFile(
                                        '{{ asset('storage/' . $item->bukti_pembayaran) }}',
                                        '{{ $isPdf ? 'pdf' : 'image' }}',
                                        'Bukti Pembayaran — {{ addslashes($item->nama_penyewa) }}'
                                    )">
                                <i class="{{ $isPdf ? 'ri-file-pdf-line' : 'ri-image-line' }}" style="{{ $isPdf ? 'color:#EF4444;' : '' }}"></i>
                                Lihat
                            </button>
                        @else
                            <span class="no-file">—</span>
                        @endif
                    </td>

                    {{-- ══ FOTO KTP/SIM ══ --}}
                    <td class="center">
                        @if($item->foto_ktp_sim)
                            @php
                                $ktpExt   = strtolower(pathinfo($item->foto_ktp_sim, PATHINFO_EXTENSION));
                                $ktpIsPdf = $ktpExt === 'pdf';
                            @endphp
                            <button type="button" class="link-badge"
                                    onclick="previewFile(
                                        '{{ asset('storage/' . $item->foto_ktp_sim) }}',
                                        '{{ $ktpIsPdf ? 'pdf' : 'image' }}',
                                        'Foto KTP/SIM — {{ addslashes($item->nama_penyewa) }}'
                                    )">
                                <i class="{{ $ktpIsPdf ? 'ri-file-pdf-line' : 'ri-id-card-line' }}" style="{{ $ktpIsPdf ? 'color:#EF4444;' : '' }}"></i>
                                Lihat
                            </button>
                        @else
                            <span class="no-file">—</span>
                        @endif
                    </td>

                    {{-- ══ STATUS ══ --}}
                    <td class="center">
                        @if($item->status === 'dibatalkan')
                            <span class="status-badge status-dibatalkan">
                                <i class="ri-close-circle-line"></i> Dibatalkan
                            </span>
                        @else
                            <span class="status-badge {{ $item->status_class }}">
                                {{ $item->status_label }}
                            </span>
                        @endif
                    </td>

                    <td style="max-width:140px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
                               color:var(--text-muted); font-size:12.5px;"
                        title="{{ $item->keterangan }}">
                        {{ $item->keterangan ?: '—' }}
                    </td>

                    {{-- ══ AKSI DROPDOWN ══ --}}
                    <td class="center">
                        <div class="action-wrap">
                            <button class="btn-aksi-toggle"
                                    onclick="toggleDropdown(this)"
                                    title="Aksi">
                                <i class="ri-more-2-fill"></i>
                            </button>
                            <div class="dropdown-menu-aksi">
                                <a href="{{ route('penyewaan.show', $item->id) }}"
                                   class="dropdown-item item-show">
                                    <i class="ri-eye-line"></i> Detail & Extend
                                </a>
                                <a href="{{ route('penyewaan.edit', $item->id) }}"
                                   class="dropdown-item item-edit">
                                    <i class="ri-edit-line"></i> Edit Data
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('penyewaan.invoice', $item->id) }}"
                                   target="_blank"
                                   class="dropdown-item item-invoice">
                                    <i class="ri-receipt-line"></i> Cetak Invoice
                                </a>
                                <a href="{{ route('penyewaan.perjanjian', $item->id) }}"
                                   target="_blank"
                                   class="dropdown-item item-perjanjian">
                                    <i class="ri-file-text-line"></i> Cetak Perjanjian
                                </a>
                                <div class="dropdown-divider"></div>

                                {{-- ── Batalkan / Pulihkan ── --}}
                                @if($item->status !== 'dibatalkan')
                                    <button type="button"
                                            class="dropdown-item item-batal"
                                            onclick="closeAllDropdowns(); openBatalModal({{ $item->id }}, '{{ addslashes($item->nama_penyewa) }}')">
                                        <i class="ri-forbid-line"></i> Batalkan
                                    </button>
                                @else
                                    <button type="button"
                                            class="dropdown-item item-restore"
                                            onclick="closeAllDropdowns(); openRestoreModal({{ $item->id }}, '{{ addslashes($item->nama_penyewa) }}')">
                                        <i class="ri-refresh-line"></i> Pulihkan
                                    </button>
                                @endif

                                <button type="button"
                                        class="dropdown-item item-delete"
                                        onclick="closeAllDropdowns(); openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_penyewa) }}')">
                                    <i class="ri-delete-bin-line"></i> Hapus Data
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14">
                        <div class="empty-state">
                            <i class="ri-store-2-line"></i>
                            <h3>
                                @if($search || $dateFrom || $dateTo || $status)
                                    Tidak ada data yang cocok
                                @else
                                    Belum ada data penyewaan
                                @endif
                            </h3>
                            <p>
                                @if($search || $dateFrom || $dateTo || $status)
                                    Coba ubah kata kunci, filter tanggal, atau filter status.
                                @else
                                    Klik "Input Data" untuk mencatat penyewaan baru.
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ══ PAGINATION ══ --}}
    @if($penyewaans->total() > 0)
    <div class="table-footer">
        <div class="pagination-meta">
            Menampilkan
            <strong>{{ $penyewaans->firstItem() }} – {{ $penyewaans->lastItem() }}</strong>
            dari <strong>{{ $penyewaans->total() }}</strong> data
        </div>
        @if($penyewaans->hasPages())
        <nav class="pagination-nav">
            @if($penyewaans->onFirstPage())
                <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
            @else
                <a class="page-btn" href="{{ $penyewaans->previousPageUrl() }}"><i class="ri-arrow-left-s-line"></i></a>
            @endif
            @php
                $current = $penyewaans->currentPage();
                $last    = $penyewaans->lastPage();
                $pages   = [];
                for ($p = 1; $p <= $last; $p++) {
                    if ($p === 1 || $p === $last || ($p >= $current - 2 && $p <= $current + 2)) {
                        $pages[] = $p;
                    }
                }
                $rendered = []; $prev = null;
                foreach ($pages as $p) {
                    if ($prev !== null && $p - $prev > 1) $rendered[] = '...';
                    $rendered[] = $p;
                    $prev = $p;
                }
            @endphp
            @foreach($rendered as $rItem)
                @if($rItem === '...')
                    <span class="page-ellipsis">…</span>
                @elseif($rItem == $current)
                    <span class="page-btn active">{{ $rItem }}</span>
                @else
                    <a class="page-btn" href="{{ $penyewaans->url($rItem) }}">{{ $rItem }}</a>
                @endif
            @endforeach
            @if($penyewaans->hasMorePages())
                <a class="page-btn" href="{{ $penyewaans->nextPageUrl() }}"><i class="ri-arrow-right-s-line"></i></a>
            @else
                <span class="page-btn disabled"><i class="ri-arrow-right-s-line"></i></span>
            @endif
        </nav>
        @endif
    </div>
    @endif

</div>{{-- /table-card --}}


{{-- ════ MODAL PREVIEW FILE ════ --}}
<div class="modal-overlay" id="modalPreviewFile">
    <div class="modal modal-md">
        <div class="modal-header">
            <span class="modal-title" id="previewModalTitle">
                <i class="ri-file-line"></i> Preview File
            </span>
            <div style="display:flex;align-items:center;gap:8px;">
                <a id="previewDownloadBtn" href="#" target="_blank" download
                   class="btn btn-ghost" style="height:30px;padding:0 12px;font-size:12px;">
                    <i class="ri-download-2-line"></i> Buka di Tab Baru
                </a>
                <button class="modal-close" onclick="closePreviewFile()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </div>
        <div class="modal-body" style="padding:16px;">
            <div class="file-preview-wrap" id="previewContent"></div>
        </div>
    </div>
</div>

{{-- ════ MODAL HAPUS ════ --}}
<div class="modal-overlay" id="modalHapus">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-delete-bin-line" style="color:#EF4444;"></i> Hapus Data
            </span>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="ri-error-warning-line"></i>
                <h3>Yakin ingin menghapus?</h3>
                <p>Data penyewaan atas nama <strong id="modal-nama">-</strong> akan dihapus permanen beserta file KTP/SIM-nya dan tidak dapat dikembalikan.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-reset" onclick="closeDeleteModal()">
                <i class="ri-close-line"></i> Batal
            </button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ════ MODAL BATALKAN ════ --}}
<div class="modal-overlay" id="modalBatalkan">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-forbid-line" style="color:#BE123C;"></i> Batalkan Penyewaan
            </span>
            <button class="modal-close" onclick="closeBatalModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:4px;">
                <div class="batal-icon-wrap">
                    <i class="ri-forbid-line" style="font-size:22px;color:#BE123C;"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:3px;">
                        Batalkan penyewaan ini?
                    </div>
                    <div style="font-size:13px;color:var(--text-muted);">
                        a.n. <span id="batal-nama" style="font-weight:600;color:var(--text-primary);"></span>
                    </div>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin:10px 0 0;">
                Status akan berubah ke <strong style="color:#BE123C;">Dibatalkan</strong>.
                Stok alat akan dikembalikan otomatis. Data <strong>tidak dihapus</strong> dan masih bisa dipulihkan kapan saja.
            </p>
            <div class="form-group-modal">
                <label class="form-label-modal">Alasan Pembatalan <span class="req">*</span></label>
                <textarea id="inputAlasanBatal"
                          class="textarea-modal"
                          placeholder="Contoh: Penyewa membatalkan permintaan karena kebutuhan sudah tidak ada..."></textarea>
                <div class="field-error" id="alasanBatalError">
                    <i class="ri-error-warning-line"></i> Alasan pembatalan wajib diisi.
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-reset" onclick="closeBatalModal()">
                <i class="ri-close-line"></i> Tidak, Kembali
            </button>
            <button type="button" class="btn btn-danger" id="btnSubmitBatal" onclick="submitBatal()">
                <i class="ri-forbid-line"></i> Ya, Batalkan
            </button>
        </div>
    </div>
</div>

{{-- ════ MODAL PULIHKAN ════ --}}
<div class="modal-overlay" id="modalRestore">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-refresh-line" style="color:#B45309;"></i> Pulihkan Penyewaan
            </span>
            <button class="modal-close" onclick="closeRestoreModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:4px;">
                <div class="restore-icon-wrap">
                    <i class="ri-refresh-line" style="font-size:22px;color:#B45309;"></i>
                </div>
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:700;color:var(--text-primary);margin-bottom:3px;">
                        Pulihkan penyewaan ini?
                    </div>
                    <div style="font-size:13px;color:var(--text-muted);">
                        a.n. <span id="restore-nama" style="font-weight:600;color:var(--text-primary);"></span>
                    </div>
                </div>
            </div>
            <p style="font-size:13px;color:var(--text-muted);line-height:1.6;margin:10px 0 0;">
                Status akan dikembalikan ke <strong style="color:#16A34A;">Berjalan</strong>
                dan stok alat akan dikurangi kembali sesuai jumlah penyewaan.
                Pastikan stok alat masih mencukupi sebelum memulihkan.
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-reset" onclick="closeRestoreModal()">
                <i class="ri-close-line"></i> Tidak, Kembali
            </button>
            <button type="button" class="btn btn-warning" id="btnSubmitRestore" onclick="submitRestore()">
                <i class="ri-refresh-line"></i> Ya, Pulihkan
            </button>
        </div>
    </div>
</div>

{{-- ════ MODAL MONITORING ════ --}}
<div class="modal-overlay" id="modalMonitoring">
    <div class="modal modal-lg">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-radar-line" style="color:#7C3AED;"></i> Monitoring Penyewaan Aktif
            </span>
            <button class="modal-close" onclick="closeMonitoring()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body" style="padding:0;">
            <div id="monitoringContent">
                <div class="monitoring-loading">
                    <i class="ri-loader-4-line"></i>
                    Memuat data...
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════ MODAL SELESAIKAN — Kondisi 1 ════ --}}
<div class="modal-overlay" id="modalSelesaikanNormal">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-checkbox-circle-line" style="color:#16A34A;"></i> Selesaikan Penyewaan
            </span>
            <button class="modal-close" onclick="closeSelesaikanNormal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="confirm-box">
                <i class="ri-checkbox-circle-line" style="color:#16A34A;"></i>
                <h3>Apakah anda ingin menyelesaikan penyewaan?</h3>
                <p>
                    <span class="durasi-info">Durasi penyewaan masih <span id="sisaHariNormal">0</span> hari lagi</span>
                </p>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:center;">
            <button class="btn btn-reset" onclick="closeSelesaikanNormal()">
                <i class="ri-close-line"></i> Batal
            </button>
            <button class="btn btn-success" onclick="doSelesaikan('selesai_sekarang')">
                <i class="ri-checkbox-circle-line"></i> Selesaikan Penyewaan
            </button>
        </div>
    </div>
</div>

{{-- ════ MODAL SELESAIKAN — Kondisi 2 ════ --}}
<div class="modal-overlay" id="modalKonfirmasiDulu">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-error-warning-line" style="color:#F59E0B;"></i> Perlu Konfirmasi
            </span>
            <button class="modal-close" onclick="closeKonfirmasiDulu()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="konfirmasi-box">
                <i class="ri-error-warning-line"></i>
                <h3>Diharap melakukan konfirmasi ke customer terlebih dahulu!</h3>
                <p>Hubungi customer dan tanyakan kelanjutan penyewaan sebelum mengambil tindakan.</p>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:center; gap:10px;">
            <button class="btn btn-success" onclick="sudahKonfirmasi()">
                <i class="ri-check-line"></i> Sudah Konfirmasi
            </button>
            <button class="btn btn-reset" onclick="closeKonfirmasiDulu()">
                <i class="ri-close-line"></i> Belum Konfirmasi
            </button>
        </div>
    </div>
</div>

{{-- ════ MODAL PILIH ACTION ════ --}}
<div class="modal-overlay" id="modalPilihAction">
    <div class="modal modal-sm">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-settings-3-line" style="color:#7C3AED;"></i> Pilih Action
            </span>
            <button class="modal-close" onclick="closePilihAction()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="confirm-box">
                <i class="ri-settings-3-line" style="color:#7C3AED; font-size:40px;"></i>
                <h3 style="margin-bottom:4px;">Pilih action berdasarkan hasil konfirmasi customer</h3>
            </div>
            <div class="action-buttons" style="flex-direction:column; margin-top:12px;">
                <button class="btn btn-danger btn-full" onclick="doSelesaikan('selesai_sekarang')">
                    <i class="ri-checkbox-circle-line"></i> Selesai Sekarang
                </button>
                <button class="btn btn-primary btn-full" onclick="doSelesaikan('sesuai_deadline')">
                    <i class="ri-calendar-check-line"></i> Sesuai Deadline
                </button>
                <button class="btn btn-warning btn-full" onclick="openExtend()">
                    <i class="ri-calendar-2-line"></i> Extend
                </button>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-reset" onclick="closePilihAction()">
                <i class="ri-close-line"></i> Batal
            </button>
        </div>
    </div>
</div>

{{-- ════ MODAL EXTEND ════ --}}
<div class="modal-overlay" id="modalExtend">
    <div class="modal modal-sm" style="max-width:500px;">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-calendar-2-line" style="color:#F59E0B;"></i> Extend Deadline
            </span>
            <button class="modal-close" onclick="closeExtend()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body" style="padding:18px 22px;">
            <div class="extend-info-bar" id="extendInfoBar">
                <i class="ri-calendar-event-line"></i>
                <span id="extendInfoText">Deadline awal: -</span>
            </div>
            <div class="extend-form-group">
                <label><i class="ri-calendar-2-line"></i> Tanggal Extend Baru <span style="color:#EF4444;">*</span></label>
                <input type="date" id="extendTanggal" min="">
            </div>
            <div class="extend-form-group">
                <label><i class="ri-money-dollar-circle-line"></i> Harga Extend <span style="color:#EF4444;">*</span></label>
                <div class="input-prefix-wrap">
                    <span>Rp</span>
                    <input type="number" id="extendHarga" placeholder="0" min="0" step="1000">
                </div>
            </div>
            <div class="extend-form-group">
                <label><i class="ri-bank-card-line"></i> Metode Pembayaran <span style="color:#EF4444;">*</span></label>
                <select id="extendMetodeBayar">
                    <option value="">-- Pilih Metode --</option>
                    <option value="Tunai">Tunai</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="QRIS">QRIS</option>
                </select>
            </div>
            <div class="extend-form-group">
                <label><i class="ri-image-line"></i> Bukti Transfer <span style="color:var(--text-muted); font-weight:400;">(opsional)</span></label>
                <label class="extend-upload-label" for="extendBuktiInput">
                    <i class="ri-upload-cloud-line"></i>
                    <span>Pilih File (JPG/PNG/PDF, maks 5MB)</span>
                </label>
                <input type="file" id="extendBuktiInput" accept=".jpg,.jpeg,.png,.pdf"
                       style="display:none;" onchange="onExtendFileChange(this)">
                <div id="extendBuktiName"></div>
            </div>
            <div class="extend-form-group">
                <label><i class="ri-sticky-note-line"></i> Catatan <span style="color:var(--text-muted); font-weight:400;">(opsional)</span></label>
                <textarea id="extendCatatan" placeholder="Catatan tambahan..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-reset" onclick="closeExtend()">
                <i class="ri-close-line"></i> Batal
            </button>
            <button class="btn btn-warning" id="btnDoExtend" onclick="doExtend()">
                <i class="ri-calendar-2-line"></i> Simpan Extend
            </button>
        </div>
    </div>
</div>

{{-- ════ MODAL SUKSES EXTEND ════ --}}
<div class="modal-overlay" id="modalSuksesExtend">
    <div class="modal modal-sm" style="max-width:460px;">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-checkbox-circle-line" style="color:#16A34A;"></i> Extend Berhasil
            </span>
            <button class="modal-close" onclick="closeSuksesExtend()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body" style="padding:20px 22px;">
            <div class="extend-sukses-box">
                <div class="sukses-icon">
                    <i class="ri-checkbox-circle-fill"></i>
                </div>
                <h3>Perpanjangan Berhasil Disimpan!</h3>
                <p>Data perpanjangan telah tersimpan dan deadline penyewaan telah diperbarui.</p>
            </div>
            <div class="extend-sukses-info">
                <div class="extend-sukses-info-row">
                    <span class="label">Deadline Baru</span>
                    <span class="value highlight" id="suksesExtendTglBaru">—</span>
                </div>
                <div class="extend-sukses-info-row">
                    <span class="label">Tambah Durasi</span>
                    <span class="value" id="suksesExtendTambahHari">—</span>
                </div>
            </div>
            <p style="font-size:12px; color:var(--text-muted); text-align:center; margin-bottom:8px;">
                Cetak dokumen perpanjangan:
            </p>
            <div class="extend-cetak-row">
                <a id="linkCetakInvoiceExtend" href="#" target="_blank" class="btn-cetak-invoice">
                    <i class="ri-receipt-line"></i> Invoice Extend
                </a>
                <a id="linkCetakPerjanjianExtend" href="#" target="_blank" class="btn-cetak-perjanjian">
                    <i class="ri-file-text-line"></i> Perjanjian Extend
                </a>
            </div>
        </div>
        <div class="modal-footer" style="justify-content:center;">
            <button class="btn btn-ghost" onclick="closeSuksesExtend()">
                <i class="ri-close-line"></i> Tutup
            </button>
            <button class="btn btn-primary" onclick="closeSuksesExtend(); location.reload();">
                <i class="ri-refresh-line"></i> Refresh Halaman
            </button>
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
        const w = new Date(today); w.setDate(today.getDate() - 6); from = ymd(w);
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
    const panel  = document.getElementById('exportPanel');
    const arrow  = document.getElementById('exportArrow');
    const isOpen = panel.classList.contains('open');
    panel.classList.toggle('open');
    arrow.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}
function resetExportFilter() {
    const form = document.getElementById('formExport');
    form.querySelector('[name="date_from"]').value = '';
    form.querySelector('[name="date_to"]').value   = '';
    form.querySelector('[name="status"]').value    = 'semua';
}

// ── Dropdown Aksi ──
function toggleDropdown(btn) {
    const menu   = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');
    closeAllDropdowns();
    if (!isOpen) menu.classList.add('open');
}
function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu-aksi.open').forEach(m => m.classList.remove('open'));
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.action-wrap')) closeAllDropdowns();
    const panel  = document.getElementById('exportPanel');
    const btnExp = document.getElementById('btnToggleExport');
    if (panel && panel.classList.contains('open') &&
        !panel.contains(e.target) && !btnExp.contains(e.target)) {
        panel.classList.remove('open');
        document.getElementById('exportArrow').style.transform = 'rotate(0deg)';
    }
});

// ════ PREVIEW FILE ════
function previewFile(url, type, title) {
    document.getElementById('previewModalTitle').innerHTML =
        `<i class="ri-${type === 'pdf' ? 'file-pdf-line' : 'image-line'}" style="${type === 'pdf' ? 'color:#EF4444;' : 'color:var(--brand-500);'}"></i> ${title}`;
    document.getElementById('previewDownloadBtn').href = url;
    const content = document.getElementById('previewContent');
    if (type === 'pdf') {
        content.innerHTML = `
            <iframe src="${url}" class="file-preview-pdf" title="Preview PDF"></iframe>
            <p class="file-preview-info"><i class="ri-information-line"></i> Jika PDF tidak tampil, klik tombol <strong>Buka di Tab Baru</strong>.</p>`;
    } else {
        content.innerHTML = `
            <img src="${url}" class="file-preview-img" alt="Preview"
                 onerror="this.outerHTML='<div style=\\'text-align:center;padding:32px;color:var(--text-muted);\\'><i class=\\'ri-image-2-line\\' style=\\'font-size:40px;display:block;margin-bottom:8px;\\'></i>Gambar tidak dapat dimuat.</div>'">
            <p class="file-preview-info"><i class="ri-information-line"></i> Klik <strong>Buka di Tab Baru</strong> untuk zoom atau download.</p>`;
    }
    document.getElementById('modalPreviewFile').classList.add('open');
}
function closePreviewFile() {
    document.getElementById('modalPreviewFile').classList.remove('open');
    setTimeout(() => { document.getElementById('previewContent').innerHTML = ''; }, 200);
}
document.getElementById('modalPreviewFile').addEventListener('click', function(e) {
    if (e.target === this) closePreviewFile();
});

// ── Hapus ──
function openDeleteModal(id, nama) {
    document.getElementById('modal-nama').textContent = nama;
    document.getElementById('deleteForm').action = '/penyewaan/' + id;
    document.getElementById('modalHapus').classList.add('open');
}
function closeDeleteModal() { document.getElementById('modalHapus').classList.remove('open'); }
document.getElementById('modalHapus').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// ════ BATALKAN ════
let batalTargetId = null;

function openBatalModal(id, nama) {
    batalTargetId = id;
    document.getElementById('batal-nama').textContent      = nama;
    document.getElementById('inputAlasanBatal').value      = '';
    document.getElementById('alasanBatalError').classList.remove('show');
    document.getElementById('modalBatalkan').classList.add('open');
}
function closeBatalModal() {
    document.getElementById('modalBatalkan').classList.remove('open');
    batalTargetId = null;
}
function submitBatal() {
    const alasan = document.getElementById('inputAlasanBatal').value.trim();
    const errEl  = document.getElementById('alasanBatalError');
    if (!alasan) { errEl.classList.add('show'); return; }
    errEl.classList.remove('show');
    if (!batalTargetId) return;

    const btn = document.getElementById('btnSubmitBatal');
    btn.disabled  = true;
    btn.innerHTML = '<i class="ri-loader-4-line" style="animation:spin 1s linear infinite;display:inline-block;"></i> Memproses...';

    fetch(`/penyewaan/${batalTargetId}/batalkan`, {
        method : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept'       : 'application/json',
        },
        body: JSON.stringify({ alasan_batal: alasan }),
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="ri-forbid-line"></i> Ya, Batalkan';
        closeBatalModal();
        if (data.success) {
            showToast?.('Penyewaan berhasil dibatalkan.', 'success');
            setTimeout(() => location.reload(), 700);
        } else {
            alert(data.message || 'Gagal membatalkan penyewaan.');
        }
    })
    .catch(() => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="ri-forbid-line"></i> Ya, Batalkan';
        alert('Terjadi kesalahan jaringan. Coba lagi.');
    });
}
document.getElementById('modalBatalkan').addEventListener('click', function(e) {
    if (e.target === this) closeBatalModal();
});

// ════ PULIHKAN ════
let restoreTargetId = null;

function openRestoreModal(id, nama) {
    restoreTargetId = id;
    document.getElementById('restore-nama').textContent = nama;
    document.getElementById('modalRestore').classList.add('open');
}
function closeRestoreModal() {
    document.getElementById('modalRestore').classList.remove('open');
    restoreTargetId = null;
}
function submitRestore() {
    if (!restoreTargetId) return;

    const btn = document.getElementById('btnSubmitRestore');
    btn.disabled  = true;
    btn.innerHTML = '<i class="ri-loader-4-line" style="animation:spin 1s linear infinite;display:inline-block;"></i> Memproses...';

    fetch(`/penyewaan/${restoreTargetId}/restore`, {
        method : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept'       : 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="ri-refresh-line"></i> Ya, Pulihkan';
        closeRestoreModal();
        if (data.success) {
            showToast?.('Penyewaan berhasil dipulihkan.', 'success');
            setTimeout(() => location.reload(), 700);
        } else {
            alert(data.message || 'Gagal memulihkan penyewaan.');
        }
    })
    .catch(() => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="ri-refresh-line"></i> Ya, Pulihkan';
        alert('Terjadi kesalahan jaringan. Coba lagi.');
    });
}
document.getElementById('modalRestore').addEventListener('click', function(e) {
    if (e.target === this) closeRestoreModal();
});

// ── Monitoring ──
let currentPenyewaanId = null;
let currentSisaHari    = 0;
let currentTglSelesai  = null;

function openMonitoring() {
    document.getElementById('modalMonitoring').classList.add('open');
    loadMonitoringData();
}
function closeMonitoring() { document.getElementById('modalMonitoring').classList.remove('open'); }
document.getElementById('modalMonitoring').addEventListener('click', function(e) {
    if (e.target === this) closeMonitoring();
});

function loadMonitoringData() {
    const el = document.getElementById('monitoringContent');
    el.innerHTML = `<div class="monitoring-loading"><i class="ri-loader-4-line"></i>Memuat data...</div>`;
    fetch('{{ route("penyewaan.monitoring") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.length) {
            el.innerHTML = `<div class="monitoring-loading" style="color:var(--text-muted);"><i class="ri-inbox-2-line" style="animation:none;font-size:40px;"></i>Tidak ada penyewaan aktif saat ini.</div>`;
            return;
        }
        let rows = data.map(d => {
            const sisaClass = d.sisa_hari <= 0 ? 'sisa-hari-danger' : (d.sisa_hari <= 7 ? 'sisa-hari-warning' : 'sisa-hari-normal');
            const sisaText  = d.sisa_hari <= 0 ? 'Lewat deadline' : (d.sisa_hari === 1 ? 'Besok deadline!' : d.sisa_hari + ' hari lagi');
            return `<tr>
                <td style="font-weight:600;">${d.nama}</td>
                <td>${d.nomor_hp}</td>
                <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${d.barang}">${d.barang}</td>
                <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${d.alamat}">${d.alamat}</td>
                <td class="center"><span class="${sisaClass}">${sisaText}</span></td>
                <td class="center"><span class="status-badge ${d.status_class}">${d.status_label}</span></td>
                <td class="center">
                    <button class="btn" style="height:30px;padding:0 12px;font-size:12px;background:#7C3AED;color:#fff;border:none;border-radius:7px;cursor:pointer;"
                            onclick="openSelesaikan(${d.id}, ${d.sisa_hari}, '${d.tgl_selesai_raw}')">
                        <i class="ri-check-double-line"></i> Selesaikan
                    </button>
                </td>
            </tr>`;
        }).join('');
        el.innerHTML = `
            <div style="overflow-x:auto;">
                <table class="monitoring-table">
                    <thead>
                        <tr>
                            <th>Nama</th><th>Nomor HP</th><th>Barang</th><th>Alamat</th>
                            <th class="center">Sisa Hari</th><th class="center">Status</th><th class="center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
    })
    .catch(() => {
        el.innerHTML = `<div class="monitoring-loading" style="color:#EF4444;">Gagal memuat data. Coba lagi.</div>`;
    });
}

// ── Selesaikan ──
function openSelesaikan(id, sisaHari, tglSelesai) {
    currentPenyewaanId = id; currentSisaHari = sisaHari; currentTglSelesai = tglSelesai;
    if (sisaHari > 7) {
        document.getElementById('sisaHariNormal').textContent = sisaHari;
        document.getElementById('modalSelesaikanNormal').classList.add('open');
    } else {
        document.getElementById('modalKonfirmasiDulu').classList.add('open');
    }
}
function closeSelesaikanNormal() { document.getElementById('modalSelesaikanNormal').classList.remove('open'); }
function closeKonfirmasiDulu()   { document.getElementById('modalKonfirmasiDulu').classList.remove('open'); }
function closePilihAction()      { document.getElementById('modalPilihAction').classList.remove('open'); }

function sudahKonfirmasi() {
    closeKonfirmasiDulu();
    document.getElementById('modalPilihAction').classList.add('open');
}

function doSelesaikan(action) {
    if (!currentPenyewaanId) return;
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    fetch(`/penyewaan/${currentPenyewaanId}/selesaikan`, {
        method : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        body   : JSON.stringify({ action })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeSelesaikanNormal(); closePilihAction();
            loadMonitoringData();
            setTimeout(() => location.reload(), 500);
        }
    });
}

// ════ EXTEND ════
function openExtend() {
    closePilihAction();
    if (currentTglSelesai) {
        const d = new Date(currentTglSelesai + 'T00:00:00');
        d.setDate(d.getDate() + 1);
        const pad     = n => String(n).padStart(2, '0');
        const minDate = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
        document.getElementById('extendTanggal').min   = minDate;
        document.getElementById('extendTanggal').value = minDate;
        document.getElementById('extendHarga').value           = '';
        document.getElementById('extendMetodeBayar').value     = '';
        document.getElementById('extendBuktiInput').value      = '';
        document.getElementById('extendBuktiName').textContent = '';
        document.getElementById('extendCatatan').value         = '';
        const tglAwal  = new Date(currentTglSelesai + 'T00:00:00');
        const tglLabel = tglAwal.toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'});
        document.getElementById('extendInfoText').textContent = `Deadline awal: ${tglLabel}. Extend dihitung mulai tanggal tersebut.`;
    }
    document.getElementById('modalExtend').classList.add('open');
}
function closeExtend() { document.getElementById('modalExtend').classList.remove('open'); }
function onExtendFileChange(input) {
    const nameEl = document.getElementById('extendBuktiName');
    if (input.files && input.files[0]) {
        nameEl.textContent = '✓ ' + input.files[0].name;
        nameEl.style.color = 'var(--brand-500)';
    } else { nameEl.textContent = ''; }
}

// ════ SUKSES EXTEND ════
function openSuksesExtend(extendId, tglBaru, tambahHari) {
    const baseUrl = '{{ url("penyewaan/extend") }}';
    document.getElementById('suksesExtendTglBaru').textContent    = tglBaru;
                document.getElementById('suksesExtendTambahHari').textContent = tambahHari + ' hari';
    document.getElementById('linkCetakInvoiceExtend').href        = `${baseUrl}/${extendId}/invoice`;
    document.getElementById('linkCetakPerjanjianExtend').href     = `${baseUrl}/${extendId}/perjanjian`;
    document.getElementById('modalSuksesExtend').classList.add('open');
}
function closeSuksesExtend() { document.getElementById('modalSuksesExtend').classList.remove('open'); }
document.getElementById('modalSuksesExtend').addEventListener('click', function(e) {
    if (e.target === this) closeSuksesExtend();
});

function doExtend() {
    const tglBaru     = document.getElementById('extendTanggal').value;
    const harga       = document.getElementById('extendHarga').value;
    const metodeBayar = document.getElementById('extendMetodeBayar').value;
    const catatan     = document.getElementById('extendCatatan').value;
    const fileInput   = document.getElementById('extendBuktiInput');
    if (!tglBaru)                         { alert('Tanggal extend wajib diisi.'); return; }
    if (harga === '' || Number(harga) < 0) { alert('Harga extend wajib diisi.'); return; }
    if (!metodeBayar)                     { alert('Metode pembayaran wajib dipilih.'); return; }
    if (!currentPenyewaanId) return;

    const token    = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    const formData = new FormData();
    formData.append('_token', token);
    formData.append('tgl_selesai_baru', tglBaru);
    formData.append('harga_extend', harga);
    formData.append('metode_bayar', metodeBayar);
    formData.append('catatan', catatan);
    if (fileInput.files[0]) formData.append('bukti_transfer', fileInput.files[0]);

    const btn = document.getElementById('btnDoExtend');
    btn.disabled  = true;
    btn.innerHTML = '<i class="ri-loader-4-line" style="animation:spin 1s linear infinite;display:inline-block;"></i> Menyimpan...';

    fetch(`/penyewaan/${currentPenyewaanId}/extend-store`, {
        method : 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        body   : formData
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="ri-calendar-2-line"></i> Simpan Extend';
        if (res.success) {
            closeExtend(); loadMonitoringData();
            openSuksesExtend(res.extend_id, res.tgl_baru, res.tambah_hari);
        } else { alert(res.message || 'Gagal menyimpan extend.'); }
    })
    .catch(() => {
        btn.disabled  = false;
        btn.innerHTML = '<i class="ri-calendar-2-line"></i> Simpan Extend';
        alert('Terjadi kesalahan jaringan. Coba lagi.');
    });
}

// ── Escape Key & Backdrop semua modal ──
document.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;
    closePreviewFile();
    closeDeleteModal();
    closeBatalModal();
    closeRestoreModal();
    closeMonitoring();
    closeSelesaikanNormal();
    closeKonfirmasiDulu();
    closePilihAction();
    closeExtend();
    closeSuksesExtend();
    closeAllDropdowns();
});

['modalSelesaikanNormal','modalKonfirmasiDulu','modalPilihAction','modalExtend'].forEach(id => {
    document.getElementById(id)?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});

document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#monitoring') setTimeout(openMonitoring, 200);
});
</script>
@endpush