<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username  = post('username');
    $password  = post('password');
    $password2 = post('password2');
    $full_name = post('full_name');
    $email     = post('email');
    $role      = post('role') === 'superadmin' ? 'superadmin' : 'admin';

    $errors = [];
    if (empty($username))              $errors[] = 'Username wajib diisi.';
    if (strlen($username) < 3)         $errors[] = 'Username minimal 3 karakter.';
    if (empty($password))              $errors[] = 'Password wajib diisi.';
    if (strlen($password) < 6)         $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $password2)      $errors[] = 'Konfirmasi password tidak cocok.';
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    // Check unique username
    if (empty($errors)) {
        $exist = db_row("SELECT id FROM users WHERE username = ?", [$username]);
        if ($exist) $errors[] = 'Username sudah digunakan.';
    }

    if ($errors) {
        foreach ($errors as $err) flash('error', $err);
        redirect(admin_url('users/create.php'));
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    db_execute(
        "INSERT INTO users (username, password, full_name, email, role) VALUES (?,?,?,?,?)",
        [$username, $hash, $full_name, $email, $role]
    );

    flash('success', 'User berhasil ditambahkan.');
    redirect(admin_url('users/index.php'));
}

$page_title  = 'Tambah Admin User';
$active_menu = 'users';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-header">
    <h4><i class="fas fa-user-plus me-2 text-primary"></i>Tambah Admin User</h4>
    <a href="<?= admin_url('users/index.php') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="card" style="max-width:560px;">
    <div class="card-body">
        <form method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" required minlength="3"
                       value="<?= e(post('username')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control"
                       value="<?= e(post('full_name')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= e(post('email')) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="admin">Admin</option>
                    <option value="superadmin">Superadmin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required minlength="6">
                <div class="form-text">Minimal 6 karakter</div>
            </div>
            <div class="mb-4">
                <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                <input type="password" name="password2" class="form-control" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Simpan User
                </button>
                <a href="<?= admin_url('users/index.php') ?>" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
