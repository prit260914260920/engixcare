@extends('layouts.admin')

@section('content')
@include('admin.partials.sidebar')

<main class="ml-[280px] min-h-screen flex flex-col">
    @include('admin.partials.topbar')

    <div class="p-margin-desktop space-y-gutter">
        <div class="flex flex-col gap-xs">
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Promotion Settings</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Manage promotional offers, homepage section visibility and widgets from here.</p>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-primary/20 bg-primary/10 p-sm text-primary">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-error/20 bg-error/10 p-sm text-error">
                <ul class="list-disc pl-md">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.promotions.update') }}" method="POST" class="space-y-gutter">
            @csrf
            @method('PUT')

            {{-- ── Announcement Bar ─────────────────────────────────── --}}
            <div id="announcement-bar-section" class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 space-y-sm">
                <div class="flex items-start justify-between gap-md">
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Announcement Bar</h3>
                        <p class="text-sm text-on-surface-variant mt-0.5">The top strip shown across all pages (like sale alerts, offers, shipping notices).</p>
                    </div>
                    {{-- Enable/Disable toggle --}}
                    <label class="flex items-center gap-xs cursor-pointer select-none flex-shrink-0" for="toggle_announcement_bar">
                        <span class="text-sm font-medium text-on-surface-variant">Enable</span>
                        <div class="relative">
                            <input type="checkbox" name="announcement_bar_enabled" value="1"
                                   {{ old('announcement_bar_enabled', $settings->announcement_bar_enabled) ? 'checked' : '' }}
                                   class="sr-only peer" id="toggle_announcement_bar" />
                            <div class="w-11 h-6 bg-outline-variant rounded-full peer peer-checked:bg-primary transition-colors duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform duration-200"></div>
                        </div>
                    </label>
                </div>

                <label class="block text-sm font-medium mt-sm">Bar Text <span class="text-error">*</span></label>
                <input type="text"
                       name="announcement_bar_text"
                       maxlength="500"
                       value="{{ old('announcement_bar_text', $settings->announcement_bar_text) }}"
                       placeholder="e.g. 🎉 Navratri Sale! Get 15% OFF on orders above ₹500. Use code: SAVE15"
                       class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-sm mt-xs">
                    <div>
                        <label class="block text-sm font-medium mb-xs">Background Color</label>
                        <div class="flex items-center gap-xs">
                            <input type="color"
                                   name="announcement_bar_bg_color"
                                   value="{{ old('announcement_bar_bg_color', $settings->announcement_bar_bg_color ?? '#1a1a1a') }}"
                                   class="h-9 w-14 rounded-md border border-outline-variant/40 cursor-pointer p-0.5 bg-transparent" />
                            <input type="text"
                                   id="announcement_bar_bg_color_hex"
                                   maxlength="7"
                                   value="{{ old('announcement_bar_bg_color', $settings->announcement_bar_bg_color ?? '#1a1a1a') }}"
                                   placeholder="#1a1a1a"
                                   class="flex-1 rounded-lg border border-outline-variant/40 px-sm py-xs font-mono text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-xs">Text Color</label>
                        <div class="flex items-center gap-xs">
                            <input type="color"
                                   name="announcement_bar_text_color"
                                   value="{{ old('announcement_bar_text_color', $settings->announcement_bar_text_color ?? '#ffffff') }}"
                                   class="h-9 w-14 rounded-md border border-outline-variant/40 cursor-pointer p-0.5 bg-transparent" />
                            <input type="text"
                                   id="announcement_bar_text_color_hex"
                                   maxlength="7"
                                   value="{{ old('announcement_bar_text_color', $settings->announcement_bar_text_color ?? '#ffffff') }}"
                                   placeholder="#ffffff"
                                   class="flex-1 rounded-lg border border-outline-variant/40 px-sm py-xs font-mono text-sm" />
                        </div>
                    </div>
                </div>

                <label class="block text-sm font-medium mt-xs">Link URL <span class="text-on-surface-variant text-xs font-normal">(optional — makes the bar text clickable)</span></label>
                <input type="url"
                       name="announcement_bar_link"
                       value="{{ old('announcement_bar_link', $settings->announcement_bar_link) }}"
                       placeholder="https://example.com/sale"
                       class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                {{-- Dismissible toggle --}}
                <label class="flex items-center gap-sm cursor-pointer select-none rounded-lg border border-outline-variant/30 px-md py-sm hover:bg-surface-container-low/40 transition-colors mt-xs">
                    <div class="flex-1">
                        <span class="font-semibold text-on-surface text-sm">Allow Users to Dismiss</span>
                        <p class="text-xs text-on-surface-variant mt-0.5">Shows an × close button so visitors can hide the bar.</p>
                    </div>
                    <div class="relative flex-shrink-0">
                        <input type="checkbox" name="announcement_bar_dismissible" value="1"
                               {{ old('announcement_bar_dismissible', $settings->announcement_bar_dismissible ?? true) ? 'checked' : '' }}
                               class="sr-only peer" id="toggle_announcement_dismissible" />
                        <div class="w-11 h-6 bg-outline-variant rounded-full peer peer-checked:bg-primary transition-colors duration-200"></div>
                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform duration-200"></div>
                    </div>
                </label>

                {{-- Live Preview --}}
                <div class="mt-sm">
                    <p class="text-xs font-medium text-on-surface-variant mb-xs uppercase tracking-wide">Live Preview</p>
                    <div id="announcement-preview"
                         class="relative flex items-center justify-center rounded-lg overflow-hidden min-h-[40px] px-10 py-2 text-sm font-medium text-center transition-colors"
                         style="background-color: {{ old('announcement_bar_bg_color', $settings->announcement_bar_bg_color ?? '#1a1a1a') }}; color: {{ old('announcement_bar_text_color', $settings->announcement_bar_text_color ?? '#ffffff') }};">
                        <span id="announcement-preview-text">{{ old('announcement_bar_text', $settings->announcement_bar_text) ?: 'Your announcement bar text will appear here...' }}</span>
                        <span class="absolute right-3 opacity-60 text-xs">✕</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter">
                <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 space-y-sm">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Special Offer</h3>
                    <label class="block text-sm font-medium">Heading</label>
                    <input type="text" name="special_offer_heading" value="{{ old('special_offer_heading', $settings->special_offer_heading) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Discount Text</label>
                    <input type="text" name="special_offer_discount" value="{{ old('special_offer_discount', $settings->special_offer_discount) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Coupon Code</label>
                    <input type="text" name="special_offer_coupon_code" value="{{ old('special_offer_coupon_code', $settings->special_offer_coupon_code) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Discount Percentage (%)</label>
                    <input type="number" name="special_offer_percentage" min="0" max="100" value="{{ old('special_offer_percentage', $settings->special_offer_percentage) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" placeholder="e.g. 25" />
                </div>

                <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 space-y-sm">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Floating Coupon</h3>
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text" name="floating_coupon_title" value="{{ old('floating_coupon_title', $settings->floating_coupon_title) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Code</label>
                    <input type="text" name="floating_coupon_code" value="{{ old('floating_coupon_code', $settings->floating_coupon_code) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Discount Percentage (%)</label>
                    <input type="number" name="floating_coupon_percentage" min="0" max="100" value="{{ old('floating_coupon_percentage', $settings->floating_coupon_percentage) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" placeholder="e.g. 25" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter">
                <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 space-y-sm">
                    <div class="flex items-center justify-between gap-md">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Welcome Modal</h3>
                        <label class="flex items-center gap-xs cursor-pointer select-none flex-shrink-0" for="toggle_welcome_modal">
                            <span class="text-sm font-medium text-on-surface-variant">Enable</span>
                            <div class="relative">
                                <input type="checkbox" name="welcome_modal_enabled" value="1"
                                       {{ old('welcome_modal_enabled', $settings->welcome_modal_enabled) ? 'checked' : '' }}
                                       class="sr-only peer" id="toggle_welcome_modal" />
                                <div class="w-11 h-6 bg-outline-variant rounded-full peer peer-checked:bg-primary transition-colors duration-200"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform duration-200"></div>
                            </div>
                        </label>
                    </div>
                    <p class="text-sm text-on-surface-variant">Show or hide the welcome offer popup on the homepage.</p>
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text" name="welcome_modal_title" value="{{ old('welcome_modal_title', $settings->welcome_modal_title) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Description</label>
                    <textarea name="welcome_modal_description" rows="4" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs">{{ old('welcome_modal_description', $settings->welcome_modal_description) }}</textarea>

                    <label class="block text-sm font-medium">Code</label>
                    <input type="text" name="welcome_modal_code" value="{{ old('welcome_modal_code', $settings->welcome_modal_code) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Discount Percentage (%)</label>
                    <input type="number" name="welcome_modal_percentage" min="0" max="100" value="{{ old('welcome_modal_percentage', $settings->welcome_modal_percentage) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" placeholder="e.g. 15" />
                </div>

                <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 space-y-sm">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Scroll Offer Popup</h3>
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text" name="scroll_offer_title" value="{{ old('scroll_offer_title', $settings->scroll_offer_title) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Description</label>
                    <textarea name="scroll_offer_description" rows="4" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs">{{ old('scroll_offer_description', $settings->scroll_offer_description) }}</textarea>

                    <label class="block text-sm font-medium">Code</label>
                    <input type="text" name="scroll_offer_code" value="{{ old('scroll_offer_code', $settings->scroll_offer_code) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Discount Percentage (%)</label>
                    <input type="number" name="scroll_offer_percentage" min="0" max="100" value="{{ old('scroll_offer_percentage', $settings->scroll_offer_percentage) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" placeholder="e.g. 40" />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-gutter">
                <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 space-y-sm lg:col-span-2">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Spin To Win</h3>
                    <label class="block text-sm font-medium">Title</label>
                    <input type="text" name="spin_to_win_title" value="{{ old('spin_to_win_title', $settings->spin_to_win_title) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Description</label>
                    <textarea name="spin_to_win_description" rows="4" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs">{{ old('spin_to_win_description', $settings->spin_to_win_description) }}</textarea>

                    <label class="block text-sm font-medium">Code</label>
                    <input type="text" name="spin_to_win_code" value="{{ old('spin_to_win_code', $settings->spin_to_win_code) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />

                    <label class="block text-sm font-medium">Discount Percentage (%)</label>
                    <input type="number" name="spin_to_win_percentage" min="0" max="100" value="{{ old('spin_to_win_percentage', $settings->spin_to_win_percentage) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" placeholder="e.g. 20" />
                </div>
            </div>

            {{-- ── Visibility Toggles ───────────────────────────────── --}}
            <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 space-y-sm">
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Visibility Controls</h3>
                <p class="text-sm text-on-surface-variant">Toggle sections on or off on the public homepage.</p>

                <div class="flex flex-col gap-sm mt-xs">
                    {{-- Engix Club toggle --}}
                    <label class="flex items-center justify-between gap-md cursor-pointer select-none rounded-lg border border-outline-variant/30 px-md py-sm hover:bg-surface-container-low/40 transition-colors">
                        <div>
                            <span class="font-semibold text-on-surface text-sm">Engix Club Widget</span>
                            <p class="text-xs text-on-surface-variant mt-0.5">The loyalty rewards FAB button &amp; sliding panel (crown icon, bottom-right).</p>
                        </div>
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="show_engix_club" value="1"
                                   {{ old('show_engix_club', $settings->show_engix_club) ? 'checked' : '' }}
                                   class="sr-only peer" id="toggle_engix_club" />
                            <div class="w-11 h-6 bg-outline-variant rounded-full peer peer-checked:bg-primary transition-colors duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform duration-200"></div>
                        </div>
                    </label>

                    {{-- Spin to Win toggle --}}
                    <label class="flex items-center justify-between gap-md cursor-pointer select-none rounded-lg border border-outline-variant/30 px-md py-sm hover:bg-surface-container-low/40 transition-colors">
                        <div>
                            <span class="font-semibold text-on-surface text-sm">Spin to Win Widget</span>
                            <p class="text-xs text-on-surface-variant mt-0.5">The spinning wheel widget for promotional discounts and engagement.</p>
                        </div>
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="is_spin_to_win_enabled" value="1"
                                   {{ old('is_spin_to_win_enabled', $settings->is_spin_to_win_enabled) ? 'checked' : '' }}
                                   class="sr-only peer" id="toggle_spin_to_win" />
                            <div class="w-11 h-6 bg-outline-variant rounded-full peer peer-checked:bg-primary transition-colors duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform duration-200"></div>
                        </div>
                    </label>

                    {{-- Wellness Quiz toggle --}}
                    <label class="flex items-center justify-between gap-md cursor-pointer select-none rounded-lg border border-outline-variant/30 px-md py-sm hover:bg-surface-container-low/40 transition-colors">
                        <div>
                            <span class="font-semibold text-on-surface text-sm">Wellness Quiz Section</span>
                            <p class="text-xs text-on-surface-variant mt-0.5">Show or hide the personalised wellness quiz on the homepage.</p>
                        </div>
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="show_wellness_quiz" value="1"
                                   {{ old('show_wellness_quiz', $settings->show_wellness_quiz) ? 'checked' : '' }}
                                   class="sr-only peer" id="toggle_wellness_quiz" />
                            <div class="w-11 h-6 bg-outline-variant rounded-full peer peer-checked:bg-primary transition-colors duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform duration-200"></div>
                        </div>
                    </label>

                    {{-- Offer Cards toggle --}}
                    <label class="flex items-center justify-between gap-md cursor-pointer select-none rounded-lg border border-outline-variant/30 px-md py-sm hover:bg-surface-container-low/40 transition-colors">
                        <div>
                            <span class="font-semibold text-on-surface text-sm">Offer Cards Section</span>
                            <p class="text-xs text-on-surface-variant mt-0.5">"Explore Exciting Deals" offer cards grid shown below the special offer banner.</p>
                        </div>
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="show_offer_cards" value="1"
                                   {{ old('show_offer_cards', $settings->show_offer_cards) ? 'checked' : '' }}
                                   class="sr-only peer" id="toggle_offer_cards" />
                            <div class="w-11 h-6 bg-outline-variant rounded-full peer peer-checked:bg-primary transition-colors duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform duration-200"></div>
                        </div>
                    </label>

                    {{-- Lab journey section toggle --}}
                    <label class="flex items-center justify-between gap-md cursor-pointer select-none rounded-lg border border-outline-variant/30 px-md py-sm hover:bg-surface-container-low/40 transition-colors">
                        <div>
                            <span class="font-semibold text-on-surface text-sm">From Lab to You Section</span>
                            <p class="text-xs text-on-surface-variant mt-0.5">Show or hide the FSSAI-certified facilities heading and four quality steps.</p>
                        </div>
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="show_lab_journey" value="1"
                                   {{ old('show_lab_journey', $settings->show_lab_journey) ? 'checked' : '' }}
                                   class="sr-only peer" id="toggle_lab_journey" />
                            <div class="w-11 h-6 bg-outline-variant rounded-full peer peer-checked:bg-primary transition-colors duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform duration-200"></div>
                        </div>
                    </label>

                    {{-- Formulated by card toggle --}}
                    <label class="flex items-center justify-between gap-md cursor-pointer select-none rounded-lg border border-outline-variant/30 px-md py-sm hover:bg-surface-container-low/40 transition-colors">
                        <div>
                            <span class="font-semibold text-on-surface text-sm">Formulated by Card</span>
                            <p class="text-xs text-on-surface-variant mt-0.5">Show or hide the doctor profile and formulation statement below the quality steps.</p>
                        </div>
                        <div class="relative flex-shrink-0">
                            <input type="checkbox" name="show_formulated_by" value="1"
                                   {{ old('show_formulated_by', $settings->show_formulated_by) ? 'checked' : '' }}
                                   class="sr-only peer" id="toggle_formulated_by" />
                            <div class="w-11 h-6 bg-outline-variant rounded-full peer peer-checked:bg-primary transition-colors duration-200"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow peer-checked:translate-x-5 transition-transform duration-200"></div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-primary px-md py-sm text-white font-semibold">Save Settings</button>
            </div>
        </form>

    </div>
</main>

@push('scripts')
<script>
(function () {
    // ── Color picker ↔ hex text field sync ──────────────────────────────────
    function syncColorInputs(colorInputName, hexInputId) {
        var colorPicker = document.querySelector('input[name="' + colorInputName + '"]');
        var hexField    = document.getElementById(hexInputId);
        if (!colorPicker || !hexField) return;

        colorPicker.addEventListener('input', function () {
            hexField.value = colorPicker.value;
            updatePreview();
        });
        hexField.addEventListener('input', function () {
            var val = hexField.value.trim();
            if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                colorPicker.value = val;
                updatePreview();
            }
        });
    }

    syncColorInputs('announcement_bar_bg_color',   'announcement_bar_bg_color_hex');
    syncColorInputs('announcement_bar_text_color', 'announcement_bar_text_color_hex');

    // ── Live preview update ──────────────────────────────────────────────────
    function updatePreview() {
        var preview   = document.getElementById('announcement-preview');
        var previewTx = document.getElementById('announcement-preview-text');
        var textInput = document.querySelector('input[name="announcement_bar_text"]');
        var bgPicker  = document.querySelector('input[name="announcement_bar_bg_color"]');
        var txPicker  = document.querySelector('input[name="announcement_bar_text_color"]');

        if (!preview) return;
        if (bgPicker) preview.style.backgroundColor = bgPicker.value;
        if (txPicker) preview.style.color = txPicker.value;
        if (previewTx && textInput) {
            previewTx.textContent = textInput.value || 'Your announcement bar text will appear here...';
        }
    }

    var textInput = document.querySelector('input[name="announcement_bar_text"]');
    if (textInput) textInput.addEventListener('input', updatePreview);
})();
</script>
@endpush

@endsection
