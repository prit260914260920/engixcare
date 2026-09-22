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
        <li class="nav-item"><a class="nav-link active" href="{{ route('offers.index') }}" data-testid="nav-offers"><i class="fa-solid fa-tag me-1"></i>Offers</a></li>
        @auth
          <li class="nav-item"><a class="nav-link" href="{{ route('orders.index') }}" data-testid="nav-orders"><i class="fa-solid fa-box me-1"></i>My Orders</a></li>
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

<!-- ============ OFFERS PAGE ============ -->
<section class="section py-5" style="padding-bottom: 80px;">
  <div class="container">
    <div class="row mb-5">
      <div class="col-12">
        <h1 class="font-headline-lg text-headline-lg mb-2" data-testid="offers-title">
          <i class="fa-solid fa-tag me-2 text-primary"></i>Available Offers & Coupons
        </h1>
        <p class="text-muted mb-0">Grab the best deals on your favorite Engix Care products</p>
      </div>
    </div>

    @if($offers->isEmpty())
      <div class="row">
        <div class="col-12 text-center py-5">
          <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
          <p class="text-muted fs-5">No offers available at the moment. Check back soon!</p>
        </div>
      </div>
    @else
      <div class="row g-4">
        @foreach($offers as $offer)
          <div class="col-md-6 col-lg-4" data-testid="offer-card-{{ $offer->id }}">
            <div class="card h-100 shadow-sm border-0 offer-card" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
              <div class="card-body d-flex flex-column">
                <!-- Offer Type Badge -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $offer->type)) }}</span>
                  @if($offer->percentage > 0)
                    <span class="badge bg-success fs-6">{{ $offer->percentage }}% OFF</span>
                  @endif
                </div>

                <!-- Title -->
                <h5 class="card-title fw-bold mb-2">{{ $offer->title }}</h5>

                <!-- Description -->
                @if($offer->description)
                  <p class="card-text text-muted small mb-3">{{ $offer->description }}</p>
                @endif

                <!-- Discount Text -->
                @if($offer->discount_text)
                  <p class="card-text mb-3">
                    <strong class="text-primary">{{ $offer->discount_text }}</strong>
                  </p>
                @endif

                <!-- Coupon Code -->
                @php $upper = $offer->coupon_code ? strtoupper($offer->coupon_code) : null; @endphp
                @if($offer->coupon_code)
                  <div class="mb-3" data-testid="coupon-{{ $offer->id }}">
                    <small class="text-muted d-block mb-1">Coupon Code:</small>
                    @if(isset($usedCodes) && $upper && in_array($upper, $usedCodes))
                      <div class="alert alert-secondary border-secondary">
                        <strong class="text-muted">Already used</strong>
                      </div>
                    @else
                      <div class="alert alert-light border border-primary">
                        <code class="fs-6 text-primary fw-bold">{{ $upper }}</code>
                      </div>
                    @endif
                  </div>
                @endif

                <!-- Target Amount Info -->
                @if($offer->target_amount)
                  <div class="alert alert-light border border-warning mb-3" data-testid="min-order-{{ $offer->id }}">
                    <small class="text-muted">
                      <i class="fa-solid fa-info-circle me-1"></i>
                      Minimum order: ₹{{ number_format($offer->target_amount) }}
                    </small>
                  </div>
                @endif

                <!-- Apply Button -->
                <div class="mt-auto">
                  @if($offer->coupon_code)
                    @if(!(isset($usedCodes) && in_array($upper, $usedCodes)))
                      <button class="btn btn-primary-engix w-100 btn-sm copy-coupon-btn"
                              data-coupon="{{ $upper }}"
                              data-testid="copy-coupon-{{ $offer->id }}">
                        <i class="fa-solid fa-copy me-1"></i>Copy Code
                      </button>
                    @else
                      <button class="btn btn-outline-secondary w-100 btn-sm" disabled>
                        <i class="fa-solid fa-check me-1"></i>Used
                      </button>
                    @endif
                  @else
                    <a href="{{ route('home') }}" class="btn btn-outline-engix w-100 btn-sm">
                      <i class="fa-solid fa-arrow-right me-1"></i>Shop Now
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>

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
          @auth
            <li><a href="{{ route('orders.index') }}">My Orders</a></li>
          @endauth
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

<!-- Loading animation styles -->
<style>
  .offer-card {
    border-radius: 12px !important;
  }

  .offer-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
  }

  .copy-coupon-btn {
    font-size: 0.9rem;
    padding: 8px 12px;
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Copy coupon code to clipboard
    document.querySelectorAll('.copy-coupon-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const coupon = this.dataset.coupon;
        navigator.clipboard.writeText(coupon).then(() => {
          const originalText = this.innerHTML;
          this.innerHTML = '<i class="fa-solid fa-check me-1"></i>Copied!';
          setTimeout(() => {
            this.innerHTML = originalText;
          }, 2000);
        });
      });
    });
  });
</script>

@endsection
