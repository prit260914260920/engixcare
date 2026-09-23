@extends('layouts.admin')

@section('content')
@include('admin.partials.sidebar')

<main class="ml-[280px] min-h-screen flex flex-col">
    @include('admin.partials.topbar')

    <div class="p-margin-desktop space-y-gutter">

        {{-- ── Header ──────────────────────────────────────────────────────── --}}
        <div class="flex flex-col gap-xs">
            <nav class="flex items-center gap-xs text-on-surface-variant text-label-sm font-label-sm">
                <a class="hover:text-primary" href="/admin/dashboard">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-semibold">Payments</span>
            </nav>
            <div class="flex flex-wrap items-end justify-between gap-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Payments</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Payment summary for all customer orders.</p>
                </div>
                <button type="button" onclick="window.location.reload()"
                    class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-sm py-2 rounded-lg transition-colors font-label-sm">
                    <span class="material-symbols-outlined text-[18px]">refresh</span> Refresh
                </button>
            </div>
        </div>

        {{-- ── Stats Cards ──────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-md">
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-[22px]" style="font-variation-settings:'FILL' 1">payments</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Total Orders</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['total'] }}</p>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-tertiary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-tertiary text-[22px]" style="font-variation-settings:'FILL' 1">check_circle</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Paid</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['paid'] }}</p>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-error/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-error text-[22px]" style="font-variation-settings:'FILL' 1">hourglass_empty</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Pending</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['pending'] }}</p>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-secondary text-[22px]" style="font-variation-settings:'FILL' 1">currency_rupee</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Total Revenue</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">₹{{ number_format($stats['revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        {{-- ── Method Summary ───────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 gap-md">
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-[22px]" style="font-variation-settings:'FILL' 1">credit_card</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Online Payments</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['online'] }}</p>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-outline-variant/20 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-on-surface-variant text-[22px]" style="font-variation-settings:'FILL' 1">money</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Cash on Delivery</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['cod'] }}</p>
                </div>
            </div>
        </div>

        {{-- ── Filter Bar ───────────────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('admin.payments.index') }}"
              class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-sm shadow-sm flex flex-wrap items-center gap-sm">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[220px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input name="search" value="{{ request('search') }}"
                    class="w-full bg-surface border border-outline-variant/50 rounded-lg pl-10 py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary"
                    placeholder="Search order #, name, email, phone…" type="text" />
            </div>

            {{-- Payment status --}}
            <select name="payment_status"
                class="bg-surface border border-outline-variant/50 rounded-lg px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary min-w-[160px]">
                <option value="">All Statuses</option>
                @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'] as $val => $label)
                    <option value="{{ $val }}" {{ request('payment_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            {{-- Payment method --}}
            <select name="payment_method"
                class="bg-surface border border-outline-variant/50 rounded-lg px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary min-w-[130px]">
                <option value="">All Methods</option>
                <option value="cod"    {{ request('payment_method') === 'cod'    ? 'selected' : '' }}>COD</option>
                <option value="online" {{ request('payment_method') === 'online' ? 'selected' : '' }}>Online</option>
            </select>

            <button type="submit"
                class="flex items-center gap-xs bg-primary text-white px-md py-2 rounded-lg font-label-sm shadow-sm shadow-primary/20 hover:bg-primary-fixed-dim transition-all">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter
            </button>

            @if(request()->hasAny(['search', 'payment_status', 'payment_method']))
                <a href="{{ route('admin.payments.index') }}"
                    class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-sm py-2 rounded-lg transition-colors font-label-sm">
                    <span class="material-symbols-outlined text-[18px]">close</span> Clear
                </a>
            @endif
        </form>

        {{-- ── Flash ────────────────────────────────────────────────────────── --}}
        @if(session('success'))
            <div id="flash-success" class="rounded-2xl border border-primary/20 bg-primary/10 p-md text-sm text-primary">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Payments Table ───────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 border-b border-outline-variant/10">
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Order</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Customer</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Amount</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Payment Status</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Method</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Razorpay ID</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Date</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                    @forelse($orders as $order)
                        <tr class="hover:bg-surface-container-low transition-colors group" id="row-{{ $order->id }}">

                            {{-- Order # --}}
                            <td class="px-md py-md whitespace-nowrap">
                                <p class="font-label-md text-label-md text-on-surface font-bold group-hover:text-primary transition-colors">{{ $order->order_number }}</p>
                                <p class="text-label-sm text-on-surface-variant/60">{{ count($order->items ?? []) }} item(s)</p>
                            </td>

                            {{-- Customer --}}
                            <td class="px-md py-md">
                                <div class="flex items-center gap-sm">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <span class="text-label-md font-bold text-primary">{{ strtoupper(substr($order->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-label-md text-label-md text-on-surface font-semibold">{{ $order->name }}</p>
                                        <p class="text-label-sm text-on-surface-variant/70">{{ $order->email }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Amount --}}
                            <td class="px-md py-md whitespace-nowrap">
                                <p class="font-label-md text-label-md text-on-surface font-semibold">₹{{ number_format($order->total, 2) }}</p>
                                @if($order->discount > 0)
                                    <p class="text-label-sm text-tertiary">-₹{{ number_format($order->discount, 2) }} off</p>
                                @endif
                            </td>

                            {{-- Payment status --}}
                            <td class="px-md py-md">
                                @php
                                    $ps = $order->payment_status;
                                    $psColor = match($ps) {
                                        'paid'    => 'bg-tertiary/10 text-tertiary',
                                        'failed'  => 'bg-error/10 text-error',
                                        default   => 'bg-secondary/10 text-secondary',
                                    };
                                    $psIcon = match($ps) {
                                        'paid'    => 'check_circle',
                                        'failed'  => 'cancel',
                                        default   => 'schedule',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-xs px-sm py-0.5 rounded-full text-label-sm font-label-sm {{ $psColor }}"
                                      id="ps-badge-{{ $order->id }}">
                                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">{{ $psIcon }}</span>
                                    {{ ucfirst($ps) }}
                                </span>
                            </td>

                            {{-- Method --}}
                            <td class="px-md py-md">
                                <span class="inline-flex items-center gap-xs text-label-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[16px]">{{ $order->payment_method === 'online' ? 'credit_card' : 'payments' }}</span>
                                    {{ strtoupper($order->payment_method) }}
                                </span>
                            </td>

                            {{-- Razorpay ID --}}
                            <td class="px-md py-md text-body-sm text-on-surface-variant">
                                @if($order->razorpay_payment_id)
                                    <span class="font-mono text-label-sm text-on-surface bg-surface-container-low px-sm py-0.5 rounded-lg">
                                        {{ $order->razorpay_payment_id }}
                                    </span>
                                @else
                                    <span class="text-on-surface-variant/40 text-label-sm">—</span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td class="px-md py-md text-body-sm text-on-surface-variant whitespace-nowrap">
                                {{ $order->created_at->format('d M Y') }}
                                <div class="text-label-sm text-on-surface-variant/60">{{ $order->created_at->format('H:i') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-md py-md text-right">
                                <button type="button"
                                    onclick="openPaymentModal({{ $order->id }})"
                                    class="p-2 text-on-surface-variant hover:text-primary transition-colors hover:bg-surface-variant rounded-lg"
                                    title="View Payment Details">
                                    <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-md py-xl text-center text-on-surface-variant text-body-sm">
                                No payments found{{ request()->hasAny(['search','payment_status','payment_method']) ? ' matching your filters' : ' yet' }}.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                    {{-- Pagination footer --}}
                    <tfoot>
                        <tr>
                            <td colspan="8" class="px-md py-md bg-surface-container-low/30 border-t border-outline-variant/10">
                                <div class="flex items-center justify-between flex-wrap gap-sm">
                                    <p class="text-body-sm text-on-surface-variant">
                                        @if($orders->total() > 0)
                                            Showing <span class="font-bold text-on-surface">{{ $orders->firstItem() }}&nbsp;&ndash;&nbsp;{{ $orders->lastItem() }}</span>
                                            of <span class="font-bold text-on-surface">{{ $orders->total() }}</span> payments
                                        @else
                                            No payments found
                                        @endif
                                    </p>
                                    @if($orders->hasPages())
                                        <div>{{ $orders->links() }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</main>

{{-- ── Payment Detail Modal (reuses order show endpoint) ──────────────────── --}}
<div id="payment-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-md">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closePaymentModal()"></div>

    <div class="relative bg-surface rounded-3xl shadow-2xl w-full max-w-2xl z-10 flex flex-col overflow-hidden" style="max-height:90vh">

        {{-- Header --}}
        <div class="bg-primary px-xl pt-lg pb-md flex-shrink-0">
            <div class="flex items-start justify-between gap-md">
                <div class="flex items-center gap-md">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-[22px]" style="font-variation-settings:'FILL' 1">payments</span>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-[18px] leading-tight" id="pm-order-number">—</h3>
                        <p class="text-white/70 text-[12px] flex items-center gap-1 mt-0.5">
                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                            <span id="pm-date">—</span>
                        </p>
                    </div>
                </div>
                <button onclick="closePaymentModal()"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/20 hover:bg-white/30 text-white transition-colors flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="flex flex-wrap items-center gap-xs mt-sm">
                <span id="pm-ps-pill" class="inline-flex items-center gap-xs px-sm py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white">
                    <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1">schedule</span>
                    <span id="pm-ps-label">—</span>
                </span>
                <span class="inline-flex items-center gap-xs px-sm py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white">
                    <span class="material-symbols-outlined text-[12px]" id="pm-method-icon">payments</span>
                    <span id="pm-method">—</span>
                </span>
            </div>
        </div>

        {{-- Scrollable Body --}}
        <div class="overflow-y-auto custom-scrollbar flex-1 p-md space-y-sm bg-surface-container-lowest/50">

            {{-- Update payment status --}}
            <div class="bg-surface rounded-2xl border border-outline-variant/30 p-md">
                <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-xs flex items-center gap-xs">
                    <span class="material-symbols-outlined text-[12px]">credit_card</span> Update Payment Status
                </p>
                <select id="pm-payment-status-select" onchange="updatePaymentStatusFromPayments()"
                    class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-sm py-2 text-body-sm font-semibold text-on-surface focus:border-primary focus:outline-none">
                    @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'] as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Customer --}}
            <div class="bg-surface rounded-2xl border border-outline-variant/30 overflow-hidden">
                <div class="flex items-center gap-xs px-md py-sm bg-surface-container-low border-b border-outline-variant/20">
                    <span class="material-symbols-outlined text-primary text-[15px]" style="font-variation-settings:'FILL' 1">person</span>
                    <p class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Customer</p>
                </div>
                <div class="p-md">
                    <div class="flex items-center gap-sm mb-sm">
                        <div class="w-9 h-9 rounded-full bg-primary/10 border-2 border-primary/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-[14px] font-bold text-primary" id="pm-avatar">?</span>
                        </div>
                        <div>
                            <p class="text-body-sm font-bold text-on-surface" id="pm-name">—</p>
                            <a id="pm-email" href="#" class="text-label-sm text-primary hover:underline">—</a>
                        </div>
                    </div>
                    <div class="flex items-center gap-sm bg-surface-container-low rounded-xl px-sm py-xs">
                        <span class="material-symbols-outlined text-[14px] text-on-surface-variant">call</span>
                        <a id="pm-phone" href="#" class="text-label-sm text-on-surface hover:text-primary transition-colors">—</a>
                    </div>
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="bg-surface rounded-2xl border border-outline-variant/30 overflow-hidden">
                <div class="flex items-center gap-xs px-md py-sm bg-surface-container-low border-b border-outline-variant/20">
                    <span class="material-symbols-outlined text-secondary text-[15px]" style="font-variation-settings:'FILL' 1">payments</span>
                    <p class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Payment Info</p>
                </div>
                <div class="p-md space-y-xs">
                    <div id="pm-razorpay-info"></div>
                    <div id="pm-coupon-row" class="hidden flex items-center gap-sm bg-tertiary/8 border border-tertiary/20 rounded-xl px-md py-sm">
                        <span class="material-symbols-outlined text-[15px] text-tertiary" style="font-variation-settings:'FILL' 1">confirmation_number</span>
                        <span class="text-label-sm text-on-surface">Coupon: <span id="pm-coupon" class="font-bold text-tertiary font-mono"></span></span>
                    </div>
                </div>
            </div>

            {{-- Amount Summary --}}
            <div class="bg-surface rounded-2xl border border-outline-variant/30 overflow-hidden">
                <div class="flex items-center gap-xs px-md py-sm bg-surface-container-low border-b border-outline-variant/20">
                    <span class="material-symbols-outlined text-on-surface-variant text-[15px]" style="font-variation-settings:'FILL' 1">receipt_long</span>
                    <p class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Amount Summary</p>
                </div>
                <div class="mx-md my-md space-y-xs">
                    <div class="flex justify-between">
                        <span class="text-label-sm text-on-surface-variant">Subtotal</span>
                        <span class="text-label-sm text-on-surface font-medium" id="pm-subtotal">—</span>
                    </div>
                    <div class="flex justify-between hidden" id="pm-discount-row">
                        <span class="text-label-sm text-tertiary flex items-center gap-xs">
                            <span class="material-symbols-outlined text-[12px]">local_offer</span> Discount
                        </span>
                        <span class="text-label-sm text-tertiary font-semibold" id="pm-discount">—</span>
                    </div>
                    <div class="flex justify-between items-center pt-xs border-t border-outline-variant/20">
                        <span class="text-body-sm font-bold text-on-surface">Total</span>
                        <span class="text-[17px] font-extrabold text-primary" id="pm-total">—</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex justify-end px-md py-sm border-t border-outline-variant/20 flex-shrink-0 bg-surface">
            <button onclick="closePaymentModal()"
                class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-md py-xs rounded-xl text-label-sm font-semibold transition-colors">
                <span class="material-symbols-outlined text-[15px]">close</span> Close
            </button>
        </div>

    </div>
</div>

{{-- Loading overlay --}}
<div id="pm-loading" class="fixed inset-0 z-[60] hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl p-xl shadow-xl flex items-center gap-md">
        <span class="material-symbols-outlined text-primary text-[28px] animate-spin">progress_activity</span>
        <span class="text-body-md text-on-surface">Loading…</span>
    </div>
</div>

{{-- Toast --}}
<div id="pm-toast"
     class="fixed bottom-6 right-6 z-[70] hidden items-center gap-sm bg-surface-container-highest border border-outline-variant/30 rounded-2xl px-md py-sm shadow-xl text-body-sm text-on-surface transition-all">
    <span class="material-symbols-outlined text-[18px]" id="pm-toast-icon">check_circle</span>
    <span id="pm-toast-msg">Done.</span>
</div>

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    let currentOrderId = null;

    const PAYMENT_STATUS_MAP = {
        paid:    { icon: 'check_circle' },
        failed:  { icon: 'cancel' },
        pending: { icon: 'schedule' },
    };

    async function openPaymentModal(id) {
        currentOrderId = id;
        showPmLoading(true);
        try {
            const res = await fetch(`/admin/orders/${id}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            });
            if (!res.ok) throw new Error();
            const o = await res.json();
            populatePaymentModal(o);
            showPmLoading(false);
            const modal = document.getElementById('payment-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } catch {
            showPmLoading(false);
            showPmToast('Failed to load payment details.', 'error', true);
        }
    }

    function populatePaymentModal(o) {
        document.getElementById('pm-order-number').textContent = o.order_number;
        document.getElementById('pm-date').textContent         = o.created_at;

        // Payment status pill
        const cfg = PAYMENT_STATUS_MAP[o.payment_status] ?? { icon: 'help' };
        document.getElementById('pm-ps-pill').querySelector('.material-symbols-outlined').textContent = cfg.icon;
        document.getElementById('pm-ps-label').textContent = o.payment_status.charAt(0).toUpperCase() + o.payment_status.slice(1);

        // Method pill
        document.getElementById('pm-method-icon').textContent = o.payment_method === 'online' ? 'credit_card' : 'payments';
        document.getElementById('pm-method').textContent      = o.payment_method.toUpperCase();

        // Payment status select
        document.getElementById('pm-payment-status-select').value = o.payment_status;

        // Customer
        document.getElementById('pm-avatar').textContent = o.name.charAt(0).toUpperCase();
        document.getElementById('pm-name').textContent   = o.name;
        const emailEl = document.getElementById('pm-email');
        emailEl.textContent = o.email;
        emailEl.href        = 'mailto:' + o.email;
        const phoneEl = document.getElementById('pm-phone');
        phoneEl.textContent = o.phone;
        phoneEl.href        = 'tel:' + o.phone;

        // Razorpay
        const rzpEl = document.getElementById('pm-razorpay-info');
        if (o.razorpay_payment_id) {
            rzpEl.innerHTML = `
                <div class="flex items-center gap-sm bg-surface-container-low rounded-xl px-md py-sm">
                    <span class="material-symbols-outlined text-[16px] text-on-surface-variant">receipt_long</span>
                    <div>
                        <p class="text-label-xs text-on-surface-variant" style="font-size:10px;text-transform:uppercase;letter-spacing:.05em">Razorpay Payment ID</p>
                        <p class="font-mono text-body-sm text-on-surface font-semibold">${o.razorpay_payment_id}</p>
                    </div>
                </div>`;
        } else {
            rzpEl.innerHTML = `
                <div class="flex items-center gap-sm text-on-surface-variant/60 bg-surface-container-low rounded-xl px-md py-sm">
                    <span class="material-symbols-outlined text-[16px]">info</span>
                    <span class="text-body-sm">No online payment recorded.</span>
                </div>`;
        }

        // Coupon
        const couponRow = document.getElementById('pm-coupon-row');
        if (o.coupon_code) {
            document.getElementById('pm-coupon').textContent = o.coupon_code;
            couponRow.classList.remove('hidden');
        } else {
            couponRow.classList.add('hidden');
        }

        // Amounts
        document.getElementById('pm-subtotal').textContent = '₹' + o.subtotal;
        document.getElementById('pm-total').textContent    = '₹' + o.total;

        const discRow = document.getElementById('pm-discount-row');
        if (parseFloat(o.discount) > 0) {
            document.getElementById('pm-discount').textContent = '-₹' + o.discount;
            discRow.classList.remove('hidden');
        } else {
            discRow.classList.add('hidden');
        }

    }

    function closePaymentModal() {
        const modal = document.getElementById('payment-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentOrderId = null;
    }

    async function updatePaymentStatusFromPayments() {
        if (!currentOrderId) return;
        const payment_status = document.getElementById('pm-payment-status-select').value;
        try {
            const res = await fetch(`/admin/orders/${currentOrderId}/payment-status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ payment_status }),
            });
            const data = await res.json();
            if (!data.success) throw new Error();

            // Update modal header pill
            const cfg = PAYMENT_STATUS_MAP[payment_status] ?? { icon: 'help' };
            document.getElementById('pm-ps-pill').querySelector('.material-symbols-outlined').textContent = cfg.icon;
            document.getElementById('pm-ps-label').textContent = payment_status.charAt(0).toUpperCase() + payment_status.slice(1);

            // Update table row badge
            const psColors = {
                paid:    'bg-tertiary/10 text-tertiary',
                failed:  'bg-error/10 text-error',
                pending: 'bg-secondary/10 text-secondary',
            };
            const badge = document.getElementById(`ps-badge-${currentOrderId}`);
            if (badge) {
                badge.className = `inline-flex items-center gap-xs px-sm py-0.5 rounded-full text-label-sm font-label-sm ${psColors[payment_status]}`;
                badge.innerHTML = `<span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">${cfg.icon}</span> ${payment_status.charAt(0).toUpperCase() + payment_status.slice(1)}`;
            }

            showPmToast(data.message, 'check_circle');
        } catch {
            showPmToast('Failed to update payment status.', 'error', true);
        }
    }

    let pmToastTimer;
    function showPmToast(msg, icon = 'check_circle', isError = false) {
        const toast = document.getElementById('pm-toast');
        document.getElementById('pm-toast-msg').textContent  = msg;
        document.getElementById('pm-toast-icon').textContent = icon;
        toast.classList.toggle('text-error', isError);
        toast.classList.remove('hidden');
        toast.classList.add('flex');
        clearTimeout(pmToastTimer);
        pmToastTimer = setTimeout(() => {
            toast.classList.add('hidden');
            toast.classList.remove('flex');
        }, 3000);
    }

    function showPmLoading(show) {
        const el = document.getElementById('pm-loading');
        el.classList.toggle('hidden', !show);
        el.classList.toggle('flex', show);
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closePaymentModal();
    });

    const flash = document.getElementById('flash-success');
    if (flash) setTimeout(() => flash.style.display = 'none', 4000);
</script>
@endpush

@endsection
