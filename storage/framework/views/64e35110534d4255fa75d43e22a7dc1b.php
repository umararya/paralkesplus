


<?php $__env->startSection('title', 'Pembelian Barang'); ?>
<?php $__env->startSection('breadcrumb', 'Pembelian Barang'); ?>

<?php $__env->startPush('styles'); ?>
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

    /* ── Filter Tanggal Bar ── */
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

    /* ── Filter Tabs ── */
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

    .action-group { display:flex; align-items:center; gap:4px; justify-content:center; }
    .btn-action { width:30px; height:30px; border-radius:7px; display:inline-flex; align-items:center; justify-content:center; font-size:15px; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-secondary); transition:all 0.2s; }
    .btn-action:hover { background:var(--bg-hover); color:var(--text-primary); }
    .btn-action.edit:hover   { background:#EFF6FF; color:var(--brand-500); border-color:var(--brand-100); }
    .btn-action.delete:hover { background:#FFF1F2; color:#E11D48; border-color:#FFE4E6; }
    html.dark .btn-action.edit:hover   { background:rgba(29,111,164,0.15); color:#60A5FA; border-color:rgba(29,111,164,0.3); }
    html.dark .btn-action.delete:hover { background:rgba(225,29,72,0.12); color:#FB7185; border-color:rgba(225,29,72,0.25); }

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

    .tfoot-total td { padding:12px 16px; font-size:13px; font-weight:700; color:var(--text-primary); background:var(--bg-hover); border-top:2px solid var(--border); }

    .bukti-thumb { width:44px; height:44px; border-radius:8px; object-fit:cover; cursor:pointer; border:1px solid var(--border); transition:transform 0.15s,box-shadow 0.15s; display:block; margin:0 auto; }
    .bukti-thumb:hover { transform:scale(1.08); box-shadow:0 4px 12px rgba(0,0,0,0.15); }
    .bukti-empty { width:44px; height:44px; border-radius:8px; background:var(--bg-primary); border:1px dashed var(--border); display:inline-flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:18px; }

    .lightbox-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.88); z-index:2000; align-items:center; justify-content:center; padding:20px; cursor:zoom-out; }
    .lightbox-overlay.open { display:flex; animation:fadeOverlay 0.18s ease; }
    .lightbox-overlay img { max-width:90vw; max-height:88vh; border-radius:10px; object-fit:contain; box-shadow:0 20px 60px rgba(0,0,0,0.5); cursor:default; }
    .lightbox-close { position:fixed; top:16px; right:20px; width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,0.15); border:none; color:#fff; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
    .lightbox-close:hover { background:rgba(255,255,255,0.28); }
    .lightbox-caption { position:fixed; bottom:18px; left:50%; transform:translateX(-50%); font-size:13px; color:rgba(255,255,255,0.75); background:rgba(0,0,0,0.5); padding:6px 16px; border-radius:20px; white-space:nowrap; max-width:80vw; overflow:hidden; text-overflow:ellipsis; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php if(session('success')): ?>
<div class="alert alert-success">
    <i class="ri-checkbox-circle-fill"></i> <?php echo e(session('success')); ?>

</div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="alert alert-error">
    <i class="ri-error-warning-fill"></i> <?php echo e(session('error')); ?>

</div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="ri-shopping-cart-2-line"></i> Pembelian Barang
        </h1>
        <p class="page-subtitle">Kelola data pembelian barang dan alat kesehatan</p>
    </div>
</div>

<div class="table-card">

    
    <div class="table-toolbar">
        <div class="toolbar-left">
            <form method="GET" action="<?php echo e(route('pembelian.index')); ?>" id="perPageForm">
                <input type="hidden" name="search"    value="<?php echo e($search); ?>">
                <input type="hidden" name="filter"    value="<?php echo e($filter); ?>">
                <input type="hidden" name="date_from" value="<?php echo e($dateFrom); ?>">
                <input type="hidden" name="date_to"   value="<?php echo e($dateTo); ?>">
                <div class="per-page-wrap">
                    <span>Tampilkan</span>
                    <select name="per_page" class="per-page-select"
                            onchange="document.getElementById('perPageForm').submit()">
                        <?php $__currentLoopData = [5, 10, 25, 50]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($pp); ?>" <?php echo e($perPage == $pp ? 'selected' : ''); ?>><?php echo e($pp); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span>data</span>
                </div>
            </form>

            <div class="toolbar-divider"></div>

            <form method="GET" action="<?php echo e(route('pembelian.index')); ?>" class="search-form" id="searchForm">
                <input type="hidden" name="per_page"  value="<?php echo e($perPage); ?>">
                <input type="hidden" name="filter"    value="<?php echo e($filter); ?>">
                <input type="hidden" name="date_from" value="<?php echo e($dateFrom); ?>">
                <input type="hidden" name="date_to"   value="<?php echo e($dateTo); ?>">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input type="text" name="search" value="<?php echo e($search); ?>"
                           placeholder="Cari nama barang, pelanggan..."
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                <?php if($search): ?>
                <a href="<?php echo e(route('pembelian.index', ['per_page' => $perPage, 'filter' => $filter, 'date_from' => $dateFrom, 'date_to' => $dateTo])); ?>"
                   class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="toolbar-right">
            <a href="<?php echo e(route('pembelian.export', ['search' => $search, 'filter' => $filter])); ?>"
               class="btn btn-export" title="Export ke Excel">
                <i class="ri-file-excel-2-line"></i> Export XLSX
            </a>
            <a href="<?php echo e(route('pembelian.create')); ?>" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Pembelian
            </a>
        </div>
    </div>

    
    <div class="date-filter-bar">
        <span class="date-filter-label">
            <i class="ri-calendar-2-line"></i> Filter Tanggal:
        </span>
        <form method="GET" action="<?php echo e(route('pembelian.index')); ?>"
              id="dateFilterForm" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <input type="hidden" name="search"   value="<?php echo e($search); ?>">
            <input type="hidden" name="filter"   value="<?php echo e($filter); ?>">
            <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
            <div class="date-input-wrap">
                <input type="date" name="date_from" id="dateFrom"
                       class="date-input <?php echo e($dateFrom ? 'date-filter-active' : ''); ?>"
                       value="<?php echo e($dateFrom); ?>"
                       title="Dari tanggal"
                       onchange="this.form.submit()">
                <span class="date-separator">—</span>
                <input type="date" name="date_to" id="dateTo"
                       class="date-input <?php echo e($dateTo ? 'date-filter-active' : ''); ?>"
                       value="<?php echo e($dateTo); ?>"
                       title="Sampai tanggal"
                       onchange="this.form.submit()">
            </div>
            <?php if($dateFrom || $dateTo): ?>
            <span class="date-filter-badge">
                <i class="ri-filter-fill" style="font-size:10px;"></i>
                Filter aktif
            </span>
            <a href="<?php echo e(route('pembelian.index', ['search' => $search, 'filter' => $filter, 'per_page' => $perPage])); ?>"
               class="btn btn-ghost" style="height:30px;font-size:12px;padding:0 10px;">
                <i class="ri-close-line"></i> Reset
            </a>
            <?php endif; ?>
        </form>

        
        <div style="display:flex;align-items:center;gap:5px;margin-left:4px;">
            <span style="font-size:11px;color:var(--text-muted);">Cepat:</span>
            <button type="button" class="btn btn-ghost" style="height:28px;font-size:11.5px;padding:0 9px;"
                    onclick="setDateRange('today')">Hari ini</button>
            <button type="button" class="btn btn-ghost" style="height:28px;font-size:11.5px;padding:0 9px;"
                    onclick="setDateRange('week')">7 hari</button>
            <button type="button" class="btn btn-ghost" style="height:28px;font-size:11.5px;padding:0 9px;"
                    onclick="setDateRange('month')">Bulan ini</button>
            <button type="button" class="btn btn-ghost" style="height:28px;font-size:11.5px;padding:0 9px;"
                    onclick="setDateRange('year')">Tahun ini</button>
        </div>
    </div>

    
    <div class="filter-tabs">
        <?php $dq = ['per_page' => $perPage, 'search' => $search, 'date_from' => $dateFrom, 'date_to' => $dateTo]; ?>
        <a href="<?php echo e(route('pembelian.index', array_merge($dq, ['filter' => 'semua']))); ?>"
           class="tab-btn <?php echo e($filter === 'semua' ? 'active' : ''); ?>">
            <i class="ri-list-check"></i> Semua
            <span class="tab-count"><?php echo e($countSemua); ?></span>
        </a>
        <a href="<?php echo e(route('pembelian.index', array_merge($dq, ['filter' => 'normal']))); ?>"
           class="tab-btn <?php echo e($filter === 'normal' ? 'active' : ''); ?>">
            <i class="ri-shopping-cart-line"></i> Pembelian Normal
            <span class="tab-count"><?php echo e($countNormal); ?></span>
        </a>
        <a href="<?php echo e(route('pembelian.index', array_merge($dq, ['filter' => 'buy_back']))); ?>"
           class="tab-btn <?php echo e($filter === 'buy_back' ? 'active-buyback' : ''); ?>">
            <i class="ri-loop-left-line"></i> Buy Back
            <span class="tab-count"><?php echo e($countBuyBack); ?></span>
        </a>
    </div>

    
    <div class="info-bar">
        <div class="info-bar-text">
            <?php if($dateFrom || $dateTo): ?>
                <i class="ri-calendar-line"></i>
                <?php if($dateFrom && $dateTo): ?>
                    <strong><?php echo e(\Carbon\Carbon::parse($dateFrom)->format('d M Y')); ?></strong>
                    s/d
                    <strong><?php echo e(\Carbon\Carbon::parse($dateTo)->format('d M Y')); ?></strong>
                <?php elseif($dateFrom): ?>
                    Dari <strong><?php echo e(\Carbon\Carbon::parse($dateFrom)->format('d M Y')); ?></strong>
                <?php else: ?>
                    Sampai <strong><?php echo e(\Carbon\Carbon::parse($dateTo)->format('d M Y')); ?></strong>
                <?php endif; ?>
                &nbsp;
            <?php endif; ?>
            <?php if($search): ?>
                <i class="ri-filter-3-line"></i>
                "<strong><?php echo e($search); ?></strong>"
            <?php endif; ?>
            <span class="badge-count"><?php echo e($pembelians->total()); ?> data</span>
        </div>
        <?php if($pembelians->total() > 0): ?>
        <div class="info-bar-text">
            Halaman <strong><?php echo e($pembelians->currentPage()); ?></strong>
            dari <strong><?php echo e($pembelians->lastPage()); ?></strong>
        </div>
        <?php endif; ?>
    </div>

    
    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:46px;">#</th>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th class="center">Status</th>
                    <th class="center">Kondisi</th>
                    <th class="center">Qty</th>
                    <th class="right">Harga Satuan</th>
                    <th class="right">Total</th>
                    <th>Keterangan</th>
                    <th class="center" style="width:70px;">Bukti</th>
                    <th class="center" style="width:90px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $pembelians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12.5px;font-weight:500;">
                        <?php echo e($pembelians->firstItem() + $loop->index); ?>

                    </td>

                    <td>
                        <span class="tanggal-badge">
                            <i class="ri-calendar-line" style="font-size:12px;color:var(--text-muted);"></i>
                            <?php echo e(\Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y')); ?>

                        </span>
                    </td>

                    <td>
                        <div style="font-weight:600;">
                            <?php if($search): ?>
                                <?php echo preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                    '<mark style="background:#FEF08A;border-radius:3px;padding:0 2px;">$1</mark>',
                                    e($item->nama_barang)); ?>

                            <?php else: ?>
                                <?php echo e($item->nama_barang); ?>

                            <?php endif; ?>
                        </div>
                        <?php if($item->status === 'buy_back' && $item->nama_pelanggan): ?>
                        <div style="font-size:11.5px;color:var(--text-muted);margin-top:2px;">
                            <i class="ri-user-line" style="font-size:11px;"></i> <?php echo e($item->nama_pelanggan); ?>

                        </div>
                        <?php endif; ?>
                    </td>

                    <td class="center">
                        <?php if($item->status === 'buy_back'): ?>
                            <span class="status-badge status-buy-back">
                                <i class="ri-loop-left-line"></i> Buy Back
                            </span>
                        <?php else: ?>
                            <span class="status-badge status-normal">
                                <i class="ri-shopping-cart-line"></i> Normal
                            </span>
                        <?php endif; ?>
                    </td>

                    <td class="center">
                        <?php $k = $item->kondisi_barang; ?>
                        <?php if($k === 'baru'): ?>
                            <span class="kondisi-badge kondisi-baru"><i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Baru</span>
                        <?php elseif($k === 'bekas'): ?>
                            <span class="kondisi-badge kondisi-bekas"><i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Bekas</span>
                        <?php elseif($k === 'baik'): ?>
                            <span class="kondisi-badge kondisi-baik"><i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Baik</span>
                        <?php elseif($k === 'rusak'): ?>
                            <span class="kondisi-badge kondisi-rusak"><i class="ri-checkbox-blank-circle-fill" style="font-size:8px;"></i> Rusak</span>
                        <?php else: ?>
                            <span style="color:var(--text-muted);font-size:13px;">—</span>
                        <?php endif; ?>
                    </td>

                    <td class="center">
                        <span class="qty-badge"><?php echo e(number_format($item->jumlah)); ?></span>
                    </td>

                    <td class="right" style="font-size:13px;white-space:nowrap;">
                        <?php echo e($item->harga_formatted); ?>

                    </td>

                    <td class="right total-value" style="font-size:13px;white-space:nowrap;">
                        <?php echo e($item->total_formatted); ?>

                    </td>

                    <td style="max-width:160px;">
                        <?php if($item->keterangan): ?>
                            <span title="<?php echo e($item->keterangan); ?>"
                                  style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;color:var(--text-muted);">
                                <?php if($search): ?>
                                    <?php echo preg_replace('/(' . preg_quote($search, '/') . ')/i',
                                        '<mark style="background:#FEF08A;border-radius:3px;padding:0 2px;">$1</mark>',
                                        e($item->keterangan)); ?>

                                <?php else: ?>
                                    <?php echo e($item->keterangan); ?>

                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span style="color:var(--text-muted);font-style:italic;font-size:13px;">—</span>
                        <?php endif; ?>
                    </td>

                    <td class="center">
                        <?php if($item->bukti_transaksi): ?>
                            <img src="<?php echo e(asset('storage/' . $item->bukti_transaksi)); ?>"
                                 alt="Bukti <?php echo e($item->nama_barang); ?>"
                                 class="bukti-thumb"
                                 onclick="openLightbox(
                                     '<?php echo e(asset('storage/' . $item->bukti_transaksi)); ?>',
                                     'Bukti — <?php echo e(addslashes($item->nama_barang)); ?>'
                                 )">
                        <?php else: ?>
                            <span class="bukti-empty" title="Tidak ada bukti">
                                <i class="ri-image-line"></i>
                            </span>
                        <?php endif; ?>
                    </td>

                    <td class="center">
                        <div class="action-group">
                            <?php if($item->status === 'buy_back'): ?>
                            <a href="<?php echo e(route('pembelian.invoice', $item->id)); ?>"
                               target="_blank"
                               class="btn-action"
                               title="Cetak Invoice Buy Back"
                               style="color:#D97706;border-color:#FDE68A;background:#FFFBEB;">
                                <i class="ri-printer-line"></i>
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('pembelian.edit', $item->id)); ?>"
                               class="btn-action edit" title="Edit data">
                                <i class="ri-edit-line"></i>
                            </a>
                            <button type="button"
                                    class="btn-action delete"
                                    title="Hapus data"
                                    onclick="openDeleteModal(<?php echo e($item->id); ?>, '<?php echo e(addslashes($item->nama_barang)); ?>')">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                            <form id="formHapus-<?php echo e($item->id); ?>"
                                  action="<?php echo e(route('pembelian.destroy', $item->id)); ?>"
                                  method="POST" style="display:none;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="11">
                        <div class="empty-state">
                            <i class="ri-shopping-cart-2-line"></i>
                            <h3>
                                <?php if($dateFrom || $dateTo): ?>
                                    Tidak ada data pada rentang tanggal ini
                                <?php elseif($search): ?>
                                    Tidak ditemukan
                                <?php else: ?>
                                    Belum ada data pembelian
                                <?php endif; ?>
                            </h3>
                            <p>
                                <?php if($dateFrom || $dateTo): ?>
                                    Coba ubah rentang tanggal atau klik Reset.
                                <?php elseif($search): ?>
                                    Coba kata kunci lain atau klik Reset.
                                <?php else: ?>
                                    Klik "Tambah Pembelian" untuk mencatat pembelian baru.
                                <?php endif; ?>
                            </p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>

            <?php if($pembelians->count() > 0): ?>
            <tfoot>
                <tr class="tfoot-total">
                    <td colspan="7" class="right">
                        Total <?php echo e(($dateFrom || $dateTo) ? 'periode ini' : ($search ? 'hasil filter' : 'keseluruhan')); ?>:
                    </td>
                    <td class="right total-value" style="font-size:14px;white-space:nowrap;">
                        Rp <?php echo e(number_format($totalKeseluruhan, 0, ',', '.')); ?>

                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>

    
    <?php if($pembelians->total() > 0): ?>
    <div class="table-footer">
        <div class="pagination-meta">
            Menampilkan
            <strong><?php echo e($pembelians->firstItem()); ?> – <?php echo e($pembelians->lastItem()); ?></strong>
            dari <strong><?php echo e($pembelians->total()); ?></strong> data
        </div>

        <?php if($pembelians->hasPages()): ?>
        <nav class="pagination-nav" aria-label="Pagination">
            <?php if($pembelians->onFirstPage()): ?>
                <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
            <?php else: ?>
                <a class="page-btn" href="<?php echo e($pembelians->previousPageUrl()); ?>">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            <?php endif; ?>

            <?php
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
            ?>

            <?php $__currentLoopData = $rendered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($pageItem === '...'): ?>
                    <span class="page-ellipsis">…</span>
                <?php elseif($pageItem == $current): ?>
                    <span class="page-btn active"><?php echo e($pageItem); ?></span>
                <?php else: ?>
                    <a class="page-btn" href="<?php echo e($pembelians->url($pageItem)); ?>"><?php echo e($pageItem); ?></a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($pembelians->hasMorePages()): ?>
                <a class="page-btn" href="<?php echo e($pembelians->nextPageUrl()); ?>">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            <?php else: ?>
                <span class="page-btn disabled"><i class="ri-arrow-right-s-line"></i></span>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>



<div class="modal-overlay" id="modalHapus">
    <div class="modal" style="max-width:400px;">
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
                <p>Kamu akan menghapus data pembelian:<br>
                   <strong id="deleteNamaBarang"></strong><br><br>
                   Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalHapus')">Batal</button>
            <form id="formDeleteSubmit" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>



<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="event.stopPropagation();closeLightbox()">
        <i class="ri-close-line"></i>
    </button>
    <img id="lightboxImg" src="" alt="Bukti Transaksi" onclick="event.stopPropagation()">
    <div class="lightbox-caption" id="lightboxCaption"></div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
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
        if (e.target === this) closeModal(this.id);
    });
});
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeLightbox();
        document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
    }
});

function openDeleteModal(id, namaBarang) {
    document.getElementById('deleteNamaBarang').textContent = namaBarang;
    document.getElementById('formDeleteSubmit').action = '/pembelian/' + id;
    openModal('modalHapus');
}

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

// ── Shortcut filter tanggal cepat ──
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\paralkesplus\resources\views/admin/pembelian/index.blade.php ENDPATH**/ ?>