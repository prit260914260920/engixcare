<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>Invoice – {{ $order->order_number }}</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      color: #1e293b;
      background: #fff;
      line-height: 1.5;
    }

    /* ── Header band ── */
    .header {
      background: #0f172a;
      color: #fff;
      padding: 24px 32px;
      width: 100%;
    }
    .header-inner {
      width: 100%;
    }
    .header-left {
      display: inline-block;
      width: 55%;
      vertical-align: top;
    }
    .header-right {
      display: inline-block;
      width: 40%;
      vertical-align: top;
      text-align: right;
    }
    .brand-name {
      font-size: 22px;
      font-weight: 700;
      color: #fff;
      letter-spacing: -0.5px;
    }
    .brand-name span { color: #22c55e; }
    .brand-sub {
      font-size: 9px;
      color: #94a3b8;
      margin-top: 3px;
    }
    .inv-label {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: #94a3b8;
      margin-bottom: 4px;
    }
    .inv-number {
      font-size: 18px;
      font-weight: 700;
      color: #fff;
      letter-spacing: 0.5px;
    }
    .inv-date {
      font-size: 10px;
      color: #cbd5e1;
      margin-top: 4px;
    }

    /* ── Status pill ── */
    .pill {
      display: inline-block;
      padding: 2px 10px;
      border-radius: 999px;
      font-size: 9px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
    }
    .pill-placed      { background:#e3f2fd; color:#1565c0; }
    .pill-processing  { background:#fff3e0; color:#e65100; }
    .pill-shipped     { background:#f3e5f5; color:#6a1b9a; }
    .pill-delivered   { background:#e8f5e9; color:#2e7d32; }
    .pill-cancelled   { background:#ffebee; color:#c62828; }
    .pill-paid        { background:#e8f5e9; color:#2e7d32; }
    .pill-pending     { background:#fff3e0; color:#e65100; }
    .pill-failed      { background:#ffebee; color:#c62828; }

    /* ── Body ── */
    .body { padding: 28px 32px; }

    /* ── Info grid ── */
    .info-table { width: 100%; margin-bottom: 24px; }
    .info-cell  { width: 50%; vertical-align: top; padding-right: 16px; }
    .info-cell:last-child { padding-right: 0; }
    .info-label {
      font-size: 8px;
      text-transform: uppercase;
      letter-spacing: 1.2px;
      color: #64748b;
      font-weight: 700;
      margin-bottom: 5px;
      border-bottom: 1px solid #e2e8f0;
      padding-bottom: 4px;
    }
    .info-value { font-size: 11px; color: #334155; line-height: 1.7; }
    .info-value strong { color: #0f172a; font-weight: 700; }

    /* ── Divider ── */
    .divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }

    /* ── Items table ── */
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .items-table thead th {
      background: #f1f5f9;
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: #475569;
      font-weight: 700;
      padding: 8px 10px;
      border-bottom: 2px solid #e2e8f0;
      text-align: left;
    }
    .items-table thead th.right { text-align: right; }
    .items-table thead th.center { text-align: center; }

    .items-table tbody td {
      padding: 10px 10px;
      border-bottom: 1px solid #f1f5f9;
      font-size: 11px;
      color: #334155;
      vertical-align: middle;
    }
    .items-table tbody tr:last-child td { border-bottom: none; }
    .items-table .sno   { color: #94a3b8; font-size: 10px; }
    .items-table .name  { font-weight: 700; color: #0f172a; }
    .items-table .center { text-align: center; }
    .items-table .right  { text-align: right; font-weight: 600; color: #0f172a; }

    /* ── Totals ── */
    .totals-table { width: 260px; margin-left: auto; border-collapse: collapse; }
    .totals-table td { padding: 5px 0; font-size: 11px; color: #475569; }
    .totals-table .label { text-align: left; }
    .totals-table .value { text-align: right; }
    .totals-table .discount { color: #16a34a; }
    .totals-table .grand td {
      font-size: 13px;
      font-weight: 700;
      color: #0f172a;
      border-top: 2px solid #0f172a;
      padding-top: 8px;
    }

    /* ── Notes ── */
    .notes-box {
      margin-top: 24px;
      padding: 12px 14px;
      background: #f8fafc;
      border-left: 3px solid #22c55e;
      font-size: 11px;
      color: #334155;
    }
    .notes-box strong { font-size: 9px; text-transform: uppercase; letter-spacing: .8px; color: #64748b; }

    /* ── Footer band ── */
    .footer {
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      padding: 14px 32px;
      font-size: 9px;
      color: #64748b;
      width: 100%;
    }
    .footer-inner { width: 100%; }
    .footer-left  { display: inline-block; width: 70%; vertical-align: middle; }
    .footer-right { display: inline-block; width: 28%; vertical-align: middle; text-align: right; font-weight: 700; color: #0f172a; }

    /* ── Thank you ── */
    .thankyou { text-align: center; font-size: 10px; color: #64748b; margin: 20px 0 8px; }
    .thankyou strong { color: #0f172a; }
  </style>
</head>
<body>

  {{-- Header --}}
  <div class="header">
    <div class="header-inner">
      <div class="header-left">
        <div class="brand-name">engix<span>CARE</span></div>
        <div class="brand-sub">WHO-GMP &amp; HACCP Certified &nbsp;·&nbsp; FSSAI Approved</div>
      </div>
      <div class="header-right">
        <div class="inv-label">Tax Invoice</div>
        <div class="inv-number">{{ $order->order_number }}</div>
        <div class="inv-date">Date: {{ $order->created_at->format('d M Y') }}</div>
        <div class="inv-date" style="margin-top:6px;">
          Status: &nbsp;<span class="pill pill-{{ strtolower($order->status) }}">{{ ucfirst($order->status) }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Body --}}
  <div class="body">

    {{-- Billed to / Sold by --}}
    <table class="info-table">
      <tr>
        <td class="info-cell">
          <div class="info-label">Billed &amp; Shipped To</div>
          <div class="info-value">
            <strong>{{ $order->name }}</strong><br>
            {{ $order->address_line1 }}<br>
            @if($order->address_line2){{ $order->address_line2 }}<br>@endif
            {{ $order->city }}, {{ $order->state }} – {{ $order->pincode }}<br>
            Ph: {{ $order->phone }}<br>
            {{ $order->email }}
          </div>
        </td>
        <td class="info-cell">
          <div class="info-label">Sold By</div>
          <div class="info-value">
            <strong>VHK International</strong><br>
            Shop No-3, Surjit Colony, Bapunagar,<br>
            Ahmedabad, Gujarat – 380024<br>
            vhkinternational2026@gmail.com
          </div>

          <div class="info-label" style="margin-top:14px;">Payment</div>
          <div class="info-value">
            {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment' }}<br>
            <span class="pill pill-{{ strtolower($order->payment_status) }}" style="margin-top:4px;display:inline-block;">
              {{ ucfirst($order->payment_status) }}
            </span>
            @if($order->razorpay_payment_id)
              <br><span style="font-size:9px;color:#94a3b8;">Txn: {{ $order->razorpay_payment_id }}</span>
            @endif
          </div>
        </td>
      </tr>
    </table>

    <hr class="divider">

    {{-- Items --}}
    <table class="items-table">
      <thead>
        <tr>
          <th style="width:30px;">#</th>
          <th>Product</th>
          <th class="center" style="width:60px;">Qty</th>
          <th class="right" style="width:90px;">Unit Price</th>
          <th class="right" style="width:90px;">Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach($order->items as $index => $item)
          <tr>
            <td class="sno">{{ $index + 1 }}</td>
            <td class="name">{{ $item['name'] }}</td>
            <td class="center">{{ $item['qty'] }}</td>
            <td class="right">&#8377;{{ number_format($item['price'], 0) }}</td>
            <td class="right">&#8377;{{ number_format($item['price'] * $item['qty'], 0) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals-table">
      <tr>
        <td class="label">Subtotal</td>
        <td class="value">&#8377;{{ number_format($order->subtotal, 0) }}</td>
      </tr>
      @if($order->discount > 0)
        <tr class="discount">
          <td class="label">
            Coupon Discount@if($order->coupon_code) ({{ $order->coupon_code }})@endif
          </td>
          <td class="value">&#8722; &#8377;{{ number_format($order->discount, 0) }}</td>
        </tr>
      @endif
      @if($order->gst > 0)
        <tr>
          <td class="label">GST / Tax</td>
          <td class="value">&#8377;{{ number_format($order->gst, 0) }}</td>
        </tr>
      @endif
      <tr class="grand">
        <td class="label">Total Paid</td>
        <td class="value">&#8377;{{ number_format($order->total, 0) }}</td>
      </tr>
    </table>

    @if($order->notes)
      <div class="notes-box">
        <strong>Order Notes</strong><br>
        {{ $order->notes }}
      </div>
    @endif

    <div class="thankyou">
      Thank you for choosing <strong>engixCARE</strong>!
      For support: vhkinternational2026@gmail.com
    </div>

  </div>

  {{-- Footer --}}
  <div class="footer">
    <div class="footer-inner">
      <div class="footer-left">
        Manufactured by Growequal (WHO-GMP &amp; HACCP Certified) &nbsp;·&nbsp; D-15, Sahjanand Business Park, S.P. Ring Road, Nikol, Ahmedabad – 382350
      </div>
      <div class="footer-right">engixCARE &copy; {{ date('Y') }}</div>
    </div>
  </div>

</body>
</html>
