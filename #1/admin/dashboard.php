<?php
$page_title  = 'Dashboard';
$active_menu = 'dashboard';
require_once __DIR__ . '/includes/header.php';

// Stats
$rsvp    = get_rsvp_counts();
$wishes  = get_wishes_counts();
$gallery = db_value("SELECT COUNT(*) FROM gallery WHERE is_active = 1") ?? 0;
$events  = db_value("SELECT COUNT(*) FROM events WHERE is_active = 1") ?? 0;
$couple  = get_couple();

// Recent RSVP
$recent_rsvps = db_query(
    "SELECT * FROM rsvps ORDER BY created_at DESC LIMIT 5"
);

// Recent Wishes (pending)
$pending_wishes = db_query(
    "SELECT * FROM wishes WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5"
);
?>

<div class="page-header">
    <div>
        <h4><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard</h4>
        <div class="text-muted small">Selamat datang di Wedding Invitation CMS</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= admin_url('guests/index.php') ?>" class="btn btn-success">
            <i class="fas fa-paper-plane me-1"></i>Buat Link Tamu
        </a>
        <a href="<?= base_url() ?>" target="_blank" class="btn btn-outline-primary">
            <i class="fas fa-external-link-alt me-1"></i>Lihat Website
        </a>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted fw-bold small me-2"><i class="fas fa-bolt text-warning me-1"></i>Aksi Cepat:</span>
            <a href="<?= admin_url('guests/index.php') ?>" class="btn btn-sm btn-outline-success">
                <i class="fab fa-whatsapp me-1"></i>Kirim Link WA
            </a>
            <a href="<?= admin_url('gallery/create.php') ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-upload me-1"></i>Upload Foto
            </a>
            <a href="<?= admin_url('music/index.php') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-music me-1"></i>Kelola Musik
            </a>
            <a href="<?= admin_url('events/create.php') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-calendar-plus me-1"></i>Tambah Acara
            </a>
            <a href="<?= admin_url('settings/index.php') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-palette me-1"></i>Atur Warna &amp; Font
            </a>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary"><i class="fas fa-clipboard-list"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$rsvp['total'] ?></div>
                <div class="stat-label">Total RSVP</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$rsvp['hadir'] ?></div>
                <div class="stat-label">Tamu Hadir</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$rsvp['tidak'] ?></div>
                <div class="stat-label">Tidak Hadir</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning"><i class="fas fa-question-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$rsvp['ragu'] ?></div>
                <div class="stat-label">Masih Ragu</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-purple"><i class="fas fa-comments"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$wishes['total'] ?></div>
                <div class="stat-label">Total Ucapan</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-pink"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$wishes['pending'] ?></div>
                <div class="stat-label">Ucapan Pending</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-info"><i class="fas fa-images"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$gallery ?></div>
                <div class="stat-label">Foto Galeri</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$events ?></div>
                <div class="stat-label">Acara Aktif</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent RSVP -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-clipboard-list"></i> RSVP Terbaru
                <a href="<?= admin_url('rsvp/index.php') ?>" class="btn btn-sm btn-outline-secondary ms-auto">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent_rsvps)): ?>
                    <div class="empty-state py-4">
                        <i class="fas fa-clipboard-list"></i>
                        <p class="mb-0">Belum ada data RSVP dari tamu.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Kehadiran</th>
                                    <th>Tamu</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_rsvps as $r): ?>
                                <tr>
                                    <td><strong><?= e($r['guest_name']) ?></strong></td>
                                    <td>
                                        <?php if ($r['attendance'] === 'hadir'): ?>
                                            <span class="badge bg-success">Hadir</span>
                                        <?php elseif ($r['attendance'] === 'tidak_hadir'): ?>
                                            <span class="badge bg-danger">Tidak Hadir</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Ragu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= (int)$r['guest_count'] ?> org</td>
                                    <td><?= e(date('d/m H:i', strtotime($r['created_at']))) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Pending Wishes -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <i class="fas fa-comments"></i> Ucapan Pending
                <a href="<?= admin_url('wishes/index.php') ?>" class="btn btn-sm btn-outline-secondary ms-auto">Kelola</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pending_wishes)): ?>
                    <div class="empty-state py-4">
                        <i class="fas fa-comments"></i>
                        <p class="mb-0">Semua ucapan sudah disetujui / tidak ada antrean.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Pesan</th>
                                    <th>Aksi Cepat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_wishes as $w): ?>
                                <tr>
                                    <td><strong><?= e($w['guest_name']) ?></strong></td>
                                    <td><?= e(truncate($w['message'], 45)) ?></td>
                                    <td>
                                        <a href="<?= admin_url('wishes/index.php?approve=' . $w['id'] . '&filter=pending') ?>" class="btn btn-sm btn-success py-1 px-2">
                                            <i class="fas fa-check"></i> Setujui
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
