<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';
require_once dirname(__DIR__) . '/includes/helpers.php';

start_session();

// Already logged in
if (is_logged_in()) {
    redirect(admin_url('dashboard.php'));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username = post('username');
    $password = post('password');

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $user = db_row(
            "SELECT * FROM users WHERE username = ? LIMIT 1",
            [$username]
        );

        if ($user && password_verify($password, $user['password'])) {
            login_admin($user);
            redirect(admin_url('dashboard.php'));
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Admin CMS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8b5e5e;
            --primary-dark: #7a4f4f;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e1e2d 0%, #2d2d3f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo .icon {
            width: 64px; height: 64px;
            background: rgba(139,94,94,0.12);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 12px;
        }
        .login-logo h4 {
            font-size: 20px;
            font-weight: 700;
            color: #1e1e2d;
            margin: 0 0 4px;
        }
        .login-logo p {
            font-size: 13px;
            color: #6c757d;
            margin: 0;
        }
        .form-label { font-size: 13px; font-weight: 500; }
        .form-control {
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            border-color: #e0e0e8;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139,94,94,0.15);
        }
        .btn-login {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            padding: 11px;
            width: 100%;
            transition: background 0.2s;
        }
        .btn-login:hover { background: var(--primary-dark); }
        .alert { border-radius: 8px; font-size: 13px; border: none; }
        .input-group-text {
            background: #f8f9fa;
            border-color: #e0e0e8;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <div class="icon"><i class="fas fa-heart"></i></div>
        <h4>Wedding CMS</h4>
        <p>Masuk ke panel administrasi</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username"
                   value="<?= e(post('username')) ?>" required autofocus
                   autocomplete="username" placeholder="Masukkan username">
        </div>

        <div class="mb-4">
            <label class="form-label" for="password">Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password"
                       required autocomplete="current-password" placeholder="Masukkan password"
                       style="border-radius:8px 0 0 8px;">
                <span class="input-group-text" onclick="togglePassword()">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </span>
            </div>
        </div>

        <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt me-2"></i>Masuk
        </button>
    </form>
</div>

<script>
function togglePassword() {
    const input   = document.getElementById('password');
    const icon    = document.getElementById('eyeIcon');
    const isText  = input.type === 'text';
    input.type    = isText ? 'password' : 'text';
    icon.className = isText ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
</body>
</html>
