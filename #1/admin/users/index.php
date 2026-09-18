<?php
// Handle delete BEFORE any output
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if (isset($_GET['delete'])) {
    $uid     = int_input('delete', 0, 'GET');
    $current = current_admin();
    if ($uid === (int)$current['id']) {
        flash('error', 'Tidak dapat menghapus akun yang sedang aktif.');
    } else {
        $total = db_value("SELECT COUNT(*) FROM users");
        if ($total <= 1) {
            flash('error', 'Minimal harus ada satu admin.');
        } else {
            db_execute("DELETE FROM users WHERE id = ?", [$uid]);
            flash('success', 'User berhasil dihapus.');
        }
    }
    redirect(admin_url('users/index.php'));
}

$page_title  = 'Admin Users';
$active_menu = 'users';
require_once dirname(__DIR__) . '/includes/header.php';

$users = get_all_users();
?>

<div class="page-header">
    <h4><i class="fas fa-users-cog me-2 text-primary"></i>Admin Users</h4>
    <a href="<?= admin_url('users/create.php') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Tambah User
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($users)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h5>Belum ada user</h5>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $current = current_admin();
                    foreach ($users as $i => $u):
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <strong><?= e($u['username']) ?></strong>
                            <?php if ((int)$u['id'] === (int)$current['id']): ?>
                                <span class="badge bg-primary ms-1">Anda</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e($u['full_name']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td>
                            <?php if ($u['role'] === 'superadmin'): ?>
                                <span class="badge bg-danger">Superadmin</span>
                            <?php else: ?>
                                <span class="badge bg-info">Admin</span>
                            <?php endif; ?>
                        </td>
                        <td><?= e(date('d/m/Y', strtotime($u['created_at']))) ?></td>
                        <td>
                            <a href="<?= admin_url('users/edit.php?id=' . $u['id']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if ((int)$u['id'] !== (int)$current['id']): ?>
                            <a href="?delete=<?= $u['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               data-confirm="Hapus user '<?= e($u['username']) ?>'?">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
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
