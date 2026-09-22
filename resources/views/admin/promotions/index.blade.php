@extends('layouts.admin')

@section('content')
@include('admin.partials.sidebar')

<main class="ml-[280px] min-h-screen flex flex-col">
    @include('admin.partials.topbar')

    <div class="p-margin-desktop space-y-gutter">
        <div class="flex flex-col gap-xs">
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Promotion Settings</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Manage the offer banner, floating coupon, welcome modal, scroll popup and spin to win widget from here.</p>
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
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Welcome Modal</h3>
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
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-primary px-md py-sm text-white font-semibold">Save Settings</button>
            </div>
        </form>

    </div>
</main>

@endsection
