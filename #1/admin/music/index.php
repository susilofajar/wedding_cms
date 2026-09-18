<?php
// Handle activate BEFORE any output
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if (isset($_GET['activate'])) {
    $mid = int_input('activate', 0, 'GET');
    db_execute("UPDATE music SET is_active = 0");
    db_execute("UPDATE music SET is_active = 1 WHERE id = ?", [$mid]);
    flash('success', 'Musik berhasil diaktifkan.');
    redirect(admin_url('music/index.php'));
}

$page_title  = 'Musik Background';
$active_menu = 'music';
require_once dirname(__DIR__) . '/includes/header.php';

$music_list = get_all_music();
?>

<div class="page-header">
    <h4><i class="fas fa-music me-2 text-primary"></i>Musik Background</h4>
    <a href="<?= admin_url('music/create.php') ?>" class="btn btn-primary">
        <i class="fas fa-upload me-1"></i>Upload Musik
    </a>
</div>

<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    Hanya satu musik yang dapat aktif sekaligus. Klik <strong>Aktifkan</strong> untuk memilih musik yang diputar.
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($music_list)): ?>
            <div class="empty-state">
                <i class="fas fa-music"></i>
                <h5>Belum ada musik</h5>
                <p>Klik "Upload Musik" untuk menambahkan musik background.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Diupload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($music_list as $m): ?>
                    <tr>
                        <td>
                            <i class="fas fa-music text-primary me-2"></i>
                            <strong><?= e($m['title']) ?></strong>
                        </td>
                        <td>
                            <audio controls style="height:32px;max-width:200px;">
                                <source src="<?= upload_url(e($m['filename']), 'music') ?>" type="audio/mpeg">
                            </audio>
                        </td>
                        <td>
                            <?php if ($m['is_active']): ?>
                                <span class="badge bg-success"><i class="fas fa-volume-up me-1"></i>Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e(date('d/m/Y', strtotime($m['created_at']))) ?></td>
                        <td>
                            <?php if (!$m['is_active']): ?>
                            <a href="<?= admin_url('music/index.php?activate=' . $m['id']) ?>"
                               class="btn btn-sm btn-outline-success">
                                <i class="fas fa-check me-1"></i>Aktifkan
                            </a>
                            <?php endif; ?>
                            <a href="<?= admin_url('music/edit.php?id=' . $m['id']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= admin_url('music/delete.php?id=' . $m['id']) ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="Hapus musik '<?= e($m['title']) ?>'?">
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
