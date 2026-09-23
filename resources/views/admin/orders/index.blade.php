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
                <span class="text-primary font-semibold">Orders &amp; Payments</span>
            </nav>
            <div class="flex flex-wrap items-end justify-between gap-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Orders &amp; Payments</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">All customer orders fetched live from the database.</p>
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
                    <span class="material-symbols-outlined text-primary text-[22px]" style="font-variation-settings:'FILL' 1">shopping_bag</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Total Orders</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['total'] }}</p>
                </div>
            </div>
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-tertiary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-tertiary text-[22px]" style="font-variation-settings:'FILL' 1">pending</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">New (Placed)</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['placed'] }}</p>
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
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-error/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-error text-[22px]" style="font-variation-settings:'FILL' 1">hourglass_empty</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Pending Payment</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>

        {{-- ── Filter Bar ───────────────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('admin.orders.index') }}"
              class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-sm shadow-sm flex flex-wrap items-center gap-sm">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[220px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input name="search" value="{{ request('search') }}"
                    class="w-full bg-surface border border-outline-variant/50 rounded-lg pl-10 py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary"
                    placeholder="Search order #, name, email, phone…" type="text" />
            </div>

            {{-- Order status --}}
            <select name="status"
                class="bg-surface border border-outline-variant/50 rounded-lg px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary min-w-[150px]">
                <option value="">All Statuses</option>
                @foreach(['placed' => 'Placed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            {{-- Payment status --}}
            <select name="payment_status"
                class="bg-surface border border-outline-variant/50 rounded-lg px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary min-w-[160px]">
                <option value="">All Payments</option>
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

            @if(request()->hasAny(['search', 'status', 'payment_status', 'payment_method']))
                <a href="{{ route('admin.orders.index') }}"
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

        {{-- ── Orders Table ─────────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse" id="orders-table">
                    <thead>
                        <tr class="bg-surface-container-low/50 border-b border-outline-variant/10">
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Order</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Customer</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Amount</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Payment</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Order Status</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Method</th>
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

                            {{-- Order status --}}
                            <td class="px-md py-md">
                                @php
                                    $os = $order->status;
                                    $osColor = match($os) {
                                        'delivered'  => 'bg-tertiary/10 text-tertiary',
                                        'shipped'    => 'bg-primary/10 text-primary',
                                        'processing' => 'bg-secondary/10 text-secondary',
                                        'cancelled'  => 'bg-error/10 text-error',
                                        default      => 'bg-outline-variant/20 text-on-surface-variant',
                                    };
                                    $osIcon = match($os) {
                                        'delivered'  => 'done_all',
                                        'shipped'    => 'local_shipping',
                                        'processing' => 'autorenew',
                                        'cancelled'  => 'cancel',
                                        default      => 'inbox',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-xs px-sm py-0.5 rounded-full text-label-sm font-label-sm {{ $osColor }}"
                                      id="os-badge-{{ $order->id }}">
                                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">{{ $osIcon }}</span>
                                    {{ ucfirst($os) }}
                                </span>
                            </td>

                            {{-- Method --}}
                            <td class="px-md py-md">
                                <span class="inline-flex items-center gap-xs text-label-sm text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[16px]">{{ $order->payment_method === 'online' ? 'credit_card' : 'payments' }}</span>
                                    {{ strtoupper($order->payment_method) }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td class="px-md py-md text-body-sm text-on-surface-variant whitespace-nowrap">
                                {{ $order->created_at->format('d M Y') }}
                                <div class="text-label-sm text-on-surface-variant/60">{{ $order->created_at->format('H:i') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-md py-md text-right">
                                <button type="button"
                                    onclick="openOrderModal({{ $order->id }})"
                                    class="p-2 text-on-surface-variant hover:text-primary transition-colors hover:bg-surface-variant rounded-lg"
                                    title="View Order Details">
                                    <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-md py-xl text-center text-on-surface-variant text-body-sm">
                                No orders found{{ request()->hasAny(['search','status','payment_status','payment_method']) ? ' matching your filters' : ' yet' }}.
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
                                            of <span class="font-bold text-on-surface">{{ $orders->total() }}</span> orders
                                        @else
                                            No orders found
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

{{-- ── Order Detail Modal ───────────────────────────────────────────────────── --}}
<div id="order-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-md">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeOrderModal()"></div>

    <div class="relative bg-surface rounded-3xl shadow-2xl w-full max-w-2xl z-10 flex flex-col overflow-hidden" style="max-height:90vh">

        {{-- ── Header ──────────────────────────────────────────────────────── --}}
        <div class="bg-primary px-xl pt-lg pb-md flex-shrink-0">
            <div class="flex items-start justify-between gap-md">
                <div class="flex items-center gap-md">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-[22px]" style="font-variation-settings:'FILL' 1">shopping_bag</span>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-[18px] leading-tight" id="m-order-number">—</h3>
                        <p class="text-white/70 text-[12px] flex items-center gap-1 mt-0.5">
                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                            <span id="m-date">—</span>
                        </p>
                    </div>
                </div>
                <button onclick="closeOrderModal()"
                    class="w-7 h-7 flex items-center justify-center rounded-lg bg-white/20 hover:bg-white/30 text-white transition-colors flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            {{-- Status pills --}}
            <div class="flex flex-wrap items-center gap-xs mt-sm">
                <span id="m-os-pill" class="inline-flex items-center gap-xs px-sm py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white">
                    <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1">inbox</span>
                    <span id="m-os-label">—</span>
                </span>
                <span id="m-ps-pill" class="inline-flex items-center gap-xs px-sm py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white">
                    <span class="material-symbols-outlined text-[12px]" style="font-variation-settings:'FILL' 1">schedule</span>
                    <span id="m-ps-label">—</span>
                </span>
                <span class="inline-flex items-center gap-xs px-sm py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white">
                    <span class="material-symbols-outlined text-[12px]" id="m-method-icon">payments</span>
                    <span id="m-method">—</span>
                </span>
            </div>
        </div>

        {{-- ── Scrollable Body ─────────────────────────────────────────────── --}}
        <div class="overflow-y-auto custom-scrollbar flex-1 p-md space-y-sm bg-surface-container-lowest/50">

            {{-- Status selects --}}
            <div class="grid grid-cols-2 gap-sm">
                <div class="bg-surface rounded-2xl border border-outline-variant/30 p-md">
                    <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-xs flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[12px]">local_shipping</span> Order Status
                    </p>
                    <select id="m-status-select" onchange="updateOrderStatus()"
                        class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-sm py-2 text-body-sm font-semibold text-on-surface focus:border-primary focus:outline-none">
                        @foreach(['placed' => 'Placed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="bg-surface rounded-2xl border border-outline-variant/30 p-md">
                    <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-xs flex items-center gap-xs">
                        <span class="material-symbols-outlined text-[12px]">credit_card</span> Payment Status
                    </p>
                    <select id="m-payment-status-select" onchange="updatePaymentStatus()"
                        class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-sm py-2 text-body-sm font-semibold text-on-surface focus:border-primary focus:outline-none">
                        @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'] as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
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
                            <span class="text-[14px] font-bold text-primary" id="m-avatar">?</span>
                        </div>
                        <div>
                            <p class="text-body-sm font-bold text-on-surface" id="m-name">—</p>
                            <a id="m-email" href="#" class="text-label-sm text-primary hover:underline">—</a>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-xs">
                        <div class="flex items-center gap-sm bg-surface-container-low rounded-xl px-sm py-xs">
                            <span class="material-symbols-outlined text-[14px] text-on-surface-variant">call</span>
                            <a id="m-phone" href="#" class="text-label-sm text-on-surface hover:text-primary transition-colors">—</a>
                        </div>
                        <div class="flex items-start gap-sm bg-surface-container-low rounded-xl px-sm py-xs">
                            <span class="material-symbols-outlined text-[14px] text-on-surface-variant mt-0.5 flex-shrink-0">location_on</span>
                            <span class="text-label-sm text-on-surface leading-snug" id="m-address">—</span>
                        </div>
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
                    <div id="m-razorpay-info"></div>
                    <div id="m-coupon-row" class="hidden flex items-center gap-sm bg-tertiary/8 border border-tertiary/20 rounded-xl px-md py-sm">
                        <span class="material-symbols-outlined text-[15px] text-tertiary" style="font-variation-settings:'FILL' 1">confirmation_number</span>
                        <span class="text-label-sm text-on-surface">Coupon: <span id="m-coupon" class="font-bold text-tertiary font-mono"></span></span>
                    </div>
                </div>
            </div>

            {{-- Items + Totals --}}
            <div class="bg-surface rounded-2xl border border-outline-variant/30 overflow-hidden">
                <div class="flex items-center gap-xs px-md py-sm bg-surface-container-low border-b border-outline-variant/20">
                    <span class="material-symbols-outlined text-on-surface-variant text-[15px]" style="font-variation-settings:'FILL' 1">inventory_2</span>
                    <p class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Items Ordered</p>
                </div>
                <div class="px-md pt-md pb-xs space-y-xs" id="m-items"></div>
                <div class="mx-md mb-md mt-xs border-t border-outline-variant/20 pt-sm space-y-xs">
                    <div class="flex justify-between">
                        <span class="text-label-sm text-on-surface-variant">Subtotal</span>
                        <span class="text-label-sm text-on-surface font-medium" id="m-subtotal">—</span>
                    </div>
                    <div class="flex justify-between hidden" id="m-discount-row">
                        <span class="text-label-sm text-tertiary flex items-center gap-xs">
                            <span class="material-symbols-outlined text-[12px]">local_offer</span> Discount
                        </span>
                        <span class="text-label-sm text-tertiary font-semibold" id="m-discount">—</span>
                    </div>
                    <div class="flex justify-between items-center pt-xs border-t border-outline-variant/20">
                        <span class="text-body-sm font-bold text-on-surface">Total Paid</span>
                        <span class="text-[17px] font-extrabold text-primary" id="m-total">—</span>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div id="m-notes-block" class="hidden bg-surface rounded-2xl border border-outline-variant/30 overflow-hidden">
                <div class="flex items-center gap-xs px-md py-sm bg-surface-container-low border-b border-outline-variant/20">
                    <span class="material-symbols-outlined text-on-surface-variant text-[15px]">sticky_note_2</span>
                    <p class="text-[10px] font-bold text-on-surface uppercase tracking-widest">Notes</p>
                </div>
                <p class="p-md text-body-sm text-on-surface" id="m-notes"></p>
            </div>

        </div>

        {{-- ── Footer ──────────────────────────────────────────────────────── --}}
        <div class="flex justify-end px-md py-sm border-t border-outline-variant/20 flex-shrink-0 bg-surface">
            <button onclick="closeOrderModal()"
                class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-md py-xs rounded-xl text-label-sm font-semibold transition-colors">
                <span class="material-symbols-outlined text-[15px]">close</span> Close
            </button>
        </div>

    </div>
</div>

{{-- ── Loading overlay for modal ────────────────────────────────────────────── --}}
<div id="modal-loading" class="fixed inset-0 z-[60] hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/20 backdrop-blur-sm"></div>
    <div class="relative bg-surface rounded-2xl p-xl shadow-xl flex items-center gap-md">
        <span class="material-symbols-outlined text-primary text-[28px] animate-spin">progress_activity</span>
        <span class="text-body-md text-on-surface">Loading order…</span>
    </div>
</div>

{{-- ── Toast ────────────────────────────────────────────────────────────────── --}}
<div id="toast"
     class="fixed bottom-6 right-6 z-[70] hidden items-center gap-sm bg-surface-container-highest border border-outline-variant/30 rounded-2xl px-md py-sm shadow-xl text-body-sm text-on-surface transition-all">
    <span class="material-symbols-outlined text-[18px]" id="toast-icon">check_circle</span>
    <span id="toast-msg">Done.</span>
</div>

@push('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    let currentOrderId = null;

    // ── Open modal ────────────────────────────────────────────────────────────
    async function openOrderModal(id) {
        currentOrderId = id;
        showLoading(true);

        try {
            const res = await fetch(`/admin/orders/${id}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            });
            if (!res.ok) throw new Error('Failed to load order.');
            const o = await res.json();
            populateModal(o);
            showLoading(false);
            const modal = document.getElementById('order-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } catch (e) {
            showLoading(false);
            showToast('Failed to load order details.', 'error', true);
        }
    }

    // ── Status config maps ────────────────────────────────────────────────────
    const ORDER_STATUS_MAP = {
        delivered:  { color: 'bg-tertiary/10 text-tertiary',                 icon: 'done_all' },
        shipped:    { color: 'bg-primary/10 text-primary',                   icon: 'local_shipping' },
        processing: { color: 'bg-secondary/10 text-secondary',               icon: 'autorenew' },
        cancelled:  { color: 'bg-error/10 text-error',                       icon: 'cancel' },
        placed:     { color: 'bg-outline-variant/20 text-on-surface-variant', icon: 'inbox' },
    };
    const PAYMENT_STATUS_MAP = {
        paid:    { color: 'bg-tertiary/10 text-tertiary',   icon: 'check_circle' },
        failed:  { color: 'bg-error/10 text-error',         icon: 'cancel' },
        pending: { color: 'bg-secondary/10 text-secondary', icon: 'schedule' },
    };

    function setPill(pillId, labelId, value, map) {
        // header pills are always white-on-primary, just update icon + text
        const pill  = document.getElementById(pillId);
        const label = document.getElementById(labelId);
        const cfg   = map[value] ?? { color: '', icon: 'help' };
        pill.querySelector('.material-symbols-outlined').textContent = cfg.icon;
        label.textContent = value.charAt(0).toUpperCase() + value.slice(1);
    }

    function populateModal(o) {
        // Header
        document.getElementById('m-order-number').textContent = o.order_number;
        document.getElementById('m-date').textContent         = o.created_at;

        // Header pills
        setPill('m-os-pill', 'm-os-label', o.status,         ORDER_STATUS_MAP);
        setPill('m-ps-pill', 'm-ps-label', o.payment_status, PAYMENT_STATUS_MAP);

        // Method pill
        const methodIcon  = o.payment_method === 'online' ? 'credit_card' : 'payments';
        document.getElementById('m-method-icon').textContent = methodIcon;
        document.getElementById('m-method').textContent = o.payment_method.toUpperCase();

        // Status selects (sync with current values)
        document.getElementById('m-status-select').value         = o.status;
        document.getElementById('m-payment-status-select').value = o.payment_status;

        // Customer
        document.getElementById('m-avatar').textContent = o.name.charAt(0).toUpperCase();
        document.getElementById('m-name').textContent   = o.name;

        const emailEl = document.getElementById('m-email');
        emailEl.textContent = o.email;
        emailEl.href        = 'mailto:' + o.email;

        const phoneEl = document.getElementById('m-phone');
        phoneEl.textContent = o.phone;
        phoneEl.href        = 'tel:' + o.phone;

        document.getElementById('m-address').textContent = o.address;

        // Razorpay
        const rzpEl = document.getElementById('m-razorpay-info');
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
                    <span class="text-body-sm">No online payment recorded for this order.</span>
                </div>`;
        }

        // Coupon
        const couponRow = document.getElementById('m-coupon-row');
        if (o.coupon_code) {
            document.getElementById('m-coupon').textContent = o.coupon_code;
            couponRow.classList.remove('hidden');
        } else {
            couponRow.classList.add('hidden');
        }

        // Items
        const itemsEl = document.getElementById('m-items');
        itemsEl.innerHTML = '';
        if (o.items && o.items.length) {
            o.items.forEach(item => {
                const name      = item.name  ?? item.product_name ?? 'Item';
                const qty       = item.qty   ?? item.quantity ?? 1;
                const price     = item.price ?? item.unit_price ?? 0;
                const lineTotal = (parseFloat(price) * parseInt(qty)).toFixed(2);
                itemsEl.innerHTML += `
                    <div class="flex items-center justify-between bg-surface-container-low/60 hover:bg-surface-container-low rounded-xl px-md py-sm transition-colors">
                        <div class="flex items-center gap-sm">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[16px] text-primary">inventory_2</span>
                            </div>
                            <div>
                                <p class="text-body-sm text-on-surface font-semibold leading-tight">${name}</p>
                                <p class="text-label-sm text-on-surface-variant">₹${parseFloat(price).toFixed(2)} each</p>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 ml-md">
                            <span class="inline-flex items-center bg-surface-container rounded-lg px-sm py-0.5 text-label-sm text-on-surface-variant">×${qty}</span>
                            <p class="text-body-sm text-on-surface font-bold mt-0.5">₹${lineTotal}</p>
                        </div>
                    </div>`;
            });
        } else {
            itemsEl.innerHTML = '<p class="text-body-sm text-on-surface-variant/60 py-sm px-md">No items data available.</p>';
        }

        // Totals
        document.getElementById('m-subtotal').textContent = '₹' + o.subtotal;
        document.getElementById('m-total').textContent    = '₹' + o.total;

        const discRow = document.getElementById('m-discount-row');
        if (parseFloat(o.discount) > 0) {
            document.getElementById('m-discount').textContent = '-₹' + o.discount;
            discRow.classList.remove('hidden');
        } else {
            discRow.classList.add('hidden');
        }

        // Notes
        const notesBlock = document.getElementById('m-notes-block');
        if (o.notes) {
            document.getElementById('m-notes').textContent = o.notes;
            notesBlock.classList.remove('hidden');
        } else {
            notesBlock.classList.add('hidden');
        }
    }

    function closeOrderModal() {
        const modal = document.getElementById('order-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        currentOrderId = null;
    }

    // ── Update order status ───────────────────────────────────────────────────
    async function updateOrderStatus() {
        if (!currentOrderId) return;
        const status = document.getElementById('m-status-select').value;
        try {
            const res = await fetch(`/admin/orders/${currentOrderId}/status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ status }),
            });
            const data = await res.json();
            if (!data.success) throw new Error();

            // Update modal header pill
            setPill('m-os-pill', 'm-os-label', status, ORDER_STATUS_MAP);
            // Update table row badge
            updateRowBadge('os-badge', currentOrderId, status, ORDER_STATUS_MAP);
            showToast(data.message, 'check_circle');
        } catch {
            showToast('Failed to update order status.', 'error', true);
        }
    }

    // ── Update payment status ─────────────────────────────────────────────────
    async function updatePaymentStatus() {
        if (!currentOrderId) return;
        const payment_status = document.getElementById('m-payment-status-select').value;
        try {
            const res = await fetch(`/admin/orders/${currentOrderId}/payment-status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ payment_status }),
            });
            const data = await res.json();
            if (!data.success) throw new Error();

            // Update modal header pill
            setPill('m-ps-pill', 'm-ps-label', payment_status, PAYMENT_STATUS_MAP);
            // Update table row badge
            updateRowBadge('ps-badge', currentOrderId, payment_status, PAYMENT_STATUS_MAP);
            showToast(data.message, 'check_circle');
        } catch {
            showToast('Failed to update payment status.', 'error', true);
        }
    }

    // ── Helper: update a badge cell in the table ──────────────────────────────
    function updateRowBadge(prefix, id, value, map) {
        const badge = document.getElementById(`${prefix}-${id}`);
        if (!badge) return;
        const cfg = map[value];
        if (!cfg) return;
        badge.className = `inline-flex items-center gap-xs px-sm py-0.5 rounded-full text-label-sm font-label-sm ${cfg.color}`;
        badge.innerHTML = `<span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">${cfg.icon}</span> ${value.charAt(0).toUpperCase() + value.slice(1)}`;
    }

    // ── Toast ─────────────────────────────────────────────────────────────────
    let toastTimer;
    function showToast(msg, icon = 'check_circle', isError = false) {
        const toast = document.getElementById('toast');
        document.getElementById('toast-msg').textContent  = msg;
        document.getElementById('toast-icon').textContent = icon;
        toast.classList.toggle('text-error', isError);
        toast.classList.remove('hidden');
        toast.classList.add('flex');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.add('hidden');
            toast.classList.remove('flex');
        }, 3000);
    }

    // ── Loading overlay ───────────────────────────────────────────────────────
    function showLoading(show) {
        const el = document.getElementById('modal-loading');
        el.classList.toggle('hidden', !show);
        el.classList.toggle('flex', show);
    }

    // ── Escape key to close modal ─────────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeOrderModal();
    });

    // ── Auto-dismiss flash ────────────────────────────────────────────────────
    const flash = document.getElementById('flash-success');
    if (flash) setTimeout(() => flash.style.display = 'none', 4000);
</script>
@endpush

@endsection
