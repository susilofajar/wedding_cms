<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $couple = get_couple();
    $data = [
        'bride_full_name' => post('bride_full_name'),
        'bride_nickname'  => post('bride_nickname'),
        'bride_father'    => post('bride_father'),
        'bride_mother'    => post('bride_mother'),
        'bride_order'     => post('bride_order'),
        'bride_instagram' => post('bride_instagram'),
        'groom_full_name' => post('groom_full_name'),
        'groom_nickname'  => post('groom_nickname'),
        'groom_father'    => post('groom_father'),
        'groom_mother'    => post('groom_mother'),
        'groom_order'     => post('groom_order'),
        'groom_instagram' => post('groom_instagram'),
        'description'     => post('description'),
        'bride_photo'     => $couple['bride_photo'] ?? '',
        'groom_photo'     => $couple['groom_photo'] ?? '',
    ];

    if (!empty($_FILES['bride_photo']['name'])) {
        $fname = upload_image($_FILES['bride_photo']);
        if ($fname) {
            if (!empty($couple['bride_photo'])) delete_upload($couple['bride_photo']);
            $data['bride_photo'] = $fname;
        } else {
            flash('error', 'Gagal upload foto mempelai wanita. Pastikan format JPG/PNG/WebP, maks 5MB.');
        }
    }

    if (!empty($_FILES['groom_photo']['name'])) {
        $fname = upload_image($_FILES['groom_photo']);
        if ($fname) {
            if (!empty($couple['groom_photo'])) delete_upload($couple['groom_photo']);
            $data['groom_photo'] = $fname;
        } else {
            flash('error', 'Gagal upload foto mempelai pria. Pastikan format JPG/PNG/WebP, maks 5MB.');
        }
    }

    if ($couple) {
        db_execute(
            "UPDATE couples SET
                bride_full_name=?, bride_nickname=?, bride_photo=?, bride_father=?, bride_mother=?, bride_order=?, bride_instagram=?,
                groom_full_name=?, groom_nickname=?, groom_photo=?, groom_father=?, groom_mother=?, groom_order=?, groom_instagram=?,
                description=?
             WHERE id=?",
            [
                $data['bride_full_name'], $data['bride_nickname'], $data['bride_photo'],
                $data['bride_father'],    $data['bride_mother'],    $data['bride_order'], $data['bride_instagram'],
                $data['groom_full_name'], $data['groom_nickname'], $data['groom_photo'],
                $data['groom_father'],    $data['groom_mother'],    $data['groom_order'], $data['groom_instagram'],
                $data['description'],     $couple['id']
            ]
        );
    } else {
        db_execute(
            "INSERT INTO couples (bride_full_name,bride_nickname,bride_photo,bride_father,bride_mother,bride_order,bride_instagram,
                groom_full_name,groom_nickname,groom_photo,groom_father,groom_mother,groom_order,groom_instagram,description)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [
                $data['bride_full_name'], $data['bride_nickname'], $data['bride_photo'],
                $data['bride_father'],    $data['bride_mother'],    $data['bride_order'], $data['bride_instagram'],
                $data['groom_full_name'], $data['groom_nickname'], $data['groom_photo'],
                $data['groom_father'],    $data['groom_mother'],    $data['groom_order'], $data['groom_instagram'],
                $data['description']
            ]
        );
    }

    flash('success', 'Data kedua mempelai berhasil diperbarui.');
    redirect(admin_url('couple/index.php'));
}

$page_title  = 'Data Mempelai';
$active_menu = 'couple';
require_once dirname(__DIR__) . '/includes/header.php';

$couple = get_couple();
?>

<div class="page-header">
    <h4><i class="fas fa-heart me-2 text-primary"></i>Data Kedua Mempelai</h4>
    <a href="<?= base_url() ?>#couple" target="_blank" class="btn btn-outline-primary">
        <i class="fas fa-external-link-alt me-1"></i>Lihat Bagian Mempelai
    </a>
</div>

<form method="POST" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Mempelai Wanita -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-female text-danger"></i> Mempelai Wanita (Bride)</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="bride_full_name" class="form-control"
                               value="<?= e($couple['bride_full_name'] ?? '') ?>" required
                               placeholder="Nama lengkap beserta gelar jika ada">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Panggilan / Sapaan</label>
                            <input type="text" name="bride_nickname" class="form-control"
                                   value="<?= e($couple['bride_nickname'] ?? '') ?>"
                                   placeholder="Contoh: Anisa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Urutan Anak</label>
                            <input type="text" name="bride_order" class="form-control"
                                   value="<?= e($couple['bride_order'] ?? '') ?>"
                                   placeholder="Contoh: Putri pertama">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Ayah</label>
                            <input type="text" name="bride_father" class="form-control"
                                   value="<?= e($couple['bride_father'] ?? '') ?>"
                                   placeholder="Nama ayah kandung">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Ibu</label>
                            <input type="text" name="bride_mother" class="form-control"
                                   value="<?= e($couple['bride_mother'] ?? '') ?>"
                                   placeholder="Nama ibu kandung">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-instagram"></i> @</span>
                            <input type="text" name="bride_instagram" class="form-control"
                                   value="<?= e(ltrim($couple['bride_instagram'] ?? '', '@')) ?>"
                                   placeholder="username_instagram">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Mempelai Wanita</label>
                        <?php if (!empty($couple['bride_photo'])): ?>
                            <div class="mb-2">
                                <img src="<?= upload_url(e($couple['bride_photo'])) ?>"
                                     alt="Foto Wanita" class="img-preview">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="bride_photo" class="form-control" accept="image/*"
                               data-preview="prevBride">
                        <img id="prevBride" class="img-preview" style="display:none;">
                        <div class="form-text">Format JPG/PNG/WebP, disarankan rasio vertikal (portrait).</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mempelai Pria -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header"><i class="fas fa-male text-primary"></i> Mempelai Pria (Groom)</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="groom_full_name" class="form-control"
                               value="<?= e($couple['groom_full_name'] ?? '') ?>" required
                               placeholder="Nama lengkap beserta gelar jika ada">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Panggilan / Sapaan</label>
                            <input type="text" name="groom_nickname" class="form-control"
                                   value="<?= e($couple['groom_nickname'] ?? '') ?>"
                                   placeholder="Contoh: Budi">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Urutan Anak</label>
                            <input type="text" name="groom_order" class="form-control"
                                   value="<?= e($couple['groom_order'] ?? '') ?>"
                                   placeholder="Contoh: Putra kedua">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Ayah</label>
                            <input type="text" name="groom_father" class="form-control"
                                   value="<?= e($couple['groom_father'] ?? '') ?>"
                                   placeholder="Nama ayah kandung">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Ibu</label>
                            <input type="text" name="groom_mother" class="form-control"
                                   value="<?= e($couple['groom_mother'] ?? '') ?>"
                                   placeholder="Nama ibu kandung">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-instagram"></i> @</span>
                            <input type="text" name="groom_instagram" class="form-control"
                                   value="<?= e(ltrim($couple['groom_instagram'] ?? '', '@')) ?>"
                                   placeholder="username_instagram">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Mempelai Pria</label>
                        <?php if (!empty($couple['groom_photo'])): ?>
                            <div class="mb-2">
                                <img src="<?= upload_url(e($couple['groom_photo'])) ?>"
                                     alt="Foto Pria" class="img-preview">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="groom_photo" class="form-control" accept="image/*"
                               data-preview="prevGroom">
                        <img id="prevGroom" class="img-preview" style="display:none;">
                        <div class="form-text">Format JPG/PNG/WebP, disarankan rasio vertikal (portrait).</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deskripsi / Kata Pengantar Pasangan -->
        <div class="col-12">
            <div class="card">
                <div class="card-header"><i class="fas fa-align-left"></i> Deskripsi Pengantar Pasangan</div>
                <div class="card-body">
                    <textarea name="description" class="form-control" rows="3"
                              placeholder="Deskripsi singkat yang tampil di atas profil kedua mempelai..."><?= e($couple['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save me-2"></i>Simpan Data Mempelai
                </button>
            </div>
        </div>
    </div>
</form>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
