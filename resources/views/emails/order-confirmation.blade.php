<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Confirmed – {{ $order->order_number }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: Arial, Helvetica, sans-serif;
      background: #f1f5f9;
      color: #1e293b;
      font-size: 14px;
      line-height: 1.6;
    }

    .wrapper {
      max-width: 620px;
      margin: 32px auto;
      background: #ffffff;
      border-radius: 10px;
      overflow: hidden;
      border: 1px solid #e2e8f0;
    }

    /* ── Header ── */
    .header {
      background: #0f172a;
      padding: 28px 36px;
    }
    .brand-name {
      font-size: 22px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: -0.5px;
    }
    .brand-name span { color: #22c55e; }
    .brand-sub {
      font-size: 11px;
      color: #94a3b8;
      margin-top: 3px;
    }

    /* ── Hero banner ── */
    .hero {
      background: linear-gradient(135deg, #14532d 0%, #166534 100%);
      padding: 28px 36px;
      text-align: center;
    }
    .hero-icon {
      font-size: 40px;
      margin-bottom: 10px;
    }
    .hero h1 {
      font-size: 20px;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 6px;
    }
    .hero p {
      font-size: 13px;
      color: #bbf7d0;
    }

    /* ── Body ── */
    .body {
      padding: 30px 36px;
    }

    /* ── Order summary box ── */
    .order-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 18px 20px;
      margin-bottom: 24px;
    }
    .order-box-title {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #64748b;
      font-weight: 700;
      margin-bottom: 12px;
      padding-bottom: 8px;
      border-bottom: 1px solid #e2e8f0;
    }
    .order-row {
      display: flex;
      justify-content: space-between;
      font-size: 13px;
      color: #475569;
      padding: 5px 0;
    }
    .order-row strong { color: #0f172a; }
    .order-row.total-row {
      border-top: 2px solid #0f172a;
      margin-top: 8px;
      padding-top: 10px;
      font-size: 15px;
      font-weight: 700;
      color: #0f172a;
    }

    /* ── Status pill ── */
    .pill {
      display: inline-block;
      padding: 2px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
    }
    .pill-placed     { background: #e3f2fd; color: #1565c0; }
    .pill-pending    { background: #fff3e0; color: #e65100; }
    .pill-paid       { background: #e8f5e9; color: #2e7d32; }
    .pill-cod        { background: #f3e5f5; color: #6a1b9a; }

    /* ── Items table ── */
    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
      font-size: 13px;
    }
    .items-table thead th {
      background: #f1f5f9;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: .7px;
      color: #475569;
      font-weight: 700;
      padding: 8px 10px;
      border-bottom: 2px solid #e2e8f0;
      text-align: left;
    }
    .items-table thead th.right { text-align: right; }
    .items-table thead th.center { text-align: center; }
    .items-table tbody td {
      padding: 10px;
      border-bottom: 1px solid #f1f5f9;
      color: #334155;
      vertical-align: middle;
    }
    .items-table tbody tr:last-child td { border-bottom: none; }
    .items-table .item-name { font-weight: 600; color: #0f172a; }
    .items-table .center { text-align: center; }
    .items-table .right  { text-align: right; font-weight: 600; color: #0f172a; }

    /* ── Shipping address ── */
    .address-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 16px 20px;
      margin-bottom: 24px;
      font-size: 13px;
      color: #334155;
      line-height: 1.8;
    }
    .address-box .section-title {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #64748b;
      font-weight: 700;
      margin-bottom: 8px;
    }

    /* ── Attachment note ── */
    .attachment-note {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-left: 4px solid #22c55e;
      border-radius: 6px;
      padding: 14px 16px;
      font-size: 13px;
      color: #15803d;
      margin-bottom: 24px;
    }
    .attachment-note strong { color: #14532d; }

    /* ── CTA button ── */
    .cta-wrap { text-align: center; margin-bottom: 28px; }
    .cta-btn {
      display: inline-block;
      background: #22c55e;
      color: #ffffff;
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      padding: 12px 28px;
      border-radius: 8px;
    }

    /* ── Footer ── */
    .footer {
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      padding: 20px 36px;
      text-align: center;
      font-size: 12px;
      color: #64748b;
      line-height: 1.8;
    }
    .footer strong { color: #0f172a; }
    .footer a { color: #22c55e; text-decoration: none; }
  </style>
</head>
<body>

  <div class="wrapper">

    {{-- ── Header ── --}}
    <div class="header">
      <div class="brand-name">engix<span>CARE</span></div>
      <div class="brand-sub">WHO-GMP &amp; HACCP Certified &nbsp;·&nbsp; FSSAI Approved</div>
    </div>

    {{-- ── Hero ── --}}
    <div class="hero">
      <div class="hero-icon">✅</div>
      <h1>Your Order is Confirmed!</h1>
      <p>Thank you, {{ $order->name }}. We've received your order and it's being processed.</p>
    </div>

    {{-- ── Body ── --}}
    <div class="body">

      {{-- Order summary ── --}}
      <div class="order-box">
        <div class="order-box-title">Order Summary</div>

        <div class="order-row">
          <span>Order Number</span>
          <strong>{{ $order->order_number }}</strong>
        </div>
        <div class="order-row">
          <span>Order Date</span>
          <strong>{{ $order->created_at->format('d M Y, h:i A') }}</strong>
        </div>
        <div class="order-row">
          <span>Payment Method</span>
          <strong>{{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment' }}</strong>
        </div>
        <div class="order-row">
          <span>Payment Status</span>
          <span>
            @if($order->payment_status === 'paid')
              <span class="pill pill-paid">Paid</span>
            @elseif($order->payment_method === 'cod')
              <span class="pill pill-cod">Pay on Delivery</span>
            @else
              <span class="pill pill-pending">{{ ucfirst($order->payment_status) }}</span>
            @endif
          </span>
        </div>
        @if($order->razorpay_payment_id)
          <div class="order-row">
            <span>Transaction ID</span>
            <strong style="font-size:12px;">{{ $order->razorpay_payment_id }}</strong>
          </div>
        @endif
      </div>

      {{-- Items ── --}}
      <table class="items-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Product</th>
            <th class="center">Qty</th>
            <th class="right">Unit Price</th>
            <th class="right">Amount</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $index => $item)
            <tr>
              <td style="color:#94a3b8; font-size:12px;">{{ $index + 1 }}</td>
              <td class="item-name">{{ $item['name'] }}</td>
              <td class="center">{{ $item['qty'] }}</td>
              <td class="right">&#8377;{{ number_format($item['price'], 0) }}</td>
              <td class="right">&#8377;{{ number_format($item['price'] * $item['qty'], 0) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      {{-- Totals ── --}}
      <div class="order-box">
        <div class="order-row">
          <span>Subtotal</span>
          <span>&#8377;{{ number_format($order->subtotal, 0) }}</span>
        </div>
        @if($order->discount > 0)
          <div class="order-row" style="color:#16a34a;">
            <span>
              Coupon Discount
              @if($order->coupon_code) ({{ $order->coupon_code }}) @endif
            </span>
            <span>&#8722; &#8377;{{ number_format($order->discount, 0) }}</span>
          </div>
        @endif
        @if(!empty($order->gst) && $order->gst > 0)
          <div class="order-row">
            <span>GST / Tax</span>
            <span>&#8377;{{ number_format($order->gst, 0) }}</span>
          </div>
        @endif
        <div class="order-row total-row">
          <span>Total</span>
          <span>&#8377;{{ number_format($order->total, 0) }}</span>
        </div>
      </div>

      {{-- Shipping address ── --}}
      <div class="address-box">
        <div class="section-title">Shipping To</div>
        <strong>{{ $order->name }}</strong><br>
        {{ $order->address_line1 }}<br>
        @if($order->address_line2){{ $order->address_line2 }}<br>@endif
        {{ $order->city }}, {{ $order->state }} – {{ $order->pincode }}<br>
        📞 {{ $order->phone }}
      </div>

      {{-- Attachment note ── --}}
      <div class="attachment-note">
        📎 <strong>Invoice attached:</strong> Your tax invoice ({{ $order->order_number }}.pdf) is attached to this email for your records.
      </div>

      {{-- Notes ── --}}
      @if($order->notes)
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #94a3b8; border-radius:6px; padding:14px 16px; font-size:13px; color:#475569; margin-bottom:24px;">
          <strong style="font-size:11px; text-transform:uppercase; letter-spacing:.8px; color:#64748b;">Order Notes</strong><br>
          {{ $order->notes }}
        </div>
      @endif

      {{-- CTA ── --}}
      <div class="cta-wrap">
        <a href="{{ url('/my-orders/' . $order->id) }}" class="cta-btn">Track Your Order</a>
      </div>

    </div>{{-- end .body --}}

    {{-- ── Footer ── --}}
    <div class="footer">
      <strong>VHK International</strong><br>
      Shop No-3, Surjit Colony, Bapunagar, Ahmedabad, Gujarat – 380024<br>
      <a href="mailto:vhkinternational2026@gmail.com">vhkinternational2026@gmail.com</a><br><br>
      <span style="font-size:11px; color:#94a3b8;">
        You received this email because an order was placed on engixCARE with this email address.
      </span>
    </div>

  </div>

</body>
</html>
