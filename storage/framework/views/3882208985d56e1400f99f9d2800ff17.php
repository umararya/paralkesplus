

<?php $__env->startSection('title', 'User Login — Owner'); ?>
<?php $__env->startSection('breadcrumb', 'User Login'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ── Page Header ── */
    .page-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
    }
    .page-title {
        font-size: 20px; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 10px; line-height: 1.2;
    }
    .page-title i { font-size: 22px; color: var(--brand-500); }
    .page-subtitle { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

    /* ── Table Card ── */
    .table-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 14px; box-shadow: var(--shadow); overflow: hidden;
        transition: background 0.3s, border-color 0.3s;
    }

    /* ── Toolbar ── */
    .table-toolbar {
        padding: 14px 18px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        gap: 10px; flex-wrap: wrap;
    }
    .toolbar-left  { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .toolbar-right { display: flex; align-items: center; gap: 8px; }

    /* Per-page selector */
    .per-page-wrap {
        display: flex; align-items: center; gap: 7px;
        font-size: 13px; color: var(--text-secondary);
    }
    .per-page-select {
        height: 36px; padding: 0 30px 0 10px;
        border: 1px solid var(--border); border-radius: 8px;
        background: var(--bg-primary); color: var(--text-primary);
        font-size: 13px; font-family: var(--font); outline: none;
        cursor: pointer; transition: border-color 0.2s, box-shadow 0.2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 8px center;
    }
    .per-page-select:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(29,111,164,0.1); }

    /* Search */
    .search-form { display: flex; align-items: center; gap: 7px; }
    .search-input-wrap {
        display: flex; align-items: center;
        background: var(--bg-primary); border: 1px solid var(--border);
        border-radius: 8px; padding: 0 11px; height: 36px; gap: 7px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .search-input-wrap:focus-within {
        border-color: var(--brand-500);
        box-shadow: 0 0 0 3px rgba(29,111,164,0.1);
    }
    .search-input-wrap i { color: var(--text-muted); font-size: 14px; flex-shrink: 0; }
    .search-input-wrap input {
        border: none; background: transparent; outline: none;
        font-size: 13px; color: var(--text-primary);
        font-family: var(--font); width: 200px;
    }
    .search-input-wrap input::placeholder { color: var(--text-muted); }

    /* Divider */
    .toolbar-divider {
        width: 1px; height: 24px; background: var(--border); flex-shrink: 0;
    }

    /* Buttons */
    .btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 0 14px; height: 36px; border-radius: 8px;
        font-size: 13px; font-weight: 500; font-family: var(--font);
        cursor: pointer; border: none; transition: all 0.2s;
        text-decoration: none; white-space: nowrap;
    }
    .btn i { font-size: 15px; }
    .btn-search {
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100);
    }
    .btn-search:hover { background: var(--brand-100); color: var(--brand-600); }
    html.dark .btn-search { background: rgba(29,111,164,0.12); color: #60A5FA; border-color: rgba(29,111,164,0.25); }
    html.dark .btn-search:hover { background: rgba(29,111,164,0.22); }

    .btn-reset {
        background: transparent; color: var(--text-secondary); border: 1px solid var(--border);
    }
    .btn-reset:hover { background: var(--bg-hover); color: var(--text-primary); }

    .btn-primary { background: var(--brand-500); color: #fff; border: 1px solid var(--brand-500); }
    .btn-primary:hover { background: var(--brand-600); border-color: var(--brand-600); }
    .btn-ghost { background: transparent; color: var(--text-secondary); border: 1px solid var(--border); }
    .btn-ghost:hover { background: var(--bg-hover); color: var(--text-primary); }
    .btn-danger { background: #EF4444; color: #fff; border: 1px solid #EF4444; }
    .btn-danger:hover { background: #DC2626; border-color: #DC2626; }

    /* Info bar */
    .info-bar {
        padding: 9px 18px; border-bottom: 1px solid var(--border);
        background: var(--bg-primary);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 6px;
    }
    .info-bar-text { font-size: 12.5px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
    .info-bar-text strong { color: var(--text-primary); }
    .badge-count {
        display: inline-flex; align-items: center;
        background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100); border-radius: 99px;
        padding: 1px 9px; font-size: 11.5px; font-weight: 600;
    }
    html.dark .badge-count { background: rgba(29,111,164,0.12); color: #60A5FA; border-color: rgba(29,111,164,0.25); }

    /* ── Table ── */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead tr { background: var(--bg-primary); border-bottom: 2px solid var(--border); }
    .data-table th {
        padding: 10px 16px; text-align: left;
        font-size: 10.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.7px; color: var(--text-muted); white-space: nowrap;
    }
    .data-table td {
        padding: 13px 16px; font-size: 13.5px; color: var(--text-primary);
        border-bottom: 1px solid var(--border); vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr { transition: background 0.15s; }
    .data-table tbody tr:hover td { background: var(--bg-hover); }
    .data-table th.center, .data-table td.center { text-align: center; }

    /* User cell */
    .user-cell { display: flex; align-items: center; gap: 10px; }
    .user-avatar-sm {
        width: 34px; height: 34px; border-radius: 9px;
        background: linear-gradient(135deg, var(--brand-500), var(--accent));
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 13px; font-weight: 700; flex-shrink: 0;
    }
    .user-name-main { font-weight: 600; color: var(--text-primary); font-size: 13.5px; }

    /* Username mono */
    .username-mono {
        font-family: 'Courier New', monospace; font-size: 12.5px;
        color: var(--text-secondary); background: var(--bg-primary);
        padding: 3px 8px; border-radius: 6px;
        border: 1px solid var(--border); display: inline-block;
    }

    /* Password dots */
    .password-dots { display: inline-flex; align-items: center; gap: 3px; }
    .password-dots span {
        width: 5px; height: 5px; border-radius: 50%;
        background: var(--text-muted); display: inline-block;
    }

    /* Role badge */
    .role-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 9px; border-radius: 99px; font-size: 12px; font-weight: 600;
    }
    .role-owner   { background: #F5F3FF; color: #7C3AED; }
    .role-admin   { background: #FFF1F2; color: #E11D48; }
    .role-manager { background: #FFF7ED; color: #EA580C; }
    .role-staff   { background: #F0FDF4; color: #16A34A; }
    html.dark .role-owner   { background: rgba(124,58,237,0.12); color: #A78BFA; }
    html.dark .role-admin   { background: rgba(225,29,72,0.12);  color: #FB7185; }
    html.dark .role-manager { background: rgba(234,88,12,0.12);  color: #FB923C; }
    html.dark .role-staff   { background: rgba(22,163,74,0.12);  color: #4ADE80; }

    /* Action buttons */
    .action-group { display: flex; align-items: center; gap: 4px; justify-content: center; }
    .btn-action {
        width: 30px; height: 30px; border-radius: 7px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 15px; cursor: pointer;
        border: 1px solid var(--border); background: var(--bg-card);
        color: var(--text-secondary); transition: all 0.2s;
    }
    .btn-action:hover { background: var(--bg-hover); color: var(--text-primary); }
    .btn-action.view:hover   { background: #F5F3FF; color: #7C3AED; border-color: #EDE9FE; }
    .btn-action.edit:hover   { background: #EFF6FF; color: var(--brand-500); border-color: var(--brand-100); }
    .btn-action.delete:hover { background: #FFF1F2; color: #E11D48; border-color: #FFE4E6; }
    html.dark .btn-action.view:hover   { background: rgba(124,58,237,0.15); color: #A78BFA; border-color: rgba(124,58,237,0.3); }
    html.dark .btn-action.edit:hover   { background: rgba(29,111,164,0.15); color: #60A5FA; border-color: rgba(29,111,164,0.3); }
    html.dark .btn-action.delete:hover { background: rgba(225,29,72,0.12);  color: #FB7185; border-color: rgba(225,29,72,0.25); }
    .btn-action[disabled] { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

    /* ── PAGINATION (server-side, bawah tabel) ── */
    .table-footer {
        padding: 12px 18px; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
        flex-wrap: wrap; gap: 10px;
    }
    .pagination-meta { font-size: 12.5px; color: var(--text-muted); }
    .pagination-meta strong { color: var(--text-primary); }

    .pagination-nav { display: flex; align-items: center; gap: 3px; }

    .page-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px; padding: 0 6px;
        border-radius: 7px; font-size: 13px;
        border: 1px solid var(--border); background: var(--bg-card);
        color: var(--text-secondary); text-decoration: none;
        cursor: pointer; transition: all 0.18s; font-family: var(--font);
        white-space: nowrap;
    }
    .page-btn:hover { background: var(--bg-hover); color: var(--text-primary); border-color: var(--text-muted); }
    .page-btn.active {
        background: var(--brand-500); color: #fff;
        border-color: var(--brand-500); font-weight: 700;
    }
    .page-btn.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

    /* Ellipsis */
    .page-ellipsis {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 32px; height: 32px;
        font-size: 13px; color: var(--text-muted);
    }

    /* ── Empty state ── */
    .empty-state { text-align: center; padding: 56px 24px; }
    .empty-state i { font-size: 48px; color: var(--border); display: block; margin-bottom: 12px; }
    .empty-state h3 { font-size: 15px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; }
    .empty-state p  { font-size: 13px; color: var(--text-muted); }

    /* ── Flash Alert ── */
    .alert {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 16px; border-radius: 10px; font-size: 13.5px;
        font-weight: 500; margin-bottom: 18px; border: 1px solid transparent;
    }
    .alert-success { background: #F0FDF4; color: #15803D; border-color: #BBF7D0; }
    .alert-error   { background: #FFF1F2; color: #BE123C; border-color: #FECDD3; }
    html.dark .alert-success { background: rgba(21,128,61,0.12); color: #4ADE80; border-color: rgba(21,128,61,0.25); }
    html.dark .alert-error   { background: rgba(190,18,60,0.12); color: #FB7185; border-color: rgba(190,18,60,0.25); }

    /* ── Modal ── */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.45); z-index: 1000;
        align-items: center; justify-content: center;
        padding: 16px; backdrop-filter: blur(2px);
    }
    .modal-overlay.open { display: flex; animation: fadeOverlay 0.18s ease; }
    @keyframes fadeOverlay { from { opacity:0; } to { opacity:1; } }

    .modal {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        width: 100%; max-width: 460px; animation: slideUp 0.2s ease;
    }
    @keyframes slideUp {
        from { opacity:0; transform: translateY(18px); }
        to   { opacity:1; transform: translateY(0); }
    }
    .modal-header {
        padding: 18px 22px 14px; border-bottom: 1px solid var(--border);
        display: flex; align-items: center; justify-content: space-between;
    }
    .modal-title {
        font-size: 15px; font-weight: 700; color: var(--text-primary);
        display: flex; align-items: center; gap: 8px;
    }
    .modal-title i { font-size: 17px; color: var(--brand-500); }
    .modal-close {
        width: 28px; height: 28px; border: none; background: none;
        cursor: pointer; color: var(--text-muted); font-size: 19px;
        border-radius: 6px; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .modal-close:hover { background: var(--bg-hover); color: var(--text-primary); }
    .modal-body { padding: 18px 22px; }
    .modal-footer {
        padding: 14px 22px; border-top: 1px solid var(--border);
        display: flex; align-items: center; justify-content: flex-end; gap: 8px;
    }

    .form-group { margin-bottom: 14px; }
    .form-group:last-child { margin-bottom: 0; }
    .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 5px; }
    .form-label span { color: #EF4444; margin-left: 2px; }
    .form-control {
        width: 100%; height: 39px; padding: 0 11px;
        border: 1px solid var(--border); border-radius: 8px;
        background: var(--bg-primary); color: var(--text-primary);
        font-size: 13.5px; font-family: var(--font); outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus { border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(29,111,164,0.1); }
    .form-control::placeholder { color: var(--text-muted); }
    select.form-control {
        cursor: pointer; appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 10px center; padding-right: 32px;
    }
    .form-hint  { font-size: 11.5px; color: var(--text-muted); margin-top: 4px; }
    .form-error { font-size: 12px; color: #EF4444; margin-top: 4px; display: flex; align-items: center; gap: 4px; }

    .input-password-wrap { position: relative; }
    .input-password-wrap .form-control { padding-right: 38px; }
    .toggle-pw {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: var(--text-muted); font-size: 16px;
        display: flex; align-items: center; transition: color 0.2s;
    }
    .toggle-pw:hover { color: var(--text-primary); }

    /* Modal lihat password */
    .view-pw-box {
        background: var(--bg-primary); border: 1px solid var(--border);
        border-radius: 9px; padding: 14px 16px; margin-bottom: 10px;
    }
    .view-pw-box:last-child { margin-bottom: 0; }
    .view-pw-label { font-size: 10.5px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
    .view-pw-value {
        font-family: 'Courier New', monospace; font-size: 14px; font-weight: 700;
        color: var(--text-primary); letter-spacing: 0.5px;
        display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    }
    .view-pw-copy {
        display: inline-flex; align-items: center; gap: 3px;
        padding: 2px 9px; border-radius: 5px; font-size: 11.5px;
        font-family: var(--font); background: var(--brand-50); color: var(--brand-500);
        border: 1px solid var(--brand-100); cursor: pointer; transition: all 0.2s; font-weight: 500;
    }
    .view-pw-copy:hover { background: var(--brand-100); }
    html.dark .view-pw-copy { background: rgba(29,111,164,0.12); color: #60A5FA; border-color: rgba(29,111,164,0.25); }
    .view-pw-note {
        font-size: 11.5px; color: var(--text-muted);
        display: flex; align-items: center; gap: 4px; margin-top: 8px;
    }
    .view-pw-note i { color: #F59E0B; font-size: 13px; }

    /* Delete warning */
    .delete-warning { text-align: center; padding: 6px 0; }
    .delete-warning i { font-size: 42px; color: #EF4444; display: block; margin-bottom: 10px; }
    .delete-warning h3 { font-size: 15px; font-weight: 700; color: var(--text-primary); margin-bottom: 7px; }
    .delete-warning p { font-size: 13px; color: var(--text-muted); line-height: 1.6; }
    .delete-warning strong { color: var(--text-primary); }
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
            <i class="ri-login-circle-line"></i> User Login
        </h1>
        <p class="page-subtitle">Kelola data akun pengguna yang dapat masuk ke sistem</p>
    </div>
</div>


<div class="table-card">

    
    <div class="table-toolbar">

        
        <div class="toolbar-left">

            
            <form method="GET" action="<?php echo e(route('owner.user-login')); ?>" id="perPageForm">
                <input type="hidden" name="search" value="<?php echo e($search); ?>">
                <div class="per-page-wrap">
                    <span>Tampilkan</span>
                    <select
                        name="per_page"
                        class="per-page-select"
                        onchange="document.getElementById('perPageForm').submit()"
                        title="Jumlah baris per halaman"
                    >
                        <?php $__currentLoopData = $allowedPerPage; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($pp); ?>" <?php echo e($perPage == $pp ? 'selected' : ''); ?>>
                            <?php echo e($pp); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span>data</span>
                </div>
            </form>

            <div class="toolbar-divider"></div>

            
            <form method="GET" action="<?php echo e(route('owner.user-login')); ?>" class="search-form" id="searchForm">
                <input type="hidden" name="per_page" value="<?php echo e($perPage); ?>">
                <div class="search-input-wrap">
                    <i class="ri-search-line"></i>
                    <input
                        type="text"
                        name="search"
                        id="searchInput"
                        value="<?php echo e($search); ?>"
                        placeholder="Cari nama, username, role..."
                        autocomplete="off"
                    >
                </div>
                <button type="submit" class="btn btn-search">
                    <i class="ri-search-2-line"></i> Cari
                </button>
                <?php if($search): ?>
                <a href="<?php echo e(route('owner.user-login', ['per_page' => $perPage])); ?>" class="btn btn-reset">
                    <i class="ri-close-line"></i> Reset
                </a>
                <?php endif; ?>
            </form>
        </div>

        
        <div class="toolbar-right">
            <button type="button" class="btn btn-primary" onclick="openModal('modalTambah')">
                <i class="ri-user-add-line"></i> Input Data
            </button>
        </div>
    </div>

    
    <div class="info-bar">
        <div class="info-bar-text">
            <?php if($search): ?>
                <i class="ri-filter-3-line"></i>
                Hasil pencarian: <strong>"<?php echo e($search); ?>"</strong>
                &nbsp;<span class="badge-count"><?php echo e($users->total()); ?> pengguna</span>
            <?php else: ?>
                <i class="ri-group-line"></i>
                Total <span class="badge-count"><?php echo e($users->total()); ?> pengguna</span>
            <?php endif; ?>
        </div>
        <?php if($users->total() > 0): ?>
        <div class="info-bar-text">
            Halaman <strong><?php echo e($users->currentPage()); ?></strong> dari <strong><?php echo e($users->lastPage()); ?></strong>
        </div>
        <?php endif; ?>
    </div>

    
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:46px;">#</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Role</th>
                    <th class="center" style="width:110px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12.5px;font-weight:500;">
                        <?php echo e($users->firstItem() + $loop->index); ?>

                    </td>

                    
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm">
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                            </div>
                            <div class="user-name-main"><?php echo e($user->name); ?></div>
                        </div>
                    </td>

                    
                    <td><span class="username-mono"><?php echo e($user->username); ?></span></td>

                    
                    <td>
                        <div class="password-dots">
                            <span></span><span></span><span></span>
                            <span></span><span></span><span></span>
                            <span></span><span></span>
                        </div>
                    </td>

                    
                    <td>
                        <?php
                            $roleClass = match($user->role) {
                                'owner'   => 'role-owner',
                                'admin'   => 'role-admin',
                                'manager' => 'role-manager',
                                default   => 'role-staff',
                            };
                            $roleIcon = match($user->role) {
                                'owner'   => 'ri-vip-crown-fill',
                                'admin'   => 'ri-shield-fill',
                                'manager' => 'ri-user-star-fill',
                                default   => 'ri-user-fill',
                            };
                        ?>
                        <span class="role-badge <?php echo e($roleClass); ?>">
                            <i class="<?php echo e($roleIcon); ?>" style="font-size:11px;"></i>
                            <?php echo e(ucfirst($user->role)); ?>

                        </span>
                    </td>

                    
                    <td class="center">
                        <div class="action-group">

                            
                            <button type="button" class="btn-action view" title="Lihat detail"
                                onclick="openViewPassword(
                                    '<?php echo e(addslashes($user->name)); ?>',
                                    '<?php echo e(addslashes($user->username)); ?>',
                                    '<?php echo e($user->role); ?>'
                                )">
                                <i class="ri-eye-line"></i>
                            </button>

                            
                            <button type="button" class="btn-action edit" title="Edit pengguna"
                                onclick="openEditModal(
                                    <?php echo e($user->id); ?>,
                                    '<?php echo e(addslashes($user->name)); ?>',
                                    '<?php echo e(addslashes($user->username)); ?>',
                                    '<?php echo e($user->role); ?>'
                                )">
                                <i class="ri-edit-line"></i>
                            </button>

                            
                            <button type="button" class="btn-action delete" title="Hapus pengguna"
                                onclick="openDeleteModal(<?php echo e($user->id); ?>, '<?php echo e(addslashes($user->name)); ?>')"
                                <?php echo e($user->id === auth()->id() ? 'disabled' : ''); ?>>
                                <i class="ri-delete-bin-line"></i>
                            </button>

                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="ri-user-search-line"></i>
                            <h3><?php echo e($search ? 'Tidak ditemukan' : 'Belum ada pengguna'); ?></h3>
                            <p><?php echo e($search ? 'Coba kata kunci lain atau klik Reset.' : 'Klik "Input Data" untuk menambah pengguna.'); ?></p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($users->total() > 0): ?>
    <div class="table-footer">

        
        <div class="pagination-meta">
            Menampilkan
            <strong><?php echo e($users->firstItem()); ?> – <?php echo e($users->lastItem()); ?></strong>
            dari <strong><?php echo e($users->total()); ?></strong> pengguna
        </div>

        
        <?php if($users->hasPages()): ?>
        <nav class="pagination-nav" aria-label="Pagination">

            
            <?php if($users->onFirstPage()): ?>
                <span class="page-btn disabled" title="Halaman sebelumnya">
                    <i class="ri-arrow-left-s-line"></i>
                </span>
            <?php else: ?>
                <a class="page-btn" href="<?php echo e($users->previousPageUrl()); ?>" title="Halaman sebelumnya">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            <?php endif; ?>

            
            <?php
                $current  = $users->currentPage();
                $last     = $users->lastPage();
                $window   = 2; // halaman di kiri & kanan current
                $pages    = [];

                // Selalu tampil: 1, last
                // Window: current-2 s/d current+2
                for ($p = 1; $p <= $last; $p++) {
                    if (
                        $p === 1 || $p === $last ||
                        ($p >= $current - $window && $p <= $current + $window)
                    ) {
                        $pages[] = $p;
                    }
                }

                // Sisipkan ellipsis
                $rendered = [];
                $prev = null;
                foreach ($pages as $p) {
                    if ($prev !== null && $p - $prev > 1) {
                        $rendered[] = '...';
                    }
                    $rendered[] = $p;
                    $prev = $p;
                }
            ?>

            <?php $__currentLoopData = $rendered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($item === '...'): ?>
                    <span class="page-ellipsis">…</span>
                <?php elseif($item == $current): ?>
                    <span class="page-btn active"><?php echo e($item); ?></span>
                <?php else: ?>
                    <a class="page-btn"
                       href="<?php echo e($users->url($item)); ?>"
                       title="Halaman <?php echo e($item); ?>"><?php echo e($item); ?></a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($users->hasMorePages()): ?>
                <a class="page-btn" href="<?php echo e($users->nextPageUrl()); ?>" title="Halaman selanjutnya">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            <?php else: ?>
                <span class="page-btn disabled" title="Halaman selanjutnya">
                    <i class="ri-arrow-right-s-line"></i>
                </span>
            <?php endif; ?>

        </nav>
        <?php endif; ?>

    </div>
    <?php endif; ?>

</div>





<div class="modal-overlay" id="modalViewPw">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-eye-line" style="color:#7C3AED;"></i> Detail Akun
            </span>
            <button class="modal-close" onclick="closeModal('modalViewPw')">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="view-pw-box">
                <div class="view-pw-label">Nama</div>
                <div class="view-pw-value" id="vpName" style="font-family:var(--font);font-size:14px;letter-spacing:0;"></div>
            </div>
            <div class="view-pw-box">
                <div class="view-pw-label">Username</div>
                <div class="view-pw-value" id="vpUsername">
                    <span id="vpUsernameText"></span>
                    <button type="button" class="view-pw-copy" onclick="copyText('vpUsernameText', this)">
                        <i class="ri-file-copy-line"></i> Salin
                    </button>
                </div>
            </div>
            <div class="view-pw-box">
                <div class="view-pw-label">Role</div>
                <div class="view-pw-value" id="vpRole" style="font-family:var(--font);font-size:13.5px;letter-spacing:0;"></div>
            </div>
            <div class="view-pw-box">
                <div class="view-pw-label">Password</div>
                <div class="view-pw-value">
                    <span>••••••••</span>
                </div>
                <div class="view-pw-note">
                    <i class="ri-information-line"></i>
                    Password tidak dapat ditampilkan karena telah dienkripsi (bcrypt).
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalViewPw')">Tutup</button>
        </div>
    </div>
</div>





<div class="modal-overlay" id="modalTambah">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title"><i class="ri-user-add-line"></i> Tambah Pengguna</span>
            <button class="modal-close" onclick="closeModal('modalTambah')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?php echo e(route('owner.user-login.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <input type="hidden" name="_search"   value="<?php echo e($search); ?>">
            <input type="hidden" name="_per_page" value="<?php echo e($perPage); ?>">
            <div class="modal-body">

                <div class="form-group">
                    <label class="form-label" for="add_name">Nama Lengkap <span>*</span></label>
                    <input type="text" id="add_name" name="name"
                           class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="Contoh: Budi Santoso"
                           value="<?php echo e(old('name')); ?>" autocomplete="off">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><i class="ri-error-warning-line"></i> <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="add_username">Username <span>*</span></label>
                    <input type="text" id="add_username" name="username"
                           class="form-control <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="Contoh: budi.santoso"
                           value="<?php echo e(old('username')); ?>" autocomplete="off">
                    <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><i class="ri-error-warning-line"></i> <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="add_password">Password <span>*</span></label>
                    <div class="input-password-wrap">
                        <input type="password" id="add_password" name="password"
                               class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="Minimal 6 karakter">
                        <button type="button" class="toggle-pw" onclick="togglePassword('add_password', this)" tabindex="-1">
                            <i class="ri-eye-off-line"></i>
                        </button>
                    </div>
                    <p class="form-hint">Password akan dienkripsi secara otomatis.</p>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><i class="ri-error-warning-line"></i> <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="add_role">Role <span>*</span></label>
                    <select id="add_role" name="role" class="form-control <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="" disabled <?php echo e(old('role') ? '' : 'selected'); ?>>-- Pilih Role --</option>
                        <option value="owner"   <?php echo e(old('role') == 'owner'   ? 'selected' : ''); ?>>Owner</option>
                        <option value="admin"   <?php echo e(old('role') == 'admin'   ? 'selected' : ''); ?>>Admin</option>
                        <option value="manager" <?php echo e(old('role') == 'manager' ? 'selected' : ''); ?>>Manager</option>
                        <option value="staff"   <?php echo e(old('role') == 'staff'   ? 'selected' : ''); ?>>Staff</option>
                    </select>
                    <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><i class="ri-error-warning-line"></i> <?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalTambah')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>





<div class="modal-overlay" id="modalEdit">
    <div class="modal">
        <div class="modal-header">
            <span class="modal-title"><i class="ri-edit-line"></i> Edit Pengguna</span>
            <button class="modal-close" onclick="closeModal('modalEdit')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formEdit" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <input type="hidden" name="_search"   value="<?php echo e($search); ?>">
            <input type="hidden" name="_per_page" value="<?php echo e($perPage); ?>">
            <div class="modal-body">

                <div class="form-group">
                    <label class="form-label" for="edit_name">Nama Lengkap <span>*</span></label>
                    <input type="text" id="edit_name" name="name" class="form-control" placeholder="Nama lengkap" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_username">Username <span>*</span></label>
                    <input type="text" id="edit_username" name="username" class="form-control" placeholder="Username" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_password">Password Baru</label>
                    <div class="input-password-wrap">
                        <input type="password" id="edit_password" name="password"
                               class="form-control" placeholder="Kosongkan jika tidak diubah">
                        <button type="button" class="toggle-pw" onclick="togglePassword('edit_password', this)" tabindex="-1">
                            <i class="ri-eye-off-line"></i>
                        </button>
                    </div>
                    <p class="form-hint">Kosongkan jika tidak ingin mengubah password.</p>
                </div>

                <div class="form-group">
                    <label class="form-label" for="edit_role">Role <span>*</span></label>
                    <select id="edit_role" name="role" class="form-control">
                        <option value="owner">Owner</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>





<div class="modal-overlay" id="modalHapus">
    <div class="modal" style="max-width:400px;">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-delete-bin-line" style="color:#EF4444;"></i> Konfirmasi Hapus
            </span>
            <button class="modal-close" onclick="closeModal('modalHapus')"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="ri-alert-fill"></i>
                <h3>Hapus Pengguna?</h3>
                <p>Kamu akan menghapus <strong id="deleteUserName"></strong>.<br>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('modalHapus')">Batal</button>
            <form id="formHapus" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <input type="hidden" name="_search"   value="<?php echo e($search); ?>">
                <input type="hidden" name="_per_page" value="<?php echo e($perPage); ?>">
                <button type="submit" class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // ── MODAL ──
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
        if (e.key === 'Escape') document.querySelectorAll('.modal-overlay.open').forEach(m => closeModal(m.id));
    });

    // ── LIHAT PASSWORD MODAL ──
    function openViewPassword(name, username, role) {
        document.getElementById('vpName').textContent        = name;
        document.getElementById('vpUsernameText').textContent = username;
        document.getElementById('vpRole').textContent        = role.charAt(0).toUpperCase() + role.slice(1);
        openModal('modalViewPw');
    }

    function copyText(spanId, btn) {
        const text = document.getElementById(spanId).textContent;
        navigator.clipboard.writeText(text).then(() => {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="ri-check-line"></i> Disalin!';
            setTimeout(() => { btn.innerHTML = orig; }, 1800);
        });
    }

    // ── EDIT MODAL ──
    function openEditModal(id, name, username, role) {
        document.getElementById('edit_name').value     = name;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_role').value     = role;
        document.getElementById('edit_password').value = '';
        document.getElementById('formEdit').action     = '/owner/user-login/' + id;
        openModal('modalEdit');
    }

    // ── DELETE MODAL ──
    function openDeleteModal(id, name) {
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('formHapus').action           = '/owner/user-login/' + id;
        openModal('modalHapus');
    }

    // ── TOGGLE PASSWORD ──
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text'; icon.className = 'ri-eye-line';
        } else {
            input.type = 'password'; icon.className = 'ri-eye-off-line';
        }
    }

    // ── AUTO-OPEN MODAL TAMBAH jika ada validation error ──
    <?php if($errors->any()): ?>
    document.addEventListener('DOMContentLoaded', () => openModal('modalTambah'));
    <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\paralkesplus\resources\views/owner/user-login.blade.php ENDPATH**/ ?>