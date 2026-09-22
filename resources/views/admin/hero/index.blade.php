@extends('layouts.admin')

@section('content')
@include('admin.partials.sidebar')

<main class="ml-[280px] min-h-screen flex flex-col">
    @include('admin.partials.topbar')

    <div class="p-margin-desktop space-y-gutter">

        {{-- ── Breadcrumb & Header ─────────────────────────────────────────── --}}
        <div class="flex flex-col gap-xs">
            <nav class="flex items-center gap-xs text-on-surface-variant text-label-sm font-label-sm">
                <a class="hover:text-primary" href="/admin/dashboard">Dashboard</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-semibold">Hero Section</span>
            </nav>
            <div class="flex flex-wrap items-end justify-between gap-md">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">Hero Section</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Edit the homepage hero banner — headline, description, CTAs, minerals strip, trust badges, and image.</p>
                </div>
                <a href="/" target="_blank"
                   class="flex items-center gap-xs border border-outline-variant text-on-surface-variant hover:bg-surface-variant px-md py-sm rounded-xl font-label-md text-label-md transition-colors">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    Preview Homepage
                </a>
            </div>
        </div>

        {{-- ── Flash messages ───────────────────────────────────────────────── --}}
        @if($errors->any())
            <div class="rounded-2xl border border-error/20 bg-error-container/20 p-md text-sm text-error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif
        @if(session('success'))
            <div id="flash-success" class="rounded-2xl border border-primary/20 bg-primary/10 p-md text-sm text-primary flex items-center gap-sm">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Edit Form ────────────────────────────────────────────────────── --}}
        <form method="POST" action="{{ route('admin.hero.update') }}" class="space-y-gutter">
            @csrf
            @method('PUT')

            {{-- ── Section 1: Text Content ─────────────────────────────────── --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Text Content</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    {{-- Pill text --}}
                    <div class="md:col-span-2">
                        <label class="text-label-sm font-label-sm text-on-surface-variant">
                            Pill / Badge Text <span class="text-error">*</span>
                            <span class="font-normal opacity-60 ml-1">(small tag above the headline)</span>
                        </label>
                        <input type="text" name="pill_text" value="{{ old('pill_text', $hero->pill_text) }}" required
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>

                    {{-- Title main --}}
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">
                            Headline — Plain Part <span class="text-error">*</span>
                            <span class="font-normal opacity-60 ml-1">(e.g. "Hydrate. Restore.")</span>
                        </label>
                        <input type="text" name="title_main" value="{{ old('title_main', $hero->title_main) }}" required
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>

                    {{-- Title gradient --}}
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">
                            Headline — Gradient Part <span class="text-error">*</span>
                            <span class="font-normal opacity-60 ml-1">(shown in green, e.g. "Recharge.")</span>
                        </label>
                        <input type="text" name="title_gradient" value="{{ old('title_gradient', $hero->title_gradient) }}" required
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>

                    {{-- Subtitle / description --}}
                    <div class="md:col-span-2">
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Description / Subtitle</label>
                        <textarea name="subtitle" rows="3"
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary">{{ old('subtitle', $hero->subtitle) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ── Section 2: CTA Buttons ───────────────────────────────────── --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">CTA Buttons</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Primary Button Label <span class="text-error">*</span></label>
                        <input type="text" name="cta_primary_label" value="{{ old('cta_primary_label', $hero->cta_primary_label) }}" required
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Primary Button URL <span class="text-error">*</span></label>
                        <input type="text" name="cta_primary_url" value="{{ old('cta_primary_url', $hero->cta_primary_url) }}" required
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Secondary Button Label <span class="text-error">*</span></label>
                        <input type="text" name="cta_secondary_label" value="{{ old('cta_secondary_label', $hero->cta_secondary_label) }}" required
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Secondary Button URL <span class="text-error">*</span></label>
                        <input type="text" name="cta_secondary_url" value="{{ old('cta_secondary_url', $hero->cta_secondary_url) }}" required
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
            </div>

            {{-- ── Section 3: Minerals Strip ─────────────────────────────────── --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                <div class="flex items-center justify-between">
                    <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Minerals / Key Nutrients Strip</p>
                    <button type="button" onclick="addMineral()"
                        class="flex items-center gap-xs text-primary border border-primary/30 hover:bg-primary/5 px-sm py-1 rounded-lg text-label-sm font-label-sm transition-colors">
                        <span class="material-symbols-outlined text-[16px]">add</span> Add Row
                    </button>
                </div>

                <div id="minerals-list" class="space-y-sm">
                    @php $minerals = old('minerals', $hero->minerals ?? []); @endphp
                    @foreach($minerals as $i => $mineral)
                        <div class="mineral-row flex items-center gap-sm">
                            <input type="text" name="minerals[{{ $i }}][label]" value="{{ $mineral['label'] ?? '' }}"
                                placeholder="e.g. Chloride"
                                class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                            <input type="text" name="minerals[{{ $i }}][value]" value="{{ $mineral['value'] ?? '' }}"
                                placeholder="e.g. 220 mg"
                                class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                            <button type="button" onclick="this.closest('.mineral-row').remove(); reindexMinerals()"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-error border border-error/20 hover:bg-error/5 transition-colors flex-shrink-0">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Section 4: Trust Badges ───────────────────────────────────── --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                <div class="flex items-center justify-between">
                    <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Trust Badges</p>
                    <button type="button" onclick="addBadge()"
                        class="flex items-center gap-xs text-primary border border-primary/30 hover:bg-primary/5 px-sm py-1 rounded-lg text-label-sm font-label-sm transition-colors">
                        <span class="material-symbols-outlined text-[16px]">add</span> Add Badge
                    </button>
                </div>

                <p class="text-body-sm text-on-surface-variant -mt-sm">
                    Use any <a href="https://fontawesome.com/icons" target="_blank" class="text-primary underline">Font Awesome 6</a> class for the icon, e.g. <code class="bg-surface-container px-1 rounded text-xs">fa-solid fa-certificate</code>
                </p>

                <div id="badges-list" class="space-y-sm">
                    @php $trustBadges = old('trust_badges', $hero->trust_badges ?? []); @endphp
                    @foreach($trustBadges as $i => $badge)
                        <div class="badge-row flex items-center gap-sm">
                            <input type="text" name="trust_badges[{{ $i }}][icon]" value="{{ $badge['icon'] ?? '' }}"
                                placeholder="fa-solid fa-certificate"
                                class="w-56 flex-shrink-0 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary font-mono text-xs" />
                            <input type="text" name="trust_badges[{{ $i }}][label]" value="{{ $badge['label'] ?? '' }}"
                                placeholder="e.g. FSSAI Certified"
                                class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                            <button type="button" onclick="this.closest('.badge-row').remove(); reindexBadges()"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-error border border-error/20 hover:bg-error/5 transition-colors flex-shrink-0">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Section 5: Floating Badges (image overlay) ───────────────── --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Floating Image Badges</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Rating Badge <span class="font-normal opacity-60">(top-left of image)</span></label>
                        <input type="text" name="badge_rating" value="{{ old('badge_rating', $hero->badge_rating) }}"
                            placeholder="4.9/5 · 12k+ Reviews"
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="text-label-sm font-label-sm text-on-surface-variant">Lab Badge <span class="font-normal opacity-60">(bottom-right of image)</span></label>
                        <input type="text" name="badge_lab" value="{{ old('badge_lab', $hero->badge_lab) }}"
                            placeholder="Lab Tested"
                            class="mt-1 w-full rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
                    </div>
                </div>
            </div>

            {{-- ── Section 6: Hero Image ─────────────────────────────────────── --}}
            <div class="bg-surface-container-lowest border border-outline-variant/30 rounded-3xl p-md shadow-sm space-y-md">
                <p class="text-label-md font-label-md text-on-surface-variant uppercase tracking-wider">Hero Product Image</p>

                {{-- Current image preview --}}
                <div class="flex items-start gap-md">
                    <div class="relative w-40 h-40 rounded-2xl overflow-hidden border border-outline-variant/30 bg-surface-container flex-shrink-0" id="hero-img-wrap">
                        <img id="hero-img-preview"
                             src="{{ $hero->imageUrl() }}"
                             alt="Current hero image"
                             class="w-full h-full object-contain" />
                        <div id="hero-img-spinner" class="hidden absolute inset-0 bg-black/40 flex items-center justify-center rounded-2xl">
                            <span class="material-symbols-outlined text-white text-[32px] animate-spin">progress_activity</span>
                        </div>
                    </div>

                    <div class="flex-1">
                        <p class="text-body-sm text-on-surface-variant mb-sm">Upload a new image to replace the current one. Recommended: PNG/WEBP with transparent background, min 600×800 px.</p>

                        {{-- Drop zone --}}
                        <div id="hero-drop-zone"
                             class="relative border-2 border-dashed border-outline-variant/60 rounded-xl bg-surface hover:border-primary hover:bg-primary/5 transition-colors cursor-pointer py-6 px-md flex flex-col items-center gap-2"
                             onclick="document.getElementById('hero-file-input').click()"
                             ondragover="event.preventDefault(); this.classList.add('border-primary','bg-primary/10')"
                             ondragleave="this.classList.remove('border-primary','bg-primary/10')"
                             ondrop="handleHeroDrop(event)">
                            <span class="material-symbols-outlined text-[36px] text-on-surface-variant/50">add_photo_alternate</span>
                            <span class="text-body-sm text-on-surface-variant text-center">Drop image here or <span class="text-primary font-semibold">click to browse</span></span>
                            <span class="text-label-sm text-on-surface-variant/60">PNG, JPG, WEBP — max 4 MB</span>
                            <input type="file" id="hero-file-input" accept="image/*" class="hidden" onchange="uploadHeroImage(this.files[0])" />
                        </div>
                    </div>
                </div>

                {{-- Hidden field carries the stored path back to the server --}}
                <input type="hidden" name="image_path" id="hero-image-path" value="{{ $hero->image_path }}" />
            </div>

            {{-- ── Save button ──────────────────────────────────────────────── --}}
            <div class="flex justify-end gap-sm pb-xl">
                <a href="/admin/dashboard"
                   class="rounded-xl border border-outline-variant px-md py-sm text-sm text-on-surface-variant hover:bg-surface-variant transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="flex items-center gap-xs bg-primary text-white px-md py-sm rounded-xl font-label-md text-label-md shadow-lg shadow-primary/20 hover:bg-primary-fixed-dim hover:text-on-primary-fixed transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Save Changes
                </button>
            </div>
        </form>

    </div>
</main>

{{-- ═══ SCRIPTS ════════════════════════════════════════════════════════════════ --}}
<script>
    const HERO_UPLOAD_URL = '{{ route("admin.hero.upload-image") }}';
    const CSRF = '{{ csrf_token() }}';

    // ── Flash auto-hide ───────────────────────────────────────────────────────
    const flash = document.getElementById('flash-success');
    if (flash) setTimeout(() => flash.style.display = 'none', 4000);

    // ── Minerals: add / reindex ───────────────────────────────────────────────
    function addMineral() {
        const list  = document.getElementById('minerals-list');
        const index = list.querySelectorAll('.mineral-row').length;
        const row   = document.createElement('div');
        row.className = 'mineral-row flex items-center gap-sm';
        row.innerHTML = `
            <input type="text" name="minerals[${index}][label]" placeholder="e.g. Zinc"
                class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
            <input type="text" name="minerals[${index}][value]" placeholder="e.g. 10 mg"
                class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
            <button type="button" onclick="this.closest('.mineral-row').remove(); reindexMinerals()"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-error border border-error/20 hover:bg-error/5 transition-colors flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>`;
        list.appendChild(row);
    }

    function reindexMinerals() {
        document.querySelectorAll('#minerals-list .mineral-row').forEach((row, i) => {
            row.querySelectorAll('input').forEach(input => {
                input.name = input.name.replace(/minerals\[\d+\]/, `minerals[${i}]`);
            });
        });
    }

    // ── Trust badges: add / reindex ───────────────────────────────────────────
    function addBadge() {
        const list  = document.getElementById('badges-list');
        const index = list.querySelectorAll('.badge-row').length;
        const row   = document.createElement('div');
        row.className = 'badge-row flex items-center gap-sm';
        row.innerHTML = `
            <input type="text" name="trust_badges[${index}][icon]" placeholder="fa-solid fa-certificate"
                class="w-56 flex-shrink-0 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary font-mono text-xs" />
            <input type="text" name="trust_badges[${index}][label]" placeholder="e.g. FSSAI Certified"
                class="flex-1 rounded-lg border border-outline-variant bg-surface px-sm py-2 text-body-sm focus:border-primary focus:ring-1 focus:ring-primary" />
            <button type="button" onclick="this.closest('.badge-row').remove(); reindexBadges()"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-error border border-error/20 hover:bg-error/5 transition-colors flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>`;
        list.appendChild(row);
    }

    function reindexBadges() {
        document.querySelectorAll('#badges-list .badge-row').forEach((row, i) => {
            row.querySelectorAll('input').forEach(input => {
                input.name = input.name.replace(/trust_badges\[\d+\]/, `trust_badges[${i}]`);
            });
        });
    }

    // ── Hero image upload ─────────────────────────────────────────────────────
    function handleHeroDrop(e) {
        e.preventDefault();
        document.getElementById('hero-drop-zone').classList.remove('border-primary', 'bg-primary/10');
        const file = e.dataTransfer.files[0];
        if (file) uploadHeroImage(file);
    }

    async function uploadHeroImage(file) {
        if (!file) return;

        const spinner = document.getElementById('hero-img-spinner');
        const preview = document.getElementById('hero-img-preview');
        const pathInput = document.getElementById('hero-image-path');

        spinner.classList.remove('hidden');

        const fd = new FormData();
        fd.append('image', file);
        fd.append('_token', CSRF);

        try {
            const res  = await fetch(HERO_UPLOAD_URL, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });
            const data = await res.json();

            if (res.ok && data.path) {
                preview.src    = data.url;
                pathInput.value = data.path;
            } else {
                const msg = data.message ?? (data.errors ? Object.values(data.errors).flat().join(', ') : 'Upload failed');
                alert('Upload failed: ' + msg);
            }
        } catch (err) {
            alert('Upload error: ' + err.message);
        } finally {
            spinner.classList.add('hidden');
        }
    }
</script>
@endsection
