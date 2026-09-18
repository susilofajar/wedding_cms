<?php $acc = $acc ?? []; ?>
<div class="row g-3">
    <div class="col-12">
        <label class="form-label">Nama Bank / Dompet Digital <span class="text-danger">*</span></label>
        <input type="text" name="bank_name" class="form-control"
               value="<?= e($acc['bank_name'] ?? '') ?>" required placeholder="Contoh: Bank BCA / Mandiri / GoPay / DANA">
    </div>
    <div class="col-12">
        <label class="form-label">Nomor Rekening / No. HP <span class="text-danger">*</span></label>
        <input type="text" name="account_number" class="form-control"
               value="<?= e($acc['account_number'] ?? '') ?>" required placeholder="Contoh: 1234567890">
    </div>
    <div class="col-12">
        <label class="form-label">Nama Pemilik Rekening <span class="text-danger">*</span></label>
        <input type="text" name="account_holder" class="form-control"
               value="<?= e($acc['account_holder'] ?? '') ?>" required placeholder="Nama sesuai buku tabungan / akun">
    </div>
    <div class="col-md-6">
        <label class="form-label">Logo Bank (Opsional)</label>
        <?php if (!empty($acc['bank_logo'])): ?>
            <div class="mb-2">
                <img src="<?= upload_url(e($acc['bank_logo'])) ?>"
                     alt="Logo" style="max-height:40px;object-fit:contain;">
            </div>
        <?php endif; ?>
        <input type="file" name="bank_logo" class="form-control" accept="image/*" data-preview="prevLogo">
        <img id="prevLogo" class="img-preview" style="display:none;max-height:60px;max-width:120px;">
        <div class="form-text">JPG, PNG, WebP, maks 5MB</div>
    </div>
    <div class="col-md-6">
        <label class="form-label">Gambar QRIS (Opsional)</label>
        <?php if (!empty($acc['qris_image'])): ?>
            <div class="mb-2">
                <img src="<?= upload_url(e($acc['qris_image'])) ?>"
                     alt="QRIS" style="max-height:60px;object-fit:contain;border:1px solid #ddd;border-radius:4px;padding:2px;">
            </div>
        <?php endif; ?>
        <input type="file" name="qris_image" class="form-control" accept="image/*" data-preview="prevQris">
        <img id="prevQris" class="img-preview" style="display:none;max-height:100px;">
        <div class="form-text">Gambar barcode QRIS jika tersedia</div>
    </div>
    <div class="col-12">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                   <?= ($acc['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Aktifkan Rekening ini di Undangan</label>
        </div>
    </div>
</div>
