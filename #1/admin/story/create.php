<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $title       = post('title');
    $story_date  = post('story_date');
    $description = post('description');
    $sort_order  = int_input('sort_order');
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $image       = '';

    if (empty($title)) {
        flash('error', 'Judul wajib diisi.');
        redirect(admin_url('story/create.php'));
    }

    if (!empty($_FILES['image']['name'])) {
        $fname = upload_image($_FILES['image']);
        if ($fname) {
            $image = $fname;
        } else {
            flash('error', 'Gagal upload foto. Format JPG/PNG/WebP, maks 5MB.');
        }
    }

    db_execute(
        "INSERT INTO love_stories (title, story_date, description, image, sort_order, is_active) VALUES (?,?,?,?,?,?)",
        [$title, $story_date, $description, $image, $sort_order, $is_active]
    );

    flash('success', 'Cerita berhasil ditambahkan.');
    redirect(admin_url('story/index.php'));
}

$page_title  = 'Tambah Cerita';
$active_menu = 'story';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-plus me-2 text-primary"></i>Tambah Cerita</h4>
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
                    <i class="fas fa-save me-1"></i>Simpan Cerita
                </button>
                <a href="<?= admin_url('story/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
