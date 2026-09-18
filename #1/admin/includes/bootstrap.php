<?php
/**
 * Admin Bootstrap - minimal include untuk action-only files (delete, dll.)
 * Tidak merender HTML apapun.
 */
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 2) . '/includes/db.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/csrf.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';

start_session();
require_admin();
