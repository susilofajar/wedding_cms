<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id = int_input('id', 0, 'GET');
$ev = get_event($id);
if (!$ev) {
    flash('error', 'Acara tidak ditemukan.');
    redirect(admin_url('events/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $maps_url = post('maps_url');
    if (!empty($maps_url) && !filter_var($maps_url, FILTER_VALIDATE_URL)) {
        $maps_url = '';
    }

    db_execute(
        "UPDATE events SET name=?,type=?,event_date=?,start_time=?,end_time=?,
         location=?,address=?,maps_url=?,description=?,icon=?,sort_order=?,is_active=?
         WHERE id=?",
        [
            post('name'), post('type'), post('event_date'), post('start_time'),
            post('end_time') ?: null, post('location'), post('address'),
            $maps_url, post('description'),
            post('icon') ?: 'fas fa-calendar-alt',
            int_input('sort_order'), isset($_POST['is_active']) ? 1 : 0,
            $id
        ]
    );

    flash('success', 'Acara berhasil diperbarui.');
    redirect(admin_url('events/index.php'));
}

$page_title  = 'Edit Acara';
$active_menu = 'events';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-edit me-2 text-primary"></i>Edit Acara</h4>
    <a href="<?= admin_url('events/index.php') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <?= csrf_field() ?>
            <?php include __DIR__ . '/form.php'; ?>
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Perbarui Acara
                </button>
                <a href="<?= admin_url('events/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
