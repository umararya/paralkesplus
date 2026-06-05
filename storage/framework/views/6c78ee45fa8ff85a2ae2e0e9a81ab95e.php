

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('breadcrumb', 'Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ===== PAGE HEADER ===== */
    .page-header {
        margin-bottom: 28px;
    }

    .page-header h1 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.3px;
    }

    .page-header p {
        font-size: 13.5px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    /* ===== STATS GRID ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        box-shadow: var(--shadow);
        transition: all 0.25s;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--card-accent, var(--brand-500));
        border-radius: 12px 12px 0 0;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 14px;
        background: var(--icon-bg, rgba(29, 111, 164, 0.1));
        color: var(--icon-color, var(--brand-500));
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .stat-value {
        font-size: 26px;
        font-weight: 700;
        color: var(--text-primary);
        letter-spacing: -0.5px;
    }

    .stat-meta {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 6px;
    }

    /* ===== EMPTY STATE ===== */
    .empty-section {
        background: var(--bg-card);
        border: 1px dashed var(--border);
        border-radius: 12px;
        padding: 60px 24px;
        text-align: center;
        color: var(--text-muted);
    }

    .empty-icon {
        font-size: 48px;
        margin-bottom: 14px;
        opacity: 0.4;
    }

    .empty-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }

    .empty-desc {
        font-size: 13px;
        color: var(--text-muted);
    }

    /* ===== BOTTOM GRID ===== */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 24px;
    }

    @media (max-width: 900px) {
        .bottom-grid { grid-template-columns: 1fr; }
    }

    .section-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title i {
        color: var(--brand-500);
        font-size: 16px;
    }

    /* ===== WELCOME BANNER ===== */
    .welcome-banner {
        background: linear-gradient(135deg, var(--brand-500) 0%, var(--brand-700) 60%, var(--accent) 100%);
        border-radius: 14px;
        padding: 28px 32px;
        margin-bottom: 28px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        right: -20px;
        top: -30px;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        right: 60px;
        bottom: -40px;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }

    .welcome-banner h2 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .welcome-banner p {
        font-size: 13.5px;
        opacity: 0.85;
    }

    .welcome-banner .banner-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255,255,255,0.18);
        backdrop-filter: blur(10px);
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 12px;
        border: 1px solid rgba(255,255,255,0.2);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

    
    <div class="welcome-banner">
        <span class="banner-badge">
            <i class="ri-shield-check-line"></i>
            Admin Panel
        </span>
        <h2>Selamat datang, <?php echo e(auth()->user()->name ?? 'Admin'); ?>! 👋</h2>
        <p>Panel administrasi Paralkes+ — Sistem Informasi Paramedis & Tenaga Kesehatan.</p>
    </div>

    
    <div class="page-header">
        <h1>Dashboard</h1>
        <p>Ringkasan data & aktivitas sistem — <?php echo e(now()->translatedFormat('l, d F Y')); ?></p>
    </div>

    
    <div class="stats-grid">
        <div class="stat-card" style="--card-accent:#1D6FA4; --icon-bg:rgba(29,111,164,0.1); --icon-color:#1D6FA4;">
            <div class="stat-icon"><i class="ri-user-line"></i></div>
            <div class="stat-label">Total Pengguna</div>
            <div class="stat-value">—</div>
            <div class="stat-meta">Belum ada data</div>
        </div>

        <div class="stat-card" style="--card-accent:#2E9E6B; --icon-bg:rgba(46,158,107,0.1); --icon-color:#2E9E6B;">
            <div class="stat-icon"><i class="ri-stethoscope-line"></i></div>
            <div class="stat-label">Tenaga Kesehatan</div>
            <div class="stat-value">—</div>
            <div class="stat-meta">Belum ada data</div>
        </div>

        <div class="stat-card" style="--card-accent:#F59E0B; --icon-bg:rgba(245,158,11,0.1); --icon-color:#F59E0B;">
            <div class="stat-icon"><i class="ri-file-list-3-line"></i></div>
            <div class="stat-label">Laporan</div>
            <div class="stat-value">—</div>
            <div class="stat-meta">Belum ada data</div>
        </div>

        <div class="stat-card" style="--card-accent:#8B5CF6; --icon-bg:rgba(139,92,246,0.1); --icon-color:#8B5CF6;">
            <div class="stat-icon"><i class="ri-calendar-check-line"></i></div>
            <div class="stat-label">Jadwal Aktif</div>
            <div class="stat-value">—</div>
            <div class="stat-meta">Belum ada data</div>
        </div>
    </div>

    
    <div class="bottom-grid">

        
        <div class="card">
            <div class="section-title">
                <i class="ri-time-line"></i>
                Aktivitas Terbaru
            </div>
            <div class="empty-section" style="padding:40px 16px;">
                <div class="empty-icon"><i class="ri-inbox-line"></i></div>
                <div class="empty-title">Belum ada aktivitas</div>
                <div class="empty-desc">Aktivitas sistem akan muncul di sini</div>
            </div>
        </div>

        
        <div class="card">
            <div class="section-title">
                <i class="ri-megaphone-line"></i>
                Pengumuman
            </div>
            <div class="empty-section" style="padding:40px 16px;">
                <div class="empty-icon"><i class="ri-notification-line"></i></div>
                <div class="empty-title">Tidak ada pengumuman</div>
                <div class="empty-desc">Pengumuman sistem akan ditampilkan di sini</div>
            </div>
        </div>

    </div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\paralkesplus\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>