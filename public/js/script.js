/* ============================================
   Engix Care – Landing Page Interactions
   Vanilla JS · No dependencies (Bootstrap + AOS via CDN)
   ============================================ */

(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  function safe(name, fn) {
    try { fn(); } catch (err) { console.warn('[EngixCare] ' + name + ' failed:', err); }
  }

  ready(function () {

    // ===== AOS =====
    safe('AOS.init', function () {
      if (typeof AOS !== 'undefined') {
        AOS.init({
          duration: 800,
          easing: 'ease-out-cubic',
          once: true,
          offset: 60,
          disable: window.innerWidth < 576 ? 'phone' : false,
        });
      }
    });

    // ===== STICKY NAV / BACK-TO-TOP / ACTIVE LINK =====
    var nav = document.getElementById('mainNav');
    var btt = document.getElementById('backToTop');
    var sections = Array.prototype.slice.call(document.querySelectorAll('section[id]'));
    var navLinks = Array.prototype.slice.call(document.querySelectorAll('.navbar-engix .nav-link'));

    function setActiveNav() {
      var pos = window.scrollY + 140;
      sections.forEach(function (sec) {
        var top = sec.offsetTop;
        var height = sec.offsetHeight;
        var id = sec.getAttribute('id');
        var link = document.querySelector('.navbar-engix .nav-link[href="#' + id + '"]');
        if (!link) return;
        if (pos >= top && pos < top + height) {
          navLinks.forEach(function (l) { l.classList.remove('active'); });
          link.classList.add('active');
        }
      });
    }

    function onScroll() {
      safe('scroll-handler', function () {
        if (nav) {
          if (window.scrollY > 20) nav.classList.add('scrolled');
          else nav.classList.remove('scrolled');
        }
        if (btt) {
          if (window.scrollY > 400) btt.classList.add('show');
          else btt.classList.remove('show');
        }
        setActiveNav();
        animateCounters();
      });
    }
    window.addEventListener('scroll', onScroll, { passive: true });

    // ===== SMOOTH SCROLL =====
    safe('smooth-scroll', function () {
      document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
          var href = this.getAttribute('href');
          if (href.length > 1 && document.querySelector(href)) {
            e.preventDefault();
            var el = document.querySelector(href);
            var top = el.getBoundingClientRect().top + window.scrollY - 80;
            window.scrollTo({ top: top, behavior: 'smooth' });
            var collapse = document.getElementById('navContent');
            if (collapse && collapse.classList.contains('show') && typeof bootstrap !== 'undefined') {
              new bootstrap.Collapse(collapse).hide();
            }
          }
        });
      });
    });

    // ===== BACK TO TOP =====
    if (btt) {
      btt.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

    // ===== ANIMATED COUNTERS =====
    var counters = Array.prototype.slice.call(document.querySelectorAll('.counter-num'));
    var counterStarted = false;
    function animateCounters() {
      if (counterStarted) return;
      var section = document.querySelector('.counters');
      if (!section) return;
      var rect = section.getBoundingClientRect();
      if (rect.top < window.innerHeight - 60) {
        counterStarted = true;
        counters.forEach(function (el) {
          var target = parseInt(el.getAttribute('data-count'), 10) || 0;
          var duration = 1800;
          var startTime = null;
          function step(ts) {
            if (!startTime) startTime = ts;
            var progress = Math.min((ts - startTime) / duration, 1);
            var ease = 1 - Math.pow(1 - progress, 3);
            var value = Math.floor(ease * target);
            el.textContent = value.toLocaleString('en-IN');
            if (progress < 1) requestAnimationFrame(step);
          }
          requestAnimationFrame(step);
        });
      }
    }

    // ===== TOAST + ADD TO CART =====
    var toast = document.getElementById('toast');
    var toastText = document.getElementById('toastText');
    function showToast(msg) {
      if (!toast) return;
      if (toastText) toastText.textContent = msg;
      toast.classList.add('show');
      clearTimeout(showToast._t);
      showToast._t = setTimeout(function () { toast.classList.remove('show'); }, 2600);
    }

    document.querySelectorAll('.add-cart-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var name = this.getAttribute('data-name') || 'Product';
        var original = this.innerHTML;
        var self = this;
        this.innerHTML = '<i class="fa-solid fa-check me-1"></i>Added';
        showToast(name + ' added to cart');
        setTimeout(function () { self.innerHTML = original; }, 1400);
      });
    });

    // ===== PRODUCT DETAIL GALLERY (images + video combined) =====
    safe('ProductGallery', function () {
    var galleryRoot = document.querySelector('.product-gallery');
    if (!galleryRoot) return;

    var mediaList = [];
    try { mediaList = JSON.parse(galleryRoot.getAttribute('data-media') || '[]'); } catch (e) {}
    if (!mediaList.length) return;

    var galleryImg    = galleryRoot.querySelector('.product-gallery-main img');
    var galleryVideo  = galleryRoot.querySelector('.product-gallery-video');
    var unmuteBtn     = galleryRoot.querySelector('.video-unmute-btn');
    var unmuteBtnIcon = unmuteBtn ? unmuteBtn.querySelector('i') : null;
    var thumbBtns     = galleryRoot.querySelectorAll('.gallery-thumb');
    var autoTimer     = null;
    var currentIndex  = 0;
    var loadedVideoSrc = '';   // track what src is currently loaded

    // CSS transitions
    var transStyle = document.createElement('style');
    transStyle.textContent =
      '.product-gallery-main img { transition: opacity 0.3s ease; }' +
      '.product-gallery-video    { transition: opacity 0.3s ease; }' +
      '.video-unmute-btn:hover   { background: rgba(0,0,0,0.75) !important; }';
    document.head.appendChild(transStyle);

    // --- helpers ---
    function syncUnmuteIcon() {
      if (!unmuteBtnIcon || !galleryVideo) return;
      unmuteBtnIcon.className = galleryVideo.muted
        ? 'fa-solid fa-volume-xmark'
        : 'fa-solid fa-volume-high';
    }

    function showUnmuteBtn(v) {
      if (unmuteBtn) unmuteBtn.style.display = v ? 'flex' : 'none';
    }

    function updateThumbs(idx) {
      thumbBtns.forEach(function (btn, i) {
        btn.classList.toggle('active', i === idx);
        btn.style.borderColor = (i === idx) ? '#b08d57' : '#e0e0e0';
      });
    }

    function stopAutoSlide() {
      if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
    }

    function startAutoSlide() {
      if (mediaList.length <= 1) return;
      if (mediaList[currentIndex] && mediaList[currentIndex].type === 'video') return;
      stopAutoSlide();
      autoTimer = setInterval(function () {
        if (mediaList[currentIndex] && mediaList[currentIndex].type === 'video') {
          stopAutoSlide(); return;
        }
        var next = (currentIndex + 1) % mediaList.length;
        showMediaAt(next, false);
        if (mediaList[next] && mediaList[next].type === 'video') stopAutoSlide();
      }, 3200);
    }

    // --- core switch ---
    function showMediaAt(index, instant) {
      currentIndex = index;
      var item = mediaList[index];
      updateThumbs(index);

      if (item.type === 'video') {
        // hide image
        if (galleryImg) { galleryImg.style.opacity = '0'; galleryImg.style.display = 'none'; }

        if (galleryVideo) {
          // set src only if different
          if (item.url !== loadedVideoSrc) {
            loadedVideoSrc = item.url;
            galleryVideo.src = item.url;
            // do NOT call load() — let browser handle it; avoids blocking preload
          }

          galleryVideo.muted = true;
          galleryVideo.style.display = 'block';

          if (instant) {
            galleryVideo.style.opacity = '1';
            galleryVideo.currentTime = 0;
            galleryVideo.play().catch(function () {});
          } else {
            galleryVideo.style.opacity = '0';
            // wait for enough data before showing & playing
            function onReady() {
              galleryVideo.removeEventListener('canplay', onReady);
              galleryVideo.currentTime = 0;
              galleryVideo.play().catch(function () {});
              requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                  galleryVideo.style.opacity = '1';
                });
              });
            }
            // if already loaded enough, fire immediately
            if (galleryVideo.readyState >= 3) {
              onReady();
            } else {
              galleryVideo.addEventListener('canplay', onReady);
            }
          }

          syncUnmuteIcon();
          showUnmuteBtn(true);
        }

      } else {
        // hide video
        if (galleryVideo) {
          galleryVideo.pause();
          galleryVideo.style.opacity = '0';
          galleryVideo.style.display = 'none';
        }
        showUnmuteBtn(false);

        if (galleryImg) {
          galleryImg.src = item.url;
          galleryImg.style.display = 'block';
          if (instant) {
            galleryImg.style.opacity = '1';
          } else {
            galleryImg.style.opacity = '0';
            requestAnimationFrame(function () {
              requestAnimationFrame(function () { galleryImg.style.opacity = '1'; });
            });
          }
        }
      }
    }

    // unmute toggle
    if (unmuteBtn && galleryVideo) {
      unmuteBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        galleryVideo.muted = !galleryVideo.muted;
        if (!galleryVideo.muted && galleryVideo.paused) {
          galleryVideo.play().catch(function () { galleryVideo.muted = true; });
        }
        syncUnmuteIcon();
      });
    }

    // thumb clicks
    thumbBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var idx = parseInt(btn.getAttribute('data-index'), 10);
        if (isNaN(idx)) return;
        stopAutoSlide();
        showMediaAt(idx, false);
        if (mediaList[idx] && mediaList[idx].type !== 'video') startAutoSlide();
      });
    });

    // video ended
    if (galleryVideo) {
      galleryVideo.addEventListener('ended', function () {
        var next = (currentIndex + 1) % mediaList.length;
        if (next === currentIndex) {
          galleryVideo.currentTime = 0;
          galleryVideo.play().catch(function () {});
        } else {
          showMediaAt(next, false);
          startAutoSlide();
        }
      });
    }

    // kick off
    showMediaAt(0, true);
    startAutoSlide();

    }); // end safe('ProductGallery')

    function syncProductGalleryHeight() {
      var gallery = document.querySelector('.product-gallery-main');
      var details = document.querySelector('.product-detail-intro');
      if (!gallery || !details) return;
      var detailsHeight = details.getBoundingClientRect().height;
      // Only cap height if the details column is actually rendered (height > 100)
      // Use minHeight so gallery never collapses to 0
      if (detailsHeight > 100) {
        gallery.style.maxHeight = detailsHeight + 'px';
        gallery.style.minHeight = '200px';
      }
    }

    // Run after full page load so fonts/images are rendered and heights are accurate
    window.addEventListener('load', syncProductGalleryHeight);
    window.addEventListener('resize', syncProductGalleryHeight);

    // ===== CONTACT FORM =====
    var contactForm = document.getElementById('contactForm');
    if (contactForm) {
      contactForm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!contactForm.checkValidity()) {
          contactForm.classList.add('was-validated');
          return;
        }

        var success = document.getElementById('formSuccess');
        var submitBtn = contactForm.querySelector('[type="submit"]');
        var formData = new FormData(contactForm);
        var payload = {
          name: formData.get('name'),
          email: formData.get('email'),
          phone: formData.get('phone'),
          message: formData.get('message'),
        };

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.classList.add('opacity-70');
        }

        fetch('/contact/messages', {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          },
          body: JSON.stringify(payload),
        })
          .then(function (response) {
            return response.json().then(function (data) {
              if (!response.ok) {
                throw data;
              }
              return data;
            });
          })
          .then(function () {
            if (success) {
              success.classList.add('show');
            }
            contactForm.reset();
            contactForm.classList.remove('was-validated');
            setTimeout(function () { if (success) success.classList.remove('show'); }, 5000);
          })
          .catch(function (error) {
            console.error(error);
            var errorMessage = 'Unable to send message. Please try again later.';
            if (error && error.errors) {
              errorMessage = Object.values(error.errors).flat().join(' ');
            } else if (error && error.message) {
              errorMessage = error.message;
            }
            if (success) {
              success.textContent = errorMessage;
              success.classList.add('show');
            }
          })
          .finally(function () {
            if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.classList.remove('opacity-70');
            }
          });
      });
    }

    // ===== NEWSLETTER =====
    var news = document.getElementById('newsletterForm');
    if (news) {
      news.addEventListener('submit', function (e) {
        e.preventDefault();
        var input = news.querySelector('input');
        var msg = document.getElementById('newsletterMsg');
        var email = (input.value || '').trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          if (msg) { msg.style.color = '#ef4444'; msg.textContent = 'Please enter a valid email.'; }
          return;
        }
        if (msg) { msg.style.color = '#22C55E'; msg.textContent = 'Subscribed! Check your inbox for a 10% OFF code.'; }
        input.value = '';
      });
    }

    // ===== FOOTER YEAR =====
    var y = document.getElementById('year');
    if (y) y.textContent = new Date().getFullYear();

    // ===== COUNTDOWN TIMER (48hr rolling) =====
    safe('countdown', function () {
      var STORAGE = 'engixCountdownEnd';
      var end = parseInt(localStorage.getItem(STORAGE), 10);
      var now = Date.now();
      if (!end || end < now) {
        end = now + 48 * 60 * 60 * 1000;
        localStorage.setItem(STORAGE, String(end));
      }
      var d = document.getElementById('cd-days'),
          h = document.getElementById('cd-hours'),
          m = document.getElementById('cd-mins'),
          s = document.getElementById('cd-secs');
      if (!d) return;
      function pad(n) { return n < 10 ? '0' + n : '' + n; }
      function tick() {
        var diff = Math.max(0, end - Date.now());
        var days = Math.floor(diff / 86400000);
        var hrs  = Math.floor((diff % 86400000) / 3600000);
        var mins = Math.floor((diff % 3600000) / 60000);
        var secs = Math.floor((diff % 60000) / 1000);
        d.textContent = pad(days); h.textContent = pad(hrs);
        m.textContent = pad(mins); s.textContent = pad(secs);
      }
      tick();
      setInterval(tick, 1000);
    });

    // ===== WELCOME MODAL (once per session) =====
    safe('welcome-modal', function () {
      var modal = document.getElementById('welcomeModal');
      if (!modal) return;
      var shown = sessionStorage.getItem('engixWelcome');
      var codeEl = document.getElementById('wmCodeText');
      var welcomeCode = codeEl ? codeEl.textContent.trim() : 'WELCOME15';

      function shouldShowModal() {
        if (shown) return Promise.resolve(false);
        // Ask server whether the authenticated user already used this coupon
        try {
          return fetch('/user/has-used-coupon?coupon_code=' + encodeURIComponent(welcomeCode), { credentials: 'same-origin' })
            .then(function (res) {
              if (res.status === 401) return true; // unauthenticated — allow showing
              return res.json().then(function (json) { return !json.used; });
            }).catch(function () { return true; });
        } catch (e) { return Promise.resolve(true); }
      }

      shouldShowModal().then(function (ok) {
        if (ok) setTimeout(function () { modal.classList.add('show'); }, 3500);
        else sessionStorage.setItem('engixWelcome', '1');
      });

      function close() {
        modal.classList.remove('show');
        sessionStorage.setItem('engixWelcome', '1');
      }

      var btnClose = document.getElementById('wmClose');
      var btnNope = document.getElementById('wmNope');
      var btnOverlay = document.getElementById('wmOverlay');
      var btnClaim = document.getElementById('wmClaim');
      if (btnClose) btnClose.addEventListener('click', close);
      if (btnNope) btnNope.addEventListener('click', close);
      if (btnOverlay) btnOverlay.addEventListener('click', close);
      if (btnClaim) {
        btnClaim.addEventListener('click', function () {
          var email = document.getElementById('wmEmail').value.trim();
          if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showToast('Please enter a valid email');
            return;
          }
          var code = welcomeCode;
          showToast('Coupon ' + code + ' sent to your email!');
          // Pre-apply the welcome coupon to the cart
          if (code && typeof window.applyOfferCoupon === 'function') {
            window.applyOfferCoupon(code, 'Welcome Offer');
          }
          close();
        });
      }
    });

    // ===== LOYALTY WIDGET =====
    safe('loyalty', function () {
      var fab = document.getElementById('loyaltyFab');
      var panel = document.getElementById('loyaltyPanel');
      var close = document.getElementById('lpClose');
      var num = document.getElementById('lpNum');
      var started = false;
      function open() {
        panel.classList.add('show');
        if (!started && num) {
          started = true;
          var target = parseInt(num.getAttribute('data-count'), 10) || 0;
          var startTime = null;
          function step(ts) {
            if (!startTime) startTime = ts;
            var p = Math.min((ts - startTime) / 1400, 1);
            var ease = 1 - Math.pow(1 - p, 3);
            num.textContent = Math.floor(ease * target).toLocaleString('en-IN');
            if (p < 1) requestAnimationFrame(step);
          }
          requestAnimationFrame(step);
        }
      }
      function closeFn() { panel.classList.remove('show'); }
      if (fab) fab.addEventListener('click', function () {
        panel.classList.contains('show') ? closeFn() : open();
      });
      if (close) close.addEventListener('click', closeFn);
      document.addEventListener('click', function (e) {
        if (!panel || !fab) return;
        if (!panel.classList.contains('show')) return;
        if (panel.contains(e.target) || fab.contains(e.target)) return;
        closeFn();
      });
    });

    // ===== FLOATING COUPON =====
    safe('floating-coupon', function () {
      var fc = document.getElementById('floatingCoupon');
      var close = document.getElementById('fcClose');
      var copy = document.getElementById('fcCopy');
      if (!fc) return;
      var codeEl = document.getElementById('fcCodeText');
      var floatingCode = codeEl ? codeEl.firstChild.textContent.trim() : '';

      function shouldShowFloating() {
        if (sessionStorage.getItem('engixCouponClosed')) return Promise.resolve(false);
        if (!floatingCode) return Promise.resolve(false);
        try {
          return fetch('/user/has-used-coupon?coupon_code=' + encodeURIComponent(floatingCode), { credentials: 'same-origin' })
            .then(function (res) {
              if (res.status === 401) return true; // unauthenticated: show
              return res.json().then(function (json) { return !json.used; });
            }).catch(function () { return true; });
        } catch (e) { return Promise.resolve(true); }
      }

      shouldShowFloating().then(function (ok) {
        if (ok) setTimeout(function () { if (!sessionStorage.getItem('engixCouponClosed')) fc.classList.add('show'); }, 6000);
      });
      if (close) close.addEventListener('click', function () {
        fc.classList.remove('show');
        fc.classList.add('hide');
        sessionStorage.setItem('engixCouponClosed', '1');
      });
      if (copy) copy.addEventListener('click', function () {
        // Read the code from the DOM element (set dynamically by the server)
        var codeEl = document.getElementById('fcCodeText');
        var code = codeEl ? codeEl.firstChild.textContent.trim() : '';
        if (!code) return;
        try {
          navigator.clipboard.writeText(code);
          showToast('Coupon ' + code + ' copied!');
        } catch (e) { showToast('Coupon: ' + code); }
      });
      // Clicking the code itself also applies it to cart
      var codeEl = document.getElementById('fcCodeText');
      if (fc && codeEl) {
        fc.addEventListener('click', function (e) {
          // only if user clicks the code text / copy area, not close btn
          if (e.target.closest('.fc-close')) return;
          var code = codeEl.firstChild.textContent.trim();
          if (code && typeof window.applyOfferCoupon === 'function') {
            window.applyOfferCoupon(code, 'Floating Coupon');
            fc.classList.remove('show');
            sessionStorage.setItem('engixCouponClosed', '1');
            openCart();
          }
        });
      }
    });

    // ===== LIVE PURCHASE NOTIFICATIONS =====
    safe('live-notif', function () {
      var notif = document.getElementById('liveNotif');
      if (!notif) return;
      var names = [
        { n: 'Anika', c: 'Mumbai', p: 'Vitamin C 1000mg', a: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=100&q=80' },
        { n: 'Rohan', c: 'Delhi', p: 'Multivitamin', a: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=100&q=80' },
        { n: 'Sara', c: 'Bengaluru', p: 'Vitamin D3 + K2', a: 'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=100&q=80' },
        { n: 'Karan', c: 'New York', p: 'Omega 3 Fish Oil', a: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80' },
        { n: 'Meera', c: 'Chennai', p: 'Immunity Shield Stack', a: 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=100&q=80' },
        { n: 'Aisha', c: 'London', p: 'Energy & Focus Stack', a: 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=100&q=80' }
      ];
      var i = 0;
      var img = document.getElementById('lnAvatar');
      var nm = document.getElementById('lnName');
      var ct = document.getElementById('lnCity');
      var pd = document.getElementById('lnProduct');
      var tm = document.getElementById('lnTime');
      function show() {
        var p = names[i % names.length];
        img.src = p.a; nm.textContent = p.n; ct.textContent = p.c; pd.textContent = p.p;
        tm.textContent = (Math.floor(Math.random() * 8) + 1) + ' minutes ago';
        notif.classList.add('show');
        setTimeout(function () { notif.classList.remove('show'); }, 5000);
        i++;
      }
      setTimeout(show, 9000);
      setInterval(show, 15000);
    });

    // ===== STICKY BUY BAR =====
    safe('sticky-buy', function () {
      var bar = document.getElementById('stickyBuy');
      if (!bar) return;
      function toggle() {
        if (window.scrollY > 900) bar.classList.add('show');
        else bar.classList.remove('show');
      }
      window.addEventListener('scroll', toggle, { passive: true });
      toggle();
    });

    // ===== PARALLAX ON HERO =====
    safe('parallax', function () {
      var img = document.querySelector('.hero-image');
      var shape = document.querySelector('.hero-bg-shape');
      if (!img && !shape) return;
      window.addEventListener('scroll', function () {
        var y = window.scrollY;
        if (y > 800) return;
        if (img) img.style.transform = 'translateY(' + (y * 0.08) + 'px)';
        if (shape) shape.style.transform = 'translate(' + (y * 0.04) + 'px, ' + (y * 0.06) + 'px)';
      }, { passive: true });
    });

    // ===== PRODUCT QUICK ACTIONS =====
    safe('quick-actions', function () {
      document.querySelectorAll('.pq-btn').forEach(function (b) {
        b.addEventListener('click', function (e) {
          e.preventDefault(); e.stopPropagation();
          var isWish = this.querySelector('.fa-heart');
          if (isWish) {
            isWish.classList.toggle('fa-regular');
            isWish.classList.toggle('fa-solid');
            isWish.style.color = isWish.classList.contains('fa-solid') ? '#ef4444' : '';
            showToast(isWish.classList.contains('fa-solid') ? 'Added to wishlist' : 'Removed from wishlist');
          } else {
            showToast('Quick view coming soon');
          }
        });
      });
    });

    // ===== CART SYSTEM =====
    var cart = [];
    var appliedCoupon = null; // { code, discount, message }
    var badge = document.getElementById('cartBadge');
    var headCount = document.getElementById('cartHeadCount');
    var cartItemsEl = document.getElementById('cartItems');
    var cartEmpty = document.getElementById('cartEmpty');
    var cartFoot = document.getElementById('cartFoot');
    var cartTotalEl = document.getElementById('cartTotal');
    var cartSavings = document.getElementById('cartSavings');

    function priceOf(name) {
      var map = { 'Glow Elixir':694, 'Hair Revive':340, 'Slim Trim':340, 'Liver Shield':340,
                  'DeepRest':340, 'Vital Fuel':340,
                  'Boost Energy':299, 'Vitamin B12':299, 'Boost Energy + Vitamin B12':539,
                  'Energy Stack':1299, 'Immunity Stack':999, 'Heart Stack':1499,
                  'Immunity Shield Stack':999, 'Custom Bundle':0, 'Your Custom Stack':1299 };
      return map[name] || 299;
    }
    function imgOf(name) {
      var map = {
        'Glow Elixir':'https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?auto=format&fit=crop&w=200&q=80',
        'Hair Revive':'https://images.unsplash.com/photo-1522337660859-02fbefca4702?auto=format&fit=crop&w=200&q=80',
        'Slim Trim':'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=200&q=80',
        'Liver Shield':'https://images.unsplash.com/photo-1615485500704-8e990f9900f7?auto=format&fit=crop&w=200&q=80',
        'DeepRest':'https://images.unsplash.com/photo-1531353826977-0941b4779a1c?auto=format&fit=crop&w=200&q=80',
        'Vital Fuel':'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=200&q=80',
        'Boost Energy':'/asset/210A0226.png',
        'Vitamin B12':'/asset/210A0258.png',
        'Boost Energy + Vitamin B12':'/asset/210A0226.png',
        'Energy Stack':'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?auto=format&fit=crop&w=200&q=80',
        'Immunity Stack':'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?auto=format&fit=crop&w=200&q=80',
        'Immunity Shield Stack':'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?auto=format&fit=crop&w=200&q=80',
        'Heart Stack':'https://images.unsplash.com/photo-1626716493137-b67fe9501e76?auto=format&fit=crop&w=200&q=80'
      };
      return map[name] || '/asset/210A0226.png';
    }

    function renderCart() {
      var count = cart.reduce(function (a, it) { return a + it.qty; }, 0);
      var subtotal = cart.reduce(function (a, it) { return a + (it.price * it.qty); }, 0);

      if (badge) {
        badge.textContent = count;
        badge.classList.add('bump');
        setTimeout(function () { badge.classList.remove('bump'); }, 500);
      }
      if (headCount) headCount.textContent = '(' + count + ')';

      if (count === 0) {
        if (cartEmpty) cartEmpty.style.display = 'block';
        if (cartItemsEl) cartItemsEl.innerHTML = '';
        if (cartFoot) cartFoot.classList.remove('show');
        return;
      }
      if (cartEmpty) cartEmpty.style.display = 'none';
      if (cartFoot) cartFoot.classList.add('show');

      if (cartItemsEl) cartItemsEl.innerHTML = cart.map(function (it, i) {
        return '<li>' +
          '<img src="' + it.img + '" alt="' + it.name + '" />' +
          '<div class="ci-info"><h6>' + it.name + '</h6>' +
          '<div class="ci-price">₹' + (it.price * it.qty).toLocaleString('en-IN') + '</div>' +
          '<div class="ci-qty"><button data-act="dec" data-i="' + i + '">−</button><span>' + it.qty + '</span><button data-act="inc" data-i="' + i + '">+</button></div>' +
          '</div>' +
          '<button class="ci-remove" data-act="rm" data-i="' + i + '"><i class="fa-solid fa-trash"></i></button>' +
          '</li>';
      }).join('');

      // ── Bundle savings message removed ──
      if (cartSavings) cartSavings.textContent = '';

      // ── Price breakdown with coupon only ──
      var couponDiscount = 0;
      if (appliedCoupon) {
        if (appliedCoupon.percentage) {
          couponDiscount = Math.round(subtotal * (appliedCoupon.percentage / 100));
          appliedCoupon.discount = couponDiscount;
        } else {
          couponDiscount = appliedCoupon.discount || 0;
        }
      }
      var total = subtotal - couponDiscount;
      if (total < 0) total = 0;

      var subtotalEl = document.getElementById('cartSubtotal');
      var cpbDiscountRow = document.getElementById('cpbDiscountRow');
      var cpbDiscountAmt = document.getElementById('cpbDiscountAmt');
      var cpbDiscountLabel = document.getElementById('cpbDiscountLabel');
      if (subtotalEl) subtotalEl.textContent = '₹' + subtotal.toLocaleString('en-IN');
      if (cartTotalEl) cartTotalEl.textContent = '₹' + total.toLocaleString('en-IN');
      if (cpbDiscountRow) {
        if (couponDiscount > 0) {
          cpbDiscountRow.style.display = 'flex';
          if (cpbDiscountAmt) cpbDiscountAmt.textContent = '– ₹' + couponDiscount.toLocaleString('en-IN');
          if (cpbDiscountLabel && appliedCoupon) cpbDiscountLabel.textContent = 'Coupon (' + appliedCoupon.code + ')';
        } else {
          cpbDiscountRow.style.display = 'none';
        }
      }

      // Notify checkout page summary to re-render
      document.dispatchEvent(new CustomEvent('cartUpdated', { detail: cart }));
    }

    function openCart() {
      var overlay = document.getElementById('cartOverlay');
      var drawer = document.getElementById('cartDrawer');
      if (overlay) overlay.classList.add('show');
      if (drawer) drawer.classList.add('show');
      document.body.style.overflow = 'hidden';
    }
    function closeCart() {
      var overlay = document.getElementById('cartOverlay');
      var drawer = document.getElementById('cartDrawer');
      if (overlay) overlay.classList.remove('show');
      if (drawer) drawer.classList.remove('show');
      document.body.style.overflow = '';
    }
    document.getElementById('cartOpen')?.addEventListener('click', function (e) { e.preventDefault(); openCart(); });
    document.getElementById('cartClose')?.addEventListener('click', closeCart);
    document.getElementById('cartOverlay')?.addEventListener('click', closeCart);
    document.getElementById('cartShopBtn')?.addEventListener('click', function () {
      closeCart();
    });

    // Checkout handler
    var checkoutBtn = document.querySelector('[data-testid="cart-checkout"]');
    var checkoutBtnOriginalHTML = checkoutBtn ? checkoutBtn.innerHTML : '<i class="fa-solid fa-lock me-2"></i>Secure Checkout';

    // Reset button if user navigates back from checkout (bfcache restore)
    window.addEventListener('pageshow', function(e) {
      if (e.persisted && checkoutBtn) {
        checkoutBtn.disabled = false;
        checkoutBtn.innerHTML = checkoutBtnOriginalHTML;
      }
    });

    if (checkoutBtn) {
      checkoutBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (cart.length === 0) {
          showToast('Please add items to cart');
          return;
        }

        // Check if user is authenticated
        var userCart = document.getElementById('userCartData');
        if (!userCart) {
          showToast('Please login to checkout');
          setTimeout(function() { window.location.href = window.APP_USER && window.APP_USER.loginUrl ? window.APP_USER.loginUrl : '/login'; }, 800);
          return;
        }

        // Save cart then redirect to checkout
        checkoutBtn.disabled = true;
        checkoutBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Please wait…';
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('/cart/save', {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
          body: JSON.stringify({ cart: cart })
        }).then(function() {
          var checkoutUrl = window.APP_USER && window.APP_USER.checkoutUrl ? window.APP_USER.checkoutUrl : '/checkout';
          if (appliedCoupon && appliedCoupon.code) {
            checkoutUrl += (checkoutUrl.indexOf('?') === -1 ? '?' : '&') + 'coupon_code=' + encodeURIComponent(appliedCoupon.code);
          }
          window.location.href = checkoutUrl;
        }).catch(function() {
          var checkoutUrl = '/checkout';
          if (appliedCoupon && appliedCoupon.code) {
            checkoutUrl += '?coupon_code=' + encodeURIComponent(appliedCoupon.code);
          }
          window.location.href = checkoutUrl;
        });
      });
    }

    cartItemsEl?.addEventListener('click', function (e) {
      var btn = e.target.closest('button[data-act]');
      if (!btn) return;
      var i = parseInt(btn.getAttribute('data-i'), 10);
      var act = btn.getAttribute('data-act');
      if (act === 'inc') cart[i].qty++;
      else if (act === 'dec') { cart[i].qty--; if (cart[i].qty <= 0) cart.splice(i, 1); }
      else if (act === 'rm') cart.splice(i, 1);
      renderCart();
      saveCartToDatabase();
      // If a coupon is applied, revalidate it against the new subtotal
      if (appliedCoupon && appliedCoupon.code) {
        revalidateAppliedCoupon();
      }
    });

    function flyToCart(sourceEl) {
      var fly = document.getElementById('flyItem');
      var target = document.getElementById('cartBadge');
      if (!fly || !target || !sourceEl) return;
      var s = sourceEl.getBoundingClientRect();
      var t = target.getBoundingClientRect();
      fly.style.left = s.left + s.width / 2 - 22 + 'px';
      fly.style.top = s.top + 'px';
      fly.style.transform = 'translate(0, 0) scale(1)';
      fly.classList.add('active');
      requestAnimationFrame(function () {
        fly.style.transform = 'translate(' + (t.left - s.left + t.width / 2 - s.width / 2) + 'px, ' + (t.top - s.top) + 'px) scale(0.2)';
      });
      setTimeout(function () {
        fly.classList.remove('active');
        fly.style.transform = 'translate(0, 0) scale(1)';
      }, 850);
    }

    // ===== CART PERSISTENCE =====
    function saveCartToDatabase() {
      var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      fetch('/cart/save', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ cart: cart })
      }).then(function(res) {
        if (res.ok) {
          console.log('[Cart] Saved to database');
        } else {
          res.text().then(function(body) {
            console.error('[Cart] Save failed – status:', res.status, 'body:', body);
          });
        }
      }).catch(function(err) {
        console.error('[Cart] Fetch error:', err);
      });
    }

    function loadCartFromDatabase() {
      var cartData = document.getElementById('userCartData');
      if (cartData) {
        try {
          var savedCart = JSON.parse(cartData.getAttribute('data-user-cart'));
          if (Array.isArray(savedCart) && savedCart.length > 0) {
            cart = savedCart;
            renderCart();
          }
        } catch (e) {
          console.warn('Error loading cart from database:', e);
        }
      }
    }

    // Load cart from database on page load
    loadCartFromDatabase();

    // ===== COUPON SYSTEM =====
    safe('coupon-system', function () {
      var couponInput    = document.getElementById('cartCouponInput');
      var couponApplyBtn = document.getElementById('cartCouponApply');
      var couponMsg      = document.getElementById('cartCouponMsg');
      var couponRow      = document.getElementById('cartCouponRow');
      var couponApplied  = document.getElementById('cartCouponApplied');
      var appliedCode    = document.getElementById('cartCouponAppliedCode');
      var removeBtn      = document.getElementById('cartCouponRemove');
      if (!couponInput || !couponApplyBtn) return;

      function showCouponMsg(msg, isSuccess) {
        if (!couponMsg) return;
        couponMsg.textContent = msg;
        couponMsg.className = 'ccp-msg ' + (isSuccess ? 'success' : 'error');
        couponMsg.style.display = 'block';
        clearTimeout(showCouponMsg._t);
        showCouponMsg._t = setTimeout(function () { couponMsg.style.display = 'none'; }, 4000);
      }

      function setCouponAppliedUI(code) {
        if (couponRow) couponRow.style.display = 'none';
        if (couponApplied) couponApplied.style.display = 'flex';
        if (appliedCode) appliedCode.textContent = code;
        if (couponMsg) couponMsg.style.display = 'none';
      }

      function clearCouponUI(keepInput) {
        if (couponRow) couponRow.style.display = 'flex';
        if (couponApplied) couponApplied.style.display = 'none';
        if (!keepInput && couponInput) couponInput.value = '';
      }

      function applyCartCoupon(code) {
        var subtotal = cart.reduce(function (a, it) { return a + (it.price * it.qty); }, 0);
        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        couponApplyBtn.disabled = true;
        couponApplyBtn.textContent = '…';

        fetch('/coupon/validate', {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
          body: JSON.stringify({ coupon_code: code, subtotal: subtotal })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          couponApplyBtn.disabled = false;
          couponApplyBtn.textContent = 'Apply';
          if (data.valid) {
            appliedCoupon = { code: data.code, discount: data.discount, message: data.message, percentage: data.percentage || null };
            setCouponAppliedUI(data.code);
            showToast('✅ ' + data.message);
            renderCart();
          } else {
            // Invalid coupon — clear any previously applied coupon and show error
            appliedCoupon = null;
            clearCouponUI(true); // keep the bad code in input so user sees what failed
            showCouponMsg(data.message, false);
            renderCart();
          }
        })
        .catch(function () {
          couponApplyBtn.disabled = false;
          couponApplyBtn.textContent = 'Apply';
          // Network/server error — never apply an unvalidated coupon
          appliedCoupon = null;
          clearCouponUI(true);
          showCouponMsg('Could not validate coupon. Please check your connection.', false);
          renderCart();
        });
      }

      couponApplyBtn.addEventListener('click', function () {
        var code = (couponInput.value || '').trim();
        if (!code) { showCouponMsg('Please enter a coupon code', false); return; }
        applyCartCoupon(code);
      });

      couponInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') couponApplyBtn.click();
      });

      if (removeBtn) {
        removeBtn.addEventListener('click', function () {
          appliedCoupon = null;
          clearCouponUI(); // blank the input on manual remove
          renderCart();
          showToast('Coupon removed');
        });
      }

      // Expose applyCartCoupon for offer buttons
      window.applyOfferCoupon = function (code, title) {
        appliedCoupon = null; // clear any existing
        clearCouponUI(); // blank input before setting new code
        if (couponInput) couponInput.value = code;
        applyCartCoupon(code);
      };
    });

    // ===== COUPON REVALIDATION (called after cart qty changes) =====
    function revalidateAppliedCoupon() {
      if (!appliedCoupon || !appliedCoupon.code) return;
      var subtotal    = cart.reduce(function (a, it) { return a + (it.price * it.qty); }, 0);
      var csrfToken   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      var savedCode   = appliedCoupon.code;

      fetch('/coupon/validate', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ coupon_code: savedCode, subtotal: subtotal })
      })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.valid) {
          // Update stored coupon with recalculated discount + percentage
          appliedCoupon.discount    = data.discount;
          appliedCoupon.percentage  = data.percentage || appliedCoupon.percentage;
          appliedCoupon.message     = data.message;
        } else {
          // Coupon no longer valid (e.g. amount_based and subtotal dropped below threshold)
          appliedCoupon = null;
          var couponApplied = document.getElementById('cartCouponApplied');
          var couponRow     = document.getElementById('cartCouponRow');
          var couponInput   = document.getElementById('cartCouponInput');
          if (couponApplied) couponApplied.style.display = 'none';
          if (couponRow)     couponRow.style.display     = 'flex';
          if (couponInput)   couponInput.value           = savedCode; // keep code visible
          var couponMsg = document.getElementById('cartCouponMsg');
          if (couponMsg) {
            couponMsg.textContent  = data.message;
            couponMsg.className    = 'ccp-msg error';
            couponMsg.style.display = 'block';
            clearTimeout(revalidateAppliedCoupon._t);
            revalidateAppliedCoupon._t = setTimeout(function () { couponMsg.style.display = 'none'; }, 5000);
          }
        }
        renderCart();
      })
      .catch(function () {
        // On network error: keep the coupon applied but recalculate using stored percentage
        if (appliedCoupon && appliedCoupon.percentage) {
          appliedCoupon.discount = Math.round(subtotal * (appliedCoupon.percentage / 100));
        }
        renderCart();
      });
    }

    // ===== APPLY OFFER BUTTONS (offer cards + scroll offer) =====
    safe('apply-offer-btns', function () {
      document.querySelectorAll('.apply-offer-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          var code  = this.getAttribute('data-coupon') || '';
          var title = this.getAttribute('data-offer-title') || 'Offer';
          if (!code) return;
          // Apply the coupon in the cart drawer
          if (typeof window.applyOfferCoupon === 'function') {
            window.applyOfferCoupon(code, title);
          }
          // Close any open overlay (scroll offer popup)
          var scrollOffer = document.getElementById('scrollOffer');
          if (scrollOffer) scrollOffer.classList.remove('show');
          // Open the cart drawer
          openCart();
          showToast('Coupon ' + code + ' ready — add products & checkout!');
        });
      });
    });

    // ===== BANNER APPLY BUTTON (first-order eligibility check + checkout redirect) =====
    safe('banner-apply-btn', function () {
      document.querySelectorAll('.banner-apply-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          var code  = this.getAttribute('data-coupon') || '';
          if (!code) return;

          var self = this;
          self.disabled = true;
          self.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Checking…';

          var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

          fetch('/offer/apply', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ coupon_code: code })
          })
          .then(function (res) { return res.json().then(function (d) { return { status: res.status, data: d }; }); })
          .then(function (r) {
            var data = r.data;
            if (data.ok) {
              // Save cart first, then redirect to checkout with the coupon pre-applied
              showToast(data.message || 'Offer applied!');
              var csrfSave = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
              fetch('/cart/save', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfSave },
                body: JSON.stringify({ cart: cart })
              }).finally(function () {
                window.location.href = '/checkout?coupon_code=' + encodeURIComponent(data.coupon_code);
              });
            } else {
              self.disabled = false;
              self.innerHTML = '<i class="fa-solid fa-bolt me-2"></i>Apply';
              if (data.reason === 'unauthenticated') {
                showToast('Please log in to apply this offer.');
                setTimeout(function () { window.location.href = '/login'; }, 1200);
              } else if (data.reason === 'not_first_order') {
                showToast('This offer is only valid on your first order.');
              } else {
                showToast(data.message || 'Offer could not be applied.');
              }
            }
          })
          .catch(function () {
            self.disabled = false;
            self.innerHTML = '<i class="fa-solid fa-bolt me-2"></i>Apply';
            showToast('Something went wrong. Please try again.');
          });
        });
      });
    });

    // Override the add-to-cart to use real cart
    document.querySelectorAll('.add-cart-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var name = this.getAttribute('data-name') || 'Product';
        var price = parseInt(this.getAttribute('data-price'), 10) || priceOf(name);
        var productId = this.getAttribute('data-product-id') || null;
        var existing = cart.find(function (it) { return it.name === name; });
        if (existing) existing.qty++;
        else cart.push({ name: name, price: price, qty: 1, img: imgOf(name), product_id: productId });
        flyToCart(this);
        renderCart();
        saveCartToDatabase();
        // Open cart after fly animation (850ms) completes
        setTimeout(function () { openCart(); }, 900);
      });
    }, true);

    // ===== WELLNESS QUIZ =====
    safe('quiz', function () {
      var slides = document.querySelectorAll('.quiz-slide');
      var qpBar = document.getElementById('qpBar');
      var qpText = document.getElementById('qpText');
      var answers = { q1: null, q2: null, q3: null };
      var current = 0;
      var TOTAL = 3;

      function updateProgress() {
        qpBar.style.width = ((current) / TOTAL * 100) + '%';
        qpText.textContent = current < TOTAL ? 'Question ' + (current + 1) + ' of ' + TOTAL : 'Complete!';
      }
      function goto(i) {
        slides.forEach(function (s) { s.classList.remove('active'); });
        slides[i].classList.add('active');
        current = i;
        updateProgress();
      }
      updateProgress();

      document.querySelectorAll('.quiz-opt').forEach(function (opt) {
        opt.addEventListener('click', function () {
          var slide = this.closest('.quiz-slide');
          slide.querySelectorAll('.quiz-opt').forEach(function (o) { o.classList.remove('selected'); });
          this.classList.add('selected');
          if (this.dataset.q1) answers.q1 = this.dataset.q1;
          if (this.dataset.q2) answers.q2 = this.dataset.q2;
          if (this.dataset.q3) answers.q3 = this.dataset.q3;

          setTimeout(function () {
            var next = current + 1;
            if (next < slides.length) {
              goto(next);
              if (next === 3) buildResult();
            }
          }, 380);
        });
      });

      function recommend() {
        // Both / all-of-the-above → recommend combo
        if (answers.q1 === 'both' || answers.q3 === 'overall') {
          return 'combo';
        }
        // Nerve / B12 signals → Vitamin B12
        if (answers.q1 === 'nerve' || answers.q2 === 'good' || answers.q3 === 'nerve') {
          return 'b12';
        }
        // Energy / hydration / active / crash → Boost Energy
        return 'energy';
      }

      var products = {
        energy: {
          name: 'Boost Energy',
          displayName: 'Boost Energy (Orange)',
          desc: 'Hydrate. Restore. Recharge. Drop 1 tablet in 200 ml water and enjoy a tangy orange effervescent drink. Pack of 15 tablets.',
          items: ['Electrolytes', 'Chloride 220mg', 'Magnesium 56mg', 'Potassium 115mg'],
          price: 299, old: 399,
          dataPrice: 299
        },
        b12: {
          name: 'Vitamin B12',
          displayName: 'Vitamin B12 (Pomegranate)',
          desc: 'Support nerve health & energy metabolism. Drop 1 tablet in 200 ml water for a tangy pomegranate effervescent drink. Pack of 15 tablets.',
          items: ['Vitamin B12', 'Nerve Support', 'Energy Metabolism', 'Pomegranate Flavour'],
          price: 299, old: 399,
          dataPrice: 299
        },
        combo: {
          name: 'Boost Energy + Vitamin B12',
          displayName: 'Energy + B12 Combo',
          desc: 'The ultimate daily wellness duo — Boost Energy (Orange) for hydration & recharge, Vitamin B12 (Pomegranate) for nerves & metabolism. Two packs of 15 tablets each.',
          items: ['Boost Energy (Orange)', 'Vitamin B12 (Pomegranate)', 'Complete Daily Wellness', 'Combo Pack'],
          price: 539, old: 598,
          dataPrice: 539
        }
      };

      function buildResult() {
        var key = recommend();
        var pick = products[key];
        document.getElementById('qrStackName').textContent = pick.displayName;
        document.getElementById('qrStackDesc').textContent = pick.desc;
        document.getElementById('qrPrice').textContent = '₹' + pick.price.toLocaleString('en-IN');
        document.getElementById('qrOld').textContent = '₹' + pick.old.toLocaleString('en-IN');
        document.getElementById('qrStack').innerHTML = pick.items.map(function (it) {
          return '<span class="qr-chip"><i class="fa-solid fa-check me-1"></i>' + it + '</span>';
        }).join('');
        var addBtn = document.getElementById('qrAddBtn');
        if (addBtn) {
          addBtn.setAttribute('data-name', pick.name);
          addBtn.setAttribute('data-price', pick.dataPrice);
        }
      }

      document.getElementById('qrRetake')?.addEventListener('click', function () {
        answers = { q1: null, q2: null, q3: null };
        document.querySelectorAll('.quiz-opt.selected').forEach(function (o) { o.classList.remove('selected'); });
        goto(0);
      });
    });

    // ===== BUNDLE BUILDER =====
    safe('bundle', function () {
      var checks = document.querySelectorAll('.bundle-check');
      var cntEl = document.getElementById('bsCount');
      var subEl = document.getElementById('bsSub');
      var disEl = document.getElementById('bsDiscount');
      var totEl = document.getElementById('bsTotal');
      var saveEl = document.getElementById('bsSave');
      var buyBtn = document.getElementById('bundleBuy');
      function recalc() {
        var picked = [];
        var sub = 0;
        checks.forEach(function (c) {
          if (c.checked) {
            var p = parseInt(c.dataset.price, 10);
            picked.push({ name: c.dataset.name, price: p });
            sub += p;
          }
        });
        var count = picked.length;
        var pct = 0;
        if (count >= 4) pct = 20;
        else if (count === 3) pct = 15;
        else if (count === 2) pct = 10;
        var discount = Math.round(sub * pct / 100);
        var total = sub - discount;
        cntEl.textContent = count;
        subEl.textContent = '₹' + sub.toLocaleString('en-IN');
        disEl.textContent = '– ₹' + discount.toLocaleString('en-IN');
        totEl.textContent = '₹' + total.toLocaleString('en-IN');
        if (count === 0) saveEl.textContent = 'Add 2+ items to unlock discount';
        else if (count === 1) saveEl.textContent = 'Add 1 more for 10% OFF';
        else saveEl.innerHTML = '🎉 You save <b>₹' + discount.toLocaleString('en-IN') + '</b> with <b>' + pct + '% OFF</b>!';
        buyBtn.setAttribute('data-price', total);
      }
      checks.forEach(function (c) { c.addEventListener('change', recalc); });
      recalc();
    });

    // ===== CHAT BOT =====
    safe('chat-bot', function () {
      var fab = document.getElementById('chatFab');
      var box = document.getElementById('chatBox');
      var close = document.getElementById('chatClose');
      var body = document.getElementById('chatBody');
      var input = document.getElementById('chatInput');
      var send = document.getElementById('chatSend');
      var badge2 = fab && fab.querySelector('.chat-badge');
      function open() { box.classList.add('show'); if (badge2) badge2.style.display = 'none'; }
      function closeFn() { box.classList.remove('show'); }
      fab?.addEventListener('click', function () { box.classList.contains('show') ? closeFn() : open(); });
      close?.addEventListener('click', closeFn);

      function addMsg(text, from) {
        var quick = document.getElementById('chatQuick');
        if (quick) quick.remove();
        var m = document.createElement('div');
        m.className = 'chat-msg ' + from;
        m.innerHTML = '<div class="cm-avatar"><i class="fa-solid fa-' + (from === 'bot' ? 'user-doctor' : 'user') + '"></i></div><div class="cm-bubble">' + text + '</div>';
        body.appendChild(m);
        body.scrollTop = body.scrollHeight;
      }
      function typing() {
        var m = document.createElement('div');
        m.className = 'chat-msg bot typing';
        m.id = 'typingMsg';
        m.innerHTML = '<div class="cm-avatar"><i class="fa-solid fa-user-doctor"></i></div><div class="cm-bubble"><span></span><span></span><span></span></div>';
        body.appendChild(m);
        body.scrollTop = body.scrollHeight;
      }
      function stopTyping() { var t = document.getElementById('typingMsg'); if (t) t.remove(); }

      function answer(q) {
        var ql = q.toLowerCase();
        if (ql.match(/immun|cold|flu|sick/)) return "For immunity, I recommend our <b>Vitamin C 1000mg</b> (₹499) + <b>Zinc</b> combo. Take one daily for best results. 🍊";
        if (ql.match(/doctor|safe|certif|gmp/)) return "Yes ✅ Every Engix Care product is <b>doctor-formulated</b>, <b>GMP-certified</b>, and 3rd-party lab tested for purity.";
        if (ql.match(/result|when|feel|difference|work/)) return "Most customers feel a noticeable difference within <b>2–3 weeks</b> of consistent use. Vitamin D3 may take up to 6 weeks. 🌱";
        if (ql.match(/coupon|discount|code|engix25/)) return "Use code <b>ENGIX25</b> at checkout for 25% OFF your first order! You can also spin the wheel 🎡 for a chance at 40% OFF.";
        if (ql.match(/ship|deliver|order/)) return "Free shipping on orders ₹499+ · Delivered in <b>2–4 business days</b> across India. 🚚";
        if (ql.match(/refund|return|money.back/)) return "We offer a <b>30-day money-back guarantee</b>. If you're not fully satisfied, we'll refund you — no questions asked. 💯";
        if (ql.match(/energy|tired|fatigue/)) return "Try our <b>Energy & Focus Stack</b> (B-Complex + D3 + Magnesium) — designed for busy professionals. Take our 30-second quiz above to get your personalised match! ⚡";
        if (ql.match(/price|cost|how much/)) return "Our bestsellers start at ₹499. Bundle 2+ items for automatic discounts (10%/15%/20% OFF). Multivitamin: ₹599, Vitamin C: ₹499, D3+K2: ₹699, Omega 3: ₹899. 💰";
        if (ql.match(/hi|hello|hey|namaste/)) return "Hello! 👋 I'm Engix AI. How can I help you find the perfect supplement today?";
        return "Great question! I recommend taking our 30-second <b>Wellness Quiz</b> above — it'll give you a personalised stack based on your goals. Or ask me about immunity, energy, focus, or heart health! 💚";
      }

      function ask(q) {
        addMsg(q, 'user');
        typing();
        setTimeout(function () {
          stopTyping();
          addMsg(answer(q), 'bot');
        }, 900 + Math.random() * 400);
      }

      document.querySelectorAll('#chatQuick button').forEach(function (b) {
        b.addEventListener('click', function () { ask(this.dataset.q); });
      });

      send?.addEventListener('click', function () {
        var v = input.value.trim();
        if (v) { ask(v); input.value = ''; }
      });
      input?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') send.click();
      });
    });

    // ===== SPIN THE WHEEL =====
    safe('wheel', function () {
      var trig = document.getElementById('wheelTrigger');
      var modal = document.getElementById('wheelModal');
      var closeB = document.getElementById('wheelClose');
      var overlay = document.getElementById('wheelOverlay');
      var spinBtn = document.getElementById('wheelSpin');
      var wheel = document.getElementById('wheel');
      var result = document.getElementById('wheelResult');
      var prizes = ['10% OFF','Free Shipping','25% OFF','Try Again','40% OFF','Free Gift'];
      var winSegments = [4, 2, 5, 0, 1]; // weighted: 40%, 25%, gift, 10%, shipping
      var spun = false;

      function open() { modal.classList.add('show'); }
      function closeFn() { modal.classList.remove('show'); }
      trig?.addEventListener('click', open);
      closeB?.addEventListener('click', closeFn);
      overlay?.addEventListener('click', closeFn);

      spinBtn?.addEventListener('click', function () {
        if (spun) { closeFn(); return; }
        spun = true;
        var pick = winSegments[Math.floor(Math.random() * winSegments.length)];
        var segAngle = 60;
        var target = 360 * 6 + (360 - (pick * segAngle) - segAngle / 2);
        wheel.style.transform = 'rotate(' + target + 'deg)';
        spinBtn.disabled = true;
        spinBtn.innerHTML = '<i class="fa-solid fa-rotate fa-spin me-2"></i>Spinning…';
        setTimeout(function () {
          var prize = prizes[pick];
          result.textContent = '🎉 You won ' + prize + '!';
          result.classList.add('win');
          spinBtn.disabled = false;
          spinBtn.innerHTML = '<i class="fa-solid fa-copy me-2"></i>Claim ' + prize;
          showToast('Congrats! You won ' + prize);
        }, 4200);
      });
    });

    // ===== RANDOM SCROLL OFFERS =====
    safe('scroll-offers', function () {
      var el = document.getElementById('scrollOffer');
      if (!el) return;
      var soIcon = document.getElementById('soIcon');
      var soTag = document.getElementById('soTag');
      var soTitle = document.getElementById('soTitle');
      var soDesc = document.getElementById('soDesc');
      var soCodeWrap = document.getElementById('soCode');
      var soCodeText = document.getElementById('soCodeText');
      var soCta = document.getElementById('soCta');
      var soClose = document.getElementById('soClose');
      var soCopy = document.getElementById('soCopy');

      // Use actual offers from DB (embedded by blade as window.SCROLL_OFFERS).
      // Each entry: { title, desc, code, pct, type, discountText }
      var rawOffers = (window.SCROLL_OFFERS && window.SCROLL_OFFERS.length) ? window.SCROLL_OFFERS : [];

      // Map type → icon/tag/variant for visual styling
      function offerMeta(type, pct, discountText) {
        var label = discountText || (pct > 0 ? pct + '% OFF' : 'Special Offer');
        switch (type) {
          case 'first_order':
            return { variant: 'gift',     icon: 'fa-gift',               tag: 'First Order',    cta: 'Claim ' + label };
          case 'amount_based':
            return { variant: 'cashback', icon: 'fa-hand-holding-dollar', tag: 'Order Offer',    cta: 'Unlock ' + label };
          case 'flat':
            return { variant: 'flash',    icon: 'fa-bolt',               tag: 'Flat Discount',  cta: 'Claim ' + label };
          case 'percent':
            return { variant: 'bogo',     icon: 'fa-percent',            tag: 'Discount',       cta: 'Get ' + label };
          default:
            return { variant: 'flash',    icon: 'fa-fire',               tag: 'Special Offer',  cta: 'Claim Offer' };
        }
      }

      var offers = rawOffers.map(function(o) {
        var meta = offerMeta(o.type, o.pct, o.discountText);
        return {
          variant: meta.variant,
          icon:    meta.icon,
          tag:     meta.tag,
          title:   o.title,
          desc:    o.desc,
          code:    o.code || null,
          cta:     meta.cta,
        };
      });

      // No active DB offers → skip the popup entirely
      if (!offers.length) { return; }

      var shownCount = 0;
      var lastShown = 0;
      var lastVariant = null;
      var closed = false;
      var MAX = 4;
      var MIN_GAP = 15000; // 15 seconds between popups
      var TRIGGER_POINTS = [0.15, 0.35, 0.55, 0.75]; // percent of page height
      var triggered = new Set();

      function pickOffer() {
        var candidates = offers.filter(function (o) { return o.variant !== lastVariant; });
        var pick = candidates[Math.floor(Math.random() * candidates.length)];
        lastVariant = pick.variant;
        return pick;
      }

      function show(offer) {
        // Reset previous variant
        el.className = 'scroll-offer';
        el.classList.add('variant-' + offer.variant);
        soIcon.innerHTML = '<i class="fa-solid ' + offer.icon + '"></i>';
        soTag.textContent = offer.tag;
        soTitle.textContent = offer.title;
        soDesc.textContent = offer.desc;
        if (offer.code) {
          soCodeText.textContent = offer.code;
          soCodeWrap.classList.remove('hide');
        } else {
          soCodeWrap.classList.add('hide');
        }
        soCta.innerHTML = '<i class="fa-solid ' + offer.icon + ' me-1"></i>' + offer.cta;
        soCta.dataset.code = offer.code || '';
        // Force reflow so animation restarts
        el.classList.remove('show');
        void el.offsetWidth;
        el.classList.add('show');
        lastShown = Date.now();
        shownCount++;

        // Auto-hide after 9s if not clicked
        clearTimeout(show._t);
        show._t = setTimeout(function () { el.classList.remove('show'); }, 9000);
      }

      function checkScroll() {
        if (closed) return;
        if (shownCount >= MAX) return;
        if (Date.now() - lastShown < MIN_GAP) return;
        var docH = document.documentElement.scrollHeight - window.innerHeight;
        if (docH <= 0) return;
        var pct = window.scrollY / docH;
        for (var i = 0; i < TRIGGER_POINTS.length; i++) {
          var t = TRIGGER_POINTS[i];
          if (pct >= t && !triggered.has(i)) {
            triggered.add(i);
            show(pickOffer());
            break;
          }
        }
      }

      window.addEventListener('scroll', checkScroll, { passive: true });

      soClose.addEventListener('click', function () {
        el.classList.remove('show');
        clearTimeout(show._t);
      });

      soCopy.addEventListener('click', function () {
        var code = soCodeText.textContent;
        try {
          navigator.clipboard.writeText(code);
          showToast('Code ' + code + ' copied!');
        } catch (e) { showToast('Code: ' + code); }
      });

      soCta.addEventListener('click', function () {
        var code = this.dataset.code;
        if (code) {
          try { navigator.clipboard.writeText(code); } catch (e) {}
          if (typeof window.applyOfferCoupon === 'function') {
            window.applyOfferCoupon(code, soTitle ? soTitle.textContent : 'Flash Offer');
          } else {
            showToast('Coupon ' + code + ' applied at checkout!');
          }
        }
        el.classList.remove('show');
        clearTimeout(show._t);
        // Open cart drawer so user can add items with the coupon visible
        openCart();
      });
    });

    // Initial checks
    onScroll();
    renderCart();

    // ===== INGREDIENT TABS =====
    safe('ingredient-tabs', function () {
      var tabs = document.querySelectorAll('.ing-tab');
      var panels = document.querySelectorAll('.ing-panel');
      tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          var target = this.getAttribute('data-tab');
          tabs.forEach(function (t) { t.classList.remove('active'); });
          panels.forEach(function (p) { p.classList.remove('active'); });
          this.classList.add('active');
          var panel = document.getElementById(target);
          if (panel) panel.classList.add('active');
        });
      });
    });
  });
})();
