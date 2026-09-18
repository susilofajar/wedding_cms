<?php
// Shared form partial for events create/edit
// $ev is available for edit, not for create
$ev = $ev ?? [];
?>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama Acara <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control"
               value="<?= e($ev['name'] ?? '') ?>" required placeholder="Contoh: Akad Nikah">
    </div>
    <div class="col-md-6">
        <label class="form-label">Jenis Acara</label>
        <select name="type" class="form-select">
            <option value="akad"      <?= ($ev['type'] ?? '') === 'akad'      ? 'selected' : '' ?>>Akad Nikah</option>
            <option value="reception" <?= ($ev['type'] ?? 'reception') === 'reception' ? 'selected' : '' ?>>Resepsi</option>
            <option value="other"     <?= ($ev['type'] ?? '') === 'other'     ? 'selected' : '' ?>>Lainnya</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
        <input type="date" name="event_date" class="form-control"
               value="<?= e($ev['event_date'] ?? '') ?>" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
        <input type="time" name="start_time" class="form-control"
               value="<?= e($ev['start_time'] ?? '') ?>" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Waktu Selesai</label>
        <input type="time" name="end_time" class="form-control"
               value="<?= e($ev['end_time'] ?? '') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label">Nama Lokasi</label>
        <input type="text" name="location" class="form-control"
               value="<?= e($ev['location'] ?? '') ?>" placeholder="Contoh: Masjid Al-Hikmah">
    </div>
    <div class="col-md-6">
        <label class="form-label">Icon (Font Awesome)</label>
        <input type="text" name="icon" class="form-control"
               value="<?= e($ev['icon'] ?? 'fas fa-calendar-alt') ?>"
               placeholder="Contoh: fas fa-mosque">
        <div class="form-text">Class Font Awesome, contoh: <code>fas fa-mosque</code></div>
    </div>
    <div class="col-12">
        <label class="form-label">Alamat</label>
        <textarea name="address" class="form-control" rows="2"
                  placeholder="Alamat lengkap..."><?= e($ev['address'] ?? '') ?></textarea>
    </div>
    <div class="col-12">
        <label class="form-label">Google Maps URL</label>
        <input type="url" name="maps_url" class="form-control"
               value="<?= e($ev['maps_url'] ?? '') ?>"
               placeholder="https://maps.google.com/...">
    </div>
    <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-control" rows="3"><?= e($ev['description'] ?? '') ?></textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Urutan</label>
        <input type="number" name="sort_order" class="form-control"
               value="<?= (int)($ev['sort_order'] ?? 0) ?>" min="0">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                   <?= ($ev['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Aktifkan Acara</label>
        </div>
    </div>
</div>
