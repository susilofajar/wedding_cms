/**
 * Admin JavaScript
 * Wedding Invitation CMS
 */

(function () {
    'use strict';

    // ─── Sidebar Toggle ───────────────────────────────────────────────────────
    const sidebarToggle = document.getElementById('sidebarToggle');
    const body          = document.body;

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            body.classList.toggle('sidebar-open');
        });
    }

    // Overlay click closes sidebar
    const overlay = document.querySelector('.sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', function () {
            body.classList.remove('sidebar-open');
        });
    }

    // Inject overlay if not present
    if (!document.querySelector('.sidebar-overlay')) {
        const ov = document.createElement('div');
        ov.className = 'sidebar-overlay';
        document.body.appendChild(ov);
        ov.addEventListener('click', function () {
            body.classList.remove('sidebar-open');
        });
    }

    // ─── Delete Confirmation ──────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-confirm]');
        if (!btn) return;
        const msg = btn.getAttribute('data-confirm') || 'Apakah Anda yakin ingin menghapus data ini?';
        if (!confirm(msg)) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    // ─── Image Preview ────────────────────────────────────────────────────────
    document.addEventListener('change', function (e) {
        if (e.target.matches('input[type="file"][data-preview]')) {
            const previewId = e.target.getAttribute('data-preview');
            const preview   = document.getElementById(previewId);
            if (!preview) return;
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (ev) {
                preview.src = ev.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // ─── Auto-dismiss Alerts ─────────────────────────────────────────────────
    setTimeout(function () {
        document.querySelectorAll('.alert.alert-success, .alert.alert-danger').forEach(function (el) {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 5000);

    // ─── Clipboard Copy ───────────────────────────────────────────────────────
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-copy]');
        if (!btn) return;
        const text = btn.getAttribute('data-copy');
        if (!text) return;

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function () {
                showToast('Berhasil disalin!', 'success');
            }).catch(function () {
                fallbackCopy(text);
            });
        } else {
            fallbackCopy(text);
        }
    });

    function fallbackCopy(text) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity  = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            showToast('Berhasil disalin!', 'success');
        } catch (e) {
            showToast('Gagal menyalin.', 'danger');
        }
        document.body.removeChild(ta);
    }

    // ─── Toast ────────────────────────────────────────────────────────────────
    function showToast(message, type) {
        type = type || 'success';
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'alert alert-' + type + ' py-2 px-3 m-0';
        toast.style.cssText = 'min-width:200px;box-shadow:0 4px 12px rgba(0,0,0,0.15);animation:fadeInUp 0.3s ease;';
        toast.textContent = message;
        container.appendChild(toast);

        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(function () { toast.remove(); }, 300);
        }, 3000);
    }

    window.adminShowToast = showToast;

    // ─── Color Input Preview ──────────────────────────────────────────────────
    document.querySelectorAll('input[type="color"]').forEach(function (input) {
        input.addEventListener('input', function () {
            const target = input.getAttribute('data-target');
            if (target) {
                const el = document.getElementById(target);
                if (el) el.value = input.value;
            }
        });
    });

})();
