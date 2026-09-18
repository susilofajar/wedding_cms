<?php
// Handle delete BEFORE any output
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if (isset($_GET['delete'])) {
    $did = int_input('delete', 0, 'GET');
    db_execute("DELETE FROM rsvps WHERE id = ?", [$did]);
    flash('success', 'Data RSVP berhasil dihapus.');
    redirect(admin_url('rsvp/index.php'));
}

// Now render the page
$page_title  = 'Data RSVP';
$active_menu = 'rsvp';
require_once dirname(__DIR__) . '/includes/header.php';

$counts = get_rsvp_counts();

// Filter
$filter = get_param('filter', 'all');
if ($filter !== 'all') {
    $allowed_filters = ['hadir', 'tidak_hadir', 'ragu'];
    if (!in_array($filter, $allowed_filters, true)) $filter = 'all';
}

$rsvps = ($filter !== 'all')
    ? db_query("SELECT * FROM rsvps WHERE attendance = ? ORDER BY created_at DESC", [$filter])
    : db_query("SELECT * FROM rsvps ORDER BY created_at DESC");
?>

<div class="page-header">
    <h4><i class="fas fa-clipboard-list me-2 text-primary"></i>Data RSVP</h4>
    <div class="d-flex gap-2">
        <a href="<?= admin_url('rsvp/export.php?filter=' . urlencode($filter)) ?>" class="btn btn-outline-success">
            <i class="fas fa-file-excel me-1"></i>Export CSV
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$counts['total'] ?></div>
                <div class="stat-label">Total RSVP</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$counts['hadir'] ?></div>
                <div class="stat-label">Hadir</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$counts['tidak'] ?></div>
                <div class="stat-label">Tidak Hadir</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon bg-warning"><i class="fas fa-question-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number"><?= (int)$counts['ragu'] ?></div>
                <div class="stat-label">Masih Ragu</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="mb-3 d-flex gap-2 flex-wrap">
    <a href="?filter=all"         class="btn btn-sm <?= $filter === 'all'         ? 'btn-primary'         : 'btn-outline-secondary' ?>">Semua</a>
    <a href="?filter=hadir"       class="btn btn-sm <?= $filter === 'hadir'       ? 'btn-success'         : 'btn-outline-success' ?>">Hadir</a>
    <a href="?filter=tidak_hadir" class="btn btn-sm <?= $filter === 'tidak_hadir' ? 'btn-danger'          : 'btn-outline-danger' ?>">Tidak Hadir</a>
    <a href="?filter=ragu"        class="btn btn-sm <?= $filter === 'ragu'        ? 'btn-warning text-dark' : 'btn-outline-warning' ?>">Ragu</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($rsvps)): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h5>Belum ada data RSVP</h5>
                <p>RSVP dari tamu akan muncul di sini.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Tamu</th>
                        <th>Kehadiran</th>
                        <th>Jumlah</th>
                        <th>Pesan</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rsvps as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($r['guest_name']) ?></strong></td>
                        <td>
                            <?php if ($r['attendance'] === 'hadir'): ?>
                                <span class="badge bg-success">Hadir</span>
                            <?php elseif ($r['attendance'] === 'tidak_hadir'): ?>
                                <span class="badge bg-danger">Tidak Hadir</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Masih Ragu</span>
                            <?php endif; ?>
                        </td>
                        <td><?= (int)$r['guest_count'] ?> org</td>
                        <td><?= e(truncate($r['message'], 55)) ?></td>
                        <td style="white-space:nowrap;">
                            <?= e(date('d/m/Y H:i', strtotime($r['created_at']))) ?>
                        </td>
                        <td>
                            <a href="?delete=<?= (int)$r['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="Hapus data RSVP dari <?= e($r['guest_name']) ?>?">
                                <i class="fas fa-trash"></i>
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

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
