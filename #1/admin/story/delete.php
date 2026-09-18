<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id = int_input('id', 0, 'GET');
$s  = get_story($id);

if ($s) {
    if (!empty($s['image'])) delete_upload($s['image']);
    db_execute("DELETE FROM love_stories WHERE id = ?", [$id]);
    flash('success', 'Cerita berhasil dihapus.');
} else {
    flash('error', 'Cerita tidak ditemukan.');
}

redirect(admin_url('story/index.php'));
