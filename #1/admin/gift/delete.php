<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$id  = int_input('id', 0, 'GET');
$acc = get_bank_account($id);

if ($acc) {
    if (!empty($acc['bank_logo'])) delete_upload($acc['bank_logo']);
    if (!empty($acc['qris_image'])) delete_upload($acc['qris_image']);
    db_execute("DELETE FROM bank_accounts WHERE id = ?", [$id]);
    flash('success', 'Rekening berhasil dihapus.');
} else {
    flash('error', 'Rekening tidak ditemukan.');
}

redirect(admin_url('gift/index.php'));
