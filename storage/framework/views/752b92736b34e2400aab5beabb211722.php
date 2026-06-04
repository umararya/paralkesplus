
<!DOCTYPE html>
<html lang="id" class="<?php echo e(session('theme', 'light')); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Paralkes+'); ?> — Admin Panel</title>

    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        :root {
            --font: 'Inter', sans-serif;
            --bg-primary:     #F8FAFC;
            --bg-secondary:   #FFFFFF;
            --bg-sidebar:     #FFFFFF;
            --bg-card:        #FFFFFF;
            --bg-hover:       #F1F5F9;
            --text-primary:   #0F172A;
            --text-secondary: #64748B;
            --text-muted:     #94A3B8;
            --border:         #E2E8F0;
            --shadow:         0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md:      0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
            --brand-50:  #EFF6FF;
            --brand-100: #DBEAFE;
            --brand-500: #1D6FA4;
            --brand-600: #155E8C;
            --brand-700: #0D4B73;
            --accent:    #2E9E6B;
            --sidebar-width: 260px;
            --sidebar-collapsed: 72px;
            --topbar-height: 64px;
        }

        html.dark {
            --bg-primary:     #0D1117;
            --bg-secondary:   #161B22;
            --bg-sidebar:     #161B22;
            --bg-card:        #1C2333;
            --bg-hover:       #21262D;
            --text-primary:   #E6EDF3;
            --text-secondary: #8B949E;
            --text-muted:     #6E7681;
            --border:         #30363D;
            --shadow:         0 1px 3px rgba(0,0,0,0.3), 0 1px 2px rgba(0,0,0,0.2);
            --shadow-md:      0 4px 6px -1px rgba(0,0,0,0.4), 0 2px 4px -1px rgba(0,0,0,0.3);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: var(--font);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            transition: background-color 0.3s, color 0.3s;
        }

        .layout { display: flex; height: 100vh; overflow: hidden; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease, background 0.3s;
            flex-shrink: 0;
            overflow: hidden;
            z-index: 100;
            box-shadow: var(--shadow);
        }
        .sidebar.collapsed { width: var(--sidebar-collapsed); }

        .sidebar-logo {
            display: flex; align-items: center; gap: 12px;
            padding: 20px 18px;
            border-bottom: 1px solid var(--border);
            min-height: var(--topbar-height);
            text-decoration: none;
        }
        .sidebar-logo img {
            width: 36px; height: 36px;
            object-fit: contain; flex-shrink: 0; border-radius: 8px;
        }
        .sidebar-logo-text { display: flex; flex-direction: column; overflow: hidden; }
        .sidebar-logo-text .brand-name {
            font-size: 15px; font-weight: 700;
            color: var(--brand-500); white-space: nowrap; letter-spacing: -0.3px;
        }
        .sidebar-logo-text .brand-sub { font-size: 11px; color: var(--text-muted); white-space: nowrap; }

        .sidebar.collapsed .sidebar-logo-text,
        .sidebar.collapsed .nav-label,
        .sidebar.collapsed .nav-section-title,
        .sidebar.collapsed .sidebar-footer-text,
        .sidebar.collapsed .nav-dropdown-arrow { display: none; }

        .sidebar-nav {
            flex: 1; overflow-y: auto; padding: 12px 0;
            scrollbar-width: thin; scrollbar-color: var(--border) transparent;
        }

        .nav-section-title {
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: var(--text-muted); padding: 12px 20px 6px;
        }

        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 18px; margin: 1px 8px;
            border-radius: 8px; cursor: pointer;
            text-decoration: none; color: var(--text-secondary);
            font-size: 13.5px; font-weight: 500;
            transition: all 0.2s; white-space: nowrap; overflow: hidden;
        }
        .nav-item:hover { background: var(--bg-hover); color: var(--text-primary); }
        .nav-item.active { background: var(--brand-50); color: var(--brand-500); }
        html.dark .nav-item.active { background: rgba(29,111,164,0.15); color: #60A5FA; }
        .nav-item i { font-size: 18px; flex-shrink: 0; width: 22px; text-align: center; }

        /* ── NAV DROPDOWN ── */
        .nav-dropdown-trigger {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 18px; margin: 1px 8px;
            border-radius: 8px; cursor: pointer;
            color: var(--text-secondary);
            font-size: 13.5px; font-weight: 500;
            transition: all 0.2s; white-space: nowrap; overflow: hidden;
            user-select: none;
        }
        .nav-dropdown-trigger:hover { background: var(--bg-hover); color: var(--text-primary); }
        .nav-dropdown-trigger.open { background: var(--brand-50); color: var(--brand-500); }
        html.dark .nav-dropdown-trigger.open { background: rgba(29,111,164,0.15); color: #60A5FA; }
        .nav-dropdown-trigger i:first-child { font-size: 18px; flex-shrink: 0; width: 22px; text-align: center; }
        .nav-dropdown-trigger .nav-label { flex: 1; }
        .nav-dropdown-arrow { font-size: 16px; transition: transform 0.25s ease; margin-left: auto; flex-shrink: 0; }
        .nav-dropdown-trigger.open .nav-dropdown-arrow { transform: rotate(180deg); }

        .nav-dropdown-menu { overflow: hidden; max-height: 0; transition: max-height 0.3s ease; }
        .nav-dropdown-menu.open { max-height: 500px; }
        .nav-dropdown-menu .nav-item { padding-left: 52px; font-size: 13px; }
        .sidebar.collapsed .nav-dropdown-menu { display: none; }

        /* ── SIDEBAR FOOTER ── */
        .sidebar-footer { border-top: 1px solid var(--border); padding: 14px 10px; }
        .sidebar-footer-user {
            display: flex; align-items: center; gap: 10px;
            padding: 8px; border-radius: 8px;
            transition: background 0.2s; cursor: default;
        }
        .sidebar-footer-user:hover { background: var(--bg-hover); }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, var(--brand-500), var(--accent));
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 13px; font-weight: 600; flex-shrink: 0;
        }
        .sidebar-footer-text { overflow: hidden; }
        .sidebar-footer-text .user-name {
            font-size: 13px; font-weight: 600; color: var(--text-primary);
            white-space: nowrap; text-overflow: ellipsis; overflow: hidden;
        }
        .sidebar-footer-text .user-role { font-size: 11px; color: var(--text-muted); }

        /* ── MAIN CONTENT ── */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

        /* ── TOPBAR ── */
        .topbar {
            height: var(--topbar-height);
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 24px; gap: 12px;
            flex-shrink: 0; box-shadow: var(--shadow);
            transition: background 0.3s;
        }
        .topbar-toggle {
            background: none; border: none; cursor: pointer;
            color: var(--text-secondary); font-size: 20px;
            padding: 6px; border-radius: 6px;
            display: flex; align-items: center; transition: all 0.2s;
        }
        .topbar-toggle:hover { background: var(--bg-hover); color: var(--text-primary); }
        .topbar-breadcrumb { flex: 1; display: flex; align-items: center; gap: 6px; }
        .breadcrumb-item { font-size: 13px; color: var(--text-muted); }
        .breadcrumb-item.active { color: var(--text-primary); font-weight: 600; }
        .breadcrumb-separator { color: var(--text-muted); font-size: 12px; }
        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .topbar-btn {
            width: 36px; height: 36px; border: none; background: none;
            cursor: pointer; color: var(--text-secondary); font-size: 18px;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s; position: relative;
        }
        .topbar-btn:hover { background: var(--bg-hover); color: var(--text-primary); }

        /* Badge angka notifikasi */
        .notif-badge {
            position: absolute; top: 4px; right: 4px;
            min-width: 16px; height: 16px;
            background: #EF4444;
            border-radius: 99px;
            border: 2px solid var(--bg-secondary);
            font-size: 9px; font-weight: 700; color: #fff;
            display: none; align-items: center; justify-content: center;
            padding: 0 3px; line-height: 1;
        }
        .notif-badge.show { display: flex; }

        /* ── DATETIME WIDGET ── */
        .topbar-datetime {
            display: flex; flex-direction: column; align-items: flex-end;
            padding: 5px 10px; border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            transition: background 0.2s;
            line-height: 1.3;
            white-space: nowrap;
        }
        .topbar-datetime:hover { background: var(--bg-hover); }
        .datetime-time {
            font-size: 14px; font-weight: 700;
            color: var(--text-primary); letter-spacing: 0.3px;
            font-variant-numeric: tabular-nums;
        }
        .datetime-date {
            font-size: 10.5px; color: var(--text-muted); font-weight: 500;
        }
        @media (max-width: 640px) {
            .topbar-datetime { display: none; }
        }

        /* ── NOTIFIKASI DROPDOWN ── */
        .notif-dropdown {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 320px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow-md);
            z-index: 999;
            display: none;
            overflow: hidden;
        }
        .notif-dropdown.open { display: block; animation: fadeDown 0.15s ease; }
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .notif-header {
            padding: 14px 16px 10px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .notif-header-title {
            font-size: 13px; font-weight: 700; color: var(--text-primary);
            display: flex; align-items: center; gap: 7px;
        }
        .notif-header-title i { color: #F59E0B; font-size: 16px; }
        .notif-header-count {
            font-size: 11px; background: #FEF3C7; color: #92400E;
            border-radius: 99px; padding: 2px 8px; font-weight: 700;
        }
        html.dark .notif-header-count { background: rgba(146,64,14,0.2); color: #FCD34D; }

        .notif-list {
            max-height: 280px; overflow-y: auto;
            scrollbar-width: thin; scrollbar-color: var(--border) transparent;
        }

        /* Item notifikasi — klikable */
        .notif-item {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 11px 16px;
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
            cursor: pointer;
            text-decoration: none;
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: var(--bg-hover); }
        .notif-item:hover .notif-name { color: var(--brand-500); }

        .notif-icon {
            width: 34px; height: 34px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .notif-icon.danger  { background: #FFF1F2; color: #E11D48; }
        .notif-icon.warning { background: #FFFBEB; color: #B45309; }
        html.dark .notif-icon.danger  { background: rgba(225,29,72,0.12); color: #FB7185; }
        html.dark .notif-icon.warning { background: rgba(180,83,9,0.12); color: #FCD34D; }

        .notif-text { flex: 1; min-width: 0; }
        .notif-name {
            font-size: 13px; font-weight: 600; color: var(--text-primary);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            transition: color 0.15s;
        }
        .notif-desc {
            font-size: 12px; color: var(--text-muted); margin-top: 2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .notif-time {
            font-size: 11px; font-weight: 600;
            padding: 2px 7px; border-radius: 99px;
            white-space: nowrap; flex-shrink: 0;
            margin-top: 2px;
        }
        .notif-time.danger  { background: #FFF1F2; color: #E11D48; }
        .notif-time.warning { background: #FFFBEB; color: #B45309; }
        html.dark .notif-time.danger  { background: rgba(225,29,72,0.12); color: #FB7185; }
        html.dark .notif-time.warning { background: rgba(180,83,9,0.12); color: #FCD34D; }

        /* Tooltip hint klik */
        .notif-item-hint {
            font-size: 10.5px; color: var(--brand-500);
            margin-top: 3px; display: flex; align-items: center; gap: 3px;
            opacity: 0; transition: opacity 0.15s;
        }
        .notif-item:hover .notif-item-hint { opacity: 1; }

        .notif-empty {
            text-align: center; padding: 28px 16px;
            color: var(--text-muted); font-size: 13px;
        }
        .notif-empty i { font-size: 32px; display: block; margin-bottom: 8px; }

        .notif-footer {
            padding: 10px 16px;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        .notif-footer a {
            font-size: 12.5px; color: var(--brand-500); text-decoration: none;
            font-weight: 600; display: inline-flex; align-items: center; gap: 5px;
        }
        .notif-footer a:hover { text-decoration: underline; }

        .theme-toggle {
            width: 36px; height: 36px;
            border: 1px solid var(--border); background: var(--bg-card);
            cursor: pointer; color: var(--text-secondary); font-size: 17px;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .theme-toggle:hover { background: var(--bg-hover); color: var(--brand-500); }

        .topbar-user {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 10px; border-radius: 8px; cursor: pointer;
            transition: background 0.2s;
            border: 1px solid var(--border); background: var(--bg-card);
            position: relative;
        }
        .topbar-user:hover { background: var(--bg-hover); }
        .topbar-user .user-name-sm { font-size: 13px; font-weight: 500; color: var(--text-primary); }

        /* User Dropdown */
        .dropdown-menu {
            position: absolute; top: calc(100% + 8px); right: 0;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; box-shadow: var(--shadow-md);
            min-width: 180px; z-index: 999; overflow: hidden; display: none;
        }
        .dropdown-menu.open { display: block; animation: fadeDown 0.15s ease; }
        .dropdown-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; font-size: 13px; color: var(--text-primary);
            text-decoration: none; cursor: pointer; transition: background 0.15s;
            border: none; background: none; width: 100%; text-align: left;
        }
        .dropdown-item:hover { background: var(--bg-hover); }
        .dropdown-item.danger { color: #EF4444; }
        .dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }

        /* ── PAGE CONTENT ── */
        .page-content {
            flex: 1; overflow-y: auto; padding: 28px;
            scrollbar-width: thin; scrollbar-color: var(--border) transparent;
        }

        .card {
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 12px; padding: 24px;
            box-shadow: var(--shadow); transition: background 0.3s, border-color 0.3s;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed; left: -260px; height: 100vh;
                transition: left 0.3s ease, width 0.3s ease;
            }
            .sidebar.mobile-open { left: 0; }
            .sidebar-overlay {
                display: none; position: fixed; inset: 0;
                background: rgba(0,0,0,0.4); z-index: 99;
            }
            .sidebar-overlay.active { display: block; }
        }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }
    </style>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<div class="layout">

    
    <aside class="sidebar" id="sidebar">

        <a href="<?php echo e(route('penyewaan.index')); ?>" class="sidebar-logo">
            <img src="<?php echo e(asset('images/logo-paralkes.png')); ?>" alt="Paralkes+"
                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><rect width=%2240%22 height=%2240%22 rx=%228%22 fill=%22%231D6FA4%22/><text x=%2250%25%22 y=%2255%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2214%22 font-weight=%22bold%22>P+</text></svg>'">
            <div class="sidebar-logo-text">
                <span class="brand-name">Paralkes<span style="color: var(--accent);">+</span></span>
                <span class="brand-sub">Admin Panel</span>
            </div>
        </a>

        <nav class="sidebar-nav">

            <span class="nav-section-title">Manajemen</span>

            <a href="<?php echo e(route('penyewaan.index')); ?>"
               class="nav-item <?php echo e(request()->routeIs('penyewaan.*') ? 'active' : ''); ?>">
                <i class="ri-store-2-line"></i>
                <span class="nav-label">Penyewaan</span>
            </a>

            <a href="<?php echo e(route('pembelian.index')); ?>"
               class="nav-item <?php echo e(request()->routeIs('pembelian.*') ? 'active' : ''); ?>">
                <i class="ri-shopping-cart-2-line"></i>
                <span class="nav-label">Pembelian Barang</span>
            </a>

            <a href="<?php echo e(route('penjualan.index')); ?>"
               class="nav-item <?php echo e(request()->routeIs('penjualan.*') ? 'active' : ''); ?>">
                <i class="ri-exchange-dollar-line"></i>
                <span class="nav-label">Penjualan</span>
            </a>

            <a href="<?php echo e(route('inventory.index')); ?>"
               class="nav-item <?php echo e(request()->routeIs('inventory.*') ? 'active' : ''); ?>">
                <i class="ri-box-3-line"></i>
                <span class="nav-label">Inventory</span>
            </a>

            <?php if(auth()->check() && auth()->user()->role === 'owner'): ?>
            <span class="nav-section-title" style="margin-top:8px;">Owner</span>

            <div class="nav-dropdown-trigger <?php echo e(request()->routeIs('owner.*') ? 'open' : ''); ?>"
                 id="ownerDropdownTrigger"
                 onclick="toggleNavDropdown('ownerDropdown', 'ownerDropdownTrigger')">
                <i class="ri-shield-user-line"></i>
                <span class="nav-label">Owner</span>
                <i class="ri-arrow-down-s-line nav-dropdown-arrow"></i>
            </div>

            
            
            
            <div class="nav-dropdown-menu <?php echo e(request()->routeIs('owner.*') ? 'open' : ''); ?>"
                 id="ownerDropdown">

                
                <a href="<?php echo e(route('owner.user-login')); ?>"
                   class="nav-item <?php echo e(request()->routeIs('owner.user-login') ? 'active' : ''); ?>">
                    <i class="ri-login-circle-line"></i>
                    <span class="nav-label">User Login</span>
                </a>

                
                <a href="<?php echo e(route('owner.monitor')); ?>"
                   class="nav-item <?php echo e(request()->routeIs('owner.monitor*') ? 'active' : ''); ?>">
                    <i class="ri-user-search-line"></i>
                    <span class="nav-label">Monitoring User</span>
                </a>

            </div>
            
            <?php endif; ?>

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-footer-user">
                <div class="user-avatar">
                    <?php echo e(strtoupper(substr(auth()->user()->name ?? 'A', 0, 1))); ?>

                </div>
                <div class="sidebar-footer-text">
                    <div class="user-name"><?php echo e(auth()->user()->name ?? 'Admin'); ?></div>
                    <div class="user-role"><?php echo e(ucfirst(auth()->user()->role ?? 'staff')); ?></div>
                </div>
            </div>
        </div>

    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    
    <div class="main-content">

        <header class="topbar">
            <button class="topbar-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
                <i class="ri-menu-line"></i>
            </button>
            <div class="topbar-breadcrumb">
                <span class="breadcrumb-item">Paralkes+</span>
                <i class="ri-arrow-right-s-line breadcrumb-separator"></i>
                <span class="breadcrumb-item active"><?php echo $__env->yieldContent('breadcrumb', 'Penyewaan'); ?></span>
            </div>
            <div class="topbar-actions">

                
                <div class="topbar-datetime" id="topbarDatetime">
                    <span class="datetime-time" id="datetimeClock">00:00:00</span>
                    <span class="datetime-date" id="datetimeDate">—</span>
                </div>
                

                
                <div style="position:relative;" id="notifWrapper">
                    <button class="topbar-btn" title="Notifikasi" onclick="toggleNotifDropdown()" id="notifBtn">
                        <i class="ri-notification-3-line"></i>
                        <span class="notif-badge" id="notifBadge"></span>
                    </button>

                    <div class="notif-dropdown" id="notifDropdown">
                        <div class="notif-header">
                            <span class="notif-header-title">
                                <i class="ri-alarm-warning-line"></i> Segera Konfirmasi
                            </span>
                            <span class="notif-header-count" id="notifHeaderCount">0</span>
                        </div>
                        <div class="notif-list" id="notifList">
                            <div class="notif-empty">
                                <i class="ri-loader-4-line" style="animation:spin 1s linear infinite;"></i>
                                Memuat...
                            </div>
                        </div>
                        <div class="notif-footer">
                            <a href="<?php echo e(route('penyewaan.index')); ?>#monitoring" onclick="closeNotifDropdown()">
                                <i class="ri-radar-line"></i> Buka Monitoring Penyewaan
                            </a>
                        </div>
                    </div>
                </div>
                

                <button class="theme-toggle" onclick="toggleTheme()" title="Ganti tema" id="themeBtn">
                    <i class="ri-moon-line" id="themeIcon"></i>
                </button>

                <div class="topbar-user" onclick="toggleDropdown()" id="userDropdownTrigger">
                    <div class="user-avatar" style="width:28px;height:28px;font-size:12px;">
                        <?php echo e(strtoupper(substr(auth()->user()->name ?? 'A', 0, 1))); ?>

                    </div>
                    <span class="user-name-sm"><?php echo e(auth()->user()->name ?? 'Admin'); ?></span>
                    <i class="ri-arrow-down-s-line" style="font-size:16px;color:var(--text-muted);"></i>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="#" class="dropdown-item">
                            <i class="ri-user-line"></i> Profil Saya
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="dropdown-item danger">
                                <i class="ri-logout-box-r-line"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-content">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

    </div>
</div>

<style>
    @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</style>

<script>
    // ── THEME ──
    const html      = document.documentElement;
    const themeIcon = document.getElementById('themeIcon');
    const saved     = localStorage.getItem('paralkes_theme') || '<?php echo e(session('theme', 'light')); ?>';

    function applyTheme(t) {
        if (t === 'dark') { html.classList.add('dark'); themeIcon.className = 'ri-sun-line'; }
        else { html.classList.remove('dark'); themeIcon.className = 'ri-moon-line'; }
        localStorage.setItem('paralkes_theme', t);
        fetch('/theme', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ theme: t })
        }).catch(()=>{});
    }
    function toggleTheme() { applyTheme(html.classList.contains('dark') ? 'light' : 'dark'); }
    applyTheme(saved);

    // ── SIDEBAR ──
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
        }
    }
    function closeSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    }
    if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
    }

    // ── NAV DROPDOWN ──
    function toggleNavDropdown(menuId, triggerId) {
        const menu    = document.getElementById(menuId);
        const trigger = document.getElementById(triggerId);
        const isOpen  = menu.classList.contains('open');
        document.querySelectorAll('.nav-dropdown-menu').forEach(m => m.classList.remove('open'));
        document.querySelectorAll('.nav-dropdown-trigger').forEach(t => t.classList.remove('open'));
        if (!isOpen) {
            menu.classList.add('open');
            trigger.classList.add('open');
        }
        localStorage.setItem('navDropdown_' + menuId, !isOpen ? 'open' : 'closed');
    }
    document.addEventListener('DOMContentLoaded', function () {
        ['ownerDropdown'].forEach(function(menuId) {
            const state   = localStorage.getItem('navDropdown_' + menuId);
            if (state === 'open') {
                const menu    = document.getElementById(menuId);
                const trigger = document.getElementById(menuId + 'Trigger');
                if (menu)    menu.classList.add('open');
                if (trigger) trigger.classList.add('open');
            }
        });
    });

    // ── USER DROPDOWN ──
    const userDropdown = document.getElementById('userDropdown');
    function toggleDropdown() { userDropdown.classList.toggle('open'); }
    document.addEventListener('click', function(e) {
        if (!document.getElementById('userDropdownTrigger').contains(e.target)) {
            userDropdown.classList.remove('open');
        }
    });

    // ── DATETIME WIDGET ──
    const HARI_ID  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const BULAN_ID = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    function updateClock() {
        const now  = new Date();
        const hh   = String(now.getHours()).padStart(2, '0');
        const mm   = String(now.getMinutes()).padStart(2, '0');
        const ss   = String(now.getSeconds()).padStart(2, '0');
        const hari = HARI_ID[now.getDay()];
        const tgl  = now.getDate();
        const bln  = BULAN_ID[now.getMonth()];
        const thn  = now.getFullYear();

        document.getElementById('datetimeClock').textContent = `${hh}:${mm}:${ss}`;
        document.getElementById('datetimeDate').textContent  = `${hari}, ${tgl} ${bln} ${thn}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── NOTIFIKASI ──
    const notifDropdown    = document.getElementById('notifDropdown');
    const notifBadge       = document.getElementById('notifBadge');
    const notifList        = document.getElementById('notifList');
    const notifHeaderCount = document.getElementById('notifHeaderCount');

    const penyewaanMonitoringUrl = '<?php echo e(route("penyewaan.index")); ?>#monitoring';
    const isOnPenyewaanPage      = <?php echo e(request()->routeIs('penyewaan.index') ? 'true' : 'false'); ?>;

    function goToMonitoring() {
        closeNotifDropdown();
        if (isOnPenyewaanPage) {
            if (typeof openMonitoring === 'function') openMonitoring();
        } else {
            window.location.href = penyewaanMonitoringUrl;
        }
    }

    function toggleNotifDropdown() {
        const isOpen = notifDropdown.classList.contains('open');
        userDropdown.classList.remove('open');
        if (isOpen) {
            closeNotifDropdown();
        } else {
            notifDropdown.classList.add('open');
            loadNotifikasi();
        }
    }

    function closeNotifDropdown() {
        notifDropdown.classList.remove('open');
    }

    function loadNotifikasi() {
        fetch('<?php echo e(route("penyewaan.notifikasi")); ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(res => {
            const count = res.count || 0;
            const items = res.items || [];

            notifHeaderCount.textContent = count;
            if (count > 0) {
                notifBadge.textContent = count > 9 ? '9+' : count;
                notifBadge.classList.add('show');
            } else {
                notifBadge.classList.remove('show');
            }

            if (!items.length) {
                notifList.innerHTML = `
                    <div class="notif-empty">
                        <i class="ri-checkbox-circle-line" style="color:#16A34A;"></i>
                        Semua penyewaan masih aman.
                    </div>`;
                return;
            }

            notifList.innerHTML = items.map(item => {
                const isDanger   = item.sisa_hari <= 1;
                const colorClass = isDanger ? 'danger' : 'warning';
                const iconClass  = isDanger ? 'ri-alarm-warning-line' : 'ri-time-line';

                return `
                <div class="notif-item" onclick="goToMonitoring()" title="Klik untuk buka monitoring">
                    <div class="notif-icon ${colorClass}">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="notif-text">
                        <div class="notif-name">${item.nama}</div>
                        <div class="notif-desc">${item.barang}</div>
                        <div class="notif-desc" style="margin-top:2px;">
                            <i class="ri-calendar-line" style="font-size:11px;"></i>
                            Deadline: ${item.tgl_selesai}
                        </div>
                        <div class="notif-item-hint">
                            <i class="ri-radar-line" style="font-size:11px;"></i> Buka monitoring
                        </div>
                    </div>
                    <span class="notif-time ${colorClass}">${item.sisa_label}</span>
                </div>`;
            }).join('');
        })
        .catch(() => {
            notifList.innerHTML = `
                <div class="notif-empty" style="color:#EF4444;">
                    <i class="ri-wifi-off-line"></i>
                    Gagal memuat notifikasi.
                </div>`;
        });
    }

    // Tutup notif dropdown saat klik di luar
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('notifWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeNotifDropdown();
        }
    });

    // Load pertama kali
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifikasi();
        setInterval(loadNotifikasi, 60000);
    });
</script>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\laragon\www\paralkesplus\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>