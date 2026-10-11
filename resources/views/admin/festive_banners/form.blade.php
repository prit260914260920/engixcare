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
                <a class="hover:text-primary" href="{{ route('admin.festive_banners.index') }}">Festive Banners</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-semibold">{{ $banner ? 'Edit: '.$banner->name : 'New Banner' }}</span>
            </nav>
            <div class="flex flex-wrap items-end justify-between gap-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">
                        {{ $banner ? 'Edit Festive Banner' : 'Create Festive Banner' }}
                    </h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Design a promotional festival poster — like a Navratri, Diwali, or seasonal sale banner shown on the homepage.
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Flash / Errors ───────────────────────────────────────────────── --}}
        @if($errors->any())
            <div class="rounded-2xl border border-error/20 bg-error-container/20 p-md text-sm text-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- ────────────────────────────────────────────────────────────────── --}}
        {{-- STEP 1: Banner Type Selector (shown only on create, or always on edit) --}}
        {{-- ────────────────────────────────────────────────────────────────── --}}
        @php $currentType = old('banner_type', $banner?->banner_type ?? 'custom'); @endphp

        <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md" id="typeSelector">
            <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Choose Banner Type</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">

                {{-- Option 1: Custom Banner --}}
                <label id="card-custom"
                       class="cursor-pointer rounded-2xl border-2 p-md flex gap-md items-start transition-all {{ $currentType === 'custom' ? 'border-primary bg-primary/5' : 'border-outline-variant/40 hover:border-primary/50 hover:bg-surface-container' }}"
                       onclick="switchType('custom')">
                    <input type="radio" name="banner_type" value="custom" class="sr-only" {{ $currentType === 'custom' ? 'checked' : '' }} />
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 {{ $currentType === 'custom' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant' }}" id="icon-custom">
                        <span class="material-symbols-outlined text-[24px]">palette</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-label-lg text-label-lg text-on-surface">Custom Banner Builder</p>
                        <p class="text-body-sm text-on-surface-variant mt-0.5">Khud design karo — text, colors, product image, discount pill sab set karo. Live preview milega.</p>
                    </div>
                    <span id="check-custom" class="material-symbols-outlined text-primary text-[22px] {{ $currentType === 'custom' ? '' : 'hidden' }}">check_circle</span>
                </label>

                {{-- Option 2: Upload Image --}}
                <label id="card-image_only"
                       class="cursor-pointer rounded-2xl border-2 p-md flex gap-md items-start transition-all {{ $currentType === 'image_only' ? 'border-primary bg-primary/5' : 'border-outline-variant/40 hover:border-primary/50 hover:bg-surface-container' }}"
                       onclick="switchType('image_only')">
                    <input type="radio" name="banner_type" value="image_only" class="sr-only" {{ $currentType === 'image_only' ? 'checked' : '' }} />
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 {{ $currentType === 'image_only' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant' }}" id="icon-image_only">
                        <span class="material-symbols-outlined text-[24px]">image</span>
                    </div>
                    <div class="flex-1">
                        <p class="font-label-lg text-label-lg text-on-surface">Direct Image Upload</p>
                        <p class="text-body-sm text-on-surface-variant mt-0.5">Apna ready-made banner image seedha upload karo — full width homepage par dikhega.</p>
                    </div>
                    <span id="check-image_only" class="material-symbols-outlined text-primary text-[22px] {{ $currentType === 'image_only' ? '' : 'hidden' }}">check_circle</span>
                </label>

            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════════════ --}}
        {{-- FORM ──────────────────────────────────────────────────────────────── --}}
        {{-- ════════════════════════════════════════════════════════════════════ --}}
        <form method="POST"
              action="{{ $banner ? route('admin.festive_banners.update', $banner) : route('admin.festive_banners.store') }}"
              class="space-y-gutter">
            @csrf
            @if($banner) @method('PUT') @endif

            {{-- Hidden banner_type synced by JS --}}
            <input type="hidden" name="banner_type" id="hidden_banner_type" value="{{ $currentType }}" />

            {{-- ── Section: Identity & Visibility (shared) ────────────────── --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Identity &amp; Visibility</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Banner Name (internal) <span class="text-error">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $banner?->name) }}" required
                            placeholder="e.g. Navratri 2026"
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $banner?->sort_order ?? 0) }}" min="0"
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Start Date <span class="font-normal opacity-60">(leave blank = always)</span></label>
                        <input type="date" name="start_date" value="{{ old('start_date', $banner?->start_date?->format('Y-m-d')) }}"
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">End Date <span class="font-normal opacity-60">(leave blank = always)</span></label>
                        <input type="date" name="end_date" value="{{ old('end_date', $banner?->end_date?->format('Y-m-d')) }}"
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                    <div class="flex items-center gap-sm md:col-span-2">
                        <label class="relative inline-flex items-center cursor-pointer gap-sm">
                            <input type="hidden" name="is_active" value="0" />
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                                   {{ old('is_active', $banner?->is_active) ? 'checked' : '' }} />
                            <div class="w-10 h-6 bg-outline-variant/50 peer-checked:bg-primary rounded-full transition-colors peer-focus:ring-2 peer-focus:ring-primary/30"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                            <span class="text-label-sm font-label-sm text-on-surface ml-1">Show banner on homepage</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- SECTION: CUSTOM BANNER (builder) ──────────────────────────── --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <div id="section-custom" class="{{ $currentType !== 'custom' ? 'hidden' : '' }} space-y-gutter">

                {{-- Live Preview --}}
                <div class="rounded-3xl overflow-hidden shadow-xl border border-outline-variant/20" id="bannerPreview"
                     style="{{ $banner && $banner->banner_type === 'custom' ? $banner->gradientStyle() : 'background: linear-gradient(135deg,#c0392b 0%,#8b1a1a 100%);' }}">
                    <div class="flex flex-col md:flex-row items-center gap-6 p-6 md:p-10 min-h-[200px] relative overflow-hidden">

                        <div class="absolute top-0 left-0 w-40 h-40 rounded-full opacity-10 bg-white -translate-x-10 -translate-y-10 pointer-events-none"></div>
                        <div class="absolute bottom-0 right-1/2 w-56 h-56 rounded-full opacity-5 bg-white translate-y-16 pointer-events-none"></div>

                        {{-- Left / text side --}}
                        <div class="flex-1 z-10 text-white space-y-2">
                            <p id="preview-tagline" class="text-2xl md:text-3xl font-extrabold leading-tight">
                                {{ old('tag_line', $banner?->tag_line ?? 'This Navratri') }}
                            </p>
                            <p id="preview-title" class="text-sm md:text-base font-medium opacity-90">
                                {{ old('title', $banner?->title ?? 'Make Wellness Your Celebration') }}
                            </p>
                            <div class="flex flex-wrap items-center gap-3 mt-1">
                                <span id="preview-offer-label" class="font-bold text-base uppercase tracking-wide">
                                    {{ old('offer_label', $banner?->offer_label ?? 'FESTIVE WELLNESS, NOW') }}
                                </span>
                                <span id="preview-discount"
                                      class="inline-block rounded-lg px-4 py-1 font-extrabold text-xl md:text-2xl"
                                      style="background:{{ old('discount_bg_color', $banner?->discount_bg_color ?? '#ffffff') }};color:{{ old('discount_text_color', $banner?->discount_text_color ?? '#c0392b') }};">
                                    {{ old('discount_text', $banner?->discount_text ?? '15% OFF') }}
                                </span>
                            </div>
                            @if($banner?->sub_text || old('sub_text'))
                            <p id="preview-subtext" class="text-xs md:text-sm opacity-80 mt-1">{{ old('sub_text', $banner?->sub_text) }}</p>
                            @else
                            <p id="preview-subtext" class="text-xs md:text-sm opacity-80 mt-1 hidden"></p>
                            @endif
                            @if($banner?->fine_print || old('fine_print'))
                            <p id="preview-fineprint" class="text-[10px] opacity-60 mt-1">{{ old('fine_print', $banner?->fine_print) }}</p>
                            @else
                            <p id="preview-fineprint" class="text-[10px] opacity-60 mt-1 hidden"></p>
                            @endif
                        </div>

                        {{-- Right / product image + CTA --}}
                        <div class="flex-shrink-0 flex flex-col items-center gap-3 z-10">
                            @if($banner?->accentImageUrl())
                                <img src="{{ $banner->accentImageUrl() }}" alt="" id="preview-accent-img"
                                     class="absolute bottom-0 left-1/3 h-32 object-contain opacity-40 pointer-events-none" />
                            @else
                                <img src="" alt="" id="preview-accent-img" class="absolute bottom-0 left-1/3 h-32 object-contain opacity-40 pointer-events-none hidden" />
                            @endif

                            @if($banner?->productImageUrl())
                                <img src="{{ $banner->productImageUrl() }}" alt="Products" id="preview-product-img"
                                     class="h-32 md:h-44 object-contain drop-shadow-2xl" />
                            @else
                                <div id="preview-product-img-placeholder"
                                     class="h-32 md:h-40 w-32 md:w-40 rounded-2xl border-2 border-dashed border-white/30 flex flex-col items-center justify-center text-white/50 text-xs gap-1">
                                    <span class="material-symbols-outlined text-[36px]">image</span>
                                    <span>Product Image</span>
                                </div>
                                <img src="" alt="Products" id="preview-product-img" class="h-32 md:h-44 object-contain drop-shadow-2xl hidden" />
                            @endif

                            <a id="preview-cta"
                               class="inline-block rounded-lg px-5 py-2 text-sm font-bold shadow-lg cursor-default"
                               style="background:{{ old('discount_bg_color', $banner?->discount_bg_color ?? '#ffffff') }};color:{{ old('discount_text_color', $banner?->discount_text_color ?? '#c0392b') }};">
                                {{ old('cta_label', $banner?->cta_label ?? 'SHOP NOW') }}
                            </a>
                        </div>

                    </div>
                </div>
                <p class="text-label-sm text-on-surface-variant text-center -mt-2">↑ Live preview — updates as you type</p>

                {{-- Text Content --}}
                <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                    <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Text Content</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Tag Line / Festival Name <span class="font-normal opacity-60">(e.g. "This Navratri")</span></label>
                            <input type="text" name="tag_line" id="f_tagline" value="{{ old('tag_line', $banner?->tag_line) }}"
                                placeholder="e.g. This Navratri"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Title / Subtitle <span class="font-normal opacity-60">(e.g. "Make Wellness Your Celebration")</span></label>
                            <input type="text" name="title" id="f_title" value="{{ old('title', $banner?->title) }}"
                                placeholder="e.g. Make Wellness Your Celebration"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Offer Label <span class="font-normal opacity-60">(e.g. "FESTIVE WELLNESS, NOW")</span></label>
                            <input type="text" name="offer_label" id="f_offer_label" value="{{ old('offer_label', $banner?->offer_label) }}"
                                placeholder="e.g. FESTIVE WELLNESS, NOW"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Discount Text <span class="font-normal opacity-60">(e.g. "15% OFF")</span></label>
                            <input type="text" name="discount_text" id="f_discount" value="{{ old('discount_text', $banner?->discount_text) }}"
                                placeholder="e.g. 15% OFF"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Sub Text <span class="font-normal opacity-60">(e.g. product types or tagline)</span></label>
                            <input type="text" name="sub_text" id="f_subtext" value="{{ old('sub_text', $banner?->sub_text) }}"
                                placeholder="e.g. Tablets - Capsules - Effervescent"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Fine Print / T&amp;C <span class="font-normal opacity-60">(tiny note at bottom-left)</span></label>
                            <input type="text" name="fine_print" id="f_fineprint" value="{{ old('fine_print', $banner?->fine_print) }}"
                                placeholder="e.g. *T&C Apply · Offer not applicable on Combo Products"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                    </div>
                </div>

                {{-- CTA Button --}}
                <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                    <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">CTA Button</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Button Label <span class="text-error">*</span></label>
                            <input type="text" name="cta_label" id="f_cta_label" value="{{ old('cta_label', $banner?->cta_label ?? 'SHOP NOW') }}"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Button URL <span class="text-error">*</span></label>
                            <input type="text" name="cta_url" value="{{ old('cta_url', $banner?->cta_url ?? '#products') }}"
                                class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                        </div>
                    </div>
                </div>

                {{-- Colours --}}
                <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                    <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Colours</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-md">
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Background Gradient — Start</label>
                            <div class="mt-1 flex items-center gap-sm">
                                <input type="color" name="bg_color_from" id="f_bg_from"
                                       value="{{ old('bg_color_from', $banner?->bg_color_from ?? '#c0392b') }}"
                                       class="w-10 h-10 rounded-lg border border-outline-variant cursor-pointer p-0.5" />
                                <input type="text" id="f_bg_from_text" value="{{ old('bg_color_from', $banner?->bg_color_from ?? '#c0392b') }}"
                                       class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary" />
                            </div>
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Background Gradient — End</label>
                            <div class="mt-1 flex items-center gap-sm">
                                <input type="color" name="bg_color_to" id="f_bg_to"
                                       value="{{ old('bg_color_to', $banner?->bg_color_to ?? '#8b1a1a') }}"
                                       class="w-10 h-10 rounded-lg border border-outline-variant cursor-pointer p-0.5" />
                                <input type="text" id="f_bg_to_text" value="{{ old('bg_color_to', $banner?->bg_color_to ?? '#8b1a1a') }}"
                                       class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary" />
                            </div>
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Text Colour</label>
                            <div class="mt-1 flex items-center gap-sm">
                                <input type="color" name="text_color" id="f_text_color"
                                       value="{{ old('text_color', $banner?->text_color ?? '#ffffff') }}"
                                       class="w-10 h-10 rounded-lg border border-outline-variant cursor-pointer p-0.5" />
                                <input type="text" id="f_text_color_text" value="{{ old('text_color', $banner?->text_color ?? '#ffffff') }}"
                                       class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary" />
                            </div>
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Discount Pill — Background</label>
                            <div class="mt-1 flex items-center gap-sm">
                                <input type="color" name="discount_bg_color" id="f_disc_bg"
                                       value="{{ old('discount_bg_color', $banner?->discount_bg_color ?? '#ffffff') }}"
                                       class="w-10 h-10 rounded-lg border border-outline-variant cursor-pointer p-0.5" />
                                <input type="text" id="f_disc_bg_text" value="{{ old('discount_bg_color', $banner?->discount_bg_color ?? '#ffffff') }}"
                                       class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary" />
                            </div>
                        </div>
                        <div>
                            <label class="text-label-sm font-label-sm text-on-surface-variant">Discount Pill — Text</label>
                            <div class="mt-1 flex items-center gap-sm">
                                <input type="color" name="discount_text_color" id="f_disc_text"
                                       value="{{ old('discount_text_color', $banner?->discount_text_color ?? '#c0392b') }}"
                                       class="w-10 h-10 rounded-lg border border-outline-variant cursor-pointer p-0.5" />
                                <input type="text" id="f_disc_text_text" value="{{ old('discount_text_color', $banner?->discount_text_color ?? '#c0392b') }}"
                                       class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm font-mono focus:border-primary focus:ring-1 focus:ring-primary" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Images --}}
                <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-lg">
                    <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Images</p>

                    <div>
                        <p class="text-label-sm font-label-sm text-on-surface-variant mb-sm">
                            Product Image <span class="font-normal opacity-60">(right side — PNG transparent bg recommended, min 600px)</span>
                        </p>
                        @include('admin.festive_banners._image_uploader', [
                            'fieldName'   => 'product_image_path',
                            'previewId'   => 'prev_product',
                            'currentPath' => $banner?->product_image_path,
                            'currentUrl'  => $banner?->productImageUrl(),
                            'label'       => 'Product group image',
                        ])
                    </div>

                    <div class="border-t border-outline-variant/20 pt-md">
                        <p class="text-label-sm font-label-sm text-on-surface-variant mb-sm">
                            Accent / Decoration Image <span class="font-normal opacity-60">(optional — festive motif overlay)</span>
                        </p>
                        @include('admin.festive_banners._image_uploader', [
                            'fieldName'   => 'accent_image_path',
                            'previewId'   => 'prev_accent',
                            'currentPath' => $banner?->accent_image_path,
                            'currentUrl'  => $banner?->accentImageUrl(),
                            'label'       => 'Decoration image',
                        ])
                    </div>

                    <div class="border-t border-outline-variant/20 pt-md">
                        <p class="text-label-sm font-label-sm text-on-surface-variant mb-sm">
                            Background Image <span class="font-normal opacity-60">(optional — overrides colour gradient if set)</span>
                        </p>
                        @include('admin.festive_banners._image_uploader', [
                            'fieldName'   => 'bg_image_path',
                            'previewId'   => 'prev_bg',
                            'currentPath' => $banner?->bg_image_path,
                            'currentUrl'  => $banner?->bgImageUrl(),
                            'label'       => 'Background image',
                        ])
                    </div>
                </div>

            </div>{{-- end #section-custom --}}

            {{-- ══════════════════════════════════════════════════════════════ --}}
            {{-- SECTION: IMAGE ONLY ─────────────────────────────────────────── --}}
            {{-- ══════════════════════════════════════════════════════════════ --}}
            <div id="section-image_only" class="{{ $currentType !== 'image_only' ? 'hidden' : '' }} space-y-gutter">

                {{-- Image Only Live Preview --}}
                <div id="imageOnlyPreviewWrap" class="rounded-3xl overflow-hidden shadow-xl border border-outline-variant/20 bg-surface-container flex items-center justify-center min-h-[200px]">
                    @if($banner?->fullImageUrl())
                        <img id="preview-full-image" src="{{ $banner->fullImageUrl() }}"
                             alt="Banner preview"
                             class="w-full object-cover rounded-3xl" />
                    @else
                        <div id="preview-full-image-placeholder" class="flex flex-col items-center gap-sm text-on-surface-variant/40 py-xl">
                            <span class="material-symbols-outlined text-[56px]">add_photo_alternate</span>
                            <p class="text-body-sm">Upload your banner image to see preview here</p>
                        </div>
                        <img id="preview-full-image" src="" alt="Banner preview" class="w-full object-cover rounded-3xl hidden" />
                    @endif
                </div>
                <p class="text-label-sm text-on-surface-variant text-center -mt-2">↑ Live preview — updates when image is uploaded</p>

                {{-- Upload Section --}}
                <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                    <div>
                        <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider mb-sm">Banner Image</p>
                        <p class="text-body-sm text-on-surface-variant mb-md">
                            Apna ready-made banner seedha upload karo. Image full width par homepage par dikhega.
                            <span class="text-primary font-semibold">Recommended: 1200×400 px ya wider, JPG/PNG/WEBP.</span>
                        </p>
                        @include('admin.festive_banners._image_uploader', [
                            'fieldName'   => 'full_image_path',
                            'previewId'   => 'prev_full',
                            'currentPath' => $banner?->full_image_path,
                            'currentUrl'  => $banner?->fullImageUrl(),
                            'label'       => 'Full banner image',
                        ])
                    </div>

                    <div class="border-t border-outline-variant/20 pt-md">
                        <label class="text-label-sm font-label-sm text-on-surface-variant">
                            Click URL <span class="font-normal opacity-60">(optional — banner click karne par yahan jayega)</span>
                        </label>
                        <input type="text" name="full_image_url" value="{{ old('full_image_url', $banner?->full_image_url) }}"
                            placeholder="e.g. /products ya https://example.com/offer"
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                        <p class="text-label-xs text-on-surface-variant/60 mt-1">Blank rehne par banner clickable nahi hoga.</p>
                    </div>
                </div>

            </div>{{-- end #section-image_only --}}

            {{-- ── Save ──────────────────────────────────────────────────────── --}}
            <div class="flex justify-end gap-sm pb-xl">
                <a href="{{ route('admin.festive_banners.index') }}"
                   class="rounded-xl border border-outline-variant px-md py-sm text-sm text-on-surface-variant hover:bg-surface-variant transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="flex items-center gap-xs bg-primary text-white px-md py-sm rounded-xl font-label-md text-label-md shadow-lg shadow-primary/20 hover:bg-primary-fixed-dim transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    {{ $banner ? 'Save Changes' : 'Create Banner' }}
                </button>
            </div>
        </form>

    </div>
</main>

@push('scripts')
<script>
const UPLOAD_URL = '{{ route("admin.festive_banners.upload-image") }}';
const CSRF       = '{{ csrf_token() }}';

// ── Banner type switcher ──────────────────────────────────────────────────────
function switchType(type) {
    // Update hidden input
    document.getElementById('hidden_banner_type').value = type;

    // Also update radio buttons
    document.querySelectorAll('input[name="banner_type"]').forEach(r => {
        r.checked = (r.value === type);
    });

    const types = ['custom', 'image_only'];
    types.forEach(t => {
        const card    = document.getElementById('card-' + t);
        const icon    = document.getElementById('icon-' + t);
        const check   = document.getElementById('check-' + t);
        const section = document.getElementById('section-' + t);

        if (t === type) {
            card.classList.add('border-primary', 'bg-primary/5');
            card.classList.remove('border-outline-variant/40');
            icon.classList.add('bg-primary', 'text-white');
            icon.classList.remove('bg-surface-container', 'text-on-surface-variant');
            if (check) check.classList.remove('hidden');
            if (section) section.classList.remove('hidden');
        } else {
            card.classList.remove('border-primary', 'bg-primary/5');
            card.classList.add('border-outline-variant/40');
            icon.classList.remove('bg-primary', 'text-white');
            icon.classList.add('bg-surface-container', 'text-on-surface-variant');
            if (check) check.classList.add('hidden');
            if (section) section.classList.add('hidden');
        }
    });
}

// ── Live preview helpers ──────────────────────────────────────────────────────
function syncText(inputId, previewId, emptyHide = false) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    input.addEventListener('input', () => {
        const v = input.value.trim();
        preview.textContent = v;
        if (emptyHide) preview.classList.toggle('hidden', !v);
    });
}

function syncColor(colorInputId, textInputId, applyFn) {
    const colorInput = document.getElementById(colorInputId);
    const textInput  = document.getElementById(textInputId);
    if (!colorInput || !textInput) return;
    colorInput.addEventListener('input', () => {
        textInput.value = colorInput.value;
        applyFn(colorInput.value);
    });
    textInput.addEventListener('input', () => {
        if (/^#[0-9a-fA-F]{6}$/.test(textInput.value)) {
            colorInput.value = textInput.value;
            applyFn(textInput.value);
        }
    });
}

const preview = document.getElementById('bannerPreview');

// Text fields
syncText('f_tagline',     'preview-tagline');
syncText('f_title',       'preview-title');
syncText('f_offer_label', 'preview-offer-label');
syncText('f_discount',    'preview-discount');
syncText('f_subtext',     'preview-subtext',   true);
syncText('f_fineprint',   'preview-fineprint', true);
syncText('f_cta_label',   'preview-cta');

// Background gradient
syncColor('f_bg_from', 'f_bg_from_text', () => updateGradient());
syncColor('f_bg_to',   'f_bg_to_text',   () => updateGradient());

function updateGradient() {
    if (!preview) return;
    const from = document.getElementById('f_bg_from').value;
    const to   = document.getElementById('f_bg_to').value;
    preview.style.background = `linear-gradient(135deg, ${from} 0%, ${to} 100%)`;
}

// Discount pill colours
syncColor('f_disc_bg', 'f_disc_bg_text', (v) => {
    const d = document.getElementById('preview-discount');
    const c = document.getElementById('preview-cta');
    if (d) d.style.backgroundColor = v;
    if (c) c.style.backgroundColor = v;
});
syncColor('f_disc_text', 'f_disc_text_text', (v) => {
    const d = document.getElementById('preview-discount');
    const c = document.getElementById('preview-cta');
    if (d) d.style.color = v;
    if (c) c.style.color = v;
});

// ── Image upload ──────────────────────────────────────────────────────────────
async function uploadBannerImage(file, fieldName, previewId) {
    if (!file) return;

    const maxMB = 8;
    if (file.size > maxMB * 1024 * 1024) {
        alert(`Image must be under ${maxMB} MB.`);
        return;
    }

    const spinner = document.getElementById('spinner-' + previewId);
    if (spinner) spinner.classList.remove('hidden');

    const oldPath = document.getElementById('hidden-' + fieldName)?.value;
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', CSRF);
    if (oldPath) formData.append('old_path', oldPath);

    try {
        const res  = await fetch(UPLOAD_URL, { method: 'POST', body: formData });
        const data = await res.json();

        if (!res.ok) throw new Error(data.message || 'Upload failed');

        // Update hidden input
        const hidden = document.getElementById('hidden-' + fieldName);
        if (hidden) hidden.value = data.path;

        // Update uploader preview box
        const img = document.getElementById('img-' + previewId);
        if (img) {
            img.src = data.url;
            img.classList.remove('hidden');
        }

        // Update live banner previews
        if (fieldName === 'product_image_path') {
            const prev = document.getElementById('preview-product-img');
            const ph   = document.getElementById('preview-product-img-placeholder');
            if (prev) { prev.src = data.url; prev.classList.remove('hidden'); }
            if (ph)   ph.classList.add('hidden');
        } else if (fieldName === 'accent_image_path') {
            const prev = document.getElementById('preview-accent-img');
            if (prev) { prev.src = data.url; prev.classList.remove('hidden'); }
        } else if (fieldName === 'bg_image_path') {
            if (preview) {
                preview.style.backgroundImage = `url('${data.url}')`;
                preview.style.backgroundSize  = 'cover';
            }
        }

        // Image-only full banner preview — checked via previewId to be 100% reliable
        if (previewId === 'prev_full') {
            const fullPrev = document.getElementById('preview-full-image');
            const fullPh   = document.getElementById('preview-full-image-placeholder');
            if (fullPrev) {
                fullPrev.src = data.url;
                fullPrev.removeAttribute('hidden');
                fullPrev.classList.remove('hidden');
                fullPrev.style.cssText = 'display:block !important; width:100%; height:auto; border-radius:1.5rem;';
            }
            if (fullPh) {
                fullPh.classList.add('hidden');
                fullPh.style.display = 'none';
            }
        }

        // Show remove button
        const removeBtn = document.getElementById('remove-' + fieldName);
        if (removeBtn) removeBtn.classList.remove('hidden');

    } catch (err) {
        alert('Upload error: ' + err.message);
    } finally {
        if (spinner) spinner.classList.add('hidden');
    }
}

function removeImage(fieldName, previewId) {
    const hidden = document.getElementById('hidden-' + fieldName);
    if (hidden) hidden.value = '';

    const img = document.getElementById('img-' + previewId);
    if (img) { img.src = ''; img.classList.add('hidden'); }

    if (fieldName === 'product_image_path') {
        const prev = document.getElementById('preview-product-img');
        const ph   = document.getElementById('preview-product-img-placeholder');
        if (prev) { prev.src = ''; prev.classList.add('hidden'); }
        if (ph)   ph.classList.remove('hidden');
    } else if (fieldName === 'accent_image_path') {
        const prev = document.getElementById('preview-accent-img');
        if (prev) { prev.src = ''; prev.classList.add('hidden'); }
    } else if (fieldName === 'bg_image_path') {
        updateGradient();
        if (preview) preview.style.backgroundImage = '';
    }

    // Image-only remove — use previewId check
    if (previewId === 'prev_full') {
        const fullPrev = document.getElementById('preview-full-image');
        const fullPh   = document.getElementById('preview-full-image-placeholder');
        if (fullPrev) { fullPrev.src = ''; fullPrev.style.cssText = ''; fullPrev.classList.add('hidden'); }
        if (fullPh)   { fullPh.classList.remove('hidden'); fullPh.style.display = ''; }
    }

    const removeBtn = document.getElementById('remove-' + fieldName);
    if (removeBtn) removeBtn.classList.add('hidden');
}
</script>
@endpush
@endsection
