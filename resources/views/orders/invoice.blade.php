<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Invoice – {{ $order->order_number }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: #f1f5f9;
      color: #1e293b;
      font-size: 14px;
      line-height: 1.6;
    }

    /* ── Action bar (hidden when printing) ── */
    .action-bar {
      background: #1e293b;
      color: #fff;
      padding: 12px 24px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .action-bar a, .action-bar button {
      text-decoration: none;
      font-size: 13px;
      font-weight: 500;
      padding: 8px 18px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .btn-back   { background: transparent; color: #94a3b8; }
    .btn-back:hover { color: #fff; }
    .btn-print  { background: #22c55e; color: #fff; margin-left: auto; }
    .btn-print:hover { background: #16a34a; }
    .action-bar .invoice-id { color: #94a3b8; font-size: 13px; }

    /* ── Page wrapper ── */
    .page-wrap {
      max-width: 780px;
      margin: 32px auto 60px;
      padding: 0 16px;
    }

    /* ── Invoice card ── */
    .invoice {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 24px rgba(0,0,0,.08);
      overflow: hidden;
    }

    /* ── Header band ── */
    .inv-header {
      background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
      color: #fff;
      padding: 36px 40px 28px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
    }
    .inv-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .inv-brand img { height: 44px; width: auto; object-fit: contain; }
    .inv-brand-name { font-size: 1.5rem; font-weight: 700; letter-spacing: -.5px; }
    .inv-brand-name span { color: #22c55e; }
    .inv-brand-sub { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

    .inv-meta { text-align: right; }
    .inv-meta .inv-number { font-size: 1.15rem; font-weight: 700; letter-spacing: .5px; }
    .inv-meta .inv-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 4px; }
    .inv-meta .inv-date { font-size: 0.82rem; color: #cbd5e1; margin-top: 6px; }

    /* ── Body ── */
    .inv-body { padding: 36px 40px; }

    /* ── Two-col info row ── */
    .inv-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      margin-bottom: 32px;
    }
    .inv-info-block h6 {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #64748b;
      margin-bottom: 8px;
      font-weight: 600;
    }
    .inv-info-block p {
      font-size: 0.875rem;
      color: #334155;
      line-height: 1.7;
    }
    .inv-info-block strong { color: #0f172a; }

    /* ── Status pill ── */
    .pill {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 600;
    }
    .pill-placed      { background:#e3f2fd; color:#1976d2; }
    .pill-processing  { background:#fff3e0; color:#f57c00; }
    .pill-shipped     { background:#f3e5f5; color:#7b1fa2; }
    .pill-delivered   { background:#e8f5e9; color:#388e3c; }
    .pill-cancelled   { background:#ffebee; color:#d32f2f; }
    .pill-paid        { background:#e8f5e9; color:#388e3c; }
    .pill-pending     { background:#fff3e0; color:#f57c00; }
    .pill-failed      { background:#ffebee; color:#d32f2f; }

    /* ── Divider ── */
    .inv-divider { border: none; border-top: 1px solid #e2e8f0; margin: 0 0 28px; }

    /* ── Items table ── */
    .inv-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
    .inv-table thead th {
      background: #f8fafc;
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: .8px;
      color: #64748b;
      font-weight: 600;
      padding: 10px 12px;
      text-align: left;
      border-bottom: 1px solid #e2e8f0;
    }
    .inv-table thead th:last-child { text-align: right; }
    .inv-table thead th:nth-child(2),
    .inv-table thead th:nth-child(3) { text-align: center; }

    .inv-table tbody td {
      padding: 12px 12px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
      color: #334155;
    }
    .inv-table tbody tr:last-child td { border-bottom: none; }
    .inv-table .item-name { font-weight: 600; color: #0f172a; font-size: 0.9rem; }
    .inv-table .item-qty  { text-align: center; }
    .inv-table .item-rate { text-align: center; }
    .inv-table .item-amt  { text-align: right; font-weight: 600; color: #0f172a; }

    /* ── Totals ── */
    .inv-totals {
      margin-left: auto;
      width: 300px;
      border-top: 1px solid #e2e8f0;
      padding-top: 16px;
    }
    .inv-total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.875rem;
      color: #475569;
      padding: 5px 0;
    }
    .inv-total-row.discount { color: #16a34a; }
    .inv-total-row.grand {
      font-size: 1rem;
      font-weight: 700;
      color: #0f172a;
      border-top: 2px solid #0f172a;
      margin-top: 8px;
      padding-top: 10px;
    }

    /* ── Footer band ── */
    .inv-footer {
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      font-size: 0.78rem;
      color: #64748b;
    }
    .inv-footer a { color: #22c55e; text-decoration: none; }
    .inv-footer .thank { font-weight: 600; color: #0f172a; }

    /* ── Thank you note ── */
    .inv-thankyou {
      text-align: center;
      padding: 28px 40px 0;
      font-size: 0.85rem;
      color: #64748b;
    }

    /* ── Print styles ── */
    @media print {
      body { background: #fff; }
      .action-bar { display: none !important; }
      .page-wrap { margin: 0; padding: 0; max-width: 100%; }
      .invoice { box-shadow: none; border-radius: 0; }
    }
  </style>
</head>
<body>

  {{-- ── Action bar ── --}}
  <div class="action-bar no-print">
    <a href="{{ route('orders.index') }}" class="btn-back">
      ← Back to Orders
    </a>
    <span class="invoice-id">Invoice {{ $order->order_number }}</span>
    <a href="{{ route('orders.invoice.pdf', $order) }}" class="btn-print" style="text-decoration:none;">
      ⬇ Download PDF
    </a>
    <button class="btn-print" onclick="window.print()" style="background:#334155;">
      🖨 Print / Save as PDF
    </button>
  </div>

  <div class="page-wrap">
    <div class="invoice">

      {{-- ── Header ── --}}
      <div class="inv-header">
        <div class="inv-brand">
          <img src="{{ asset('asset/logo.png') }}" alt="engix CARE" />
          <div>
            <div class="inv-brand-name">engix<span>CARE</span></div>
            <div class="inv-brand-sub">WHO-GMP &amp; HACCP Certified · FSSAI Approved</div>
          </div>
        </div>
        <div class="inv-meta">
          <div class="inv-label">Tax Invoice</div>
          <div class="inv-number">{{ $order->order_number }}</div>
          <div class="inv-date">Date: {{ $order->created_at->format('d M Y') }}</div>
          <div class="inv-date" style="margin-top:6px;">
            Order Status:
            <span class="pill pill-{{ strtolower($order->status) }}">{{ ucfirst($order->status) }}</span>
          </div>
        </div>
      </div>

      {{-- ── Body ── --}}
      <div class="inv-body">

        {{-- Billed to / Shipped to / Payment ── --}}
        <div class="inv-info-grid">
          <div class="inv-info-block">
            <h6>Billed &amp; Shipped To</h6>
            <p>
              <strong>{{ $order->name }}</strong><br>
              {{ $order->address_line1 }}<br>
              @if($order->address_line2){{ $order->address_line2 }}<br>@endif
              {{ $order->city }}, {{ $order->state }} – {{ $order->pincode }}<br>
              📞 {{ $order->phone }}<br>
              ✉️ {{ $order->email }}
            </p>
          </div>
          <div class="inv-info-block">
            <h6>Sold By</h6>
            <p>
              <strong>VHK International</strong><br>
              Shop No-3, Surjit Colony, Bapunagar,<br>
              Ahmedabad, Gujarat – 380024<br>
              ✉️ <a href="mailto:vhkinternational2026@gmail.com">vhkinternational2026@gmail.com</a>
            </p>
            <div style="margin-top:14px;">
              <h6>Payment</h6>
              <p>
                {{ $order->payment_method === 'cod' ? 'Cash on Delivery' : 'Online Payment' }}<br>
                <span class="pill pill-{{ strtolower($order->payment_status) }}" style="margin-top:4px;display:inline-block;">
                  {{ ucfirst($order->payment_status) }}
                </span>
                @if($order->razorpay_payment_id)
                  <br><small style="color:#94a3b8;font-size:0.72rem;">Txn: {{ $order->razorpay_payment_id }}</small>
                @endif
              </p>
            </div>
          </div>
        </div>

        <hr class="inv-divider">

        {{-- Items table ── --}}
        <table class="inv-table">
          <thead>
            <tr>
              <th>#</th>
              <th style="text-align:left;">Product</th>
              <th>Qty</th>
              <th>Unit Price</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($order->items as $index => $item)
              <tr>
                <td style="color:#94a3b8;font-size:0.8rem;">{{ $index + 1 }}</td>
                <td class="item-name">{{ $item['name'] }}</td>
                <td class="item-qty">{{ $item['qty'] }}</td>
                <td class="item-rate">₹{{ number_format($item['price'], 0) }}</td>
                <td class="item-amt">₹{{ number_format($item['price'] * $item['qty'], 0) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>

        {{-- Totals ── --}}
        <div class="inv-totals">
          <div class="inv-total-row">
            <span>Subtotal</span>
            <span>₹{{ number_format($order->subtotal, 0) }}</span>
          </div>
          @if($order->discount > 0)
            <div class="inv-total-row discount">
              <span>
                Coupon Discount
                @if($order->coupon_code)
                  <small style="font-size:0.72rem;">({{ $order->coupon_code }})</small>
                @endif
              </span>
              <span>− ₹{{ number_format($order->discount, 0) }}</span>
            </div>
          @endif
          @if($order->gst > 0)
            <div class="inv-total-row">
              <span>GST / Tax</span>
              <span>₹{{ number_format($order->gst, 0) }}</span>
            </div>
          @endif
          <div class="inv-total-row grand">
            <span>Total</span>
            <span>₹{{ number_format($order->total, 0) }}</span>
          </div>
        </div>

        {{-- Notes ── --}}
        @if($order->notes)
          <div style="margin-top:28px;padding:14px 16px;background:#f8fafc;border-radius:8px;border-left:3px solid #22c55e;">
            <strong style="font-size:0.78rem;text-transform:uppercase;letter-spacing:.8px;color:#64748b;">Order Notes</strong>
            <p style="margin-top:6px;font-size:0.875rem;color:#334155;">{{ $order->notes }}</p>
          </div>
        @endif

        <div class="inv-thankyou">
          Thank you for choosing <strong>engixCARE</strong>! 💚 For support, reach us at
          <a href="mailto:vhkinternational2026@gmail.com">vhkinternational2026@gmail.com</a>
        </div>

      </div>{{-- /inv-body --}}

      {{-- ── Footer ── --}}
      <div class="inv-footer">
        <span>Manufactured by Growequal (WHO-GMP &amp; HACCP Certified) · Nikol, Ahmedabad – 382350</span>
        <span class="thank">engixCARE © {{ date('Y') }}</span>
      </div>

    </div>{{-- /invoice --}}
  </div>{{-- /page-wrap --}}

</body>
</html>
