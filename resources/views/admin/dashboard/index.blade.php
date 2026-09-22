@extends('layouts.admin')

@section('content')
@include('admin.partials.sidebar')

<main class="ml-[280px] min-h-screen flex flex-col bg-surface">
    @include('admin.partials.topbar')

    <div class="px-margin-desktop py-xl space-y-gutter">
        <div class="flex flex-col gap-xs">
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Dashboard Overview</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Welcome back. Here is what is happening with EngixCare today.</p>
        </div>

        {{-- ── Stats Cards ─────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-gutter">

            {{-- Total Revenue --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-primary/10 text-primary rounded-lg"><span class="material-symbols-outlined">payments</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Total Revenue</h3>
                    <p class="text-headline-md font-headline-md text-on-surface mt-1">
                        ₹{{ number_format($totalRevenue, 0) }}
                    </p>
                </div>
            </div>

            {{-- Today's Sales --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-secondary/10 text-secondary rounded-lg"><span class="material-symbols-outlined">point_of_sale</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Today's Sales</h3>
                    <p class="text-headline-md font-headline-md text-on-surface mt-1">
                        ₹{{ number_format($todaySales, 0) }}
                    </p>
                </div>
            </div>

            {{-- Total Orders --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-tertiary/10 text-tertiary rounded-lg"><span class="material-symbols-outlined">shopping_bag</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Total Orders</h3>
                    <p class="text-headline-md font-headline-md text-on-surface mt-1">
                        {{ number_format($totalOrders) }}
                    </p>
                </div>
            </div>

            {{-- Pending Orders --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-error/10 text-error rounded-lg"><span class="material-symbols-outlined">pending_actions</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Pending Orders</h3>
                    <p class="text-headline-md font-headline-md text-error mt-1">
                        {{ number_format($pendingOrders) }}
                    </p>
                </div>
            </div>

            {{-- Completed Orders --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-primary/10 text-primary rounded-lg"><span class="material-symbols-outlined">task_alt</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Completed</h3>
                    <p class="text-headline-md font-headline-md text-on-surface mt-1">
                        {{ number_format($completedOrders) }}
                    </p>
                </div>
            </div>

            {{-- Products --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-secondary/10 text-secondary rounded-lg"><span class="material-symbols-outlined">inventory</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Products</h3>
                    <p class="text-headline-md font-headline-md text-on-surface mt-1">
                        {{ number_format($totalProducts) }}
                    </p>
                </div>
            </div>

            {{-- Total Users --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-tertiary/10 text-tertiary rounded-lg"><span class="material-symbols-outlined">group</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Total Users</h3>
                    <p class="text-headline-md font-headline-md text-on-surface mt-1">
                        {{ number_format($totalUsers) }}
                    </p>
                </div>
            </div>

            {{-- Offers --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-secondary/10 text-secondary rounded-lg"><span class="material-symbols-outlined">campaign</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Offers</h3>
                    <p class="text-headline-md font-headline-md text-on-surface mt-1">
                        {{ number_format($totalOffers) }}
                    </p>
                </div>
            </div>

            {{-- Low Stock --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-error/10 text-error rounded-lg"><span class="material-symbols-outlined">warning</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Low Stock</h3>
                    <p class="text-headline-md font-headline-md text-error mt-1">
                        {{ $lowStockProducts->count() }}
                    </p>
                </div>
            </div>

            {{-- New Inquiries --}}
            <div class="dashboard-card">
                <div class="flex justify-between items-start">
                    <span class="p-xs bg-primary/10 text-primary rounded-lg"><span class="material-symbols-outlined">contact_support</span></span>
                </div>
                <div class="mt-md">
                    <h3 class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">New Inquiries</h3>
                    <p class="text-headline-md font-headline-md text-primary mt-1">
                        {{ number_format($newInquiries) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Charts Row ──────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">

            {{-- Sales Analytics (static decorative SVG kept, labels live) --}}
            <div class="lg:col-span-2 bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 h-[400px] flex flex-col">
                <div class="flex justify-between items-center mb-md">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Sales Analytics</h3>
                </div>
                <div class="flex-1 flex items-end gap-1 px-sm overflow-hidden relative">
                    <svg class="absolute inset-0 w-full h-full pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100">
                        <defs>
                            <linearGradient id="chartGradient" x1="0%" x2="0%" y1="0%" y2="100%">
                                <stop offset="0%" style="stop-color:#006c49;stop-opacity:0.2"></stop>
                                <stop offset="100%" style="stop-color:#006c49;stop-opacity:0"></stop>
                            </linearGradient>
                        </defs>
                        <path d="M0 80 Q10 75 20 60 T40 40 T60 50 T80 20 T100 30 L100 100 L0 100 Z" fill="url(#chartGradient)"></path>
                        <path d="M0 80 Q10 75 20 60 T40 40 T60 50 T80 20 T100 30" fill="none" stroke="#006c49" stroke-width="2"></path>
                    </svg>
                    <div class="absolute inset-0 flex flex-col justify-between opacity-5 pointer-events-none py-10">
                        <div class="border-t border-on-surface w-full"></div>
                        <div class="border-t border-on-surface w-full"></div>
                        <div class="border-t border-on-surface w-full"></div>
                        <div class="border-t border-on-surface w-full"></div>
                    </div>
                </div>
                <div class="flex justify-between px-md pt-sm text-label-sm text-on-surface-variant opacity-60">
                    @foreach($monthlyRevenue as $m)
                        <span>{{ $m->label }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Monthly Revenue Bar Chart (live heights) --}}
            <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 flex flex-col h-[400px]">
                <div class="flex justify-between items-center mb-md">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Monthly Revenue</h3>
                    <span class="material-symbols-outlined text-on-surface-variant cursor-pointer">more_vert</span>
                </div>
                <div class="flex-1 flex items-end justify-between px-xs gap-2 h-full">
                    @foreach($monthlyRevenue as $i => $m)
                        @php $isLast = $loop->last; @endphp
                        <div class="w-full {{ $isLast ? 'bg-primary-container' : 'bg-secondary-container/40' }} rounded-t-md hover:bg-secondary transition-all relative group"
                             style="height: {{ max($m->pct, 4) }}%;">
                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-on-surface text-white text-[10px] px-1.5 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                ₹{{ number_format($m->revenue, 0) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between mt-sm text-label-sm text-on-surface-variant opacity-60">
                    @foreach($monthlyRevenue as $m)
                        <span>{{ $m->label }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Recent Orders + Latest Inquiries ───────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">

            {{-- Recent Orders --}}
            <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/10 overflow-hidden">
                <div class="p-md flex justify-between items-center border-b border-outline-variant/10">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Recent Orders</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-primary font-label-md text-label-md hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-surface-container-low/50 text-on-surface-variant font-label-md text-label-md">
                        <tr>
                            <th class="px-md py-sm">Order ID</th>
                            <th class="px-md py-sm">Customer</th>
                            <th class="px-md py-sm">Amount</th>
                            <th class="px-md py-sm">Status</th>
                            <th class="px-md py-sm">Date</th>
                        </tr>
                        </thead>
                        <tbody class="text-body-sm text-on-surface">
                        @forelse($recentOrders as $order)
                            @php
                                $statusColor = match($order->status) {
                                    'delivered'  => 'bg-primary/10 text-primary',
                                    'pending'    => 'bg-tertiary-container/20 text-tertiary',
                                    'processing' => 'bg-secondary/10 text-secondary',
                                    'shipped'    => 'bg-secondary/10 text-secondary',
                                    'cancelled'  => 'bg-error/10 text-error',
                                    default      => 'bg-surface-variant text-on-surface-variant',
                                };
                            @endphp
                            <tr class="border-b border-outline-variant/5 hover:bg-surface-variant/20 transition-colors">
                                <td class="px-md py-sm font-medium">{{ $order->order_number }}</td>
                                <td class="px-md py-sm">{{ $order->name }}</td>
                                <td class="px-md py-sm font-bold">₹{{ number_format($order->total, 2) }}</td>
                                <td class="px-md py-sm">
                                    <span class="px-2 py-1 {{ $statusColor }} text-[10px] font-bold rounded-full uppercase">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-md py-sm opacity-70">{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-md py-lg text-center text-on-surface-variant opacity-60">No orders yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Latest Inquiries --}}
            <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 flex flex-col">
                <div class="flex justify-between items-center mb-md">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Latest Inquiries</h3>
                    <span class="p-1 bg-primary/10 text-primary rounded-full text-label-sm font-bold px-2">{{ $newInquiries }} New</span>
                </div>
                <div class="space-y-sm">
                    @forelse($latestInquiries as $inquiry)
                        <div class="p-sm bg-surface-container-low rounded-lg border border-outline-variant/5">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-label-md font-bold text-on-surface">{{ $inquiry->name }}</span>
                                <span class="text-[10px] text-on-surface-variant opacity-60">{{ $inquiry->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-body-sm text-on-surface-variant line-clamp-1 italic">"{{ $inquiry->message }}"</p>
                            <div class="flex justify-end mt-2">
                                <a href="mailto:{{ $inquiry->email }}" class="text-primary font-label-sm text-label-sm hover:underline">Reply</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-body-sm text-on-surface-variant opacity-60 text-center py-md">No inquiries yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ── Top Selling + Low Stock ─────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter">

            {{-- Top Selling Products --}}
            <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10">
                <div class="flex justify-between items-center mb-md">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Top Selling Products</h3>
                </div>
                <div class="space-y-md">
                    @forelse($topProducts as $entry)
                        @php
                            $product = $entry->product;
                            $images  = is_array($product->image) ? $product->image : [];
                            $thumb   = $images[0] ?? null;
                        @endphp
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-md">
                                <div class="w-12 h-12 rounded-lg bg-surface-variant flex items-center justify-center overflow-hidden border border-outline-variant/10 flex-shrink-0">
                                    @if($thumb)
                                        <img class="w-full h-full object-cover"
                                             src="{{ asset('storage/' . $thumb) }}"
                                             alt="{{ $product->name }}"/>
                                    @else
                                        <span class="material-symbols-outlined text-on-surface-variant opacity-40">image_not_supported</span>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="font-label-md text-label-md text-on-surface group-hover:text-primary transition-colors">{{ $product->name }}</h4>
                                    <p class="text-[10px] text-on-surface-variant">{{ $product->category ?? 'Uncategorised' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-body-sm font-bold text-on-surface">{{ number_format($entry->total_sold) }} Sold</p>
                                <p class="text-[10px] text-primary font-bold">+₹{{ number_format($entry->total_revenue, 0) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-body-sm text-on-surface-variant opacity-60 text-center py-md">No sales data yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Low Stock Alert --}}
            <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-error/10 border-dashed bg-error/5">
                <div class="flex justify-between items-center mb-md">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-error">warning</span>
                        <h3 class="font-headline-sm text-headline-sm text-error">Low Stock Alert</h3>
                    </div>
                    <a href="{{ route('admin.products.index') }}"
                       class="bg-error text-on-error px-3 py-1 rounded-md text-label-sm font-bold hover:bg-error/90 transition-colors">
                        Manage
                    </a>
                </div>
                <div class="space-y-sm">
                    @forelse($lowStockProducts as $product)
                        @php
                            if ($product->stock <= 0) {
                                $urgency = ['label' => 'Out of Stock', 'class' => 'text-error'];
                            } elseif ($product->stock <= 3) {
                                $urgency = ['label' => 'Critical', 'class' => 'text-error'];
                            } elseif ($product->stock <= 6) {
                                $urgency = ['label' => 'Urgent', 'class' => 'text-error'];
                            } else {
                                $urgency = ['label' => 'Warning', 'class' => 'text-tertiary'];
                            }
                        @endphp
                        <div class="flex items-center justify-between p-sm bg-white/60 rounded-lg shadow-sm border border-outline-variant/10">
                            <div class="flex items-center gap-sm">
                                <div class="w-10 h-10 rounded bg-surface-variant flex-shrink-0 overflow-hidden">
                                    @php $images = is_array($product->image) ? $product->image : []; @endphp
                                    @if(!empty($images[0]))
                                        <img src="{{ asset('storage/' . $images[0]) }}"
                                             class="w-full h-full object-cover"
                                             alt="{{ $product->name }}"/>
                                    @endif
                                </div>
                                <div>
                                    <h4 class="text-label-md font-bold text-on-surface">{{ $product->name }}</h4>
                                    <p class="text-[10px] text-on-surface-variant">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-body-sm font-bold {{ $urgency['class'] }}">
                                    {{ $product->stock }} Units Left
                                </p>
                                <p class="text-[10px] text-on-surface-variant uppercase">{{ $urgency['label'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-body-sm text-on-surface-variant opacity-60 text-center py-md">All products are well-stocked.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @include('admin.partials.footer')
</main>
@endsection
