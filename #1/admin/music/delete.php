<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id = int_input('id', 0, 'GET');
$m  = get_music_item($id);

if ($m) {
    delete_upload($m['filename'], 'music');
    db_execute("DELETE FROM music WHERE id = ?", [$id]);
    flash('success', 'Musik berhasil dihapus.');
} else {
    flash('error', 'Musik tidak ditemukan.');
}

redirect(admin_url('music/index.php'));
