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
                <span class="text-primary font-semibold">Reviews &amp; Ratings</span>
            </nav>
            <div class="flex flex-wrap items-end justify-between gap-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Reviews &amp; Ratings</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">All customer reviews submitted for products, fetched live from the database.</p>
                </div>
                <button type="button" onclick="window.location.reload()"
                    class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-sm py-2 rounded-lg transition-colors font-label-sm">
                    <span class="material-symbols-outlined text-[18px]">refresh</span> Refresh
                </button>
            </div>
        </div>

        {{-- ── Stats Cards ──────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-md">
            {{-- Total --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-primary text-[22px]" style="font-variation-settings:'FILL' 1">rate_review</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Total Reviews</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['total'] }}</p>
                </div>
            </div>
            {{-- Visible --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-tertiary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-tertiary text-[22px]" style="font-variation-settings:'FILL' 1">visibility</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Visible</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['visible'] }}</p>
                </div>
            </div>
            {{-- Hidden --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-error/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-error text-[22px]" style="font-variation-settings:'FILL' 1">visibility_off</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Hidden</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['hidden'] }}</p>
                </div>
            </div>
            {{-- Avg Rating --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md shadow-sm flex items-center gap-md">
                <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-secondary text-[22px]" style="font-variation-settings:'FILL' 1">star</span>
                </div>
                <div>
                    <p class="text-label-sm font-label-sm text-on-surface-variant">Avg Rating</p>
                    <p class="text-headline-sm font-headline-sm text-on-surface font-bold">{{ $stats['avg'] }} <span class="text-label-sm font-label-sm text-on-surface-variant">/ 5</span></p>
                </div>
            </div>
        </div>

        {{-- ── Filter Bar ───────────────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('admin.reviews.index') }}"
              class="bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-sm shadow-sm flex flex-wrap items-center gap-sm" id="filter-form">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[220px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input name="search" value="{{ request('search') }}"
                    class="w-full bg-surface border border-outline-variant/50 rounded-lg pl-10 py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary"
                    placeholder="Search name, email, review text…" type="text" />
            </div>

            {{-- Star filter --}}
            <select name="stars"
                class="bg-surface border border-outline-variant/50 rounded-lg px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary min-w-[130px]">
                <option value="">All Stars</option>
                @for($s = 5; $s >= 1; $s--)
                    <option value="{{ $s }}" {{ request('stars') == $s ? 'selected' : '' }}>{{ $s }} ★</option>
                @endfor
            </select>

            {{-- Visibility filter --}}
            <select name="visible"
                class="bg-surface border border-outline-variant/50 rounded-lg px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary min-w-[140px]">
                <option value="">All Visibility</option>
                <option value="1" {{ request('visible') === '1' ? 'selected' : '' }}>Visible Only</option>
                <option value="0" {{ request('visible') === '0' ? 'selected' : '' }}>Hidden Only</option>
            </select>

            <button type="submit"
                class="flex items-center gap-xs bg-primary text-white px-md py-2 rounded-lg font-label-sm shadow-sm shadow-primary/20 hover:bg-primary-fixed-dim transition-all">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter
            </button>

            @if(request()->hasAny(['search', 'stars', 'visible']))
                <a href="{{ route('admin.reviews.index') }}"
                    class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-sm py-2 rounded-lg transition-colors font-label-sm">
                    <span class="material-symbols-outlined text-[18px]">close</span> Clear
                </a>
            @endif
        </form>

        {{-- ── Flash / Errors ───────────────────────────────────────────────── --}}
        @if(session('success'))
            <div id="flash-success" class="rounded-2xl border border-primary/20 bg-primary/10 p-md text-sm text-primary">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Reviews Table ────────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low/50 border-b border-outline-variant/10">
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Date</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Reviewer</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Product</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Rating</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Review</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider text-center">Visible</th>
                            <th class="px-md py-md text-label-md font-label-md text-on-surface-variant uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-surface-container-low transition-colors group" id="row-{{ $review->id }}">

                            {{-- Date --}}
                            <td class="px-md py-md text-body-sm text-on-surface-variant whitespace-nowrap">
                                {{ $review->created_at->format('d M Y') }}
                                <div class="text-label-sm text-on-surface-variant/60">{{ $review->created_at->format('H:i') }}</div>
                            </td>

                            {{-- Reviewer --}}
                            <td class="px-md py-md">
                                <div class="flex items-center gap-sm">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <span class="text-label-md font-bold text-primary">{{ strtoupper(substr($review->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-label-md text-label-md text-on-surface font-semibold group-hover:text-primary transition-colors">{{ $review->name }}</p>
                                        @if($review->email)
                                            <p class="text-label-sm text-on-surface-variant/70">{{ $review->email }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Product --}}
                            <td class="px-md py-md">
                                <span class="text-body-sm text-on-surface">{{ $review->product?->name ?? '—' }}</span>
                            </td>

                            {{-- Stars --}}
                            <td class="px-md py-md whitespace-nowrap">
                                <div class="flex items-center gap-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="material-symbols-outlined text-[18px] {{ $i <= $review->stars ? 'text-yellow-400' : 'text-outline-variant/40' }}"
                                              style="{{ $i <= $review->stars ? "font-variation-settings:'FILL' 1" : '' }}">star</span>
                                    @endfor
                                    <span class="text-label-sm text-on-surface-variant ml-xs">({{ $review->stars }})</span>
                                </div>
                            </td>

                            {{-- Review body --}}
                            <td class="px-md py-md text-body-sm text-on-surface max-w-xs">
                                @if($review->title)
                                    <p class="font-semibold text-on-surface mb-xs">{{ $review->title }}</p>
                                @endif
                                <p class="line-clamp-2 text-on-surface-variant">{{ $review->body }}</p>
                            </td>

                            {{-- Visibility toggle --}}
                            <td class="px-md py-md text-center">
                                <button type="button"
                                    onclick="toggleVisibility({{ $review->id }}, this)"
                                    data-visible="{{ $review->is_visible ? '1' : '0' }}"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                           {{ $review->is_visible ? 'bg-primary' : 'bg-outline-variant/50' }}"
                                    title="{{ $review->is_visible ? 'Hide review' : 'Show review' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                                 {{ $review->is_visible ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="px-md py-md text-right">
                                <div class="flex items-center justify-end gap-xs">
                                    {{-- View detail --}}
                                    <button type="button"
                                        onclick="openReviewModal(
                                            '{{ addslashes($review->name) }}',
                                            '{{ addslashes($review->email ?? '') }}',
                                            '{{ $review->stars }}',
                                            '{{ addslashes($review->title ?? '') }}',
                                            '{{ addslashes($review->body) }}',
                                            '{{ $review->product?->name ?? '' }}',
                                            '{{ $review->created_at->format('d M Y H:i') }}'
                                        )"
                                        class="p-2 text-on-surface-variant hover:text-primary transition-colors hover:bg-surface-variant rounded-lg"
                                        title="View Full Review">
                                        <span class="material-symbols-outlined text-[20px]">open_in_new</span>
                                    </button>
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                          onsubmit="return confirm('Delete this review from {{ addslashes($review->name) }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="p-2 text-on-surface-variant hover:text-error transition-colors hover:bg-error-container/20 rounded-lg" title="Delete">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-md py-xl text-center text-on-surface-variant text-body-sm">
                                No reviews found{{ request()->hasAny(['search','stars','visible']) ? ' matching your filters' : ' yet' }}.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>

                    {{-- Pagination footer --}}
                    <tfoot>
                        <tr>
                            <td colspan="7" class="px-md py-md bg-surface-container-low/30 border-t border-outline-variant/10">
                                <div class="flex items-center justify-between flex-wrap gap-sm">
                                    <p class="text-body-sm text-on-surface-variant">
                                        @if($reviews->total() > 0)
                                            Showing <span class="font-bold text-on-surface">{{ $reviews->firstItem() }}&nbsp;&ndash;&nbsp;{{ $reviews->lastItem() }}</span>
                                            of <span class="font-bold text-on-surface">{{ $reviews->total() }}</span> reviews
                                        @else
                                            No reviews found
                                        @endif
                                    </p>
                                    @if($reviews->hasPages())
                                        <div>{{ $reviews->links() }}</div>
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

{{-- ── Review Detail Modal ──────────────────────────────────────────────────── --}}
<div id="review-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-md">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeReviewModal()"></div>
    <div class="relative bg-surface rounded-3xl shadow-2xl w-full max-w-lg p-xl space-y-md z-10">

        <div class="flex items-start justify-between gap-md">
            <div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface" id="modal-name">—</h3>
                <p class="text-body-sm text-on-surface-variant" id="modal-date">—</p>
            </div>
            <button onclick="closeReviewModal()" class="text-on-surface-variant hover:text-primary transition-colors flex-shrink-0">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        {{-- Stars --}}
        <div class="flex items-center gap-xs" id="modal-stars-row"></div>

        {{-- Product --}}
        <div class="flex items-center gap-sm">
            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">inventory_2</span>
            <span class="text-body-sm text-on-surface" id="modal-product">—</span>
        </div>

        {{-- Email --}}
        <div class="flex items-center gap-sm" id="modal-email-row">
            <span class="material-symbols-outlined text-[18px] text-primary">mail</span>
            <a id="modal-email" href="#" class="text-body-sm text-primary hover:underline">—</a>
        </div>

        {{-- Title + Body --}}
        <div class="bg-surface-container-low rounded-2xl p-md space-y-sm">
            <p id="modal-title" class="font-semibold text-on-surface hidden"></p>
            <p class="text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">Review</p>
            <p id="modal-body" class="text-body-md text-on-surface leading-relaxed">—</p>
        </div>

        <div class="flex justify-end pt-sm">
            <button onclick="closeReviewModal()"
                class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-md py-sm rounded-xl font-label-md transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

{{-- ── Toast Notification ───────────────────────────────────────────────────── --}}
<div id="toast"
     class="fixed bottom-6 right-6 z-[60] hidden items-center gap-sm bg-surface-container-highest border border-outline-variant/30 rounded-2xl px-md py-sm shadow-xl text-body-sm text-on-surface transition-all">
    <span class="material-symbols-outlined text-[18px]" id="toast-icon">check_circle</span>
    <span id="toast-msg">Done.</span>
</div>

@push('scripts')
<script>
    // ── Visibility Toggle (AJAX) ──────────────────────────────────────────────
    async function toggleVisibility(id, btn) {
        try {
            const res = await fetch(`/admin/reviews/${id}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();

            const isVisible = data.is_visible;
            btn.dataset.visible = isVisible ? '1' : '0';

            // Update toggle colour
            btn.classList.toggle('bg-primary', isVisible);
            btn.classList.toggle('bg-outline-variant/50', !isVisible);

            // Move thumb
            const thumb = btn.querySelector('span');
            thumb.classList.toggle('translate-x-6', isVisible);
            thumb.classList.toggle('translate-x-1', !isVisible);

            btn.title = isVisible ? 'Hide review' : 'Show review';

            showToast(data.message, isVisible ? 'visibility' : 'visibility_off');
        } catch (e) {
            showToast('Failed to update visibility.', 'error', true);
        }
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

    // ── Review Detail Modal ───────────────────────────────────────────────────
    function openReviewModal(name, email, stars, title, body, product, date) {
        document.getElementById('modal-name').textContent    = name;
        document.getElementById('modal-date').textContent    = date;
        document.getElementById('modal-product').textContent = product || '—';
        document.getElementById('modal-body').textContent    = body;

        // Title
        const titleEl = document.getElementById('modal-title');
        if (title) {
            titleEl.textContent = title;
            titleEl.classList.remove('hidden');
        } else {
            titleEl.classList.add('hidden');
        }

        // Email
        const emailRow = document.getElementById('modal-email-row');
        if (email) {
            document.getElementById('modal-email').textContent = email;
            document.getElementById('modal-email').href        = 'mailto:' + email;
            emailRow.classList.remove('hidden');
        } else {
            emailRow.classList.add('hidden');
        }

        // Stars
        const starsRow = document.getElementById('modal-stars-row');
        starsRow.innerHTML = '';
        const s = parseInt(stars, 10);
        for (let i = 1; i <= 5; i++) {
            const span = document.createElement('span');
            span.className = 'material-symbols-outlined text-[22px] ' + (i <= s ? 'text-yellow-400' : 'text-outline-variant/40');
            if (i <= s) span.style.fontVariationSettings = "'FILL' 1";
            span.textContent = 'star';
            starsRow.appendChild(span);
        }
        const label = document.createElement('span');
        label.className = 'text-label-sm text-on-surface-variant ml-xs';
        label.textContent = `(${s}/5)`;
        starsRow.appendChild(label);

        const modal = document.getElementById('review-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeReviewModal() {
        const modal = document.getElementById('review-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // ── Auto-dismiss flash ────────────────────────────────────────────────────
    const flash = document.getElementById('flash-success');
    if (flash) setTimeout(() => flash.style.display = 'none', 4000);
</script>
@endpush

@endsection
