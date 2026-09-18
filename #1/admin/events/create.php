<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $name        = post('name');
    $type        = post('type');
    $event_date  = post('event_date');
    $start_time  = post('start_time');
    $end_time    = post('end_time') ?: null;
    $location    = post('location');
    $address     = post('address');
    $maps_url    = post('maps_url');
    $description = post('description');
    $icon        = post('icon') ?: 'fas fa-calendar-alt';
    $sort_order  = int_input('sort_order', 0);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name) || empty($event_date) || empty($start_time)) {
        flash('error', 'Nama acara, tanggal, dan waktu mulai wajib diisi.');
        redirect(admin_url('events/create.php'));
    }

    if (!empty($maps_url) && !filter_var($maps_url, FILTER_VALIDATE_URL)) {
        $maps_url = '';
    }

    db_execute(
        "INSERT INTO events (name,type,event_date,start_time,end_time,location,address,maps_url,description,icon,sort_order,is_active)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
        [$name, $type, $event_date, $start_time, $end_time, $location, $address, $maps_url, $description, $icon, $sort_order, $is_active]
    );

    flash('success', 'Acara berhasil ditambahkan.');
    redirect(admin_url('events/index.php'));
}

$page_title  = 'Tambah Acara';
$active_menu = 'events';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-plus me-2 text-primary"></i>Tambah Acara</h4>
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
                    <i class="fas fa-save me-1"></i>Simpan Acara
                </button>
                <a href="<?= admin_url('events/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
