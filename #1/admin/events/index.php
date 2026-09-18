<?php
$page_title  = 'Acara Pernikahan';
$active_menu = 'events';
require_once dirname(__DIR__) . '/includes/header.php';

$events = get_all_events();
?>

<div class="page-header">
    <h4><i class="fas fa-calendar-alt me-2 text-primary"></i>Acara Pernikahan</h4>
    <a href="<?= admin_url('events/create.php') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Acara
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($events)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <h5>Belum ada acara</h5>
                <p>Klik "Tambah Acara" untuk membuat acara pertama.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Acara</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $i => $ev): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <strong><?= e($ev['name']) ?></strong>
                            <div class="text-muted" style="font-size:11px;"><?= e($ev['type']) ?></div>
                        </td>
                        <td><?= e(format_date($ev['event_date'])) ?></td>
                        <td>
                            <?= e(date('H:i', strtotime($ev['start_time']))) ?>
                            <?php if ($ev['end_time']): ?>
                                — <?= e(date('H:i', strtotime($ev['end_time']))) ?>
                            <?php else: ?>
                                — selesai
                            <?php endif; ?>
                        </td>
                        <td><?= e(truncate($ev['location'], 30)) ?></td>
                        <td>
                            <?php if ($ev['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= admin_url('events/edit.php?id=' . $ev['id']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= admin_url('events/delete.php?id=' . $ev['id']) ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="Hapus acara '<?= e($ev['name']) ?>'?">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
