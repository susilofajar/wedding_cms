<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id   = int_input('id', 0, 'GET');
$item = get_gallery_item($id);
if (!$item) {
    flash('error', 'Foto tidak ditemukan.');
    redirect(admin_url('gallery/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $image = $item['image'];
    if (!empty($_FILES['image']['name'])) {
        $fname = upload_image($_FILES['image']);
        if ($fname) {
            delete_upload($image);
            $image = $fname;
        } else {
            flash('error', 'Gagal upload foto baru.');
        }
    }

    db_execute(
        "UPDATE gallery SET image=?, title=?, caption=?, sort_order=?, is_active=? WHERE id=?",
        [$image, post('title'), post('caption'), int_input('sort_order'), isset($_POST['is_active']) ? 1 : 0, $id]
    );

    flash('success', 'Foto berhasil diperbarui.');
    redirect(admin_url('gallery/index.php'));
}

$page_title  = 'Edit Foto Galeri';
$active_menu = 'gallery';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-edit me-2 text-primary"></i>Edit Foto Galeri</h4>
    <a href="<?= admin_url('gallery/index.php') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Foto Saat Ini</label>
                <div>
                    <img src="<?= upload_url(e($item['image'])) ?>"
                         alt="Gallery" class="img-preview">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Ganti Foto (opsional)</label>
                <input type="file" name="image" class="form-control" accept="image/*" data-preview="prevImg">
                <img id="prevImg" class="img-preview" style="display:none;">
                <div class="form-text">Kosongkan jika tidak ingin mengganti foto</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="title" class="form-control" value="<?= e($item['title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Caption</label>
                <textarea name="caption" class="form-control" rows="2"><?= e($item['caption']) ?></textarea>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="<?= (int)$item['sort_order'] ?>" min="0">
                </div>
                <div class="col-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                               <?= $item['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Aktifkan</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
                <a href="<?= admin_url('gallery/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
