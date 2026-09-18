<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id = int_input('id', 0, 'GET');
$m  = get_music_item($id);
if (!$m) {
    flash('error', 'Musik tidak ditemukan.');
    redirect(admin_url('music/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    db_execute("UPDATE music SET title=? WHERE id=?", [post('title'), $id]);
    flash('success', 'Judul musik berhasil diperbarui.');
    redirect(admin_url('music/index.php'));
}

$page_title  = 'Edit Musik';
$active_menu = 'music';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-edit me-2 text-primary"></i>Edit Musik</h4>
    <a href="<?= admin_url('music/index.php') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:480px;">
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label">Pratinjau</label>
            <audio controls style="width:100%;">
                <source src="<?= upload_url(e($m['filename']), 'music') ?>" type="audio/mpeg">
            </audio>
        </div>
        <form method="POST">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="form-label">Judul Musik</label>
                <input type="text" name="title" class="form-control" value="<?= e($m['title']) ?>" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan
                </button>
                <a href="<?= admin_url('music/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
