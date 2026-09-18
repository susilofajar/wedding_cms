<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id   = int_input('id', 0, 'GET');
$user = get_user($id);
if (!$user) {
    flash('error', 'User tidak ditemukan.');
    redirect(admin_url('users/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $full_name = post('full_name');
    $email     = post('email');
    $role      = post('role') === 'superadmin' ? 'superadmin' : 'admin';
    $password  = post('password');
    $password2 = post('password2');

    $errors = [];
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }
    if (!empty($password)) {
        if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
        if ($password !== $password2) $errors[] = 'Konfirmasi password tidak cocok.';
    }

    if ($errors) {
        foreach ($errors as $err) flash('error', $err);
        redirect(admin_url('users/edit.php?id=' . $id));
    }

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        db_execute(
            "UPDATE users SET full_name=?, email=?, role=?, password=? WHERE id=?",
            [$full_name, $email, $role, $hash, $id]
        );
    } else {
        db_execute(
            "UPDATE users SET full_name=?, email=?, role=? WHERE id=?",
            [$full_name, $email, $role, $id]
        );
    }

    flash('success', 'User berhasil diperbarui.');
    redirect(admin_url('users/index.php'));
}

$page_title  = 'Edit User';
$active_menu = 'users';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-user-edit me-2 text-primary"></i>Edit User</h4>
    <a href="<?= admin_url('users/index.php') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:560px;">
    <div class="card-body">
        <div class="mb-3 p-3 bg-light rounded">
            <strong>Username:</strong> <?= e($user['username']) ?>
        </div>
        <form method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control"
                       value="<?= e($user['full_name']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= e($user['email']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="admin"      <?= $user['role'] === 'admin'      ? 'selected' : '' ?>>Admin</option>
                    <option value="superadmin" <?= $user['role'] === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                </select>
            </div>
            <hr>
            <p class="text-muted" style="font-size:13px;">Kosongkan kolom password jika tidak ingin menggantinya.</p>
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" minlength="6">
            </div>
            <div class="mb-4">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password2" class="form-control">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
                <a href="<?= admin_url('users/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
