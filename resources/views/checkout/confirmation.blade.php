@extends('layouts.app')

@section('content')
<div class="checkout-page">
  <div class="container" style="max-width:780px; padding-top:110px; padding-bottom:80px;">

    {{-- Success hero --}}
    <div class="co-confirm-hero">
      <div class="co-confirm-icon">
        <i class="fa-solid fa-circle-check"></i>
      </div>
      <h1 class="co-confirm-title">Order Placed!</h1>
      <p class="co-confirm-sub">
        Thank you, <strong>{{ $order->name }}</strong>. Your order <strong>{{ $order->order_number }}</strong>
        has been received and will be processed shortly.
      </p>
      <p class="co-confirm-email">
        A confirmation will be sent to <strong>{{ $order->email }}</strong>.
      </p>
    </div>

    {{-- Order details card --}}
    <div class="co-card mt-4">
      <div class="co-card-head" style="border-bottom:1px solid var(--line); padding-bottom:14px; margin-bottom:20px;">
        <h2 style="font-size:1.1rem;">Order Details</h2>
        <span class="co-status-badge co-status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
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
      <div class="co-summary-totals mb-4">
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
      <div class="row g-3">
        <div class="col-sm-6">
          <div class="co-info-block">
            <h6><i class="fa-solid fa-location-dot me-1"></i> Shipping To</h6>
            <p>
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
              {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment (Razorpay)' }}<br>
              <span class="co-pay-status co-pay-{{ $order->payment_status }}">
                {{ ucfirst($order->payment_status) }}
              </span>
              @if($order->razorpay_payment_id)
                <br><small class="text-muted">Payment ID: {{ $order->razorpay_payment_id }}</small>
              @endif
            </p>
          </div>
        </div>
      </div>

      @if($order->notes)
      <div class="co-info-block mt-3">
        <h6><i class="fa-solid fa-note-sticky me-1"></i> Your Notes</h6>
        <p>{{ $order->notes }}</p>
      </div>
      @endif
    </div>

    {{-- CTA --}}
    <div class="co-confirm-ctas">
      <a href="{{ url('/') }}" class="btn btn-primary-engix btn-lg">
        <i class="fa-solid fa-house me-2"></i>Back to Home
      </a>
      <a href="{{ url('/') }}#products" class="btn btn-outline-engix btn-lg">
        <i class="fa-solid fa-arrow-right me-2"></i>Continue Shopping
      </a>
    </div>

  </div>
</div>

{{-- Clear JS cart now that order is placed --}}
<script>
  if (typeof cart !== 'undefined') { cart = []; }
  if (typeof renderCart === 'function') { renderCart(); }
  // Clear localStorage/sessionStorage if used
  try { localStorage.removeItem('engixCart'); } catch(e) {}
</script>
@endsection
