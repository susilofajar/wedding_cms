<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    if (empty($_FILES['image']['name'])) {
        flash('error', 'Silakan pilih foto untuk diupload.');
        redirect(admin_url('gallery/create.php'));
    }

    $fname = upload_image($_FILES['image']);
    if (!$fname) {
        flash('error', 'Gagal upload foto. Pastikan format JPG/PNG/WebP dan maks 5MB.');
        redirect(admin_url('gallery/create.php'));
    }

    db_execute(
        "INSERT INTO gallery (image, title, caption, sort_order, is_active) VALUES (?,?,?,?,?)",
        [$fname, post('title'), post('caption'), int_input('sort_order'), isset($_POST['is_active']) ? 1 : 0]
    );

    flash('success', 'Foto berhasil diupload.');
    redirect(admin_url('gallery/index.php'));
}

$page_title  = 'Upload Foto Galeri';
$active_menu = 'gallery';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-upload me-2 text-primary"></i>Upload Foto Galeri</h4>
    <a href="<?= admin_url('gallery/index.php') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Foto <span class="text-danger">*</span></label>
                <input type="file" name="image" class="form-control" accept="image/*"
                       data-preview="prevImg" required>
                <img id="prevImg" class="img-preview" style="display:none;">
                <div class="form-text">JPG, PNG, WebP, maks 5MB</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="title" class="form-control" placeholder="Judul foto">
            </div>
            <div class="mb-3">
                <label class="form-label">Caption</label>
                <textarea name="caption" class="form-control" rows="2" placeholder="Keterangan foto..."></textarea>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-6">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="sort_order" class="form-control" value="0" min="0">
                </div>
                <div class="col-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                        <label class="form-check-label" for="is_active">Aktifkan</label>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload me-1"></i>Upload Foto
                </button>
                <a href="<?= admin_url('gallery/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
