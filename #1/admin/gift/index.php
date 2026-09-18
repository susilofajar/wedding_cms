<?php
$page_title  = 'Wedding Gift';
$active_menu = 'gift';
require_once dirname(__DIR__) . '/includes/header.php';

$accounts = get_all_bank_accounts();
?>

<div class="page-header">
    <h4><i class="fas fa-gift me-2 text-primary"></i>Wedding Gift / Rekening</h4>
    <a href="<?= admin_url('gift/create.php') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah Rekening
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($accounts)): ?>
            <div class="empty-state">
                <i class="fas fa-gift"></i>
                <h5>Belum ada rekening</h5>
                <p>Klik "Tambah Rekening" untuk menambahkan data rekening.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Logo</th>
                        <th>Bank</th>
                        <th>Nomor Rekening</th>
                        <th>Nama Pemilik</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $acc): ?>
                    <tr>
                        <td>
                            <?php if ($acc['bank_logo']): ?>
                                <img src="<?= upload_url(e($acc['bank_logo'])) ?>"
                                     alt="Logo" style="width:40px;height:24px;object-fit:contain;">
                            <?php else: ?>
                                <i class="fas fa-university text-muted fa-lg"></i>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= e($acc['bank_name']) ?></strong>
                            <?php if (!empty($acc['qris_image'])): ?>
                                <span class="badge bg-info ms-1"><i class="fas fa-qrcode me-1"></i>QRIS</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <code><?= e($acc['account_number']) ?></code>
                            <button class="btn btn-sm btn-link p-0 ms-2"
                                    data-copy="<?= e($acc['account_number']) ?>"
                                    title="Salin">
                                <i class="fas fa-copy text-muted"></i>
                            </button>
                        </td>
                        <td><?= e($acc['account_holder']) ?></td>
                        <td>
                            <?php if ($acc['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= admin_url('gift/edit.php?id=' . $acc['id']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= admin_url('gift/delete.php?id=' . $acc['id']) ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="Hapus rekening '<?= e($acc['bank_name']) ?>'?">
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
