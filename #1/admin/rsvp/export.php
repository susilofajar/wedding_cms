<?php
/**
 * Export RSVP Data to CSV
 * Wedding Invitation CMS
 */

require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once dirname(__DIR__, 2) . '/includes/db.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/helpers.php';

require_admin();

$filter = get_param('filter', 'all');
$allowed_filters = ['hadir', 'tidak_hadir', 'ragu'];
if ($filter !== 'all' && !in_array($filter, $allowed_filters, true)) {
    $filter = 'all';
}

$query = ($filter !== 'all')
    ? "SELECT * FROM rsvps WHERE attendance = ? ORDER BY created_at DESC"
    : "SELECT * FROM rsvps ORDER BY created_at DESC";

$rsvps = ($filter !== 'all') ? db_query($query, [$filter]) : db_query($query);

$filename = 'rsvp_tamu_' . date('Ymd_His') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// UTF-8 BOM for Microsoft Excel compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Header
fputcsv($output, ['No', 'Nama Tamu', 'Konfirmasi Kehadiran', 'Jumlah Tamu', 'Pesan / Ucapan', 'Waktu RSVP']);

$no = 1;
foreach ($rsvps as $row) {
    $attendance_label = match($row['attendance']) {
        'hadir'       => 'Hadir',
        'tidak_hadir' => 'Tidak Hadir',
        'ragu'        => 'Masih Ragu',
        default       => $row['attendance']
    };

    fputcsv($output, [
        $no++,
        $row['guest_name'],
        $attendance_label,
        (int)$row['guest_count'],
        $row['message'],
        date('d/m/Y H:i:s', strtotime($row['created_at']))
    ]);
}

fclose($output);
exit;
