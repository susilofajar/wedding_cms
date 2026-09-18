<?php
/**
 * CSRF Protection
 * Wedding Invitation CMS
 */

require_once __DIR__ . '/auth.php';

function csrf_generate(): string {
    start_session();
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function csrf_token(): string {
    return csrf_generate();
}

function csrf_field(): string {
    $token = csrf_generate();
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_verify(): bool {
    start_session();
    $token = $_POST[CSRF_TOKEN_NAME] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    $session_token = $_SESSION[CSRF_TOKEN_NAME] ?? '';
    if (empty($token) || empty($session_token)) {
        return false;
    }
    return hash_equals($session_token, $token);
}

function csrf_check(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify()) {
            http_response_code(403);
            die('Invalid CSRF token. Please go back and try again.');
        }
    }
}
