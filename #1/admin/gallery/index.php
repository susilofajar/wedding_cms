<?php
$page_title  = 'Galeri Foto';
$active_menu = 'gallery';
require_once dirname(__DIR__) . '/includes/header.php';

$gallery = get_all_gallery();
?>

<div class="page-header">
    <h4><i class="fas fa-images me-2 text-primary"></i>Galeri Foto</h4>
    <a href="<?= admin_url('gallery/create.php') ?>" class="btn btn-primary">
        <i class="fas fa-upload me-1"></i>Upload Foto
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($gallery)): ?>
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <h5>Belum ada foto galeri</h5>
                <p>Klik "Upload Foto" untuk menambahkan foto pertama.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Judul</th>
                        <th>Caption</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gallery as $item): ?>
                    <tr>
                        <td>
                            <img src="<?= upload_url(e($item['image'])) ?>"
                                 alt="<?= e($item['title']) ?>" class="img-thumb">
                        </td>
                        <td><?= e($item['title']) ?></td>
                        <td><?= e(truncate($item['caption'], 40)) ?></td>
                        <td><?= (int)$item['sort_order'] ?></td>
                        <td>
                            <?php if ($item['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= admin_url('gallery/edit.php?id=' . $item['id']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= admin_url('gallery/delete.php?id=' . $item['id']) ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="Hapus foto ini?">
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
