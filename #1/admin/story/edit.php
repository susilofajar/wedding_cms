<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id = int_input('id', 0, 'GET');
$s  = get_story($id);
if (!$s) {
    flash('error', 'Cerita tidak ditemukan.');
    redirect(admin_url('story/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $image = $s['image'];
    if (!empty($_FILES['image']['name'])) {
        $fname = upload_image($_FILES['image']);
        if ($fname) {
            if ($image) delete_upload($image);
            $image = $fname;
        } else {
            flash('error', 'Gagal upload foto baru.');
        }
    }

    db_execute(
        "UPDATE love_stories SET title=?, story_date=?, description=?, image=?, sort_order=?, is_active=? WHERE id=?",
        [post('title'), post('story_date'), post('description'), $image, int_input('sort_order'), isset($_POST['is_active']) ? 1 : 0, $id]
    );

    flash('success', 'Cerita berhasil diperbarui.');
    redirect(admin_url('story/index.php'));
}

$page_title  = 'Edit Cerita';
$active_menu = 'story';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-edit me-2 text-primary"></i>Edit Cerita</h4>
    <a href="<?= admin_url('story/index.php') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <?php include __DIR__ . '/form.php'; ?>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Perbarui Cerita
                </button>
                <a href="<?= admin_url('story/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
