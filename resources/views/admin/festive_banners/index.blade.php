@extends('layouts.admin')

@section('content')
@include('admin.partials.sidebar')

<main class="ml-[280px] min-h-screen flex flex-col">
    @include('admin.partials.topbar')

    <div class="p-margin-desktop space-y-gutter">

        {{-- ── Breadcrumb & Header ───────────────────────────────────────────── --}}
        <div class="flex flex-col gap-xs">
            <nav class="flex items-center gap-xs text-on-surface-variant text-label-sm font-label-sm">
                <a class="hover:text-primary" href="/admin/dashboard">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-semibold">Festive Banners</span>
            </nav>
            <div class="flex flex-wrap items-end justify-between gap-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Festive Banners</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Create and manage promotional festival posters shown on the homepage.</p>
                </div>
                <a href="{{ route('admin.festive_banners.create') }}"
                   class="flex items-center gap-xs bg-primary text-white px-md py-sm rounded-xl font-label-md text-label-md shadow-lg shadow-primary/20 hover:bg-primary-fixed-dim transition-all">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    New Banner
                </a>
            </div>
        </div>

        {{-- ── Flash Messages ────────────────────────────────────────────────── --}}
        @if(session('success'))
            <div id="flash-success" class="rounded-2xl border border-primary/20 bg-primary/10 p-md text-sm text-primary flex items-center gap-sm">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Banners Table ─────────────────────────────────────────────────── --}}
        @if($banners->isEmpty())
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-xl flex flex-col items-center gap-md text-center shadow-sm">
                <span class="material-symbols-outlined text-[56px] text-on-surface-variant/40">image</span>
                <div>
                    <p class="font-label-lg text-label-lg text-on-surface">No festive banners yet</p>
                    <p class="text-body-sm text-on-surface-variant mt-1">Create your first banner to show a festive promotional poster on the homepage.</p>
                </div>
                <a href="{{ route('admin.festive_banners.create') }}"
                   class="flex items-center gap-xs bg-primary text-white px-md py-sm rounded-xl font-label-md text-label-md shadow-md shadow-primary/20 hover:bg-primary-fixed-dim transition-all">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Create Banner
                </a>
            </div>
        @else
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-outline-variant/30 bg-surface-container/50">
                            <th class="text-left px-md py-sm font-label-md text-label-md text-on-surface-variant">Banner</th>
                            <th class="text-left px-md py-sm font-label-md text-label-md text-on-surface-variant">Offer</th>
                            <th class="text-left px-md py-sm font-label-md text-label-md text-on-surface-variant">Dates</th>
                            <th class="text-center px-md py-sm font-label-md text-label-md text-on-surface-variant">Active</th>
                            <th class="text-right px-md py-sm font-label-md text-label-md text-on-surface-variant">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/20">
                        @foreach($banners as $banner)
                        <tr class="hover:bg-surface-container/30 transition-colors" id="banner-row-{{ $banner->id }}">

                            {{-- Preview swatch + name --}}
                            <td class="px-md py-sm">
                                <div class="flex items-center gap-sm">
                                    <div class="w-14 h-9 rounded-lg flex-shrink-0 flex items-center justify-center text-white text-xs font-bold overflow-hidden"
                                         style="{{ $banner->gradientStyle() }}">
                                        @if($banner->isImageOnly() && $banner->fullImageUrl())
                                            <img src="{{ $banner->fullImageUrl() }}" alt="" class="h-full w-full object-cover" />
                                        @elseif($banner->productImageUrl())
                                            <img src="{{ $banner->productImageUrl() }}" alt="" class="h-full w-full object-cover" />
                                        @else
                                            <span class="material-symbols-outlined text-[20px] opacity-70">image</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-label-md text-label-md text-on-surface">{{ $banner->name }}</p>
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-semibold mt-0.5
                                            {{ $banner->banner_type === 'image_only' ? 'bg-secondary/10 text-secondary' : 'bg-primary/10 text-primary' }}">
                                            <span class="material-symbols-outlined text-[10px]">{{ $banner->banner_type === 'image_only' ? 'image' : 'palette' }}</span>
                                            {{ $banner->banner_type === 'image_only' ? 'Image Upload' : 'Custom Builder' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Offer --}}
                            <td class="px-md py-sm">
                                @if($banner->discount_text)
                                    <span class="inline-flex items-center px-sm py-0.5 rounded-full text-xs font-bold"
                                          style="background-color:{{ $banner->discount_bg_color }};color:{{ $banner->discount_text_color }};">
                                        {{ $banner->discount_text }}
                                    </span>
                                @else
                                    <span class="text-on-surface-variant/50 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Dates --}}
                            <td class="px-md py-sm text-body-sm text-on-surface-variant">
                                @if($banner->start_date || $banner->end_date)
                                    <span>{{ $banner->start_date?->format('d M') ?? '∞' }}</span>
                                    <span class="text-on-surface-variant/40 mx-1">→</span>
                                    <span>{{ $banner->end_date?->format('d M Y') ?? '∞' }}</span>
                                @else
                                    <span class="opacity-50">No limit</span>
                                @endif
                            </td>

                            {{-- Toggle --}}
                            <td class="px-md py-sm text-center">
                                <button type="button"
                                    onclick="toggleBanner({{ $banner->id }}, this)"
                                    data-active="{{ $banner->is_active ? '1' : '0' }}"
                                    class="relative inline-flex items-center w-10 h-6 rounded-full transition-colors duration-200 focus:outline-none {{ $banner->is_active ? 'bg-primary' : 'bg-outline-variant/50' }}"
                                    aria-label="Toggle active">
                                    <span class="inline-block w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 {{ $banner->is_active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                </button>
                            </td>

                            {{-- Actions --}}
                            <td class="px-md py-sm text-right">
                                <div class="flex items-center justify-end gap-xs">
                                    <a href="{{ route('admin.festive_banners.edit', $banner) }}"
                                       class="w-8 h-8 flex items-center justify-center rounded-lg border border-outline-variant/30 text-on-surface-variant hover:bg-surface-variant hover:text-primary transition-colors"
                                       title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.festive_banners.destroy', $banner) }}"
                                          onsubmit="return confirm('Delete this banner?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-error/20 text-error hover:bg-error/5 transition-colors"
                                            title="Delete">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</main>

@push('scripts')
<script>
    const TOGGLE_BASE = '/admin/festive-banners/';
    const CSRF = '{{ csrf_token() }}';

    // Auto-hide flash
    const flash = document.getElementById('flash-success');
    if (flash) setTimeout(() => flash.style.display = 'none', 4000);

    async function toggleBanner(id, btn) {
        try {
            const res = await fetch(TOGGLE_BASE + id + '/toggle', {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const data = await res.json();
            if (data.success) {
                btn.dataset.active = data.is_active ? '1' : '0';
                btn.className = 'relative inline-flex items-center w-10 h-6 rounded-full transition-colors duration-200 focus:outline-none '
                    + (data.is_active ? 'bg-primary' : 'bg-outline-variant/50');
                const dot = btn.querySelector('span');
                dot.className = 'inline-block w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 '
                    + (data.is_active ? 'translate-x-5' : 'translate-x-1');
            }
        } catch (e) {
            alert('Could not toggle status. Please try again.');
        }
    }
</script>
@endpush
@endsection
