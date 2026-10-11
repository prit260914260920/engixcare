<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="description" content="Engix Care - Premium Vitamins & Nutritional Supplements for Energy, Immunity and Overall Wellness. Doctor recommended, GMP certified, 100% authentic." />
  <meta name="keywords" content="Engix Care, vitamins, supplements, multivitamin, healthcare, immunity, omega 3, vitamin d3, wellness" />
  <meta name="author" content="Engix Care" />
  <meta property="og:title" content="Engix Care – Premium Vitamins & Supplements" />
  <meta property="og:description" content="Your Daily Health Starts With Engix Care. Doctor recommended supplements for immunity, energy & wellness." />
  <meta property="og:type" content="website" />
  <title>Engix Care | Premium Vitamins & Nutritional Supplements</title>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Ccircle cx='32' cy='32' r='30' fill='%230A84FF'/%3E%3Cpath d='M20 32h24M32 20v24' stroke='white' stroke-width='6' stroke-linecap='round'/%3E%3C/svg%3E" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

  <!-- AOS Scroll Animation -->
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

  <!-- Google Fonts: Manrope for headings, Inter for body -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />

  <!-- Custom Styles -->
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}" />
</head>
<body>
    {{-- ===== ANNOUNCEMENT BAR ===== --}}
    @php
        $announcementSettings = \App\Models\PromotionSetting::current();
    @endphp
    @if($announcementSettings->announcement_bar_enabled && $announcementSettings->announcement_bar_text && View::hasSection('show_announcement_bar'))
    <div id="announcement-bar"
         style="background-color:{{ $announcementSettings->announcement_bar_bg_color }};color:{{ $announcementSettings->announcement_bar_text_color }};"
         role="banner"
         aria-label="Announcement">
        <div class="announcement-bar-inner">
            @if($announcementSettings->announcement_bar_link)
                <a href="{{ $announcementSettings->announcement_bar_link }}"
                   class="announcement-bar-text announcement-bar-link"
                   style="color:{{ $announcementSettings->announcement_bar_text_color }};">
                    {{ $announcementSettings->announcement_bar_text }}
                </a>
            @else
                <span class="announcement-bar-text">{{ $announcementSettings->announcement_bar_text }}</span>
            @endif
        </div>
        @if($announcementSettings->announcement_bar_dismissible)
        <button class="announcement-bar-close"
                id="announcementBarClose"
                aria-label="Dismiss announcement"
                style="color:{{ $announcementSettings->announcement_bar_text_color }};">
            <i class="fa-solid fa-xmark"></i>
        </button>
        @endif
    </div>
    <style>
        /* ── Announcement Bar ─────────────────────────────────────── */
        #announcement-bar {
            position: sticky;
            top: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 8px 48px;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            font-weight: 500;
            letter-spacing: 0.01em;
            line-height: 1.4;
            text-align: center;
            z-index: 1200;   /* above navbar (Bootstrap fixed-top = 1030) */
        }
        #announcement-bar.hidden {
            display: none !important;
        }
        .announcement-bar-inner { flex: 1; text-align: center; }
        .announcement-bar-text  { font-size: 13.5px; }
        .announcement-bar-link  {
            text-decoration: underline;
            text-underline-offset: 2px;
        }
        .announcement-bar-link:hover { opacity: 0.85; }
        .announcement-bar-close {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px 6px;
            opacity: 0.75;
            font-size: 14px;
            line-height: 1;
            transition: opacity 0.15s;
        }
        .announcement-bar-close:hover { opacity: 1; }

        /* ── Push Bootstrap fixed-top navbar below the bar ───────── */
        .navbar.fixed-top,
        nav.fixed-top {
            top: var(--announcement-bar-h, 0px);
            transition: top 0.2s ease;
        }
    </style>
    <script>
        (function () {
            var bar     = document.getElementById('announcement-bar');
            var closeBtn= document.getElementById('announcementBarClose');

            /* Helper — measure bar height and write CSS variable on <html> */
            function setNavOffset() {
                var h = (bar && !bar.classList.contains('hidden'))
                        ? bar.getBoundingClientRect().height
                        : 0;
                document.documentElement.style.setProperty('--announcement-bar-h', h + 'px');

            }

            /* Restore dismissed state before first paint */
            if (sessionStorage.getItem('announcementBarDismissed') === '1') {
                if (bar) bar.classList.add('hidden');
            }

            /* Set offset as early as possible */
            setNavOffset();

            /* Re-measure after fonts/images may have reflowed */
            document.addEventListener('DOMContentLoaded', setNavOffset);
            window.addEventListener('load', setNavOffset);
            window.addEventListener('resize', setNavOffset);

            /* Dismiss handler */
            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    bar.classList.add('hidden');
                    sessionStorage.setItem('announcementBarDismissed', '1');
                    setNavOffset(); /* collapse the gap immediately */
                });
            }
        })();
    </script>
    @endif
    <script>
        (function () {
            function setMainNavbarHeight() {
                var navbar = document.getElementById('mainNav');
                if (navbar) {
                    document.documentElement.style.setProperty('--main-navbar-h', navbar.getBoundingClientRect().height + 'px');
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                var navbar = document.getElementById('mainNav');
                if (navbar && 'ResizeObserver' in window) {
                    new ResizeObserver(setMainNavbarHeight).observe(navbar);
                }
                setMainNavbarHeight();
            });
            window.addEventListener('resize', setMainNavbarHeight);
        })();
    </script>
    @php
      $appUser = [
        'authenticated' => auth()->check(),
        'cart'          => auth()->check() ? (auth()->user()->cart_data ?: []) : [],
        'name'          => auth()->check() ? auth()->user()->name : null,
        'checkoutUrl'   => route('checkout.show'),
        'loginUrl'      => route('login'),
      ];
    @endphp
    <script>
      window.APP_USER = @json($appUser);
    </script>
    {{-- data-user-cart attribute used by JS to detect auth state and load persisted cart --}}
    @auth
      @php
        $userCartRaw = auth()->user()->cart_data ?: [];
        // Repair product images using actual storage URLs so the cart drawer shows correct images
        $productIdsInCart = array_filter(array_column($userCartRaw, 'product_id'));
        if (!empty($productIdsInCart)) {
            $cartProducts = \App\Models\Product::whereIn('id', $productIdsInCart)->get()->keyBy('id');
            $userCartRaw = array_map(function ($item) use ($cartProducts) {
                $pid = $item['product_id'] ?? null;
                if ($pid && isset($cartProducts[$pid])) {
                    $imgs = $cartProducts[$pid]->image ?? [];
                    if (!empty($imgs[0])) {
                        $item['img'] = \Illuminate\Support\Facades\Storage::disk('public')->url($imgs[0]);
                    }
                }
                return $item;
            }, $userCartRaw);
        }
      @endphp
      <div id="userCartData"
           data-user-cart="{{ json_encode($userCartRaw) }}"
           style="display:none"></div>
    @endauth
    @yield('content')

  <!-- ============ CART DRAWER ============ -->
  <div id="cartOverlay" class="cart-overlay" data-testid="cart-overlay"></div>
  <aside id="cartDrawer" class="cart-drawer" data-testid="cart-drawer">
    <div class="cart-head">
      <h5><i class="fa-solid fa-bag-shopping"></i> Your Cart <span id="cartHeadCount">(0)</span></h5>
      <button class="cart-close" id="cartClose" aria-label="Close" data-testid="cart-close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="cart-body">
      <div id="cartEmpty" class="cart-empty" data-testid="cart-empty">
        <i class="fa-solid fa-cart-shopping"></i>
        <h6>Your cart is empty</h6>
        <p>Add supplements to get started with your wellness journey.</p>
        <a href="{{ url('/') }}#products" class="btn btn-primary-engix" id="cartShopBtn"><i class="fa-solid fa-arrow-right me-1"></i>Shop Bestsellers</a>
      </div>
      <ul id="cartItems" class="cart-items" data-testid="cart-items"></ul>
    </div>
    <div class="cart-foot" id="cartFoot">
      <div class="cart-savings" id="cartSavings"></div>

      {{-- Coupon Input Row --}}
      <div class="cart-coupon-wrap" id="cartCouponWrap">
        <div class="cart-coupon-row" id="cartCouponRow">
          <div class="ccp-icon"><i class="fa-solid fa-tag"></i></div>
          <input type="text" id="cartCouponInput" class="ccp-input" placeholder="Enter coupon code" autocomplete="off" />
          <button id="cartCouponApply" class="ccp-apply-btn">Apply</button>
        </div>
        <div id="cartCouponMsg" class="ccp-msg" style="display:none;"></div>
        <div class="cart-coupon-applied" id="cartCouponApplied" style="display:none;">
          <div class="cca-left">
            <i class="fa-solid fa-circle-check cca-icon"></i>
            <div>
              <span class="cca-code" id="cartCouponAppliedCo de"></span>
              <span class="cca-label">Coupon applied</span>
            </div>
          </div>
          <button class="cca-remove" id="cartCouponRemove" title="Remove coupon"><i class="fa-solid fa-xmark"></i></button>
        </div>
      </div>

      <div class="cart-price-breakdown" id="cartPriceBreakdown">
        <div class="cpb-row"><span>Subtotal</span><span id="cartSubtotal">₹0</span></div>
        <div class="cpb-row cpb-discount" id="cpbDiscountRow" style="display:none;">
          <span id="cpbDiscountLabel">Coupon discount</span>
          <span id="cpbDiscountAmt" class="cpb-green">– ₹0</span>
        </div>
        <div class="cpb-row cpb-total"><span>Total</span><b id="cartTotal">₹0</b></div>
      </div>

      <button class="btn btn-primary-engix w-100 btn-lg" data-testid="cart-checkout"><i class="fa-solid fa-lock me-2"></i>Secure Checkout</button>
      <p class="cart-trust"><i class="fa-solid fa-shield-halved"></i> 30-day money-back guarantee</p>
    </div>
  </aside>

  <!-- Fly-to-cart animation element -->
  <div id="flyItem" class="fly-item"><i class="fa-solid fa-capsules"></i></div>

  <!-- Toast notification -->
  <div id="toast" class="engix-toast" data-testid="toast"><i class="fa-solid fa-circle-check"></i> <span id="toastText">Added to cart</span></div>
    <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AOS -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <!-- Custom JS -->
  <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
