@extends('layouts.app')

@section('content')
<!-- Loading Screen -->
<div id="preloader" data-testid="preloader">
  <div class="loader-inner">
    <img src="{{ asset('asset/logo.png') }}" alt="Engix Care" class="loader-logo" />
  </div>
</div>

<!-- User Cart Data (for persistence) -->
@auth
  <div data-user-cart='@json(auth()->user()->cart_data ?? [])' style="display:none;"></div>
@endauth

<!-- ============ HEADER ============ -->
<nav class="navbar navbar-expand-lg fixed-top navbar-engix" id="mainNav" data-testid="main-nav">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="#home" data-testid="brand-logo">
      <img src="{{ asset('asset/logo.png') }}" alt="engix CARE" class="navbar-logo" />
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation" data-testid="nav-toggle">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#home" data-testid="nav-home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#products" data-testid="nav-products">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#benefits" data-testid="nav-benefits">Benefits</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#ingredients" data-testid="nav-ingredients">Ingredients</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#testimonials" data-testid="nav-testimonials">Testimonials</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#faq" data-testid="nav-faq">FAQ</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#contact" data-testid="nav-contact">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('offers.index') }}" data-testid="nav-offers"><i class="fa-solid fa-tag me-1"></i>Offers</a></li>
        @auth
          <li class="nav-item"><a class="nav-link active" href="{{ route('orders.index') }}" data-testid="nav-orders"><i class="fa-solid fa-box me-1"></i>My Orders</a></li>
        @endauth
      </ul>
      <div class="d-flex gap-2 nav-cta">
        <a href="#" class="btn btn-primary-engix" id="cartOpen" data-testid="nav-shop-now"><i class="fa-solid fa-bag-shopping me-1"></i>Cart <span class="cart-badge" id="cartBadge" data-testid="cart-badge">0</span></a>
        @auth
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-ghost-engix"><i class="fa-solid fa-right-from-bracket me-1"></i>Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="btn btn-ghost-engix" data-testid="nav-login"><i class="fa-regular fa-user me-1"></i>Login</a>
        @endauth
      </div>
    </div>
  </div>
</nav>

<!-- Spacer for fixed header -->
<div style="height: 100px;"></div>

<!-- ============ ORDER DETAIL PAGE ============ -->
<div class="checkout-page">
  <div class="container" style="max-width: 780px; padding-bottom: 80px;">

    <!-- Back Link -->
    <div class="mb-4">
      <a href="{{ route('orders.index') }}" class="text-decoration-none text-primary" data-testid="back-to-orders">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Orders
      </a>
    </div>

    {{-- Order Header --}}
    <div class="co-confirm-hero">
      <div class="co-confirm-icon" style="color: var(--primary); font-size: 3rem;">
        <i class="fa-solid fa-box-open"></i>
      </div>
      <h1 class="co-confirm-title">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
      <p class="co-confirm-sub">
        Ordered on <strong>{{ $order->created_at->format('F d, Y \a\t h:i A') }}</strong>
      </p>
    </div>

    {{-- Order details card --}}
    <div class="co-card mt-4">
      <div class="co-card-head" style="border-bottom:1px solid var(--line); padding-bottom:14px; margin-bottom:20px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="font-size:1.1rem; margin: 0;">Order Details</h2>
        <span class="co-status-badge co-status-{{ strtolower($order->status) }}" data-testid="order-status">
          {{ ucfirst($order->status) }}
        </span>
      </div>

      {{-- Items --}}
      <ul class="co-summary-items mb-4">
        @foreach($order->items as $item)
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

      {{-- Totals --}}
      <div class="co-summary-totals mb-4" data-testid="order-totals">
        <div class="co-total-row">
          <span>Subtotal</span>
          <span>₹{{ number_format($order->subtotal, 0) }}</span>
        </div>
        @if($order->discount > 0)
        <div class="co-total-row co-discount-row">
          <span><i class="fa-solid fa-tag"></i> Coupon Discount</span>
          <span>− ₹{{ number_format($order->discount, 0) }}</span>
        </div>
        @endif
        <div class="co-total-row co-total-grand">
          <span>Total Paid</span>
          <span>₹{{ number_format($order->total, 0) }}</span>
        </div>
      </div>

      {{-- Shipping & payment --}}
      <div class="row g-3 mb-4">
        <div class="col-sm-6">
          <div class="co-info-block">
            <h6><i class="fa-solid fa-location-dot me-1"></i> Shipping To</h6>
            <p data-testid="order-address">
              {{ $order->address_line1 }}<br>
              @if($order->address_line2) {{ $order->address_line2 }}<br> @endif
              {{ $order->city }}, {{ $order->state }} – {{ $order->pincode }}
            </p>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="co-info-block">
            <h6><i class="fa-solid fa-credit-card me-1"></i> Payment</h6>
            <p>
              {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment' }}<br>
              <span class="co-pay-status co-pay-{{ strtolower($order->payment_status) }}" data-testid="order-payment-status">
                {{ ucfirst($order->payment_status) }}
              </span>
            </p>
          </div>
        </div>
      </div>

      {{-- Contact Info --}}
      <div class="row g-3 mb-4">
        <div class="col-sm-6">
          <div class="co-info-block">
            <h6><i class="fa-solid fa-phone me-1"></i> Contact</h6>
            <p>
              <strong>{{ $order->name }}</strong><br>
              {{ $order->email }}<br>
              {{ $order->phone }}
            </p>
          </div>
        </div>
        <div class="col-sm-6">
          <div class="co-info-block">
            <h6><i class="fa-solid fa-box me-1"></i> Shipment</h6>
            <p>
              <strong>Method:</strong> Standard Delivery<br>
              <strong>Estimated:</strong> 5-7 Business Days
            </p>
          </div>
        </div>
      </div>

      @if($order->notes)
      <div class="co-info-block">
        <h6><i class="fa-solid fa-note-sticky me-1"></i> Your Notes</h6>
        <p>{{ $order->notes }}</p>
      </div>
      @endif
    </div>

    <!-- Action Buttons -->
    <div class="text-center mt-5">
      <a href="{{ route('orders.index') }}" class="btn btn-outline-engix me-2">
        <i class="fa-solid fa-arrow-left me-1"></i>Back to Orders
      </a>
      <a href="{{ route('home') }}" class="btn btn-primary-engix">
        <i class="fa-solid fa-shopping-bag me-1"></i>Continue Shopping
      </a>
    </div>
  </div>
</div>

<!-- ============ FOOTER ============ -->
<footer class="footer" data-testid="footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <a href="/" class="footer-brand"><img src="{{ asset('asset/logo.png') }}" alt="engix CARE" style="height:40px;width:auto;object-fit:contain;margin-right:10px;" /><span>engix<span class="brand-accent">CARE</span></span></a>
        <p class="footer-desc">Premium effervescent health supplements crafted for real Indian lifestyles. Doctor formulated, WHO-GMP &amp; HACCP certified manufacturing, FSSAI approved.</p>
        <div class="social">
          <a href="#" aria-label="Facebook" data-testid="social-fb"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram" data-testid="social-ig"><i class="fa-brands fa-instagram"></i></a>
          <a href="#" aria-label="X" data-testid="social-x"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="#" aria-label="YouTube" data-testid="social-yt"><i class="fa-brands fa-youtube"></i></a>
          <a href="#" aria-label="LinkedIn" data-testid="social-li"><i class="fa-brands fa-linkedin-in"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-title">Quick Links</h6>
        <ul class="footer-links">
          <li><a href="{{ route('home') }}">Home</a></li>
          <li><a href="{{ route('offers.index') }}">Offers</a></li>
          <li><a href="{{ route('orders.index') }}">My Orders</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>
      <div class="col-6 col-lg-2">
        <h6 class="footer-title">Policies</h6>
        <ul class="footer-links">
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Refund Policy</a></li>
          <li><a href="#">Terms of Service</a></li>
          <li><a href="#">Shipping</a></li>
        </ul>
      </div>
      <div class="col-lg-4">
        <h6 class="footer-title">Brand &amp; Manufacturer Info</h6>
        <div style="font-size:0.82rem;color:#94a3b8;line-height:1.65;margin-bottom:16px;">
          <p style="margin:0 0 8px;"><strong style="color:#cbd5e1;">Marketed by:</strong><br />
          VHK International<br />
          Shop No-3, Surjit Colony, Bapunagar,<br />
          Ahmedabad, Gujarat – 380024</p>
          <p style="margin:0 0 8px;"><strong style="color:#cbd5e1;">Manufactured by:</strong><br />
          Growequal <span style="font-size:0.75rem;opacity:0.8;">(WHO-GMP &amp; HACCP Certified)</span><br />
          D-15, Sahjanand Business Park, S.P. Ring Road,<br />
          Nikol, Ahmedabad, Gujarat – 382350, India</p>
          <p style="margin:0;"><strong style="color:#cbd5e1;">Customer Care:</strong><br />
          <a href="mailto:vhkinternational2026@gmail.com" style="color:#22C55E;text-decoration:none;">vhkinternational2026@gmail.com</a></p>
        </div>
      </div>
    </div>
    <hr class="footer-divider" />
    <div class="footer-bottom">
      <p>&copy; <span id="year"></span> Engix Care. All rights reserved.</p>
      <p>Made with <i class="fa-solid fa-heart" style="color:#22C55E"></i> for a healthier tomorrow.</p>
    </div>
  </div>
</footer>

<script>
  document.getElementById('year').textContent = new Date().getFullYear();
</script>

<style>
  .co-status-placed {
    background-color: #e3f2fd;
    color: #1976d2;
  }

  .co-status-processing {
    background-color: #fff3e0;
    color: #f57c00;
  }

  .co-status-shipped {
    background-color: #f3e5f5;
    color: #7b1fa2;
  }

  .co-status-delivered {
    background-color: #e8f5e9;
    color: #388e3c;
  }

  .co-status-cancelled {
    background-color: #ffebee;
    color: #d32f2f;
  }

  .co-pay-pending {
    padding: 4px 8px;
    background-color: #fff3e0;
    color: #f57c00;
    border-radius: 4px;
    font-size: 0.85rem;
  }

  .co-pay-paid {
    padding: 4px 8px;
    background-color: #e8f5e9;
    color: #388e3c;
    border-radius: 4px;
    font-size: 0.85rem;
  }

  .co-pay-failed {
    padding: 4px 8px;
    background-color: #ffebee;
    color: #d32f2f;
    border-radius: 4px;
    font-size: 0.85rem;
  }
</style>

@endsection
