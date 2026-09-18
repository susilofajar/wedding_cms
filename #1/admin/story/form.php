<?php
$s = $s ?? [];
?>
<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Judul <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control"
               value="<?= e($s['title'] ?? '') ?>" required placeholder="Contoh: Pertemuan Pertama">
    </div>
    <div class="col-md-4">
        <label class="form-label">Tahun / Tanggal</label>
        <input type="text" name="story_date" class="form-control"
               value="<?= e($s['story_date'] ?? '') ?>" placeholder="Contoh: 2020">
    </div>
    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-control" rows="4"
                  placeholder="Ceritakan momen ini..."><?= e($s['description'] ?? '') ?></textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Foto (opsional)</label>
        <?php if (!empty($s['image'])): ?>
            <div class="mb-2">
                <img src="<?= upload_url(e($s['image'])) ?>" alt="Story" class="img-preview">
            </div>
        <?php endif; ?>
        <input type="file" name="image" class="form-control" accept="image/*" data-preview="prevStory">
        <img id="prevStory" class="img-preview" style="display:none;">
        <div class="form-text">JPG, PNG, WebP, maks 5MB</div>
    </div>
    <div class="col-md-4">
        <label class="form-label">Urutan</label>
        <input type="number" name="sort_order" class="form-control"
               value="<?= (int)($s['sort_order'] ?? 0) ?>" min="0">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                   <?= ($s['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Aktifkan</label>
        </div>
    </div>
</div>
