<?php
// Handle all actions BEFORE any output
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if (isset($_GET['approve'])) {
    $wid = int_input('approve', 0, 'GET');
    db_execute("UPDATE wishes SET status='approved' WHERE id=?", [$wid]);
    flash('success', 'Ucapan berhasil disetujui.');
    redirect(admin_url('wishes/index.php?filter=' . urlencode(get_param('filter', 'pending'))));
}

if (isset($_GET['hide'])) {
    $wid = int_input('hide', 0, 'GET');
    db_execute("UPDATE wishes SET status='hidden' WHERE id=?", [$wid]);
    flash('success', 'Ucapan berhasil disembunyikan.');
    redirect(admin_url('wishes/index.php?filter=' . urlencode(get_param('filter', 'pending'))));
}

if (isset($_GET['delete'])) {
    $wid = int_input('delete', 0, 'GET');
    db_execute("DELETE FROM wishes WHERE id=?", [$wid]);
    flash('success', 'Ucapan berhasil dihapus.');
    redirect(admin_url('wishes/index.php?filter=' . urlencode(get_param('filter', 'pending'))));
}

// Render page
$page_title  = 'Ucapan Tamu';
$active_menu = 'wishes';
require_once dirname(__DIR__) . '/includes/header.php';

$allowed_statuses = ['pending', 'approved', 'hidden', 'all'];
$filter = get_param('filter', 'pending');
if (!in_array($filter, $allowed_statuses, true)) $filter = 'pending';

$wishes = ($filter === 'all')
    ? db_query("SELECT * FROM wishes ORDER BY created_at DESC")
    : db_query("SELECT * FROM wishes WHERE status = ? ORDER BY created_at DESC", [$filter]);

$counts = get_wishes_counts();
?>

<div class="page-header">
    <h4><i class="fas fa-comments me-2 text-primary"></i>Ucapan Tamu</h4>
</div>

<!-- Filter Tabs -->
<div class="mb-3 d-flex gap-2 flex-wrap align-items-center">
    <a href="?filter=pending" class="btn btn-sm <?= $filter === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning' ?>">
        Pending
        <?php if ($counts['pending'] > 0): ?>
            <span class="badge bg-danger ms-1"><?= (int)$counts['pending'] ?></span>
        <?php endif; ?>
    </a>
    <a href="?filter=approved"  class="btn btn-sm <?= $filter === 'approved'  ? 'btn-success'   : 'btn-outline-success' ?>">Disetujui</a>
    <a href="?filter=hidden"    class="btn btn-sm <?= $filter === 'hidden'    ? 'btn-secondary' : 'btn-outline-secondary' ?>">Disembunyikan</a>
    <a href="?filter=all"       class="btn btn-sm <?= $filter === 'all'       ? 'btn-primary'   : 'btn-outline-primary' ?>">Semua</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($wishes)): ?>
            <div class="empty-state">
                <i class="fas fa-comments"></i>
                <h5>Tidak ada ucapan</h5>
                <p>Belum ada ucapan dengan status "<?= e($filter) ?>".</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Pesan</th>
                        <th>Kehadiran</th>
                        <th>Status</th>
                        <th>Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wishes as $i => $w): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= e($w['guest_name']) ?></strong></td>
                        <td><?= e(truncate($w['message'], 75)) ?></td>
                        <td>
                            <?php if ($w['attendance'] === 'hadir'): ?>
                                <span class="badge bg-success">Hadir</span>
                            <?php elseif ($w['attendance'] === 'tidak_hadir'): ?>
                                <span class="badge bg-danger">Tidak Hadir</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Ragu</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($w['status'] === 'approved'): ?>
                                <span class="badge bg-success">Disetujui</span>
                            <?php elseif ($w['status'] === 'hidden'): ?>
                                <span class="badge bg-secondary">Disembunyikan</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td style="white-space:nowrap;">
                            <?= e(date('d/m/Y H:i', strtotime($w['created_at']))) ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <?php if ($w['status'] !== 'approved'): ?>
                                <a href="?approve=<?= (int)$w['id'] ?>&filter=<?= e($filter) ?>"
                                   class="btn btn-sm btn-outline-success" title="Setujui">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($w['status'] !== 'hidden'): ?>
                                <a href="?hide=<?= (int)$w['id'] ?>&filter=<?= e($filter) ?>"
                                   class="btn btn-sm btn-outline-secondary" title="Sembunyikan">
                                    <i class="fas fa-eye-slash"></i>
                                </a>
                                <?php endif; ?>
                                <a href="?delete=<?= (int)$w['id'] ?>&filter=<?= e($filter) ?>"
                                   class="btn btn-sm btn-outline-danger" title="Hapus"
                                   data-confirm="Hapus ucapan dari <?= e($w['guest_name']) ?> secara permanen?">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
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
