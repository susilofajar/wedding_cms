<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $data = [
        'site_title'           => post('site_title'),
        'invitation_title'     => post('invitation_title'),
        'meta_title'           => post('meta_title'),
        'meta_description'     => post('meta_description'),
        'primary_color'        => post('primary_color'),
        'secondary_color'      => post('secondary_color'),
        'background_color'     => post('background_color'),
        'text_color'           => post('text_color'),
        'font_heading'         => post('font_heading'),
        'font_body'            => post('font_body'),
        'wedding_quote'        => post('wedding_quote'),
        'website_status'       => post('website_status'),
        'show_section_couple'  => isset($_POST['show_section_couple'])  ? '1' : '0',
        'show_section_story'   => isset($_POST['show_section_story'])   ? '1' : '0',
        'show_section_events'  => isset($_POST['show_section_events'])  ? '1' : '0',
        'show_section_gallery' => isset($_POST['show_section_gallery']) ? '1' : '0',
        'show_section_gift'    => isset($_POST['show_section_gift'])    ? '1' : '0',
        'show_section_rsvp'    => isset($_POST['show_section_rsvp'])    ? '1' : '0',
        'show_section_wishes'  => isset($_POST['show_section_wishes'])  ? '1' : '0',
        'show_section_music'   => isset($_POST['show_section_music'])   ? '1' : '0',
    ];

    // Handle favicon upload
    if (!empty($_FILES['favicon']['name'])) {
        $fname = upload_image($_FILES['favicon']);
        if ($fname) {
            $old = get_setting('favicon');
            if ($old) delete_upload($old);
            $data['favicon'] = $fname;
        } else {
            flash('error', 'Gagal upload favicon. Pastikan format JPG/PNG/WebP, maks 5MB.');
        }
    }

    // Handle logo upload
    if (!empty($_FILES['logo']['name'])) {
        $fname = upload_image($_FILES['logo']);
        if ($fname) {
            $old = get_setting('logo');
            if ($old) delete_upload($old);
            $data['logo'] = $fname;
        } else {
            flash('error', 'Gagal upload logo. Pastikan format JPG/PNG/WebP, maks 5MB.');
        }
    }

    // Handle social image upload
    if (!empty($_FILES['social_image']['name'])) {
        $fname = upload_image($_FILES['social_image']);
        if ($fname) {
            $old = get_setting('social_image');
            if ($old) delete_upload($old);
            $data['social_image'] = $fname;
        } else {
            flash('error', 'Gagal upload social image. Pastikan format JPG/PNG/WebP, maks 5MB.');
        }
    }

    if (save_settings($data)) {
        flash('success', 'Pengaturan website berhasil disimpan.');
    } else {
        flash('error', 'Gagal menyimpan pengaturan.');
    }
    redirect(admin_url('settings/index.php'));
}

$page_title  = 'Pengaturan Website';
$active_menu = 'settings';
require_once dirname(__DIR__) . '/includes/header.php';

$s = get_all_settings();
?>

<div class="page-header">
    <h4><i class="fas fa-cog me-2 text-primary"></i>Pengaturan Website &amp; Tema</h4>
    <a href="<?= base_url() ?>" target="_blank" class="btn btn-outline-primary">
        <i class="fas fa-external-link-alt me-1"></i>Lihat Tampilan Undangan
    </a>
</div>

<form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- General Information -->
            <div class="card">
                <div class="card-header"><i class="fas fa-globe"></i> Informasi Umum &amp; SEO</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Website / Undangan <span class="text-danger">*</span></label>
                            <input type="text" name="site_title" class="form-control"
                                   value="<?= e($s['site_title'] ?? 'Undangan Pernikahan') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Judul Cover Undangan</label>
                            <input type="text" name="invitation_title" class="form-control"
                                   value="<?= e($s['invitation_title'] ?? 'The Wedding Of') ?>">
                            <div class="form-text">Contoh: The Wedding Of / Pernikahan Kami</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Meta Title (SEO &amp; Share)</label>
                            <input type="text" name="meta_title" class="form-control"
                                   value="<?= e($s['meta_title'] ?? '') ?>" placeholder="Undangan Pernikahan | Nama Mempelai">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2"><?= e($s['meta_description'] ?? '') ?></textarea>
                            <div class="form-text">Deskripsi singkat saat link dibagikan di WhatsApp / Media Sosial.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Kutipan / Quote Pernikahan</label>
                            <textarea name="wedding_quote" class="form-control" rows="3"><?= e($s['wedding_quote'] ?? '') ?></textarea>
                            <div class="form-text">Kutipan ayat suci atau kata-kata mutiara cinta.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Website</label>
                            <select name="website_status" class="form-select">
                                <option value="active"   <?= ($s['website_status'] ?? '') === 'active'   ? 'selected' : '' ?>>Aktif (Dapat Diakses Tamu)</option>
                                <option value="inactive" <?= ($s['website_status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Nonaktif (Halaman Maintenance)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Theme & Palette -->
            <div class="card">
                <div class="card-header"><i class="fas fa-palette"></i> Tema Warna &amp; Tipografi</div>
                <div class="card-body">
                    <!-- Preset Theme Palettes -->
                    <div class="mb-4">
                        <label class="form-label d-block">Pilihan Preset Warna Cepat:</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="theme-preset-btn" onclick="applyPreset('#8b5e5e', '#d8b4a0', '#fffaf7', '#2d2424')">
                                <span class="theme-preset-color" style="background:#8b5e5e;"></span>
                                Rose Gold Classic
                            </button>
                            <button type="button" class="theme-preset-btn" onclick="applyPreset('#1b4332', '#d4af37', '#f8f9fa', '#1b2d24')">
                                <span class="theme-preset-color" style="background:#1b4332;"></span>
                                Emerald &amp; Gold
                            </button>
                            <button type="button" class="theme-preset-btn" onclick="applyPreset('#1e293b', '#cbd5e1', '#f8fafc', '#0f172a')">
                                <span class="theme-preset-color" style="background:#1e293b;"></span>
                                Royal Navy Modern
                            </button>
                            <button type="button" class="theme-preset-btn" onclick="applyPreset('#556b2f', '#c2b280', '#fafaf7', '#2b3318')">
                                <span class="theme-preset-color" style="background:#556b2f;"></span>
                                Soft Sage Olive
                            </button>
                            <button type="button" class="theme-preset-btn" onclick="applyPreset('#9c413d', '#e09f67', '#fdf6ee', '#381a18')">
                                <span class="theme-preset-color" style="background:#9c413d;"></span>
                                Terracotta Warm
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <label class="form-label">Warna Utama</label>
                            <input type="color" name="primary_color" id="primaryColorInput" class="form-control form-control-color w-100"
                                   value="<?= e($s['primary_color'] ?? '#8b5e5e') ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Warna Sekunder</label>
                            <input type="color" name="secondary_color" id="secondaryColorInput" class="form-control form-control-color w-100"
                                   value="<?= e($s['secondary_color'] ?? '#d8b4a0') ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Warna Background</label>
                            <input type="color" name="background_color" id="bgColorInput" class="form-control form-control-color w-100"
                                   value="<?= e($s['background_color'] ?? '#fffaf7') ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">Warna Teks</label>
                            <input type="color" name="text_color" id="textColorInput" class="form-control form-control-color w-100"
                                   value="<?= e($s['text_color'] ?? '#2d2424') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Font Judul (Heading)</label>
                            <select name="font_heading" class="form-select">
                                <?php
                                $headings = ['Playfair Display', 'Cormorant Garamond', 'Cinzel', 'Great Vibes', 'Alex Brush', 'Prata', 'Lora', 'Merriweather'];
                                $cur_heading = $s['font_heading'] ?? 'Playfair Display';
                                foreach ($headings as $f): ?>
                                <option value="<?= e($f) ?>" <?= $cur_heading === $f ? 'selected' : '' ?>><?= e($f) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Font Teks Isi (Body)</label>
                            <select name="font_body" class="form-select">
                                <?php
                                $bodies = ['Lato', 'Plus Jakarta Sans', 'Outfit', 'Inter', 'Montserrat', 'Poppins', 'Open Sans', 'Roboto'];
                                $cur_body = $s['font_body'] ?? 'Lato';
                                foreach ($bodies as $fb): ?>
                                <option value="<?= e($fb) ?>" <?= $cur_body === $fb ? 'selected' : '' ?>><?= e($fb) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Visibility -->
            <div class="card">
                <div class="card-header"><i class="fas fa-toggle-on"></i> Aktifkan / Nonaktifkan Section</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Centang bagian-bagian yang ingin ditampilkan pada website undangan publik.</p>
                    <div class="row g-3">
                        <?php
                        $sections = [
                            'show_section_couple'  => ['Mempelai (Bride & Groom)', 'fas fa-heart'],
                            'show_section_events'  => ['Rangkaian Acara (Events)', 'fas fa-calendar-alt'],
                            'show_section_story'   => ['Love Story Timeline', 'fas fa-book-open'],
                            'show_section_gallery' => ['Galeri Foto', 'fas fa-images'],
                            'show_section_gift'    => ['Wedding Gift (Amplop Digital)', 'fas fa-gift'],
                            'show_section_rsvp'    => ['Konfirmasi Kehadiran (RSVP)', 'fas fa-clipboard-check'],
                            'show_section_wishes'  => ['Buku Tamu / Ucapan', 'fas fa-comments'],
                            'show_section_music'   => ['Pemutar Musik Background', 'fas fa-music'],
                        ];
                        foreach ($sections as $key => $info): ?>
                        <div class="col-md-6">
                            <div class="form-check form-switch p-2 border rounded">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="<?= $key ?>"
                                       id="<?= $key ?>" <?= ($s[$key] ?? '1') === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label fw-semibold" for="<?= $key ?>">
                                    <i class="<?= $info[1] ?> me-1 text-primary"></i><?= e($info[0]) ?>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Media Uploads -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-image"></i> Media &amp; Ikon</div>
                <div class="card-body">
                    <!-- Favicon -->
                    <div class="mb-4">
                        <label class="form-label">Favicon Website</label>
                        <?php if (!empty($s['favicon'])): ?>
                            <div class="mb-2">
                                <img src="<?= upload_url(e($s['favicon'])) ?>"
                                     alt="Favicon" style="width:44px;height:44px;object-fit:contain;border:1px solid #e2e8f0;border-radius:8px;padding:4px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="favicon" class="form-control" accept="image/*" data-preview="prevFavicon">
                        <img id="prevFavicon" class="img-preview" style="display:none;max-width:60px;">
                        <div class="form-text">Format PNG/ICO/JPG/WebP, maks 5MB.</div>
                    </div>

                    <!-- Logo -->
                    <div class="mb-4">
                        <label class="form-label">Logo / Monogram</label>
                        <?php if (!empty($s['logo'])): ?>
                            <div class="mb-2">
                                <img src="<?= upload_url(e($s['logo'])) ?>"
                                     alt="Logo" class="img-preview" style="max-width:140px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="logo" class="form-control" accept="image/*" data-preview="prevLogo">
                        <img id="prevLogo" class="img-preview" style="display:none;">
                    </div>

                    <!-- Social Image -->
                    <div class="mb-2">
                        <label class="form-label">Thumbnail Share (Open Graph)</label>
                        <div class="form-text mb-2">Gambar thumbnail saat link undangan dibagikan ke WhatsApp (ideal 1200x630px).</div>
                        <?php if (!empty($s['social_image'])): ?>
                            <div class="mb-2">
                                <img src="<?= upload_url(e($s['social_image'])) ?>"
                                     alt="Social Thumbnail" class="img-preview">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="social_image" class="form-control" accept="image/*" data-preview="prevSocial">
                        <img id="prevSocial" class="img-preview" style="display:none;">
                    </div>
                </div>
            </div>

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Simpan Seluruh Pengaturan
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    function applyPreset(primary, secondary, bg, text) {
        document.getElementById('primaryColorInput').value   = primary;
        document.getElementById('secondaryColorInput').value = secondary;
        document.getElementById('bgColorInput').value        = bg;
        document.getElementById('textColorInput').value      = text;
        if (window.adminShowToast) window.adminShowToast('Preset warna diterapkan! Jangan lupa klik Simpan Pengaturan.', 'success');
    }
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
