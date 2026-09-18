<?php
/**
 * Public Wedding Invitation - Main Single Page
 * Wedding Invitation CMS
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/functions.php';

// ─── Handle RSVP & Wishes AJAX Submissions ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_ajax()) {
    $action = post('action');

    if ($action === 'rsvp') {
        $guest_name  = trim(post('guest_name'));
        $attendance  = post('attendance');
        $guest_count = max(1, min(20, int_input('guest_count', 1)));
        $message     = trim(post('message'));

        $allowed_attendance = ['hadir', 'tidak_hadir', 'ragu'];
        if (empty($guest_name) || !in_array($attendance, $allowed_attendance, true)) {
            json_response(['success' => false, 'message' => 'Mohon lengkapi nama dan pilihan kehadiran.'], 422);
        }

        if (mb_strlen($guest_name) > 150) {
            json_response(['success' => false, 'message' => 'Nama terlalu panjang (maks 150 karakter).'], 422);
        }

        db_execute(
            "INSERT INTO rsvps (guest_name, attendance, guest_count, message) VALUES (?,?,?,?)",
            [$guest_name, $attendance, $guest_count, $message]
        );

        json_response(['success' => true, 'message' => 'Konfirmasi kehadiran (RSVP) Anda berhasil tersimpan. Terima kasih!']);
    }

    if ($action === 'wish') {
        $guest_name = trim(post('guest_name'));
        $message    = trim(post('message'));
        $attendance = post('attendance');

        $allowed = ['hadir', 'tidak_hadir', 'ragu'];
        if (!in_array($attendance, $allowed, true)) $attendance = 'hadir';

        if (empty($guest_name) || empty($message)) {
            json_response(['success' => false, 'message' => 'Nama dan ucapan doa wajib diisi.'], 422);
        }

        if (mb_strlen($guest_name) > 150 || mb_strlen($message) > 1000) {
            json_response(['success' => false, 'message' => 'Input terlalu panjang.'], 422);
        }

        db_execute(
            "INSERT INTO wishes (guest_name, message, attendance, status) VALUES (?,?,?,'pending')",
            [$guest_name, $message, $attendance]
        );

        json_response(['success' => true, 'message' => 'Ucapan dan doa terbaik Anda berhasil terkirim. Terima kasih!']);
    }

    json_response(['success' => false, 'message' => 'Aksi tidak valid.'], 400);
}

// ─── Load Website Data ────────────────────────────────────────────────────────
$s        = get_all_settings();
$couple   = get_couple();
$events   = get_active_events();
$gallery  = get_active_gallery();
$stories  = get_active_stories();
$music    = get_active_music();
$accounts = get_active_bank_accounts();
$wishes   = get_approved_wishes();

// Maintenance Check
if (($s['website_status'] ?? 'active') !== 'active') {
    http_response_code(503);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Website Maintenance</title>
    <style>body{font-family:-apple-system,BlinkMacSystemFont,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#fffaf7;color:#333;text-align:center;}
    .box{padding:40px 24px;max-width:480px;} h2{color:#8b5e5e;margin-bottom:12px;} p{color:#666;line-height:1.6;}</style></head>
    <body><div class="box"><h2>💍 Sedang Dalam Persiapan</h2><p>Website undangan pernikahan ini sedang dalam tahap penataan. Silakan kembali dalam beberapa saat.</p></div></body></html>';
    exit;
}

// Primary Event for Countdown & Meta
$primary_event = null;
foreach ($events as $ev) {
    if ($ev['type'] === 'akad' || $ev['type'] === 'reception') {
        $primary_event = $ev;
        break;
    }
}
if (!$primary_event && !empty($events)) {
    $primary_event = $events[0];
}

// Guest Name from query parameter (e.g. ?to=Bapak+Ahmad or ?to=Nama+Tamu)
$guest_name_param = htmlspecialchars(trim(get_param('to', '')), ENT_QUOTES, 'UTF-8');
$guest_pronoun    = htmlspecialchars(trim(get_param('p', 'Bapak/Ibu/Saudara/i')), ENT_QUOTES, 'UTF-8');

// Theme Styling Variables
$primary_color    = $s['primary_color']    ?? '#8b5e5e';
$secondary_color  = $s['secondary_color']  ?? '#d8b4a0';
$background_color = $s['background_color'] ?? '#fffaf7';
$text_color       = $s['text_color']       ?? '#2d2424';
$font_heading     = $s['font_heading']      ?? 'Playfair Display';
$font_body        = $s['font_body']         ?? 'Lato';

// Google Calendar URL Generator
$gcal_url = '';
if ($primary_event) {
    $event_title = urlencode(($couple['bride_nickname'] ?? 'Mempelai') . ' & ' . ($couple['groom_nickname'] ?? 'Mempelai') . ' Wedding');
    $event_loc   = urlencode($primary_event['location'] . ', ' . $primary_event['address']);
    $event_desc  = urlencode($primary_event['description'] ?: 'The Wedding of ' . ($couple['bride_full_name'] ?? '') . ' & ' . ($couple['groom_full_name'] ?? ''));
    $dt_start    = date('Ymd\THis', strtotime($primary_event['event_date'] . ' ' . $primary_event['start_time']));
    $dt_end      = !empty($primary_event['end_time'])
        ? date('Ymd\THis', strtotime($primary_event['event_date'] . ' ' . $primary_event['end_time']))
        : date('Ymd\THis', strtotime($primary_event['event_date'] . ' ' . $primary_event['start_time'] . ' +3 hours'));
    $gcal_url = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$event_title}&dates={$dt_start}/{$dt_end}&details={$event_desc}&location={$event_loc}";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= e($s['meta_title'] ?? $s['site_title'] ?? 'Undangan Pernikahan') ?></title>
    <meta name="description" content="<?= e($s['meta_description'] ?? 'Undangan Pernikahan Digital') ?>">

    <!-- Open Graph / Meta Share -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($s['meta_title'] ?? $s['site_title'] ?? 'Undangan Pernikahan') ?>">
    <meta property="og:description" content="<?= e($s['meta_description'] ?? '') ?>">
    <?php if (!empty($s['social_image'])): ?>
    <meta property="og:image" content="<?= upload_url(e($s['social_image'])) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <?php if (!empty($s['favicon'])): ?>
    <link rel="icon" href="<?= upload_url(e($s['favicon'])) ?>">
    <?php endif; ?>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=<?= urlencode($font_heading) ?>:ital,wght@0,400;0,600;0,700;1,400;1,600&family=<?= urlencode($font_body) ?>:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Libraries (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

    <!-- App Custom CSS (with Cache Buster) -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=' . (file_exists(__DIR__ . '/assets/css/style.css') ? filemtime(__DIR__ . '/assets/css/style.css') : time())) ?>">

    <!-- Dynamic Theme Palette & Critical Layout CSS -->
    <style>
        :root {
            --primary: <?= e($primary_color) ?>;
            --secondary: <?= e($secondary_color) ?>;
            --bg: <?= e($background_color) ?>;
            --text: <?= e($text_color) ?>;
            --font-heading: '<?= e($font_heading) ?>', Georgia, serif;
            --font-body: '<?= e($font_body) ?>', -apple-system, sans-serif;
        }

        /* Critical Defensive Styles (Guarantees corners never break flex layout even if CSS is cached) */
        .opening-corner-floral {
            position: absolute !important;
            width: clamp(120px, 24vw, 240px);
            pointer-events: none;
            z-index: 2;
        }
        .opening-floral-tl { top: -15px; left: -15px; }
        .opening-floral-tr { top: -15px; right: -15px; transform: scaleX(-1); }
        .opening-floral-bl { bottom: -15px; left: -15px; transform: scaleY(-1); }
        .opening-floral-br { bottom: -15px; right: -15px; transform: scale(-1); }
        .opening-curtain { position: absolute !important; }
        .hero-floral-corner {
            position: absolute !important;
            width: clamp(100px, 18vw, 190px);
            pointer-events: none;
            z-index: 1;
        }
        .hero-floral-tl { top: -10px; left: -10px; }
        .hero-floral-tr { top: -10px; right: -10px; transform: scaleX(-1); }
    </style>
</head>
<body data-aos-easing="ease-out-cubic" data-aos-duration="900" data-aos-delay="0">

<!-- Floating Petals Container (CSS Hardware-accelerated) -->
<div id="petalsContainer" aria-hidden="true"></div>

<!-- ══════════════════════════════════════════
     OPENING / COVER SCREEN
══════════════════════════════════════════ -->
<section id="opening" class="opening-screen">
    <!-- Envelope Split Curtains -->
    <div class="opening-curtain opening-curtain-top" aria-hidden="true"></div>
    <div class="opening-curtain opening-curtain-bottom" aria-hidden="true"></div>

    <!-- Opening Floral Corner Accents -->
    <div class="opening-corner-floral opening-floral-tl" aria-hidden="true">
        <img src="<?= base_url('assets/images/floral-corner.png') ?>" alt="" class="floral-corner-img">
    </div>
    <div class="opening-corner-floral opening-floral-tr" aria-hidden="true">
        <img src="<?= base_url('assets/images/floral-corner.png') ?>" alt="" class="floral-corner-img">
    </div>
    <div class="opening-corner-floral opening-floral-bl" aria-hidden="true">
        <img src="<?= base_url('assets/images/floral-corner.png') ?>" alt="" class="floral-corner-img">
    </div>
    <div class="opening-corner-floral opening-floral-br" aria-hidden="true">
        <img src="<?= base_url('assets/images/floral-corner.png') ?>" alt="" class="floral-corner-img">
    </div>

    <div class="opening-backdrop"></div>

    <div class="opening-card animate-opening-card">
        <div class="opening-envelope-badge">
            <i class="fas fa-heart"></i>
        </div>
        <p class="opening-label"><?= e($s['invitation_title'] ?? 'The Wedding Of') ?></p>

        <?php if ($couple): ?>
        <h1 class="opening-names shimmer-text">
            <?= e($couple['bride_nickname'] ?: $couple['bride_full_name']) ?>
            <span class="opening-and">&amp;</span>
            <?= e($couple['groom_nickname'] ?: $couple['groom_full_name']) ?>
        </h1>
        <?php else: ?>
        <h1 class="opening-names shimmer-text">Nama Wanita <span class="opening-and">&amp;</span> Nama Pria</h1>
        <?php endif; ?>

        <?php if ($primary_event): ?>
        <p class="opening-date"><i class="far fa-calendar-alt me-2"></i><?= e(format_date($primary_event['event_date'], 'l, d F Y')) ?></p>
        <?php endif; ?>

        <?php if ($guest_name_param): ?>
        <div class="opening-guest-box">
            <div class="guest-title">Kepada Yth. <?= e($guest_pronoun) ?>:</div>
            <div class="guest-name"><?= $guest_name_param ?></div>
        </div>
        <?php endif; ?>

        <button class="btn-open-invitation" id="btnOpen" onclick="openInvitation()">
            <i class="fas fa-envelope-open-text"></i>Buka Undangan
        </button>
    </div>
</section>

<!-- ══════════════════════════════════════════
     MAIN INVITATION CONTENT
══════════════════════════════════════════ -->
<main id="mainInvitation" style="display:none;">

<!-- ══════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════ -->
<section id="hero" class="hero-section">
    <div class="hero-bg-overlay"></div>

    <!-- Hero Floral Corner Ornaments -->
    <div class="hero-floral-corner hero-floral-tl" aria-hidden="true">
        <img src="<?= base_url('assets/images/floral-corner.png') ?>" alt="" class="floral-corner-img">
    </div>
    <div class="hero-floral-corner hero-floral-tr" aria-hidden="true">
        <img src="<?= base_url('assets/images/floral-corner.png') ?>" alt="" class="floral-corner-img">
    </div>

    <div class="hero-content" data-aos="fade-up">
        <div class="hero-badge"><?= e($s['invitation_title'] ?? 'The Wedding Of') ?></div>
        <?php if ($couple): ?>
        <h1 class="hero-names shimmer-text">
            <?= e($couple['bride_nickname'] ?: $couple['bride_full_name']) ?>
            <span class="hero-and">&amp;</span>
            <?= e($couple['groom_nickname'] ?: $couple['groom_full_name']) ?>
        </h1>
        <?php endif; ?>

        <?php if ($primary_event): ?>
        <p class="hero-date"><i class="far fa-calendar-check me-2"></i><?= e(format_date($primary_event['event_date'], 'l, d F Y')) ?></p>
        <?php endif; ?>

        <p class="hero-quote-brief">"Dan Kami menciptakan kamu berpasang-pasangan" (QS. An-Naba: 8)</p>
    </div>
    <div class="scroll-indicator">
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

<!-- ══════════════════════════════════════════
     COUPLE SECTION
══════════════════════════════════════════ -->
<?php if (($s['show_section_couple'] ?? '1') === '1' && $couple): ?>
<section id="couple" class="section">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <span class="section-eyebrow">Mempelai</span>
            <h2 class="section-title">Dua Insan Bersatu</h2>
            <div class="section-divider">
                <span></span><i class="fas fa-heart"></i><span></span>
            </div>
            <?php if (!empty($couple['description'])): ?>
            <p class="section-desc"><?= e($couple['description']) ?></p>
            <?php endif; ?>
        </div>

        <div class="row g-4 g-lg-5 justify-content-center align-items-center">
            <!-- Bride -->
            <div class="col-md-5 col-lg-5" data-aos="fade-right">
                <div class="couple-card text-center">
                    <div class="couple-icon-badge">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="couple-name"><?= e($couple['bride_full_name']) ?></h3>
                    <?php if ($couple['bride_nickname']): ?>
                    <p class="couple-nickname">"<?= e($couple['bride_nickname']) ?>"</p>
                    <?php endif; ?>
                    <div class="couple-decor-line"></div>
                    <?php if ($couple['bride_order']): ?>
                    <p class="couple-order"><?= e($couple['bride_order']) ?></p>
                    <?php endif; ?>
                    <?php if ($couple['bride_father'] || $couple['bride_mother']): ?>
                    <p class="couple-parents">
                        Putri dari Bapak <?= e($couple['bride_father']) ?><br>&amp; Ibu <?= e($couple['bride_mother']) ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($couple['bride_instagram'])): ?>
                    <a href="https://instagram.com/<?= e(ltrim($couple['bride_instagram'], '@')) ?>" target="_blank" rel="noopener" class="couple-social-link">
                        <i class="fab fa-instagram"></i>@<?= e(ltrim($couple['bride_instagram'], '@')) ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Monogram Divider -->
            <div class="col-12 col-md-2 text-center" data-aos="zoom-in">
                <div class="couple-center-badge">&amp;</div>
            </div>

            <!-- Groom -->
            <div class="col-md-5 col-lg-5" data-aos="fade-left">
                <div class="couple-card text-center">
                    <div class="couple-icon-badge">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="couple-name"><?= e($couple['groom_full_name']) ?></h3>
                    <?php if ($couple['groom_nickname']): ?>
                    <p class="couple-nickname">"<?= e($couple['groom_nickname']) ?>"</p>
                    <?php endif; ?>
                    <div class="couple-decor-line"></div>
                    <?php if ($couple['groom_order']): ?>
                    <p class="couple-order"><?= e($couple['groom_order']) ?></p>
                    <?php endif; ?>
                    <?php if ($couple['groom_father'] || $couple['groom_mother']): ?>
                    <p class="couple-parents">
                        Putra dari Bapak <?= e($couple['groom_father']) ?><br>&amp; Ibu <?= e($couple['groom_mother']) ?>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($couple['groom_instagram'])): ?>
                    <a href="https://instagram.com/<?= e(ltrim($couple['groom_instagram'], '@')) ?>" target="_blank" rel="noopener" class="couple-social-link">
                        <i class="fab fa-instagram"></i>@<?= e(ltrim($couple['groom_instagram'], '@')) ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Floral Divider -->
<div class="floral-divider" aria-hidden="true">
    <span class="divider-line"></span>
    <img src="<?= base_url('assets/images/floral-divider.png') ?>" class="floral-divider-img" alt="Bunga Dekorasi" loading="lazy">
    <span class="divider-line"></span>
</div>

<!-- ══════════════════════════════════════════
     WEDDING QUOTE SECTION
══════════════════════════════════════════ -->
<?php if (!empty($s['wedding_quote'])): ?>
<section class="quote-section">
    <div class="container">
        <div class="quote-card" data-aos="fade-up">
            <i class="fas fa-quote-left quote-icon"></i>
            <blockquote class="quote-text"><?= e($s['wedding_quote']) ?></blockquote>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Floral Divider -->
<div class="floral-divider" aria-hidden="true">
    <span class="divider-line"></span>
    <img src="<?= base_url('assets/images/floral-divider.png') ?>" class="floral-divider-img" alt="Bunga Dekorasi" loading="lazy">
    <span class="divider-line"></span>
</div>

<!-- ══════════════════════════════════════════
     COUNTDOWN SECTION
══════════════════════════════════════════ -->
<?php if ($primary_event): ?>
<section id="countdown" class="countdown-section">
    <div class="container text-center">
        <div data-aos="fade-up">
            <span class="section-eyebrow" style="color:var(--secondary);">Menuju Hari Bahagia</span>
            <h2 class="section-title" style="color:#fff;"><?= e(format_date($primary_event['event_date'])) ?></h2>
        </div>

        <div class="countdown-wrap" data-aos="fade-up" data-aos-delay="100">
            <div class="countdown-box">
                <div class="countdown-number" id="cd-days">00</div>
                <div class="countdown-label">Hari</div>
            </div>
            <div class="countdown-sep">:</div>
            <div class="countdown-box">
                <div class="countdown-number" id="cd-hours">00</div>
                <div class="countdown-label">Jam</div>
            </div>
            <div class="countdown-sep">:</div>
            <div class="countdown-box">
                <div class="countdown-number" id="cd-minutes">00</div>
                <div class="countdown-label">Menit</div>
            </div>
            <div class="countdown-sep">:</div>
            <div class="countdown-box">
                <div class="countdown-number" id="cd-seconds">00</div>
                <div class="countdown-label">Detik</div>
            </div>
        </div>

        <p id="cd-done-msg" class="countdown-done" style="display:none;">
            <i class="fas fa-heart me-2"></i>Acara Telah Berlangsung
        </p>

        <?php if (!empty($gcal_url)): ?>
        <div class="mt-4" data-aos="fade-up" data-aos-delay="200">
            <a href="<?= $gcal_url ?>" target="_blank" rel="noopener" class="btn-calendar-save">
                <i class="fab fa-google"></i>Simpan ke Google Calendar
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════
     EVENTS SECTION
══════════════════════════════════════════ -->
<?php if (($s['show_section_events'] ?? '1') === '1' && !empty($events)): ?>
<section id="events" class="section">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <span class="section-eyebrow">Rangkaian Acara</span>
            <h2 class="section-title">Waktu &amp; Tempat</h2>
            <div class="section-divider">
                <span></span><i class="fas fa-calendar-alt"></i><span></span>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ($events as $i => $ev): ?>
            <div class="col-md-6 col-lg-5" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                <div class="event-card">
                    <div class="event-icon-badge">
                        <i class="<?= e($ev['icon'] ?: 'fas fa-calendar-alt') ?>"></i>
                    </div>
                    <h3 class="event-title"><?= e($ev['name']) ?></h3>
                    <div class="event-meta-pill">
                        <i class="far fa-calendar-alt"></i><?= e(format_date($ev['event_date'], 'l, d F Y')) ?>
                    </div>
                    <p class="event-time-text">
                        <i class="far fa-clock me-1 text-primary"></i>
                        <?= e(date('H:i', strtotime($ev['start_time']))) ?> WIB
                        <?php if ($ev['end_time']): ?>
                        — <?= e(date('H:i', strtotime($ev['end_time']))) ?> WIB
                        <?php else: ?>
                        — selesai
                        <?php endif; ?>
                    </p>
                    <p class="event-location-name"><i class="fas fa-map-marker-alt me-1 text-danger"></i><?= e($ev['location']) ?></p>
                    <?php if ($ev['address']): ?>
                    <p class="event-address"><?= e($ev['address']) ?></p>
                    <?php endif; ?>
                    <?php if ($ev['description']): ?>
                    <p class="event-desc"><?= e($ev['description']) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($ev['maps_url'])): ?>
                    <div class="mt-auto">
                        <a href="<?= e($ev['maps_url']) ?>" target="_blank" rel="noopener" class="btn-event-action">
                            <i class="fas fa-map-marked-alt"></i>Petunjuk Arah Maps
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Floral Divider -->
<div class="floral-divider" aria-hidden="true">
    <span class="divider-line"></span>
    <img src="<?= base_url('assets/images/floral-divider.png') ?>" class="floral-divider-img" alt="Bunga Dekorasi" loading="lazy">
    <span class="divider-line"></span>
</div>

<!-- ══════════════════════════════════════════
     LOVE STORY SECTION
══════════════════════════════════════════ -->
<?php if (($s['show_section_story'] ?? '1') === '1' && !empty($stories)): ?>
<section id="story" class="section section-alt">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <span class="section-eyebrow">Perjalanan Cinta</span>
            <h2 class="section-title">Our Love Story</h2>
            <div class="section-divider">
                <span></span><i class="fas fa-book-open"></i><span></span>
            </div>
        </div>

        <div class="story-timeline">
            <?php foreach ($stories as $i => $st): ?>
            <div class="story-item <?= $i % 2 === 0 ? 'story-left' : 'story-right' ?>"
                 data-aos="<?= $i % 2 === 0 ? 'fade-right' : 'fade-left' ?>">
                <div class="story-dot"><i class="fas fa-heart"></i></div>
                <div class="story-card">
                    <span class="story-date-badge"><?= e($st['story_date']) ?></span>
                    <h4 class="story-title"><?= e($st['title']) ?></h4>
                    <?php if ($st['description']): ?>
                    <p class="story-desc"><?= e($st['description']) ?></p>
                    <?php endif; ?>
                    <?php if ($st['image']): ?>
                    <img src="<?= upload_url(e($st['image'])) ?>"
                         alt="<?= e($st['title']) ?>" class="story-image" loading="lazy">
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Floral Divider -->
<div class="floral-divider" aria-hidden="true">
    <span class="divider-line"></span>
    <img src="<?= base_url('assets/images/floral-divider.png') ?>" class="floral-divider-img" alt="Bunga Dekorasi" loading="lazy">
    <span class="divider-line"></span>
</div>

<!-- ══════════════════════════════════════════
     GALLERY SECTION
══════════════════════════════════════════ -->
<?php if (($s['show_section_gallery'] ?? '1') === '1' && !empty($gallery)): ?>
<section id="gallery" class="section">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <span class="section-eyebrow">Galeri Foto</span>
            <h2 class="section-title">Momen Bahagia</h2>
            <div class="section-divider">
                <span></span><i class="fas fa-camera"></i><span></span>
            </div>
        </div>

        <div class="gallery-grid" data-aos="fade-up">
            <?php foreach ($gallery as $idx => $item): ?>
            <div class="gallery-card"
                 data-src="<?= upload_url(e($item['image'])) ?>"
                 data-title="<?= e($item['title']) ?>"
                 onclick="openLightboxByIndex(<?= $idx ?>)">
                <img src="<?= upload_url(e($item['image'])) ?>" alt="<?= e($item['title'] ?: 'Foto Galeri') ?>" loading="lazy">
                <div class="gallery-overlay">
                    <div class="gallery-overlay-icon"><i class="fas fa-search-plus"></i></div>
                    <?php if ($item['title']): ?>
                    <p class="gallery-overlay-title"><?= e($item['title']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Lightbox Modal Component -->
<div id="lightboxModal" class="lightbox-modal" onclick="closeLightbox()">
    <button class="lightbox-btn-close" onclick="closeLightbox()" title="Tutup"><i class="fas fa-times"></i></button>
    <button class="lightbox-btn-nav lightbox-btn-prev" onclick="prevLightbox(event)" title="Sebelumnya"><i class="fas fa-chevron-left"></i></button>
    <button class="lightbox-btn-nav lightbox-btn-next" onclick="nextLightbox(event)" title="Selanjutnya"><i class="fas fa-chevron-right"></i></button>
    <div class="lightbox-img-wrap" onclick="event.stopPropagation()">
        <img id="lightboxImg" src="" alt="Gallery Preview">
    </div>
    <div id="lightboxCaption" class="lightbox-caption"></div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════
     WEDDING GIFT / DIGITAL ENVELOPE
══════════════════════════════════════════ -->
<?php if (($s['show_section_gift'] ?? '1') === '1' && !empty($accounts)): ?>
<section id="gift" class="section section-alt">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <span class="section-eyebrow">Tanda Kasih</span>
            <h2 class="section-title">Amplop Digital</h2>
            <div class="section-divider">
                <span></span><i class="fas fa-gift"></i><span></span>
            </div>
            <p class="section-desc">Doa restu Anda merupakan karunia terindah bagi kami. Namun jika Anda berkehendak memberikan kado tanda kasih, dapat disalurkan melalui rekening berikut.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach ($accounts as $acc): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="gift-card">
                    <div class="gift-logo-wrap">
                        <?php if (!empty($acc['bank_logo'])): ?>
                        <img src="<?= upload_url(e($acc['bank_logo'])) ?>" alt="<?= e($acc['bank_name']) ?>" class="gift-bank-logo" loading="lazy">
                        <?php else: ?>
                        <i class="fas fa-university gift-bank-icon"></i>
                        <?php endif; ?>
                    </div>
                    <h4 class="gift-bank-name"><?= e($acc['bank_name']) ?></h4>
                    <div class="gift-holder-label">Atas Nama</div>
                    <div class="gift-holder-name"><?= e($acc['account_holder']) ?></div>

                    <div class="gift-number-box">
                        <p class="gift-account-number"><?= e($acc['account_number']) ?></p>
                    </div>

                    <button class="btn-copy-account" data-number="<?= e($acc['account_number']) ?>" onclick="copyAccount(this)">
                        <i class="fas fa-copy"></i>Salin Nomor Rekening
                    </button>

                    <?php if (!empty($acc['qris_image'])): ?>
                    <button class="gift-qris-btn" onclick="showQrisModal('<?= upload_url(e($acc['qris_image'])) ?>', '<?= e(addslashes($acc['bank_name'])) ?>')">
                        <i class="fas fa-qrcode"></i>Lihat QRIS
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Floral Divider -->
<div class="floral-divider" aria-hidden="true">
    <span class="divider-line"></span>
    <img src="<?= base_url('assets/images/floral-divider.png') ?>" class="floral-divider-img" alt="Bunga Dekorasi" loading="lazy">
    <span class="divider-line"></span>
</div>

<!-- QRIS Modal -->
<div class="modal fade" id="qrisModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <div class="modal-header border-0 pb-0 justify-content-center position-relative">
                <h5 class="modal-title font-heading fw-bold" id="qrisModalTitle">QRIS Pembayaran</h5>
                <button type="button" class="btn-close position-absolute end-0 top-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <img id="qrisModalImg" src="" alt="QRIS" class="img-fluid rounded shadow-sm mx-auto mb-2" style="max-height:360px;">
                <p class="text-muted small mb-0">Pindai kode QRIS menggunakan aplikasi perbankan atau e-wallet Anda.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════
     RSVP SECTION
══════════════════════════════════════════ -->
<?php if (($s['show_section_rsvp'] ?? '1') === '1'): ?>
<section id="rsvp" class="section">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <span class="section-eyebrow">Konfirmasi</span>
            <h2 class="section-title">Konfirmasi Kehadiran (RSVP)</h2>
            <div class="section-divider">
                <span></span><i class="fas fa-clipboard-check"></i><span></span>
            </div>
            <p class="section-desc">Mohon kesediaan Anda untuk mengonfirmasi kehadiran demi kenyamanan persiapan acara kami.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6" data-aos="fade-up">
                <div class="form-card">
                    <div id="rsvp-msg" style="display:none;"></div>
                    <form id="rsvpForm" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="guest_name" id="rsvp_name" class="form-control"
                                   placeholder="Contoh: Bpk. Ahmad &amp; Keluarga" required maxlength="150"
                                   value="<?= $guest_name_param ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
                            <select name="attendance" class="form-select" required>
                                <option value="">— Pilih Kehadiran —</option>
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                                <option value="ragu">Masih Ragu</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Orang yang Hadir</label>
                            <input type="number" name="guest_count" class="form-control"
                                   value="1" min="1" max="10">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Catatan Tambahan (Opsional)</label>
                            <textarea name="message" class="form-control" rows="2"
                                      placeholder="Pesan tambahan untuk kedua mempelai..."></textarea>
                        </div>
                        <button type="submit" class="btn-form-submit w-100" id="rsvpSubmit">
                            <i class="fas fa-paper-plane"></i>Kirim RSVP
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════
     WISHES / GUESTBOOK SECTION
══════════════════════════════════════════ -->
<?php if (($s['show_section_wishes'] ?? '1') === '1'): ?>
<section id="wishes" class="section section-alt">
    <div class="container">
        <div class="section-header text-center" data-aos="fade-up">
            <span class="section-eyebrow">Ucapan &amp; Doa</span>
            <h2 class="section-title">Buku Tamu</h2>
            <div class="section-divider">
                <span></span><i class="fas fa-heart"></i><span></span>
            </div>
            <p class="section-desc">Tinggalkan untaian doa dan ucapan selamat untuk kedua mempelai.</p>
        </div>

        <!-- Wish Form -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8 col-lg-6" data-aos="fade-up">
                <div class="form-card">
                    <div id="wish-msg" style="display:none;"></div>
                    <form id="wishForm" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Nama Anda <span class="text-danger">*</span></label>
                            <input type="text" name="guest_name" class="form-control"
                                   placeholder="Nama Anda" required maxlength="150"
                                   value="<?= $guest_name_param ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kehadiran</label>
                            <select name="attendance" class="form-select">
                                <option value="hadir">Hadir</option>
                                <option value="tidak_hadir">Tidak Hadir</option>
                                <option value="ragu">Masih Ragu</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Ucapan &amp; Doa Restu <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="3"
                                      placeholder="Tulis ucapan dan doa terbaik Anda..." required maxlength="1000"></textarea>
                        </div>
                        <button type="submit" class="btn-form-submit w-100" id="wishSubmit">
                            <i class="fas fa-heart"></i>Kirim Ucapan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Wishes List -->
        <div class="wishes-container" id="wishesContainer" data-aos="fade-up">
            <?php if (empty($wishes)): ?>
            <p class="text-center text-muted">Belum ada ucapan. Jadilah yang pertama memberikan doa restu!</p>
            <?php else: ?>
                <?php foreach ($wishes as $w): ?>
                <div class="wish-item">
                    <div class="wish-avatar">
                        <?= mb_strtoupper(mb_substr($w['guest_name'], 0, 1)) ?>
                    </div>
                    <div class="wish-body">
                        <div class="wish-header">
                            <span class="wish-author"><?= e($w['guest_name']) ?></span>
                            <?php if ($w['attendance'] === 'hadir'): ?>
                                <span class="wish-badge-att hadir"><i class="fas fa-check-circle me-1"></i>Hadir</span>
                            <?php elseif ($w['attendance'] === 'tidak_hadir'): ?>
                                <span class="wish-badge-att tidak_hadir"><i class="fas fa-times-circle me-1"></i>Tidak Hadir</span>
                            <?php else: ?>
                                <span class="wish-badge-att ragu"><i class="fas fa-question-circle me-1"></i>Masih Ragu</span>
                            <?php endif; ?>
                        </div>
                        <p class="wish-message"><?= e($w['message']) ?></p>
                        <p class="wish-time"><i class="far fa-clock me-1"></i><?= e(format_date($w['created_at'], 'd F Y')) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════
     FOOTER
══════════════════════════════════════════ -->
<footer class="site-footer">
    <div class="footer-heart"><i class="fas fa-heart"></i></div>
    <?php if ($couple): ?>
    <h3 class="footer-names">
        <?= e($couple['bride_nickname'] ?: $couple['bride_full_name']) ?>
        &amp;
        <?= e($couple['groom_nickname'] ?: $couple['groom_full_name']) ?>
    </h3>
    <?php endif; ?>
    <?php if ($primary_event): ?>
    <p class="footer-date"><?= e(format_date($primary_event['event_date'], 'l, d F Y')) ?></p>
    <?php endif; ?>
    <p class="footer-credit">Made with love &bull; Wedding Invitation Digital</p>
</footer>

</main><!-- /#mainInvitation -->

<!-- ══════════════════════════════════════════
     FLOATING AUTO-SCROLL CONTROLLER (Volume-Style)
══════════════════════════════════════════ -->
<div class="autoscroll-controller" id="autoScrollWidget" style="display:none;">
    <div class="autoscroll-inner">
        <!-- Play / Pause Toggle -->
        <button type="button" class="btn-autoscroll" id="btnToggleAutoScroll" onclick="toggleAutoScroll()" title="Pause / Lanjutkan Scroll Otomatis">
            <span class="autoscroll-status-dot"></span>
            <i class="fas fa-pause" id="autoScrollIcon"></i>
        </button>

        <!-- Volume-Style Speed Fader -->
        <div class="scroll-speed-fader" id="scrollSpeedFader" title="Geser untuk mengatur kecepatan scroll">
            <i class="fas fa-gauge-low scroll-speed-icon" id="scrollSpeedIconLeft" aria-hidden="true"></i>
            <div class="scroll-slider-wrap">
                <input type="range" class="scroll-speed-slider" id="scrollSpeedSlider"
                       min="8" max="100" value="36" step="1"
                       aria-label="Kecepatan Scroll">
            </div>
            <i class="fas fa-gauge-high scroll-speed-icon" id="scrollSpeedIconRight" aria-hidden="true"></i>
            <span class="scroll-speed-badge" id="scrollSpeedBadge">1.0x</span>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════
     FLOATING MUSIC PLAYER WIDGET
══════════════════════════════════════════ -->
<?php if (($s['show_section_music'] ?? '1') === '1' && $music): ?>
<div class="music-player-widget" id="musicPlayer" style="display:none;">
    <audio id="bgMusic" loop>
        <source src="<?= upload_url(e($music['filename']), 'music') ?>" type="audio/mpeg">
    </audio>
    <div class="music-disc" id="musicDisc" onclick="toggleMusic()" title="<?= e($music['title']) ?>">
        <i class="fas fa-play" id="musicIcon"></i>
    </div>
    <div class="music-info-text"><?= e(truncate($music['title'], 22)) ?></div>
    <div class="music-waves">
        <div class="music-wave-bar"></div>
        <div class="music-wave-bar"></div>
        <div class="music-wave-bar"></div>
    </div>
</div>
<?php endif; ?>

<!-- Core JavaScript CDN & App JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    window.WEDDING_DATA = {
        hasMusicPlayer: <?= (($s['show_section_music'] ?? '1') === '1' && $music) ? 'true' : 'false' ?>,
        countdownTarget: <?= $primary_event ? '"' . e($primary_event['event_date'] . 'T' . $primary_event['start_time']) . '"' : 'null' ?>
    };
</script>
<script src="<?= base_url('assets/js/app.js?v=' . (file_exists(__DIR__ . '/assets/js/app.js') ? filemtime(__DIR__ . '/assets/js/app.js') : time())) ?>"></script>
</body>
</html>
