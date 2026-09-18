<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id   = int_input('id', 0, 'GET');
$item = get_gallery_item($id);

if ($item) {
    delete_upload($item['image']);
    db_execute("DELETE FROM gallery WHERE id = ?", [$id]);
    flash('success', 'Foto berhasil dihapus.');
} else {
    flash('error', 'Foto tidak ditemukan.');
}

redirect(admin_url('gallery/index.php'));
