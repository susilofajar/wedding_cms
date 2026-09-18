<?php
/**
 * Application Functions
 * Wedding Invitation CMS
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// ─────────────────────────────────────────────
// SETTINGS
// ─────────────────────────────────────────────

/**
 * Get all settings as key => value array
 */
function get_all_settings(): array {
    static $settings = null;
    if ($settings === null) {
        $rows     = db_query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings;
}

/**
 * Get a single setting value
 */
function get_setting(string $key, string $default = ''): string {
    $settings = get_all_settings();
    return $settings[$key] ?? $default;
}

/**
 * Save settings (bulk upsert)
 */
function save_settings(array $data): bool {
    $pdo = get_db();
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
        );
        foreach ($data as $key => $value) {
            $stmt->execute([$key, $value]);
        }
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log('save_settings error: ' . $e->getMessage());
        return false;
    }
}

// ─────────────────────────────────────────────
// COUPLE
// ─────────────────────────────────────────────

function get_couple(): ?array {
    return db_row("SELECT * FROM couples LIMIT 1");
}

// ─────────────────────────────────────────────
// EVENTS
// ─────────────────────────────────────────────

function get_active_events(): array {
    return db_query(
        "SELECT * FROM events WHERE is_active = 1 ORDER BY sort_order ASC, event_date ASC"
    );
}

function get_all_events(): array {
    return db_query("SELECT * FROM events ORDER BY sort_order ASC, event_date ASC");
}

function get_event(int $id): ?array {
    return db_row("SELECT * FROM events WHERE id = ?", [$id]);
}

// ─────────────────────────────────────────────
// GALLERY
// ─────────────────────────────────────────────

function get_active_gallery(): array {
    return db_query(
        "SELECT * FROM gallery WHERE is_active = 1 ORDER BY sort_order ASC, id ASC"
    );
}

function get_all_gallery(): array {
    return db_query("SELECT * FROM gallery ORDER BY sort_order ASC, id ASC");
}

function get_gallery_item(int $id): ?array {
    return db_row("SELECT * FROM gallery WHERE id = ?", [$id]);
}

// ─────────────────────────────────────────────
// MUSIC
// ─────────────────────────────────────────────

function get_active_music(): ?array {
    return db_row("SELECT * FROM music WHERE is_active = 1 LIMIT 1");
}

function get_all_music(): array {
    return db_query("SELECT * FROM music ORDER BY id DESC");
}

function get_music_item(int $id): ?array {
    return db_row("SELECT * FROM music WHERE id = ?", [$id]);
}

// ─────────────────────────────────────────────
// LOVE STORY
// ─────────────────────────────────────────────

function get_active_stories(): array {
    return db_query(
        "SELECT * FROM love_stories WHERE is_active = 1 ORDER BY sort_order ASC, story_date ASC"
    );
}

function get_all_stories(): array {
    return db_query("SELECT * FROM love_stories ORDER BY sort_order ASC, story_date ASC");
}

function get_story(int $id): ?array {
    return db_row("SELECT * FROM love_stories WHERE id = ?", [$id]);
}

// ─────────────────────────────────────────────
// BANK ACCOUNTS / GIFT
// ─────────────────────────────────────────────

function get_active_bank_accounts(): array {
    return db_query("SELECT * FROM bank_accounts WHERE is_active = 1 ORDER BY id ASC");
}

function get_all_bank_accounts(): array {
    return db_query("SELECT * FROM bank_accounts ORDER BY id ASC");
}

function get_bank_account(int $id): ?array {
    return db_row("SELECT * FROM bank_accounts WHERE id = ?", [$id]);
}

// ─────────────────────────────────────────────
// RSVP
// ─────────────────────────────────────────────

function get_all_rsvps(): array {
    return db_query("SELECT * FROM rsvps ORDER BY created_at DESC");
}

function get_rsvp_counts(): array {
    $total  = db_value("SELECT COUNT(*) FROM rsvps") ?? 0;
    $hadir  = db_value("SELECT COUNT(*) FROM rsvps WHERE attendance = 'hadir'") ?? 0;
    $tidak  = db_value("SELECT COUNT(*) FROM rsvps WHERE attendance = 'tidak_hadir'") ?? 0;
    $ragu   = db_value("SELECT COUNT(*) FROM rsvps WHERE attendance = 'ragu'") ?? 0;
    return compact('total', 'hadir', 'tidak', 'ragu');
}

// ─────────────────────────────────────────────
// WISHES
// ─────────────────────────────────────────────

function get_approved_wishes(): array {
    return db_query(
        "SELECT * FROM wishes WHERE status = 'approved' ORDER BY created_at DESC"
    );
}

function get_all_wishes(): array {
    return db_query("SELECT * FROM wishes ORDER BY created_at DESC");
}

function get_wish(int $id): ?array {
    return db_row("SELECT * FROM wishes WHERE id = ?", [$id]);
}

function get_wishes_counts(): array {
    $total   = db_value("SELECT COUNT(*) FROM wishes") ?? 0;
    $pending = db_value("SELECT COUNT(*) FROM wishes WHERE status = 'pending'") ?? 0;
    return compact('total', 'pending');
}

// ─────────────────────────────────────────────
// UPLOAD HELPERS
// ─────────────────────────────────────────────

/**
 * Handle image upload, returns filename or null on failure
 */
function upload_image(array $file, string $directory = ''): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($file['size'] > MAX_IMAGE_SIZE) {
        return null;
    }
    $ext = file_extension($file['name']);
    if (!in_array($ext, ALLOWED_IMAGE_EXTENSIONS, true)) {
        return null;
    }
    // Validate MIME via getimagesize
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return null;
    }
    if (!in_array($imageInfo['mime'], ALLOWED_IMAGE_TYPES, true)) {
        return null;
    }

    $filename  = random_filename($ext);
    $targetDir = $directory ? rtrim(UPLOAD_IMAGES_DIR . $directory, '/') . '/' : UPLOAD_IMAGES_DIR;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
        return null;
    }
    return $directory ? $directory . '/' . $filename : $filename;
}

/**
 * Handle music upload, returns filename or null on failure
 */
function upload_music(array $file): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($file['size'] > MAX_MUSIC_SIZE) {
        return null;
    }
    $ext = file_extension($file['name']);
    if (!in_array($ext, ALLOWED_MUSIC_EXTENSIONS, true)) {
        return null;
    }
    // MIME check
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $validMimes = ['audio/mpeg', 'audio/mp3', 'audio/x-mp3', 'audio/x-mpeg', 'audio/mpeg3'];
    if (!in_array($mime, $validMimes, true)) {
        return null;
    }

    $filename = random_filename($ext);
    if (!is_dir(UPLOAD_MUSIC_DIR)) {
        mkdir(UPLOAD_MUSIC_DIR, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_MUSIC_DIR . $filename)) {
        return null;
    }
    return $filename;
}

/**
 * Delete an uploaded file safely
 */
function delete_upload(string $filename, string $type = 'image'): void {
    if (empty($filename)) return;
    $dir  = $type === 'music' ? UPLOAD_MUSIC_DIR : UPLOAD_IMAGES_DIR;
    $path = $dir . basename($filename);
    if (file_exists($path)) {
        unlink($path);
    }
}

// ─────────────────────────────────────────────
// ADMIN USERS
// ─────────────────────────────────────────────

function get_all_users(): array {
    return db_query("SELECT id, username, full_name, email, role, created_at FROM users ORDER BY id ASC");
}

function get_user(int $id): ?array {
    return db_row("SELECT id, username, full_name, email, role, created_at FROM users WHERE id = ?", [$id]);
}
