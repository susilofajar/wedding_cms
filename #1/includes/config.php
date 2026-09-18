<?php
/**
 * Main Configuration File
 * Wedding Invitation CMS
 */

// Database Configuration
//define('DB_HOST', 'sql105.infinityfree.com');
//define('DB_NAME', 'if0_42900476_wedding_cms');
//define('DB_USER', 'if0_42900476');
//define('DB_PASS', 'Zqgb3wUpqprt');
//define('DB_CHARSET', 'utf8mb4');

//
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'if0_42900476_wedding_cms');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Wedding Invitation CMS');
define('APP_URL', ''); 
define('APP_VERSION', '1.0.0');

// Upload Configuration
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_IMAGES_DIR', UPLOAD_DIR . 'images/');
define('UPLOAD_MUSIC_DIR', UPLOAD_DIR . 'music/');
define('UPLOAD_URL', 'uploads/'); // Relative upload URL

// Upload Size Limits (in bytes)
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_MUSIC_SIZE', 20 * 1024 * 1024); // 20MB

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp', 'gif']);
define('ALLOWED_MUSIC_TYPES', ['audio/mpeg', 'audio/mp3', 'audio/x-mp3']);
define('ALLOWED_MUSIC_EXTENSIONS', ['mp3']);

// Session Configuration
define('SESSION_NAME', 'wedding_cms_session');
define('SESSION_LIFETIME', 7200); // 2 hours

// Security
define('CSRF_TOKEN_NAME', '_csrf_token');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error reporting (set to 0 for production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
