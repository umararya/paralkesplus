{{-- resources/views/admin/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Paralkes+') — Admin Panel</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
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
        .badge-dot {
            position: absolute; top: 6px; right: 6px;
            width: 7px; height: 7px; background: #EF4444;
            border-radius: 50%; border: 2px solid var(--bg-secondary);
        }

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

        /* Dropdown */
        .dropdown-menu {
            position: absolute; top: calc(100% + 8px); right: 0;
            background: var(--bg-card); border: 1px solid var(--border);
            border-radius: 10px; box-shadow: var(--shadow-md);
            min-width: 180px; z-index: 999; overflow: hidden; display: none;
        }
        .dropdown-menu.open { display: block; animation: fadeDown 0.15s ease; }
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
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

    @stack('styles')
</head>
<body>

<div class="layout">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar" id="sidebar">

        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('images/logo-paralkes.png') }}" alt="Paralkes+"
                 onerror="this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 40 40%22><rect width=%2240%22 height=%2240%22 rx=%228%22 fill=%22%231D6FA4%22/><text x=%2250%25%22 y=%2255%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2214%22 font-weight=%22bold%22>P+</text></svg>'">
            <div class="sidebar-logo-text">
                <span class="brand-name">Paralkes<span style="color: var(--accent);">+</span></span>
                <span class="brand-sub">Admin Panel</span>
            </div>
        </a>

        <nav class="sidebar-nav">

            {{-- UTAMA --}}
            <span class="nav-section-title">Utama</span>
            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="ri-dashboard-3-line"></i>
                <span class="nav-label">Dashboard</span>
            </a>

            {{-- MANAJEMEN --}}
            <span class="nav-section-title" style="margin-top:8px;">Manajemen</span>
            <a href="#" class="nav-item">
                <i class="ri-user-line"></i>
                <span class="nav-label">Pengguna</span>
            </a>
            <a href="#" class="nav-item">
                <i class="ri-stethoscope-line"></i>
                <span class="nav-label">Tenaga Kesehatan</span>
            </a>
            <a href="#" class="nav-item">
                <i class="ri-file-list-3-line"></i>
                <span class="nav-label">Laporan</span>
            </a>
            <a href="#" class="nav-item">
                <i class="ri-calendar-line"></i>
                <span class="nav-label">Jadwal</span>
            </a>

            {{-- Penyewaan --}}
            <a href="{{ route('penyewaan.index') }}"
               class="nav-item {{ request()->routeIs('penyewaan.*') ? 'active' : '' }}">
                <i class="ri-store-2-line"></i>
                <span class="nav-label">Penyewaan</span>
            </a>

            {{-- Pembelian Barang --}}
            <a href="{{ route('pembelian.index') }}"
               class="nav-item {{ request()->routeIs('pembelian.*') ? 'active' : '' }}">
                <i class="ri-shopping-cart-2-line"></i>
                <span class="nav-label">Pembelian Barang</span>
            </a>

            {{-- Penjualan --}}
            <a href="{{ route('penjualan.index') }}"
               class="nav-item {{ request()->routeIs('penjualan.*') ? 'active' : '' }}">
                <i class="ri-exchange-dollar-line"></i>
                <span class="nav-label">Penjualan</span>
            </a>

            {{-- Inventory --}}
            <a href="{{ route('inventory.index') }}"
               class="nav-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                <i class="ri-box-3-line"></i>
                <span class="nav-label">Inventory</span>
            </a>

            {{-- OWNER — hanya tampil jika role = owner --}}
            @if(auth()->check() && auth()->user()->role === 'owner')
            <span class="nav-section-title" style="margin-top:8px;">Owner</span>

            <div class="nav-dropdown-trigger {{ request()->routeIs('owner.*') ? 'open' : '' }}"
                 id="ownerDropdownTrigger"
                 onclick="toggleNavDropdown('ownerDropdown', 'ownerDropdownTrigger')">
                <i class="ri-shield-user-line"></i>
                <span class="nav-label">Owner</span>
                <i class="ri-arrow-down-s-line nav-dropdown-arrow"></i>
            </div>

            <div class="nav-dropdown-menu {{ request()->routeIs('owner.*') ? 'open' : '' }}"
                 id="ownerDropdown">
                <a href="{{ route('owner.user-login') }}"
                   class="nav-item {{ request()->routeIs('owner.user-login') ? 'active' : '' }}">
                    <i class="ri-login-circle-line"></i>
                    <span class="nav-label">User Login</span>
                </a>
            </div>
            @endif

            {{-- SISTEM --}}
            <span class="nav-section-title" style="margin-top:8px;">Sistem</span>
            <a href="#" class="nav-item">
                <i class="ri-settings-3-line"></i>
                <span class="nav-label">Pengaturan</span>
            </a>

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-footer-user">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="sidebar-footer-text">
                    <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="user-role">{{ ucfirst(auth()->user()->role ?? 'staff') }}</div>
                </div>
            </div>
        </div>

    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="main-content">

        <header class="topbar">
            <button class="topbar-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
                <i class="ri-menu-line"></i>
            </button>
            <div class="topbar-breadcrumb">
                <span class="breadcrumb-item">Paralkes+</span>
                <i class="ri-arrow-right-s-line breadcrumb-separator"></i>
                <span class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</span>
            </div>
            <div class="topbar-actions">
                <button class="topbar-btn" title="Notifikasi">
                    <i class="ri-notification-3-line"></i>
                    <span class="badge-dot"></span>
                </button>
                <button class="theme-toggle" onclick="toggleTheme()" title="Ganti tema" id="themeBtn">
                    <i class="ri-moon-line" id="themeIcon"></i>
                </button>
                <div class="topbar-user" onclick="toggleDropdown()" id="userDropdownTrigger">
                    <div class="user-avatar" style="width:28px;height:28px;font-size:12px;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="user-name-sm">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <i class="ri-arrow-down-s-line" style="font-size:16px;color:var(--text-muted);"></i>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="#" class="dropdown-item">
                            <i class="ri-user-line"></i> Profil Saya
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="ri-settings-3-line"></i> Pengaturan
                        </a>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item danger">
                                <i class="ri-logout-box-r-line"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="page-content">
            @yield('content')
        </main>

    </div>
</div>

<script>
    // ── THEME ──
    const html = document.documentElement;
    const themeIcon = document.getElementById('themeIcon');
    const saved = localStorage.getItem('paralkes_theme') || '{{ session('theme', 'light') }}';

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
    function toggleTheme() {
        applyTheme(html.classList.contains('dark') ? 'light' : 'dark');
    }
    applyTheme(saved);

    // ── SIDEBAR ──
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');

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
            const state = localStorage.getItem('navDropdown_' + menuId);
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
</script>

@stack('scripts')
</body>
</html>