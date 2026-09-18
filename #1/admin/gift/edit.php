<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id  = int_input('id', 0, 'GET');
$acc = get_bank_account($id);
if (!$acc) {
    flash('error', 'Rekening tidak ditemukan.');
    redirect(admin_url('gift/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $bank_logo  = $acc['bank_logo'] ?? '';
    $qris_image = $acc['qris_image'] ?? '';

    if (!empty($_FILES['bank_logo']['name'])) {
        $fname = upload_image($_FILES['bank_logo']);
        if ($fname) {
            if ($bank_logo) delete_upload($bank_logo);
            $bank_logo = $fname;
        }
    }

    if (!empty($_FILES['qris_image']['name'])) {
        $fname = upload_image($_FILES['qris_image']);
        if ($fname) {
            if ($qris_image) delete_upload($qris_image);
            $qris_image = $fname;
        }
    }

    db_execute(
        "UPDATE bank_accounts SET bank_name=?, account_number=?, account_holder=?, bank_logo=?, qris_image=?, is_active=? WHERE id=?",
        [post('bank_name'), post('account_number'), post('account_holder'), $bank_logo, $qris_image, isset($_POST['is_active']) ? 1 : 0, $id]
    );

    flash('success', 'Rekening berhasil diperbarui.');
    redirect(admin_url('gift/index.php'));
}

$page_title  = 'Edit Rekening';
$active_menu = 'gift';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-edit me-2 text-primary"></i>Edit Rekening / QRIS</h4>
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
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
                <a href="<?= admin_url('gift/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
