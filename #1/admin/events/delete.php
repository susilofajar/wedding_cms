<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id = int_input('id', 0, 'GET');
$ev = get_event($id);

if ($ev) {
    db_execute("DELETE FROM events WHERE id = ?", [$id]);
    flash('success', 'Acara berhasil dihapus.');
} else {
    flash('error', 'Acara tidak ditemukan.');
}

redirect(admin_url('events/index.php'));
