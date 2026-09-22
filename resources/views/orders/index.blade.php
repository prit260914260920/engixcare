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

<!-- ============ MY ORDERS PAGE ============ -->
<section class="section py-5" style="padding-bottom: 80px;">
  <div class="container" style="max-width: 900px;">
    <div class="row mb-5">
      <div class="col-12">
        <h1 class="font-headline-lg text-headline-lg mb-2" data-testid="orders-title">
          <i class="fa-solid fa-box me-2 text-primary"></i>My Orders
        </h1>
        <p class="text-muted mb-0">View and track all your orders</p>
      </div>
    </div>

    @if($orders->isEmpty())
      <div class="row">
        <div class="col-12 text-center py-5">
          <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
          <p class="text-muted fs-5 mb-3">You haven't placed any orders yet.</p>
          <a href="{{ route('home') }}" class="btn btn-primary-engix">
            <i class="fa-solid fa-shopping-bag me-1"></i>Start Shopping
          </a>
        </div>
      </div>
    @else
      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <table class="table table-hover align-middle" data-testid="orders-table">
              <thead class="table-light">
                <tr>
                  <th>Order ID</th>
                  <th>Date</th>
                  <th>Items</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Payment</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($orders as $order)
                  <tr data-testid="order-row-{{ $order->id }}">
                    <td>
                      <strong class="text-primary">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                    </td>
                    <td>
                      <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small><br>
                      <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                    </td>
                    <td>
                      @php
                        $itemCount = is_array($order->items) ? count($order->items) : 0;
                      @endphp
                      <span class="badge bg-light text-dark">{{ $itemCount }} item{{ $itemCount != 1 ? 's' : '' }}</span>
                    </td>
                    <td>
                      <strong>₹{{ number_format($order->total, 0) }}</strong>
                    </td>
                    <td>
                      <span class="badge status-badge status-{{ strtolower($order->status) }}">
                        {{ ucfirst($order->status) }}
                      </span>
                    </td>
                    <td>
                      <span class="badge payment-badge payment-{{ strtolower($order->payment_status) }}">
                        {{ ucfirst($order->payment_status) }}
                      </span>
                    </td>
                    <td>
                      <a href="{{ route('orders.detail', $order) }}" class="btn btn-sm btn-outline-engix" data-testid="view-order-{{ $order->id }}">
                        <i class="fa-solid fa-eye me-1"></i>View
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          @if($orders->hasPages())
            <div class="d-flex justify-content-center mt-4">
              {{ $orders->links() }}
            </div>
          @endif
        </div>
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
  .status-badge {
    font-size: 0.85rem;
  }

  .status-placed {
    background-color: #e3f2fd !important;
    color: #1976d2 !important;
  }

  .status-processing {
    background-color: #fff3e0 !important;
    color: #f57c00 !important;
  }

  .status-shipped {
    background-color: #f3e5f5 !important;
    color: #7b1fa2 !important;
  }

  .status-delivered {
    background-color: #e8f5e9 !important;
    color: #388e3c !important;
  }

  .status-cancelled {
    background-color: #ffebee !important;
    color: #d32f2f !important;
  }

  .payment-badge {
    font-size: 0.85rem;
  }

  .payment-pending {
    background-color: #fff3e0 !important;
    color: #f57c00 !important;
  }

  .payment-completed {
    background-color: #e8f5e9 !important;
    color: #388e3c !important;
  }

  .payment-failed {
    background-color: #ffebee !important;
    color: #d32f2f !important;
  }
</style>

@endsection
