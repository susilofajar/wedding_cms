<?php
/**
 * Helper Functions
 * Wedding Invitation CMS
 */

/**
 * Escape HTML output (XSS protection)
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if the current request is over HTTPS.
 * Compatible with InfinityFree, Cloudflare, standard SSL, and reverse proxies.
 */
function is_https(): bool {
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
        return true;
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') {
        return true;
    }
    if (!empty($_SERVER['HTTP_CF_VISITOR'])) {
        $cf = json_decode($_SERVER['HTTP_CF_VISITOR'], true);
        if (isset($cf['scheme']) && strtolower($cf['scheme']) === 'https') {
            return true;
        }
    }
    if (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) {
        return true;
    }
    if (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

/**
 * Get base URL pointing to the project root (with trailing slash).
 *
 * Works correctly whether the script is in the root or a subdirectory
 * (e.g. admin/, admin/events/) because we resolve the document-root
 * of the project rather than walking up dirname() blindly.
 */
function base_url(string $path = ''): string {
    // Allow hard-coded override via config
    if (!empty(APP_URL)) {
        return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
    }

    $protocol = is_https() ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // The project root is the directory that contains index.php (one level
    // above /includes, two levels above /admin/*, etc.).
    // __FILE__ is always helpers.php which lives in /includes/,
    // so the project root is dirname(__DIR__) of this file.
    $root_fs  = str_replace('\\', '/', dirname(__DIR__)); // absolute filesystem path to project root
    $doc_root = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\'));

    if ($doc_root !== '' && strpos($root_fs, $doc_root) === 0) {
        $base = substr($root_fs, strlen($doc_root));
    } else {
        // Fallback: derive from SCRIPT_NAME
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        // Strip everything from /admin/ or deeper
        $base   = preg_replace('#/(admin|includes|assets|uploads|database)(/.*)?$#', '', $script);
        $base   = rtrim(dirname($base), '/');
    }

    $base = rtrim($base, '/');

    return $protocol . '://' . $host . $base . '/' . ltrim($path, '/');
}

/**
 * Get admin URL
 */
function admin_url(string $path = ''): string {
    return base_url('admin/' . ltrim($path, '/'));
}

/**
 * Get upload URL (for images, music, or generic uploads)
 * Automatically routes to uploads/images/ or uploads/music/
 */
function upload_url(string $path = '', string $type = 'image'): string {
    if (empty($path)) {
        return '';
    }
    // If path is already a full URL (http:// or https://)
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $clean = ltrim($path, '/');
    // Remove leading 'uploads/' if present to avoid duplication
    if (str_starts_with($clean, 'uploads/')) {
        $clean = substr($clean, 8);
    }
    $clean = ltrim($clean, '/');

    if ($type === 'music') {
        if (!str_starts_with($clean, 'music/')) {
            $clean = 'music/' . $clean;
        }
    } else {
        // Image type: prepend images/ if not already present
        if (!str_starts_with($clean, 'images/') && !str_starts_with($clean, 'music/')) {
            $clean = 'images/' . $clean;
        }
    }

    return base_url('uploads/' . $clean);
}

/**
 * Get asset URL
 */
function asset_url(string $path = ''): string {
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Redirect to URL
 */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/**
 * Flash message (set)
 */
function flash(string $type, string $message): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash messages
 */
function get_flash(): array {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Render flash messages as HTML
 */
function render_flash(): string {
    $messages = get_flash();
    $html     = '';
    foreach ($messages as $msg) {
        $type = match($msg['type']) {
            'success' => 'success',
            'error'   => 'danger',
            'warning' => 'warning',
            default   => 'info',
        };
        $html .= '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
        $html .= e($msg['message']);
        $html .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        $html .= '</div>';
    }
    return $html;
}

/**
 * Generate random filename for uploads
 */
function random_filename(string $extension): string {
    return bin2hex(random_bytes(16)) . '.' . strtolower($extension);
}

/**
 * Get file extension from filename
 */
function file_extension(string $filename): string {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Validate and sanitize integer
 */
function int_input(string $key, int $default = 0, string $method = 'POST'): int {
    $source = $method === 'GET' ? INPUT_GET : INPUT_POST;
    $val    = filter_input($source, $key, FILTER_VALIDATE_INT);
    return ($val === false || $val === null) ? $default : (int) $val;
}

/**
 * Get POST string value
 */
function post(string $key, string $default = ''): string {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
}

/**
 * Get GET string value
 */
function get_param(string $key, string $default = ''): string {
    return isset($_GET[$key]) ? trim((string) $_GET[$key]) : $default;
}

/**
 * Format date to Indonesian format
 */
function format_date(string $date, string $format = 'd F Y'): string {
    if (empty($date)) return '';

    $days_id = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu',
        // Short forms
        'Sun' => 'Min', 'Mon' => 'Sen', 'Tue' => 'Sel',
        'Wed' => 'Rab', 'Thu' => 'Kam', 'Fri' => 'Jum', 'Sat' => 'Sab',
    ];

    $months_id = [
        'January'   => 'Januari',
        'February'  => 'Februari',
        'March'     => 'Maret',
        'April'     => 'April',
        'May'       => 'Mei',
        'June'      => 'Juni',
        'July'      => 'Juli',
        'August'    => 'Agustus',
        'September' => 'September',
        'October'   => 'Oktober',
        'November'  => 'November',
        'December'  => 'Desember',
        // Short forms
        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr',
        'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Agt', 'Sep' => 'Sep',
        'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des',
    ];

    $formatted = date($format, strtotime($date));
    $translated = str_replace(array_keys($days_id),   array_values($days_id),   $formatted);
    $translated = str_replace(array_keys($months_id), array_values($months_id), $translated);
    return $translated;
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string {
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Check if request is AJAX
 */
function is_ajax(): bool {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Return JSON response
 */
function json_response(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Sanitize filename (prevent path traversal)
 */
function safe_filename(string $filename): string {
    $filename = basename($filename);
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return $filename;
}
