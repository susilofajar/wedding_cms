<?php
$page_title  = 'Love Story';
$active_menu = 'story';
require_once dirname(__DIR__) . '/includes/header.php';

$stories = get_all_stories();
?>

<div class="page-header">
    <h4><i class="fas fa-book-open me-2 text-primary"></i>Love Story</h4>
    <a href="<?= admin_url('story/create.php') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Cerita
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($stories)): ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h5>Belum ada cerita</h5>
                <p>Klik "Tambah Cerita" untuk mulai menulis love story Anda.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Judul</th>
                        <th>Tahun/Tanggal</th>
                        <th>Deskripsi</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stories as $s): ?>
                    <tr>
                        <td>
                            <?php if ($s['image']): ?>
                                <img src="<?= upload_url(e($s['image'])) ?>"
                                     alt="<?= e($s['title']) ?>" class="img-thumb">
                            <?php else: ?>
                                <div class="img-thumb d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= e($s['title']) ?></strong></td>
                        <td><?= e($s['story_date']) ?></td>
                        <td><?= e(truncate($s['description'], 50)) ?></td>
                        <td><?= (int)$s['sort_order'] ?></td>
                        <td>
                            <?php if ($s['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= admin_url('story/edit.php?id=' . $s['id']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= admin_url('story/delete.php?id=' . $s['id']) ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="Hapus cerita '<?= e($s['title']) ?>'?">
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
