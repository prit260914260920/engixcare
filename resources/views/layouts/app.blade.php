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
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
</head>
<body>
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
      <div id="userCartData"
           data-user-cart="{{ json_encode(auth()->user()->cart_data ?: []) }}"
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
