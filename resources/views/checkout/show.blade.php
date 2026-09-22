@extends('layouts.app')

@section('content')

{{-- ============ HEADER ============ --}}
<nav class="navbar navbar-expand-lg fixed-top navbar-engix" id="mainNav" data-testid="main-nav">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" data-testid="brand-logo">
      <img src="{{ asset('asset/logo.png') }}" alt="engix CARE" class="navbar-logo" />
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent"
            aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#products">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#benefits">Benefits</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#ingredients">Ingredients</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#testimonials">Testimonials</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#faq">FAQ</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#contact">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('offers.index') }}"><i class="fa-solid fa-tag me-1"></i>Offers</a></li>
        @auth
          <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}"><i class="fa-solid fa-box me-1"></i>My Orders</a></li>
        @endauth
      </ul>
      <div class="d-flex gap-2 nav-cta">
        <a href="#" class="btn btn-primary-engix" id="cartOpen">
          <i class="fa-solid fa-bag-shopping me-1"></i>Cart
          <span class="cart-badge" id="cartBadge" data-testid="cart-badge">0</span>
        </a>
        @auth
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-ghost-engix">
              <i class="fa-solid fa-right-from-bracket me-1"></i>Logout
            </button>
          </form>
        @else
          <a href="{{ route('login') }}" class="btn btn-ghost-engix">
            <i class="fa-regular fa-user me-1"></i>Login
          </a>
        @endauth
      </div>
    </div>
  </div>
</nav>

<div class="checkout-page">
  <div class="container" style="max-width:1100px; padding-top:110px; padding-bottom:80px;">

    {{-- Back link --}}
    <a href="{{ url('/') }}" class="checkout-back">
      <i class="fa-solid fa-arrow-left"></i> Continue Shopping
    </a>

    <h1 class="checkout-title">Secure Checkout</h1>
    <p class="checkout-sub">Fill in your details below. We'll ship your order right to your door.</p>

    @if($errors->any())
      <div class="alert alert-danger mb-4">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
      @csrf
      <div class="row g-4 align-items-start">

        {{-- ── LEFT: Form ───────────────────────────────────────────────── --}}
        <div class="col-lg-7">

          {{-- Contact --}}
          <div class="co-card">
            <div class="co-card-head">
              <span class="co-step">1</span>
              <h2>Contact Information</h2>
            </div>
            <div class="row g-3">
              <div class="col-12">
                <label class="co-label" for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="co-input @error('name') is-invalid @enderror"
                       value="{{ old('name', $user->name) }}" placeholder="Ananya Sharma" required>
                @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
              <div class="col-sm-6">
                <label class="co-label" for="email">Email *</label>
                <input type="email" id="email" name="email" class="co-input @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" placeholder="you@example.com" required>
                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
              <div class="col-sm-6">
                <label class="co-label" for="phone">Mobile Number *</label>
                <input type="tel" id="phone" name="phone" class="co-input @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}" placeholder="9876543210" required maxlength="20">
                @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>

          {{-- Shipping Address --}}
          <div class="co-card">
            <div class="co-card-head">
              <span class="co-step">2</span>
              <h2>Shipping Address</h2>
            </div>
            <div class="row g-3">
              <div class="col-12">
                <label class="co-label" for="address_line1">Address Line 1 *</label>
                <input type="text" id="address_line1" name="address_line1"
                       class="co-input @error('address_line1') is-invalid @enderror"
                       value="{{ old('address_line1') }}" placeholder="Flat / House No., Street" required>
                @error('address_line1')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
              <div class="col-12">
                <label class="co-label" for="address_line2">Address Line 2 <span class="co-optional">(optional)</span></label>
                <input type="text" id="address_line2" name="address_line2"
                       class="co-input @error('address_line2') is-invalid @enderror"
                       value="{{ old('address_line2') }}" placeholder="Landmark, Area">
              </div>
              <div class="col-sm-5">
                <label class="co-label" for="city">City *</label>
                <input type="text" id="city" name="city" class="co-input @error('city') is-invalid @enderror"
                       value="{{ old('city') }}" placeholder="Mumbai" required>
                @error('city')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
              <div class="col-sm-4">
                <label class="co-label" for="state">State *</label>
                <select id="state" name="state" class="co-input co-select @error('state') is-invalid @enderror" required>
                  <option value="">Select state</option>
                  @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi','Jammu & Kashmir','Ladakh'] as $s)
                    <option value="{{ $s }}" {{ old('state') === $s ? 'selected' : '' }}>{{ $s }}</option>
                  @endforeach
                </select>
                @error('state')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
              <div class="col-sm-3">
                <label class="co-label" for="pincode">Pincode *</label>
                <input type="text" id="pincode" name="pincode" class="co-input @error('pincode') is-invalid @enderror"
                       value="{{ old('pincode') }}" placeholder="400001" maxlength="6" pattern="\d{6}" required>
                @error('pincode')<span class="invalid-feedback">{{ $message }}</span>@enderror
              </div>
            </div>
          </div>

          {{-- Coupon Code --}}
          <div class="co-card">
            <div class="co-card-head">
              <span class="co-step">3</span>
              <h2>Coupon Code</h2>
            </div>
            <div class="row g-3">
              <div class="col-12">
                <label class="co-label" for="coupon_code">Coupon Code</label>
                <div class="co-coupon-wrap">
                  <input type="text" id="coupon_code" name="coupon_code"
                         class="co-input @error('coupon_code') is-invalid @enderror"
                         value="{{ old('coupon_code', request()->query('coupon_code')) }}"
                         placeholder="Enter coupon code if you have one"
                         autocomplete="off" style="text-transform:uppercase;">
                  <button type="button" id="verifyCouponBtn" class="btn co-coupon-verify-btn">
                    Verify
                  </button>
                </div>
                @error('coupon_code')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                <div id="coupon-feedback" class="co-coupon-feedback" style="display:none;"></div>
              </div>
            </div>
          </div>

          {{-- Payment --}}
          <div class="co-card">
            <div class="co-card-head">
              <span class="co-step">4</span>
              <h2>Payment Method</h2>
            </div>
            <div class="co-payment-options">
              <label class="co-pay-option {{ old('payment_method', 'cod') === 'cod' ? 'selected' : '' }}" id="payLabelCod">
                <input type="radio" name="payment_method" value="cod"
                       {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }} class="co-pay-radio">
                <span class="co-pay-icon"><i class="fa-solid fa-truck"></i></span>
                <div>
                  <strong>Cash on Delivery</strong>
                  <small>Pay when your order arrives</small>
                </div>
              </label>
              <label class="co-pay-option {{ old('payment_method') === 'online' ? 'selected' : '' }}" id="payLabelOnline">
                <input type="radio" name="payment_method" value="online"
                       {{ old('payment_method') === 'online' ? 'checked' : '' }} class="co-pay-radio">
                <span class="co-pay-icon"><i class="fa-solid fa-credit-card"></i></span>
                <div>
                  <strong>Pay Online</strong>
                  <small>UPI / Card / Net Banking</small>
                </div>
              </label>
            </div>
            {{-- Online payment note --}}
            <div id="onlineNote" class="co-online-note" style="{{ old('payment_method') === 'online' ? '' : 'display:none' }}">
              <i class="fa-solid fa-shield-halved"></i>
              You'll be taken to the Razorpay secure payment screen to pay via UPI, Card, or Net Banking.
            </div>
          </div>

          {{-- Notes --}}
          <div class="co-card">
            <label class="co-label" for="notes">Order Notes <span class="co-optional">(optional)</span></label>
            <textarea id="notes" name="notes" class="co-input co-textarea" rows="3"
                      placeholder="Special instructions for delivery…">{{ old('notes') }}</textarea>
          </div>

        </div>{{-- /col-lg-7 --}}

        {{-- ── RIGHT: Order summary ────────────────────────────────────── --}}
        <div class="col-lg-5">
          <div class="co-summary sticky-top" style="top:100px; z-index:1;">
            <h3 class="co-summary-title"><i class="fa-solid fa-bag-shopping"></i> Order Summary</h3>

            <ul class="co-summary-items" id="co-summary-items">
              @foreach($cartItems as $item)
                <li class="co-summary-item">
                  <img src="{{ $item['img'] ?? '' }}" alt="{{ $item['name'] }}" class="co-item-img"
                       onerror="this.src='{{ asset('asset/210A0226.png') }}'">
                  <div class="co-item-info">
                    <span class="co-item-name">{{ $item['name'] }}</span>
                    <span class="co-item-qty">Qty: {{ $item['qty'] }}</span>
                  </div>
                  <span class="co-item-price">₹{{ number_format($item['price'] * $item['qty'], 0) }}</span>
                </li>
              @endforeach
            </ul>

            <div class="co-summary-totals">
              <div class="co-total-row">
                <span>Subtotal</span>
                <span id="co-subtotal">₹{{ number_format($subtotal, 0) }}</span>
              </div>
              <div class="co-total-row co-discount-row" id="co-discount-row" {{ $discount > 0 ? '' : 'style=display:none' }}>
                <span><i class="fa-solid fa-tag"></i> <span id="co-discount-label">Coupon Discount</span></span>
                <span id="co-discount">− ₹{{ number_format($discount, 0) }}</span>
              </div>
              <div class="co-total-row co-gst-row">
                <span><i class="fa-solid fa-receipt"></i> GST (18%)</span>
                <span id="co-gst">+ ₹{{ number_format($gst, 0) }}</span>
              </div>
              <div class="co-total-row co-total-grand">
                <span>Total</span>
                <span id="co-grand">₹{{ number_format($total, 0) }}</span>
              </div>
            </div>

            <div class="co-savings-badge" id="co-savings-badge" {{ $discount > 0 ? '' : 'style=display:none' }}>
              🎉 You're saving ₹{{ number_format($discount, 0) }} on this order!
            </div>

            <button type="submit" class="btn btn-primary-engix w-100 btn-lg co-place-btn" id="placeOrderBtn">
              <i class="fa-solid fa-lock me-2"></i>Place Order
            </button>
            <p class="co-trust"><i class="fa-solid fa-shield-halved"></i> Secured & encrypted &nbsp;·&nbsp; 30-day returns</p>
          </div>
        </div>{{-- /col-lg-5 --}}

      </div>{{-- /row --}}
    </form>

  </div>
</div>

<script>
  // On checkout page: hide cart drawer's checkout button and show a "you're here" note
  document.addEventListener('DOMContentLoaded', function () {
    var checkoutBtn = document.querySelector('[data-testid="cart-checkout"]');
    if (checkoutBtn) {
      checkoutBtn.style.display = 'none';
    }
    var cartFoot = document.getElementById('cartFoot');
    if (cartFoot) {
      var note = document.createElement('p');
      note.className = 'text-center text-muted small mt-2 mb-0';
      note.innerHTML = '<i class="fa-solid fa-circle-check text-success me-1"></i>You are already on the checkout page.';
      cartFoot.appendChild(note);
    }
  });
</script>

<script>
  // ── Checkout Summary Live Update ──────────────────────────────────────────

  // Tracks an applied coupon so cart-update recalculations respect it
  var _appliedCoupon = {
    code: '{{ addslashes(request()->query("coupon_code", "")) }}',
    discount: {{ $discount > 0 && request()->query('coupon_code') ? $discount : 0 }},
    label: '{{ request()->query("coupon_code") ? "Coupon Discount" : "" }}'
  };

  function fmt(n) {
    return '₹' + Number(n).toLocaleString('en-IN');
  }

  function updateSummaryTotals(subtotal, discount, label) {
    var discountRow  = document.getElementById('co-discount-row');
    var discountEl   = document.getElementById('co-discount');
    var discountLbl  = document.getElementById('co-discount-label');
    var grandEl      = document.getElementById('co-grand');
    var subtotalEl   = document.getElementById('co-subtotal');
    var gstEl        = document.getElementById('co-gst');
    var savingsBadge = document.getElementById('co-savings-badge');

    var amountAfterDiscount = subtotal - discount;
    var gst                 = Math.round(amountAfterDiscount * 0.18);
    var total               = amountAfterDiscount + gst;

    if (subtotalEl)  subtotalEl.textContent = fmt(subtotal);
    if (gstEl)       gstEl.textContent      = '+ ' + fmt(gst);
    if (grandEl)     grandEl.textContent    = fmt(total);

    if (discount > 0) {
      if (discountRow)  discountRow.style.display = '';
      if (discountEl)   discountEl.textContent    = '− ' + fmt(discount);
      if (discountLbl)  discountLbl.textContent   = label || 'Discount';
      if (savingsBadge) {
        savingsBadge.textContent  = '🎉 You\'re saving ' + fmt(discount) + ' on this order!';
        savingsBadge.style.display = '';
      }
    } else {
      if (discountRow)  discountRow.style.display  = 'none';
      if (savingsBadge) savingsBadge.style.display = 'none';
    }
  }

  function renderCheckoutSummary(cart) {
    var itemsList = document.getElementById('co-summary-items');
    if (!itemsList) return;

    var subtotal = cart.reduce(function(s, it) { return s + it.price * it.qty; }, 0);
    var count    = cart.reduce(function(s, it) { return s + it.qty; }, 0);

    // Re-render items
    itemsList.innerHTML = cart.map(function(it) {
      return '<li class="co-summary-item">' +
        '<img src="' + (it.img || '') + '" alt="' + it.name + '" class="co-item-img" ' +
        'onerror="this.src=\'/asset/210A0226.png\'">' +
        '<div class="co-item-info">' +
          '<span class="co-item-name">' + it.name + '</span>' +
          '<span class="co-item-qty">Qty: ' + it.qty + '</span>' +
        '</div>' +
        '<span class="co-item-price">' + fmt(it.price * it.qty) + '</span>' +
      '</li>';
    }).join('');

    // Use coupon discount if one is applied, else no discount
    var discount, label;
    if (_appliedCoupon.code) {
      discount = _appliedCoupon.discount;
      label    = _appliedCoupon.label || 'Coupon Discount';
    } else {
      discount = 0;
      label    = '';
    }

    updateSummaryTotals(subtotal, discount, label);
  }

  // Listen for cart changes dispatched by script.js
  document.addEventListener('cartUpdated', function(e) {
    renderCheckoutSummary(e.detail || []);
  });

  // ── Coupon Verification ───────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    var verifyBtn    = document.getElementById('verifyCouponBtn');
    var couponInput  = document.getElementById('coupon_code');
    var feedbackEl   = document.getElementById('coupon-feedback');

    if (!verifyBtn || !couponInput) return;

    // Auto-uppercase while typing
    couponInput.addEventListener('input', function () {
      var pos = this.selectionStart;
      this.value = this.value.toUpperCase();
      this.setSelectionRange(pos, pos);
      // Clear feedback when user edits the field
      hideFeedback();
      if (!this.value.trim()) {
        clearAppliedCoupon();
      }
    });

    verifyBtn.addEventListener('click', function () {
      var code = couponInput.value.trim().toUpperCase();
      if (!code) {
        showFeedback('Please enter a coupon code.', false);
        return;
      }

      // Calculate current subtotal from the displayed value
      var subtotalEl = document.getElementById('co-subtotal');
      var subtotal   = subtotalEl
        ? parseFloat(subtotalEl.textContent.replace(/[₹,]/g, '')) || 0
        : 0;

      verifyBtn.disabled    = true;
      verifyBtn.textContent = '…';

      fetch('{{ route("coupon.validate") }}', {
        method : 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN' : '{{ csrf_token() }}',
          'Accept'       : 'application/json',
        },
        body: JSON.stringify({ coupon_code: code, subtotal: subtotal }),
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.valid) {
          _appliedCoupon = { code: data.code, discount: data.discount, label: 'Coupon Discount' };
          couponInput.value = data.code;
          showFeedback(data.message, true);
          updateSummaryTotals(subtotal, data.discount, 'Coupon Discount');
        } else {
          clearAppliedCoupon();
          showFeedback(data.message, false);
          updateSummaryTotals(subtotal, 0, '');
        }
      })
      .catch(function() {
        showFeedback('Something went wrong. Please try again.', false);
      })
      .finally(function() {
        verifyBtn.disabled    = false;
        verifyBtn.textContent = 'Verify';
      });
    });

    // Also trigger verify on Enter key inside the coupon input
    couponInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        verifyBtn.click();
      }
    });

    function showFeedback(msg, success) {
      feedbackEl.textContent    = msg;
      feedbackEl.className      = 'co-coupon-feedback ' + (success ? 'co-coupon-success' : 'co-coupon-error');
      feedbackEl.style.display  = '';
    }

    function hideFeedback() {
      feedbackEl.style.display = 'none';
      feedbackEl.textContent   = '';
    }

    function clearAppliedCoupon() {
      _appliedCoupon = { code: '', discount: 0, label: '' };
    }

    // If a coupon_code was pre-filled via URL query param, auto-verify it
    @if(request()->query('coupon_code'))
    (function () {
      var preCode = couponInput.value.trim();
      if (preCode) {
        verifyBtn.click();
      }
    })();
    @endif
  });
</script>

<script>
  // Highlight selected payment option
  document.querySelectorAll('.co-pay-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
      document.querySelectorAll('.co-pay-option').forEach(function(l) { l.classList.remove('selected'); });
      this.closest('.co-pay-option').classList.add('selected');
      var onlineNote = document.getElementById('onlineNote');
      if (onlineNote) onlineNote.style.display = this.value === 'online' ? '' : 'none';
    });
  });

  // ── Form submit: COD goes normally; Online opens Razorpay modal ──────────
  document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    var method = document.querySelector('input[name="payment_method"]:checked');
    if (!method || method.value !== 'online') {
      // COD — normal form submit, just disable button to prevent double-click
      var btn = document.getElementById('placeOrderBtn');
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Placing Order…';
      return; // let the form POST through
    }

    // Online — prevent default POST, use AJAX + Razorpay modal instead
    e.preventDefault();

    var btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Initiating Payment…';

    var form    = document.getElementById('checkoutForm');
    var formData = new FormData(form);

    fetch('{{ route("checkout.store") }}', {
      method : 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept'      : 'application/json',
      },
      body: formData,
    })
    .then(function(r) {
      if (!r.ok) return r.json().then(function(d) { throw new Error(d.message || 'Server error'); });
      return r.json();
    })
    .then(function(data) {
      if (!data.success) throw new Error(data.message || 'Could not initiate payment.');

      // Open Razorpay checkout modal
      var options = {
        key        : data.key_id,
        amount     : data.amount,
        currency   : data.currency,
        name       : 'engix CARE',
        description: data.description,
        order_id   : data.razorpay_order_id,
        prefill: {
          name   : data.name,
          email  : data.email,
          contact: data.phone,
        },
        theme: { color: '#2d6a4f' },

        handler: function(response) {
          // Payment succeeded — verify on server
          btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Verifying Payment…';

          fetch('{{ route("checkout.razorpay.verify") }}', {
            method : 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept'      : 'application/json',
            },
            body: JSON.stringify({
              razorpay_order_id  : response.razorpay_order_id,
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_signature : response.razorpay_signature,
              order_id           : data.order_id,
            }),
          })
          .then(function(r) { return r.json(); })
          .then(function(v) {
            if (v.success) {
              window.location.href = v.confirmation_url;
            } else {
              showPaymentError(v.message || 'Payment verification failed. Please contact support.');
              btn.disabled = false;
              btn.innerHTML = '<i class="fa-solid fa-lock me-2"></i>Place Order';
            }
          })
          .catch(function() {
            showPaymentError('Verification request failed. Please contact support with your payment ID: ' + response.razorpay_payment_id);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-lock me-2"></i>Place Order';
          });
        },

        modal: {
          ondismiss: function() {
            // User closed the modal — mark order as failed
            fetch('{{ route("checkout.razorpay.failed") }}', {
              method : 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept'      : 'application/json',
              },
              body: JSON.stringify({ order_id: data.order_id, reason: 'modal_dismissed' }),
            });
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-lock me-2"></i>Place Order';
            showPaymentError('Payment was cancelled. You can try again.');
          },
        },
      };

      var rzp = new Razorpay(options);

      rzp.on('payment.failed', function(response) {
        fetch('{{ route("checkout.razorpay.failed") }}', {
          method : 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept'      : 'application/json',
          },
          body: JSON.stringify({ order_id: data.order_id, reason: response.error.description }),
        });
        showPaymentError('Payment failed: ' + response.error.description + '. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-lock me-2"></i>Place Order';
      });

      rzp.open();
    })
    .catch(function(err) {
      showPaymentError(err.message || 'Something went wrong. Please try again.');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-lock me-2"></i>Place Order';
    });
  });

  function showPaymentError(msg) {
    var el = document.getElementById('razorpay-error');
    if (!el) {
      el = document.createElement('div');
      el.id        = 'razorpay-error';
      el.className = 'alert alert-danger mt-3';
      var form = document.getElementById('checkoutForm');
      form.parentNode.insertBefore(el, form);
    }
    el.textContent  = msg;
    el.style.display = '';
    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
</script>

{{-- Razorpay checkout.js (must load before the form submit handler runs) --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endsection
