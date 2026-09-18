<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $bank_name      = post('bank_name');
    $account_number = post('account_number');
    $account_holder = post('account_holder');
    $is_active      = isset($_POST['is_active']) ? 1 : 0;
    $bank_logo      = '';
    $qris_image     = '';

    if (empty($bank_name) || empty($account_number) || empty($account_holder)) {
        flash('error', 'Nama bank, nomor rekening, dan nama pemilik wajib diisi.');
        redirect(admin_url('gift/create.php'));
    }

    if (!empty($_FILES['bank_logo']['name'])) {
        $fname = upload_image($_FILES['bank_logo']);
        if ($fname) $bank_logo = $fname;
    }

    if (!empty($_FILES['qris_image']['name'])) {
        $fname = upload_image($_FILES['qris_image']);
        if ($fname) $qris_image = $fname;
    }

    db_execute(
        "INSERT INTO bank_accounts (bank_name, account_number, account_holder, bank_logo, qris_image, is_active) VALUES (?,?,?,?,?,?)",
        [$bank_name, $account_number, $account_holder, $bank_logo, $qris_image, $is_active]
    );

    flash('success', 'Rekening berhasil ditambahkan.');
    redirect(admin_url('gift/index.php'));
}

$page_title  = 'Tambah Rekening';
$active_menu = 'gift';
require_once dirname(__DIR__) . '/includes/header.php';
$acc = [];
?>

<div class="page-header">
    <h4><i class="fas fa-plus me-2 text-primary"></i>Tambah Rekening / QRIS</h4>
    <a href="<?= admin_url('gift/index.php') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:620px;">
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <?php include __DIR__ . '/form.php'; ?>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan Rekening
                </button>
                <a href="<?= admin_url('gift/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
