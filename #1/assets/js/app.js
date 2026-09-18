/**
 * Wedding Invitation CMS - Ultra-Modern Public JavaScript
 * Handles Interactivity, Animations, Audio, Gallery Lightbox, RSVP/Wishes AJAX
 */

(function () {
    'use strict';

    // ─── 1. Scroll-Direction Entrance Animations (Always re-animate on scroll down, never on scroll up) ───
    // "disetiap kali scroll kebawah ada efeknya, kalau scroll ke atas tidak usah pakai efek"
    var lastScrollY = 0;
    var animTicking = false;
    var scrollAnimActive = false;

    function handleScrollEntranceAnimations() {
        var currentScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
        var isScrollingDown = currentScrollY >= lastScrollY;
        lastScrollY = currentScrollY;

        var windowHeight = window.innerHeight || document.documentElement.clientHeight || 800;
        var elements = document.querySelectorAll('#mainInvitation [data-aos]');
        if (!elements.length) return;

        for (var i = 0; i < elements.length; i++) {
            var el = elements[i];
            var rect = el.getBoundingClientRect();

            if (isScrollingDown) {
                // SCROLLING DOWN:
                // Trigger entrance animation when element enters viewport from bottom
                if (rect.top <= windowHeight - 50 && rect.bottom >= 0) {
                    if (!el.classList.contains('aos-animate')) {
                        el.classList.add('aos-animate');
                    }
                } else if (rect.top > windowHeight + 80) {
                    // Element is safely offscreen below: reset so it can animate in again on next scroll down
                    if (el.classList.contains('aos-animate')) {
                        el.classList.remove('aos-animate');
                    }
                }
            } else {
                // SCROLLING UP:
                // "kalau scroll ke atas tidak usah pakai efek"
                // Elements in view or entering from top stay visible without entrance animation
                if (rect.bottom >= 0 && rect.top <= windowHeight) {
                    if (!el.classList.contains('aos-animate')) {
                        el.classList.add('aos-animate');
                    }
                } else if (rect.top > windowHeight + 80) {
                    // Lower elements pushed offscreen below as user scrolls up: reset for next scroll down
                    if (el.classList.contains('aos-animate')) {
                        el.classList.remove('aos-animate');
                    }
                }
            }
        }
        animTicking = false;
    }

    function onScrollCheckAnimations() {
        if (!scrollAnimActive) return;
        if (!animTicking) {
            requestAnimationFrame(handleScrollEntranceAnimations);
            animTicking = true;
        }
    }

    function initScrollEntranceAnimations() {
        scrollAnimActive = true;
        lastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
        window.addEventListener('scroll', onScrollCheckAnimations, { passive: true });
        // Initial pass for hero/top elements in view
        handleScrollEntranceAnimations();
    }

    // ─── 2. Floating Flower Petals (Butter-smooth Hardware-Accelerated CSS) ──
    function initFloatingPetals() {
        var container = document.getElementById('petalsContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'petalsContainer';
            document.body.appendChild(container);
        }

        // Realistic organic petal shapes (SVG paths)
        var petalSVGs = [
            '<svg viewBox="0 0 30 30" fill="none"><path d="M15 2 C23 2 28 8 27 16 C26 23 20 28 15 28 C10 28 4 23 3 16 C2 8 7 2 15 2Z" fill="url(#petalGrad1)" opacity="0.88"/></svg>',
            '<svg viewBox="0 0 30 30" fill="none"><path d="M15 1 C22 4 28 11 25 19 C22 26 15 29 13 27 C8 24 3 18 5 11 C7 4 12 1 15 1Z" fill="url(#petalGrad2)" opacity="0.85"/></svg>',
            '<svg viewBox="0 0 30 30" fill="none"><path d="M15 3 C24 3 27 12 24 19 C20 26 13 28 10 25 C6 21 4 13 8 7 C10 4 13 3 15 3Z" fill="url(#petalGrad3)" opacity="0.9"/></svg>',
            '<svg viewBox="0 0 30 30" fill="none"><path d="M14 2 C21 2 26 9 24 17 C22 23 16 28 12 26 C7 24 5 17 6 11 C8 5 10 2 14 2Z" fill="url(#petalGrad4)" opacity="0.85"/></svg>'
        ];

        // Gradients matching theme palette (rose blush, champagne, soft sakura)
        var svgDefs = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svgDefs.style.position = 'absolute';
        svgDefs.style.width = '0';
        svgDefs.style.height = '0';
        svgDefs.style.overflow = 'hidden';
        svgDefs.innerHTML = '<defs>' +
            '<linearGradient id="petalGrad1" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#fce4ec"/><stop offset="60%" stop-color="#f8bbd0"/><stop offset="100%" stop-color="#e8a5b8"/></linearGradient>' +
            '<linearGradient id="petalGrad2" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#fff0f5"/><stop offset="50%" stop-color="#fbc4ab"/><stop offset="100%" stop-color="#e79c8d"/></linearGradient>' +
            '<linearGradient id="petalGrad3" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#fff8f0"/><stop offset="60%" stop-color="#ecd4c2"/><stop offset="100%" stop-color="#d8b4a0"/></linearGradient>' +
            '<linearGradient id="petalGrad4" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="#fdf2f4"/><stop offset="50%" stop-color="#f5cad4"/><stop offset="100%" stop-color="#d493a3"/></linearGradient>' +
        '</defs>';
        document.body.appendChild(svgDefs);

        var petalCount = window.innerWidth < 768 ? 12 : 16;
        for (var i = 0; i < petalCount; i++) {
            var petal = document.createElement('div');
            petal.className = 'falling-petal';

            var left = Math.random() * 100;
            var duration = 9 + Math.random() * 8; // 9s to 17s
            var delay = Math.random() * 12; // 0s to 12s
            var size = 16 + Math.random() * 14; // 16px to 30px
            var driftX = -60 + Math.random() * 140; // -60px to +80px
            var rotEnd = 240 + Math.random() * 480;
            var rotyEnd = 120 + Math.random() * 360;
            var svgIndex = i % petalSVGs.length;

            petal.style.left = left + '%';
            petal.style.width = size + 'px';
            petal.style.height = size + 'px';
            petal.style.animationDuration = duration + 's';
            petal.style.animationDelay = delay + 's';
            petal.style.setProperty('--drift-x', driftX + 'px');
            petal.style.setProperty('--rot-end', rotEnd + 'deg');
            petal.style.setProperty('--roty-end', rotyEnd + 'deg');
            petal.innerHTML = petalSVGs[svgIndex];

            container.appendChild(petal);
        }
    }

    // ─── 3. Continuous Smooth Auto-Scroll Controller (Volume-Style Slider) ─────
    var SCROLL_SPEED_MIN = 8;    // px/sec — very slow
    var SCROLL_SPEED_MAX = 100;  // px/sec — very fast
    var SCROLL_SPEED_DEFAULT = 36; // px/sec — 1.0x reference

    var autoScrollRaf = null;
    var autoScrollActive = false;
    var autoScrollExplicitlyPaused = false;
    var userInteractionTimeout = null;
    var lastTimestamp = null;
    var scrollPixelsPerSec = SCROLL_SPEED_DEFAULT;
    var scrollAccumulator = 0;

    // Restore saved speed from localStorage
    try {
        var savedPxSec = parseFloat(localStorage.getItem('wedding_scroll_speed_px'));
        if (!isNaN(savedPxSec) && savedPxSec >= SCROLL_SPEED_MIN && savedPxSec <= SCROLL_SPEED_MAX) {
            scrollPixelsPerSec = savedPxSec;
        }
    } catch (e) {}

    function pxSecToMultiplier(px) {
        return (px / SCROLL_SPEED_DEFAULT).toFixed(1);
    }

    function getScrollPosition() {
        return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    }

    function getMaxScroll() {
        var docHeight = Math.max(
            document.body.scrollHeight || 0,
            document.documentElement.scrollHeight || 0,
            document.body.offsetHeight || 0,
            document.documentElement.offsetHeight || 0,
            document.body.clientHeight || 0,
            document.documentElement.clientHeight || 0
        );
        var winHeight = window.innerHeight || document.documentElement.clientHeight || 0;
        return Math.max(0, docHeight - winHeight);
    }

    // ── Slider UI Sync ──
    function updateSliderUI() {
        var slider = document.getElementById('scrollSpeedSlider');
        var badge = document.getElementById('scrollSpeedBadge');
        if (slider) {
            slider.value = scrollPixelsPerSec;
            updateSliderTrackFill(slider);
        }
        if (badge) {
            badge.textContent = pxSecToMultiplier(scrollPixelsPerSec) + 'x';
        }
    }

    function updateSliderTrackFill(slider) {
        if (!slider) return;
        var pct = ((slider.value - slider.min) / (slider.max - slider.min)) * 100;
        slider.style.setProperty('--slider-pct', pct + '%');
    }

    function updateAutoScrollUI(active) {
        var widget = document.getElementById('autoScrollWidget');
        var btn = document.getElementById('btnToggleAutoScroll');
        var icon = document.getElementById('autoScrollIcon');
        if (!widget || !btn) return;

        widget.style.display = 'block';
        setTimeout(function () { widget.classList.add('active'); }, 50);

        if (active) {
            btn.classList.remove('paused');
            if (icon) icon.className = 'fas fa-pause';
        } else {
            btn.classList.add('paused');
            if (icon) icon.className = 'fas fa-play';
        }

        updateSliderUI();
    }

    // Slider event binding
    function bindSpeedSlider() {
        var slider = document.getElementById('scrollSpeedSlider');
        if (!slider) return;

        // Set initial value from restored speed
        slider.value = scrollPixelsPerSec;
        updateSliderTrackFill(slider);

        slider.addEventListener('input', function () {
            scrollPixelsPerSec = parseFloat(this.value);
            updateSliderTrackFill(this);
            var badge = document.getElementById('scrollSpeedBadge');
            if (badge) badge.textContent = pxSecToMultiplier(scrollPixelsPerSec) + 'x';

            try {
                localStorage.setItem('wedding_scroll_speed_px', scrollPixelsPerSec);
            } catch (e) {}

            // If paused by user interaction, resume gently with new speed
            if (!autoScrollActive && !autoScrollExplicitlyPaused) {
                startContinuousAutoScroll();
            }
        });

        // Prevent scroll controller touch from bubbling to auto-scroll pause
        slider.addEventListener('touchstart', function (e) { e.stopPropagation(); }, { passive: true });
        slider.addEventListener('mousedown', function (e) { e.stopPropagation(); });
    }

    function autoScrollLoop(timestamp) {
        if (!autoScrollActive) return;

        if (!lastTimestamp) lastTimestamp = timestamp;
        var deltaSec = Math.min((timestamp - lastTimestamp) / 1000, 0.08); // cap delta to prevent sudden jump
        lastTimestamp = timestamp;

        var currentScroll = getScrollPosition();
        var maxScroll = getMaxScroll();

        // Check if page reached the very bottom:
        // Only stop if the page has rendered (>400px scrollable area) and is within 12px of bottom
        if (maxScroll > 400 && currentScroll >= maxScroll - 12) {
            stopAutoScrollAtEnd();
            return;
        }

        // Sub-pixel accumulator for flawless motion without micro-stutters
        scrollAccumulator += scrollPixelsPerSec * deltaSec;
        if (scrollAccumulator >= 1) {
            var pxToScroll = Math.floor(scrollAccumulator);
            scrollAccumulator -= pxToScroll;
            window.scrollBy(0, pxToScroll);
            onScrollCheckAnimations();
        }

        autoScrollRaf = requestAnimationFrame(autoScrollLoop);
    }

    function startContinuousAutoScroll() {
        if (autoScrollRaf) cancelAnimationFrame(autoScrollRaf);
        autoScrollActive = true;
        autoScrollExplicitlyPaused = false;
        lastTimestamp = null;
        scrollAccumulator = 0;
        updateAutoScrollUI(true);

        autoScrollRaf = requestAnimationFrame(autoScrollLoop);
    }

    function pauseAutoScroll(explicit) {
        if (!autoScrollActive && !explicit) return;
        autoScrollActive = false;
        if (explicit) {
            autoScrollExplicitlyPaused = true;
            if (userInteractionTimeout) clearTimeout(userInteractionTimeout);
        }
        if (autoScrollRaf) cancelAnimationFrame(autoScrollRaf);
        updateAutoScrollUI(false);
    }

    function stopAutoScrollAtEnd() {
        autoScrollActive = false;
        autoScrollExplicitlyPaused = true;
        if (autoScrollRaf) cancelAnimationFrame(autoScrollRaf);
        var icon = document.getElementById('autoScrollIcon');
        var btn = document.getElementById('btnToggleAutoScroll');
        if (btn) btn.classList.add('paused');
        if (icon) icon.className = 'fas fa-arrow-up';
    }

    window.toggleAutoScroll = function () {
        var currentScroll = getScrollPosition();
        var maxScroll = getMaxScroll();

        if (maxScroll > 400 && currentScroll >= maxScroll - 20) {
            // Smoothly go back to top, then restart auto-scroll
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(function () {
                startContinuousAutoScroll();
            }, 900);
            return;
        }

        if (autoScrollActive) {
            pauseAutoScroll(true); // User explicitly paused
        } else {
            startContinuousAutoScroll();
        }
    };

    // User manual scroll / touch handling
    // When user touches or scrolls manually to read / fill form, auto-scroll pauses temporarily.
    // Unless explicitly paused via the button, it gently resumes after 2.5s of inactivity.
    function handleUserTouchStart() {
        if (autoScrollActive) {
            if (autoScrollRaf) cancelAnimationFrame(autoScrollRaf);
            autoScrollActive = false;
        }
    }

    function handleUserTouchEnd() {
        if (!autoScrollExplicitlyPaused) {
            if (userInteractionTimeout) clearTimeout(userInteractionTimeout);
            userInteractionTimeout = setTimeout(function () {
                var currentScroll = getScrollPosition();
                var maxScroll = getMaxScroll();
                if (maxScroll > 400 && currentScroll >= maxScroll - 20) {
                    stopAutoScrollAtEnd();
                    return;
                }
                if (!autoScrollExplicitlyPaused) {
                    startContinuousAutoScroll();
                }
            }, 2500);
        }
    }

    function handleManualUserScroll() {
        if (autoScrollActive) {
            pauseAutoScroll(false);
            handleUserTouchEnd();
        }
    }

    window.addEventListener('touchstart', handleUserTouchStart, { passive: true });
    window.addEventListener('touchend', handleUserTouchEnd, { passive: true });
    window.addEventListener('touchcancel', handleUserTouchEnd, { passive: true });
    window.addEventListener('wheel', handleManualUserScroll, { passive: true });
    window.addEventListener('keydown', function (e) {
        if (['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', 'Space'].indexOf(e.key) !== -1) {
            handleManualUserScroll();
        }
    });

    // ─── 3b. Open Invitation Flow with Envelope Curtain Split ────────────────
    window.openInvitation = function () {
        var opening = document.getElementById('opening');
        var main = document.getElementById('mainInvitation');
        var player = document.getElementById('musicPlayer');

        if (!opening || !main) return;

        // Trigger dramatic envelope opening animation
        opening.classList.add('opening-envelope-open');

        // Prepare main content underneath — MUST be visible before AOS init
        main.style.display = 'block';

        setTimeout(function () {
            opening.classList.add('hidden');
            setTimeout(function () {
                opening.style.display = 'none';
            }, 350);

            if (player) {
                player.style.display = 'flex';
                setTimeout(function () {
                    player.classList.add('active');
                }, 150);
            }

            // Attempt autoplay
            if (window.WEDDING_DATA && window.WEDDING_DATA.hasMusicPlayer) {
                attemptAudioPlay();
            }

            // ★ Initialize directional scroll entrance animations (every scroll down, never on scroll up)
            initScrollEntranceAnimations();

            // Start countdown
            if (window.WEDDING_DATA && window.WEDDING_DATA.countdownTarget) {
                startCountdown(window.WEDDING_DATA.countdownTarget);
            }

            // Initialize staggered reveal observers
            initStaggeredReveals();

            // Start continuous auto-scroll after a short delay (1100ms)
            setTimeout(function () {
                startContinuousAutoScroll();
            }, 1100);

        }, 850);
    };

    // ─── 4. Music Player Controller ────────────────────────────────────────────
    var audio = null;
    var isPlaying = false;

    function attemptAudioPlay() {
        audio = document.getElementById('bgMusic');
        if (!audio) return;
        var playPromise = audio.play();
        if (playPromise !== undefined) {
            playPromise
                .then(function () {
                    isPlaying = true;
                    updateMusicUI();
                })
                .catch(function () {
                    isPlaying = false;
                    updateMusicUI();
                });
        }
    }

    window.toggleMusic = function () {
        audio = audio || document.getElementById('bgMusic');
        if (!audio) return;

        if (isPlaying) {
            audio.pause();
            isPlaying = false;
            updateMusicUI();
        } else {
            audio
                .play()
                .then(function () {
                    isPlaying = true;
                    updateMusicUI();
                })
                .catch(function () {
                    isPlaying = false;
                    updateMusicUI();
                });
        }
    };

    function updateMusicUI() {
        var disc = document.getElementById('musicDisc');
        var icon = document.getElementById('musicIcon');
        var player = document.getElementById('musicPlayer');
        if (!disc || !icon) return;

        if (isPlaying) {
            disc.classList.add('playing');
            if (player) player.classList.add('is-playing');
            icon.className = 'fas fa-compact-disc';
        } else {
            disc.classList.remove('playing');
            if (player) player.classList.remove('is-playing');
            icon.className = 'fas fa-play';
        }
    }

    // ─── 5. Countdown Timer ───────────────────────────────────────────────────
    function startCountdown(targetStr) {
        var target = new Date(targetStr).getTime();
        var doneMsg = document.getElementById('cd-done-msg');
        var wrap = document.querySelector('.countdown-wrap');

        function tick() {
            var now = Date.now();
            var diff = target - now;

            if (diff <= 0) {
                if (wrap) wrap.style.display = 'none';
                if (doneMsg) doneMsg.style.display = 'block';
                return;
            }

            var d = Math.floor(diff / 86400000);
            var h = Math.floor((diff % 86400000) / 3600000);
            var m = Math.floor((diff % 3600000) / 60000);
            var s = Math.floor((diff % 60000) / 1000);

            setCDText('cd-days', d);
            setCDText('cd-hours', h);
            setCDText('cd-minutes', m);
            setCDText('cd-seconds', s);
        }

        function setCDText(id, val) {
            var el = document.getElementById(id);
            if (el) el.textContent = String(val).padStart(2, '0');
        }

        tick();
        setInterval(tick, 1000);
    }

    // ─── 6. Interactive Lightbox Modal ─────────────────────────────────────────
    var currentGalleryList = [];
    var currentGalleryIndex = 0;

    window.openLightboxByIndex = function (index) {
        var items = document.querySelectorAll('.gallery-card');
        currentGalleryList = [];
        items.forEach(function (item) {
            currentGalleryList.push({
                src: item.getAttribute('data-src'),
                title: item.getAttribute('data-title') || ''
            });
        });

        if (!currentGalleryList.length || !currentGalleryList[index]) return;
        currentGalleryIndex = index;
        showLightboxItem();
    };

    function showLightboxItem() {
        var lb = document.getElementById('lightboxModal');
        var img = document.getElementById('lightboxImg');
        var cap = document.getElementById('lightboxCaption');
        if (!lb || !img) return;

        var item = currentGalleryList[currentGalleryIndex];
        img.src = item.src;
        if (cap) {
            cap.textContent = item.title + (currentGalleryList.length > 1 ? ' (' + (currentGalleryIndex + 1) + '/' + currentGalleryList.length + ')' : '');
        }
        lb.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    window.closeLightbox = function () {
        var lb = document.getElementById('lightboxModal');
        if (!lb) return;
        lb.classList.remove('active');
        document.body.style.overflow = '';
    };

    window.prevLightbox = function (e) {
        if (e) e.stopPropagation();
        if (!currentGalleryList.length) return;
        currentGalleryIndex = (currentGalleryIndex - 1 + currentGalleryList.length) % currentGalleryList.length;
        showLightboxItem();
    };

    window.nextLightbox = function (e) {
        if (e) e.stopPropagation();
        if (!currentGalleryList.length) return;
        currentGalleryIndex = (currentGalleryIndex + 1) % currentGalleryList.length;
        showLightboxItem();
    };

    document.addEventListener('keydown', function (e) {
        var lb = document.getElementById('lightboxModal');
        if (!lb || !lb.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') prevLightbox(e);
        if (e.key === 'ArrowRight') nextLightbox(e);
    });

    // Touch swipe for Lightbox
    var touchStartX = 0;
    var touchEndX = 0;
    var lbModal = document.getElementById('lightboxModal');
    if (lbModal) {
        lbModal.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        lbModal.addEventListener('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) {
                nextLightbox(); // Swipe left -> Next
            } else if (touchEndX - touchStartX > 50) {
                prevLightbox(); // Swipe right -> Prev
            }
        }, { passive: true });
    }

    // ─── 7. Copy Account Number ────────────────────────────────────────────────
    window.copyAccount = function (btn) {
        var number = btn.getAttribute('data-number');
        if (!number) return;

        var copyPromise = null;
        if (navigator.clipboard && window.isSecureContext) {
            copyPromise = navigator.clipboard.writeText(number);
        } else {
            var ta = document.createElement('textarea');
            ta.value = number;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            copyPromise = Promise.resolve();
        }

        copyPromise.then(function () {
            showToast('Nomor rekening berhasil disalin: ' + number, 'success');
            var originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Tersalin!';
            btn.disabled = true;
            setTimeout(function () {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 2500);
        }).catch(function () {
            showToast('Salin manual: ' + number, 'error');
        });
    };

    // ─── 8. QRIS Modal Preview ─────────────────────────────────────────────────
    window.showQrisModal = function (src, title) {
        var qModal = document.getElementById('qrisModal');
        var qImg = document.getElementById('qrisModalImg');
        var qTitle = document.getElementById('qrisModalTitle');
        if (!qModal || !qImg) return;

        qImg.src = src;
        if (qTitle) qTitle.textContent = 'QRIS ' + (title || 'Pembayaran');
        var bsModal = new bootstrap.Modal(qModal);
        bsModal.show();
    };

    // ─── 9. RSVP Form (AJAX) ───────────────────────────────────────────────────
    var rsvpForm = document.getElementById('rsvpForm');
    if (rsvpForm) {
        rsvpForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = document.getElementById('rsvpSubmit');
            var msg = document.getElementById('rsvp-msg');
            var name = document.getElementById('rsvp_name') ? document.getElementById('rsvp_name').value.trim() : '';
            var attendance = rsvpForm.querySelector('[name="attendance"]').value;

            if (!name || !attendance) {
                showFormMsg(msg, 'Mohon isi nama dan konfirmasi kehadiran Anda.', 'error');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim Konfirmasi...';

            var fd = new FormData(rsvpForm);
            fd.append('action', 'rsvp');

            fetch(window.location.pathname, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    showFormMsg(msg, data.message, 'success');
                    rsvpForm.reset();
                    showToast('RSVP Anda berhasil terkirim!', 'success');
                } else {
                    showFormMsg(msg, data.message || 'Terjadi kesalahan.', 'error');
                }
            })
            .catch(function () {
                showFormMsg(msg, 'Terjadi kesalahan koneksi. Silakan coba lagi.', 'error');
            })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim RSVP';
            });
        });
    }

    // ─── 10. Wishes Guestbook Form (AJAX) ──────────────────────────────────────
    var wishForm = document.getElementById('wishForm');
    if (wishForm) {
        wishForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = document.getElementById('wishSubmit');
            var msg = document.getElementById('wish-msg');

            var name = wishForm.querySelector('[name="guest_name"]').value.trim();
            var message = wishForm.querySelector('[name="message"]').value.trim();
            var attendance = wishForm.querySelector('[name="attendance"]').value;

            if (!name || !message) {
                showFormMsg(msg, 'Nama dan ucapan/doa wajib diisi.', 'error');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim Ucapan...';

            var fd = new FormData(wishForm);
            fd.append('action', 'wish');

            fetch(window.location.pathname, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    showFormMsg(msg, data.message, 'success');
                    wishForm.reset();
                    prependWishItem(name, message, attendance);
                    showToast('Ucapan & doa Anda berhasil dikirim!', 'success');
                } else {
                    showFormMsg(msg, data.message || 'Terjadi kesalahan.', 'error');
                }
            })
            .catch(function () {
                showFormMsg(msg, 'Terjadi kesalahan koneksi. Silakan coba lagi.', 'error');
            })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-heart me-2"></i>Kirim Ucapan';
            });
        });
    }

    function prependWishItem(name, message, attendance) {
        var container = document.getElementById('wishesContainer');
        if (!container) return;

        var attClass = attendance === 'hadir' ? 'hadir' : attendance === 'tidak_hadir' ? 'tidak_hadir' : 'ragu';
        var attText = attendance === 'hadir' ? 'Hadir' : attendance === 'tidak_hadir' ? 'Tidak Hadir' : 'Masih Ragu';

        var item = document.createElement('div');
        item.className = 'wish-item';
        item.style.animation = 'fadeInUp 0.4s ease';

        var initial = name.charAt(0).toUpperCase();
        item.innerHTML = '<div class="wish-avatar">' + initial + '</div>' +
            '<div class="wish-body">' +
            '<div class="wish-header">' +
            '<span class="wish-author">' + escapeHtml(name) + '</span>' +
            '<span class="wish-badge-att ' + attClass + '">' + attText + '</span>' +
            '</div>' +
            '<p class="wish-message">' + escapeHtml(message) + '</p>' +
            '<p class="wish-time">Baru saja &bull; Menunggu moderasi admin</p>' +
            '</div>';

        container.insertBefore(item, container.firstChild);
    }

    // ─── 11. Helper Functions ──────────────────────────────────────────────────
    function showFormMsg(el, text, type) {
        if (!el) return;
        el.style.display = 'block';
        el.className = 'alert alert-' + (type === 'error' ? 'danger' : 'success') + ' py-2 px-3 mb-3';
        el.textContent = text;
        setTimeout(function () {
            if (type === 'success') {
                el.style.display = 'none';
            }
        }, 7000);
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showToast(message, type) {
        var container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        var el = document.createElement('div');
        el.className = 'toast-msg ' + (type || '');
        var icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-info-circle"></i>';
        el.innerHTML = icon + '<span>' + escapeHtml(message) + '</span>';
        container.appendChild(el);

        setTimeout(function () {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-15px)';
            el.style.transition = 'all 0.3s ease';
            setTimeout(function () { el.remove(); }, 300);
        }, 3500);
    }

    // ─── 12. Staggered Entrance Reveal Observer ────────────────────────────────
    function initStaggeredReveals() {
        var items = document.querySelectorAll('.reveal-stagger');
        if (!items.length) return;

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.08,
                rootMargin: '0px 0px -20px 0px'
            });

            items.forEach(function (el) {
                observer.observe(el);
            });
        } else {
            items.forEach(function (el) {
                el.classList.add('is-visible');
            });
        }
    }

    // ─── 13. App Initializer ───────────────────────────────────────────────────
    function initApp() {
        initFloatingPetals();
        bindSpeedSlider();
        updateSliderUI();
        initStaggeredReveals();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initApp);
    } else {
        initApp();
    }

})();
