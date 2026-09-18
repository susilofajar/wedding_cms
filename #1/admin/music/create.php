<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    if (empty($_FILES['music_file']['name'])) {
        flash('error', 'Silakan pilih file MP3 untuk diupload.');
        redirect(admin_url('music/create.php'));
    }

    $fname = upload_music($_FILES['music_file']);
    if (!$fname) {
        flash('error', 'Gagal upload musik. Pastikan format MP3 dan maks 20MB.');
        redirect(admin_url('music/create.php'));
    }

    $title     = post('title') ?: pathinfo($_FILES['music_file']['name'], PATHINFO_FILENAME);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($is_active) {
        db_execute("UPDATE music SET is_active = 0");
    }

    db_execute(
        "INSERT INTO music (title, filename, is_active) VALUES (?,?,?)",
        [$title, $fname, $is_active]
    );

    flash('success', 'Musik berhasil diupload.');
    redirect(admin_url('music/index.php'));
}

$page_title  = 'Upload Musik';
$active_menu = 'music';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-upload me-2 text-primary"></i>Upload Musik</h4>
    <a href="<?= admin_url('music/index.php') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:560px;">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">File Musik (MP3) <span class="text-danger">*</span></label>
                <input type="file" name="music_file" class="form-control" accept=".mp3,audio/mpeg" required>
                <div class="form-text">Format MP3, maks 20MB</div>
            </div>
            <div class="mb-4">
                <label class="form-label">Judul Musik</label>
                <input type="text" name="title" class="form-control" placeholder="Nama lagu atau artis">
                <div class="form-text">Jika kosong, nama file akan digunakan</div>
            </div>
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active">
                    <label class="form-check-label" for="is_active">Jadikan musik aktif sekarang</label>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload me-1"></i>Upload Musik
                </button>
                <a href="<?= admin_url('music/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
