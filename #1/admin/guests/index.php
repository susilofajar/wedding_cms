<?php
/**
 * Guest Invitation Link Generator
 * WhatsApp & Instagram Support
 * Wedding Invitation CMS
 */

$page_title  = 'Generator Link Undangan Tamu';
$active_menu = 'guests';
require_once dirname(__DIR__) . '/includes/header.php';

$s      = get_all_settings();
$couple = get_couple();
$events = get_active_events();

$primary_event = null;
foreach ($events as $ev) {
    if ($ev['type'] === 'akad' || $ev['type'] === 'reception') {
        $primary_event = $ev;
        break;
    }
}
if (!$primary_event && !empty($events)) $primary_event = $events[0];

$bride_name = $couple['bride_nickname'] ?? $couple['bride_full_name'] ?? 'Mempelai Wanita';
$groom_name = $couple['groom_nickname'] ?? $couple['groom_full_name'] ?? 'Mempelai Pria';
$event_date_str = $primary_event ? format_date($primary_event['event_date'], 'l, d F Y') : '';
?>

<div class="page-header">
    <h4><i class="fas fa-paper-plane me-2 text-primary"></i>Generator Link Undangan Tamu</h4>
    <a href="<?= base_url() ?>" target="_blank" class="btn btn-outline-secondary">
        <i class="fas fa-external-link-alt me-1"></i>Buka Website Undangan
    </a>
</div>

<div class="row g-4">
    <!-- Generator Form -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-magic"></i> Buat Link Personal Tamu</div>
            <div class="card-body">

                <!-- Domain / Base URL Config -->
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0 fw-bold small text-secondary">
                            <i class="fas fa-globe me-1 text-primary"></i>Domain / URL Publik Website:
                        </label>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small" onclick="resetBaseUrl()">
                            Reset
                        </button>
                    </div>
                    <div class="input-group input-group-sm">
                        <input type="url" id="customBaseUrlInput" class="form-control"
                               placeholder="https://undangan-anda.infinityfreeapp.com/"
                               oninput="onBaseUrlChange()">
                    </div>
                    <div id="localhostAlert" class="alert alert-warning py-2 px-3 mt-2 small mb-1" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Perhatian:</strong> Link menggunakan <code>localhost</code>. Agar Instagram di HP tamu bisa membuka undangan, isi kolom di atas dengan domain online/hosting publik Anda!
                    </div>
                    <div class="form-text small">URL ini otomatis digunakan untuk semua link WhatsApp &amp; Instagram.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Tamu <span class="text-danger">*</span></label>
                    <input type="text" id="guestNameInput" class="form-control"
                           placeholder="Contoh: Bpk. Ahmad &amp; Keluarga / Dian Pratiwi" autofocus
                           oninput="generateGuestLink()">
                    <div class="form-text">Nama ini akan tampil di sampul depan dan form buku tamu.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sapaan / Sebutan</label>
                    <select id="guestPronounInput" class="form-select" onchange="generateGuestLink()">
                        <option value="Bapak/Ibu/Saudara/i">Bapak/Ibu/Saudara/i</option>
                        <option value="Bapak/Ibu">Bapak/Ibu</option>
                        <option value="Saudara/i">Saudara/i</option>
                        <option value="Keluarga">Keluarga</option>
                        <option value="Sahabat">Sahabat</option>
                        <option value="Teman-teman">Teman-teman</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp Tamu (Opsional)</label>
                    <input type="text" id="guestPhoneInput" class="form-control"
                           placeholder="Contoh: 08123456789 atau 628123456789"
                           oninput="generateGuestLink()">
                    <div class="form-text">Tombol "Kirim via WhatsApp" akan langsung membuka chat nomor ini.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username Instagram Tamu (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text">@</span>
                        <input type="text" id="guestInstagramInput" class="form-control"
                               placeholder="Contoh: dian_pratiwi"
                               oninput="generateGuestLink()">
                    </div>
                    <div class="form-text">Tombol "Buka DM Instagram" akan membuka chat langsung dengan user ini.</div>
                </div>

                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-primary btn-lg" onclick="generateGuestLink()">
                        <i class="fas fa-wand-magic-sparkles me-2"></i>Perbarui Link &amp; Pesan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Output: Tabs for WhatsApp & Instagram -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs card-header-tabs" id="platformTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="wa-tab" data-bs-toggle="tab"
                                data-bs-target="#waPanel" type="button" role="tab"
                                aria-controls="waPanel" aria-selected="true">
                            <i class="fab fa-whatsapp text-success me-1"></i>WhatsApp
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ig-tab" data-bs-toggle="tab"
                                data-bs-target="#igPanel" type="button" role="tab"
                                aria-controls="igPanel" aria-selected="false">
                            <i class="fab fa-instagram text-danger me-1"></i>Instagram
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="platformTabContent">

                    <!-- WhatsApp Tab -->
                    <div class="tab-pane fade show active" id="waPanel" role="tabpanel" aria-labelledby="wa-tab">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Link Undangan Personal</label>
                            <div class="input-group">
                                <input type="text" id="generatedLink" class="form-control bg-light" readonly
                                       value="<?= base_url() ?>">
                                <button class="btn btn-outline-primary" type="button" onclick="copyGeneratedLink()">
                                    <i class="fas fa-copy me-1"></i>Salin Link
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Template Pesan WhatsApp Siap Kirim</label>
                            <textarea id="generatedWaText" class="form-control bg-light" rows="8" readonly></textarea>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success flex-grow-1" onclick="sendToWhatsApp()">
                                <i class="fab fa-whatsapp me-2"></i>Kirim via WhatsApp
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="copyWaText()">
                                <i class="fas fa-copy me-1"></i>Salin Teks Pesan
                            </button>
                        </div>
                    </div>

                    <!-- Instagram Tab -->
                    <div class="tab-pane fade" id="igPanel" role="tabpanel" aria-labelledby="ig-tab">
                        
                        <!-- Link Saja (Direkomendasikan untuk Instagram) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold d-flex justify-content-between">
                                <span><i class="fas fa-link me-1 text-danger"></i>Link Undangan Khusus Instagram</span>
                                <span class="badge bg-success">Siap Dibuka di Instagram</span>
                            </label>
                            <div class="input-group mb-2">
                                <input type="text" id="generatedLinkIg" class="form-control bg-light" readonly
                                       value="<?= base_url() ?>">
                                <button class="btn btn-danger" type="button" onclick="copyIgLink()">
                                    <i class="fas fa-copy me-1"></i>Salin Link Saja
                                </button>
                            </div>
                            <div class="form-text small text-muted">
                                Link ini terformat khusus agar Instagram In-App Browser dapat membukanya secara instan tanpa error.
                            </div>
                        </div>

                        <!-- Info Petunjuk Khusus Instagram -->
                        <div class="alert alert-light border py-2 px-3 small mb-3">
                            <div class="fw-bold mb-1 text-dark"><i class="fas fa-lightbulb text-warning me-1"></i>Tips Berbagi di Instagram:</div>
                            <ul class="mb-0 ps-3 text-secondary">
                                <li><strong>Di DM Instagram:</strong> Paste link atau template pesan di bawah ke chat DM teman Anda.</li>
                                <li><strong>Di Instagram Story:</strong> Buat Story foto/video &rarr; Tap ikon <strong>Stiker</strong> &rarr; Pilih <strong>🔗 TAUTAN (Link)</strong> &rarr; Paste link di atas.</li>
                                <li><strong>Di Profil (Bio):</strong> Edit Profil &rarr; Tambahkan Tautan &rarr; Paste link undangan.</li>
                            </ul>
                        </div>

                        <!-- Template Pesan DM Instagram -->
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fab fa-facebook-messenger me-1 text-danger"></i>Template Pesan Instagram DM</label>
                            <textarea id="generatedIgText" class="form-control bg-light" rows="7" readonly></textarea>
                        </div>

                        <div class="d-flex gap-2 flex-wrap mb-4">
                            <button type="button" class="btn btn-outline-danger flex-grow-1" onclick="copyIgText()">
                                <i class="fas fa-copy me-1"></i>Salin Pesan DM
                            </button>
                            <button type="button" class="btn btn-ig flex-grow-1" onclick="sendToInstagramDM()" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); color: #fff; border: none;">
                                <i class="fab fa-instagram me-2"></i>Buka Chat DM Tamu
                            </button>
                        </div>

                        <hr class="my-3">

                        <!-- Instagram Story Section -->
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="fas fa-photo-film me-1 text-warning"></i>Bagikan ke Instagram Story</label>
                            <p class="text-muted small mb-2">Gunakan <strong>Stiker 🔗 Tautan</strong> di Instagram Story dengan link di atas, lalu lengkapi dengan caption ini:</p>
                            <textarea id="generatedIgStoryCaption" class="form-control bg-light" rows="4" readonly></textarea>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-warning text-dark flex-grow-1" onclick="copyIgStoryCaption()">
                                <i class="fas fa-copy me-1"></i>Salin Caption Story
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="openInstagramApp()">
                                <i class="fab fa-instagram me-1"></i>Buka Instagram App / Web
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const DEFAULT_BASE_URL = '<?= base_url() ?>';
    const BRIDE_NAME       = '<?= e(addslashes($bride_name)) ?>';
    const GROOM_NAME       = '<?= e(addslashes($groom_name)) ?>';
    const EVENT_DATE       = '<?= e(addslashes($event_date_str)) ?>';

    // Load saved custom base URL from localStorage if available
    function initBaseUrl() {
        const saved = localStorage.getItem('wedding_custom_base_url');
        const input = document.getElementById('customBaseUrlInput');
        if (saved && saved.trim()) {
            input.value = saved.trim();
        } else {
            input.value = DEFAULT_BASE_URL;
        }
        checkLocalhost(input.value);
    }

    function onBaseUrlChange() {
        const val = document.getElementById('customBaseUrlInput').value.trim();
        if (val) {
            localStorage.setItem('wedding_custom_base_url', val);
        } else {
            localStorage.removeItem('wedding_custom_base_url');
        }
        checkLocalhost(val || DEFAULT_BASE_URL);
        generateGuestLink();
    }

    function resetBaseUrl() {
        localStorage.removeItem('wedding_custom_base_url');
        document.getElementById('customBaseUrlInput').value = DEFAULT_BASE_URL;
        checkLocalhost(DEFAULT_BASE_URL);
        generateGuestLink();
    }

    function checkLocalhost(url) {
        const isLocal = /localhost|127\.0\.0\.1|::1/i.test(url);
        const alertBox = document.getElementById('localhostAlert');
        if (alertBox) {
            alertBox.style.display = isLocal ? 'block' : 'none';
        }
    }

    function getActiveBaseUrl() {
        let val = document.getElementById('customBaseUrlInput').value.trim();
        if (!val) val = DEFAULT_BASE_URL;
        if (!/^https?:\/\//i.test(val)) {
            val = 'https://' + val;
        }
        return val.replace(/\/+$/, '') + '/';
    }

    function generateGuestLink() {
        const name    = document.getElementById('guestNameInput').value.trim();
        const pronoun = document.getElementById('guestPronounInput').value.trim();
        const phone   = document.getElementById('guestPhoneInput').value.trim().replace(/[^0-9]/g, '');

        const baseUrl = getActiveBaseUrl();
        let url = baseUrl;

        if (name) {
            const params = new URLSearchParams();
            params.set('to', name);
            if (pronoun && pronoun !== 'Bapak/Ibu/Saudara/i') {
                params.set('p', pronoun);
            }
            // Use %20 for clean Instagram In-App Browser compatibility
            url += '?' + params.toString().replace(/\+/g, '%20');
        }

        // Set link on both tabs
        document.getElementById('generatedLink').value = url;
        document.getElementById('generatedLinkIg').value = url;

        const targetName = name || 'Bapak/Ibu/Saudara/i';
        const targetPronoun = pronoun || 'Bapak/Ibu/Saudara/i';

        // ─── WhatsApp Template ────────────────────────────
        const waText =
`Kepada Yth. ${targetPronoun} *${targetName}*,

Tanpa mengurangi rasa hormat, perkenankan kami mengundang ${targetPronoun} untuk hadir dan memberikan doa restu pada acara pernikahan kami:

💍 *${BRIDE_NAME} & ${GROOM_NAME}*
📅 ${EVENT_DATE ? EVENT_DATE : 'Hari Bahagia Kami'}

Untuk melihat detail rangkaian acara, lokasi, serta konfirmasi kehadiran (RSVP), silakan kunjungi tautan undangan online kami:
👉 ${url}

Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila ${targetPronoun} berkenan hadir dan memberikan doa restu.

Terima kasih,
*${BRIDE_NAME} & ${GROOM_NAME}*`;

        document.getElementById('generatedWaText').value = waText;

        // ─── Instagram DM Template ────────────────────────
        const igText =
`Hai ${targetName} 💌

Tanpa mengurangi rasa hormat, kami ingin mengundangmu di hari bahagia kami ✨

💍 ${BRIDE_NAME} & ${GROOM_NAME}
📅 ${EVENT_DATE ? EVENT_DATE : 'Hari Bahagia Kami'}

🔗 Buka Undangan Digital & Konfirmasi Kehadiran:
${url}

Merupakan suatu kehormatan dan kebahagiaan bagi kami atas kehadiran serta doa restumu. Sampai jumpa! 🤍

Salam hangat,
${BRIDE_NAME} & ${GROOM_NAME}`;

        document.getElementById('generatedIgText').value = igText;

        // ─── Instagram Story Caption ──────────────────────
        const igStoryCaption =
`💍 The Wedding Of
${BRIDE_NAME} & ${GROOM_NAME}
📅 ${EVENT_DATE ? EVENT_DATE : 'Save The Date'}

Tap stiker tautan 🔗 untuk membuka undangan & RSVP! ✨
${url}

#WeddingInvitation #${BRIDE_NAME.replace(/\s+/g, '')}${GROOM_NAME.replace(/\s+/g, '')}`;

        document.getElementById('generatedIgStoryCaption').value = igStoryCaption;
    }

    // ─── WhatsApp Functions ──────────────────────────────
    function copyGeneratedLink() {
        const link = document.getElementById('generatedLink').value;
        copyTextToClipboard(link, 'Link undangan berhasil disalin!');
    }

    function copyWaText() {
        const text = document.getElementById('generatedWaText').value;
        copyTextToClipboard(text, 'Teks pesan WhatsApp berhasil disalin!');
    }

    function sendToWhatsApp() {
        let phone = document.getElementById('guestPhoneInput').value.trim().replace(/[^0-9]/g, '');
        if (phone.startsWith('0')) {
            phone = '62' + phone.substring(1);
        }
        const text = encodeURIComponent(document.getElementById('generatedWaText').value);
        let waUrl = phone ? `https://wa.me/${phone}?text=${text}` : `https://wa.me/?text=${text}`;
        window.open(waUrl, '_blank');
    }

    // ─── Instagram Functions ─────────────────────────────
    function copyIgLink() {
        const link = document.getElementById('generatedLinkIg').value;
        copyTextToClipboard(link, 'Link khusus Instagram berhasil disalin! Siap dipaste ke DM atau Stiker Story.');
    }

    function copyIgText() {
        const text = document.getElementById('generatedIgText').value;
        copyTextToClipboard(text, 'Pesan Instagram DM berhasil disalin! Paste langsung ke chat Instagram DM.');
    }

    function copyIgStoryCaption() {
        const text = document.getElementById('generatedIgStoryCaption').value;
        copyTextToClipboard(text, 'Caption Instagram Story berhasil disalin!');
    }

    function sendToInstagramDM() {
        const igUsername = document.getElementById('guestInstagramInput').value.trim().replace(/^@/, '');

        // Copy DM message to clipboard first so user can paste it in Instagram
        const text = document.getElementById('generatedIgText').value;
        copyTextToClipboard(text, 'Pesan DM disalin ke clipboard! Buka Instagram lalu paste di chat DM.');

        if (igUsername) {
            // ig.me/m/username is the official Instagram DM link.
            // On mobile with Instagram installed: the OS automatically opens the Instagram app.
            // On desktop or without app: opens Instagram web DM.
            // Do NOT use instagram:// deep link — it changes window.location and causes
            // the admin page to get stuck in infinite loading state on mobile browsers.
            window.open('https://ig.me/m/' + encodeURIComponent(igUsername), '_blank');
        } else {
            window.open('https://www.instagram.com/direct/inbox/', '_blank');
        }
    }

    function openInstagramApp() {
        // Always use web URL — on mobile, the OS will redirect to Instagram app if installed.
        window.open('https://www.instagram.com/', '_blank');
    }

    function copyTextToClipboard(text, successMsg) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                if (window.adminShowToast) window.adminShowToast(successMsg, 'success');
                else alert(successMsg);
            }).catch(() => fallbackCopy(text, successMsg));
        } else {
            fallbackCopy(text, successMsg);
        }
    }

    function fallbackCopy(text, successMsg) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            if (window.adminShowToast) window.adminShowToast(successMsg, 'success');
            else alert(successMsg);
        } catch (e) {
            alert('Gagal menyalin otomatis. Silakan salin manual.');
        }
        document.body.removeChild(ta);
    }

    // Auto generate on load
    document.addEventListener('DOMContentLoaded', () => {
        initBaseUrl();
        generateGuestLink();
    });
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
