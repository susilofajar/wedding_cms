<?php
/**
 * Authentication & Authorization
 * Wedding Invitation CMS
 */

require_once __DIR__ . '/config.php';

function start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function is_logged_in(): bool {
    start_session();
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_user']);
}

function require_admin(): void {
    if (!is_logged_in()) {
        header('Location: ' . admin_url('login.php'));
        exit;
    }
}

function login_admin(array $user): void {
    start_session();
    session_regenerate_id(true);
    $_SESSION['admin_id']   = $user['id'];
    $_SESSION['admin_user'] = $user['username'];
    $_SESSION['admin_name'] = $user['full_name'] ?? $user['username'];
    $_SESSION['admin_role'] = $user['role'] ?? 'admin';
}

function logout_admin(): void {
    start_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();
}

function current_admin(): array {
    start_session();
    return [
        'id'   => $_SESSION['admin_id']   ?? null,
        'user' => $_SESSION['admin_user'] ?? null,
        'name' => $_SESSION['admin_name'] ?? null,
        'role' => $_SESSION['admin_role'] ?? null,
    ];
}
