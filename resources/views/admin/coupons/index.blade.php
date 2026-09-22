@extends('layouts.admin')

@section('content')
@include('admin.partials.sidebar')

<main class="ml-[280px] min-h-screen flex flex-col">
    @include('admin.partials.topbar')

    <div class="p-margin-desktop space-y-gutter">

        {{-- Header --}}
        <div class="flex flex-col gap-xs">
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Coupons</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">
                Create and manage coupon codes. Each coupon can be tied to a specific offer type and discount percentage.
            </p>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="rounded-lg border border-primary/20 bg-primary/10 p-sm text-primary">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-error/20 bg-error/10 p-sm text-error">
                {{ session('error') }}
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

        {{-- ── Widget Codes Quick Reference ──────────────────────────── --}}
        <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10">
            <div class="flex items-center justify-between mb-sm">
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Widget Coupon Codes</h3>
                    <p class="text-sm text-on-surface-variant mt-0.5">These are set in <a href="{{ route('admin.promotions.index') }}" class="text-primary underline">Offers &amp; Promotions</a> settings.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-sm">
                @php
                    $widgets = [
                        ['label' => 'Special Offer',    'code' => $settings->special_offer_coupon_code,  'pct' => $settings->special_offer_percentage],
                        ['label' => 'Floating Coupon',  'code' => $settings->floating_coupon_code,       'pct' => $settings->floating_coupon_percentage],
                        ['label' => 'Welcome Modal',    'code' => $settings->welcome_modal_code,         'pct' => $settings->welcome_modal_percentage],
                        ['label' => 'Flash / Scroll',   'code' => $settings->scroll_offer_code,          'pct' => $settings->scroll_offer_percentage],
                    ];
                @endphp
                @foreach($widgets as $w)
                    <div class="rounded-lg border border-outline-variant/30 bg-surface-container-low/40 px-md py-sm flex flex-col gap-1">
                        <span class="text-xs text-on-surface-variant uppercase tracking-wide">{{ $w['label'] }}</span>
                        <span class="font-mono font-bold text-primary text-lg tracking-widest">{{ strtoupper($w['code'] ?? '—') }}</span>
                        <span class="text-sm text-on-surface-variant">{{ $w['pct'] ?? 0 }}% off</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Add New Coupon ──────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10">
            <h3 class="font-headline-sm text-headline-sm text-on-surface mb-sm">Add New Coupon</h3>

            <form action="{{ route('admin.coupons.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-gutter">
                @csrf
                @include('admin.promotions.partials.offer-form')
                <div class="flex items-end justify-start">
                    <button type="submit" class="rounded-lg bg-primary px-md py-sm text-white font-semibold">
                        Create Coupon
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Existing Coupons Table ──────────────────────────────────── --}}
        <div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10">
            <h3 class="font-headline-sm text-headline-sm text-on-surface mb-sm">All Coupons</h3>

            @if($coupons->isEmpty())
                <p class="text-sm text-on-surface-variant">No coupons created yet. Use the form above to add one.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-outline-variant/20 text-left text-xs uppercase tracking-wide text-on-surface-variant">
                                <th class="pb-sm pr-md">Coupon Code</th>
                                <th class="pb-sm pr-md">Title</th>
                                <th class="pb-sm pr-md">Type</th>
                                <th class="pb-sm pr-md">Discount</th>
                                <th class="pb-sm pr-md">Min. Amount</th>
                                <th class="pb-sm pr-md">Status</th>
                                <th class="pb-sm pr-md">Public</th>
                                <th class="pb-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @foreach($coupons as $coupon)
                                <tr class="hover:bg-surface-container-low/30 transition-colors" id="coupon-row-{{ $coupon->id }}">
                                    <td class="py-sm pr-md">
                                        @if($coupon->coupon_code)
                                            <span class="font-mono font-bold text-primary tracking-wider bg-primary/5 rounded px-2 py-0.5">
                                                {{ strtoupper($coupon->coupon_code) }}
                                            </span>
                                        @else
                                            <span class="text-on-surface-variant italic text-xs">No code</span>
                                        @endif
                                    </td>
                                    <td class="py-sm pr-md font-medium text-on-surface">{{ $coupon->title }}</td>
                                    <td class="py-sm pr-md">
                                        <span class="text-xs uppercase rounded-full bg-secondary/10 text-secondary px-2 py-0.5">
                                            {{ ucfirst(str_replace('_', ' ', $coupon->type)) }}
                                        </span>
                                    </td>
                                    <td class="py-sm pr-md font-semibold text-primary">{{ $coupon->percentage ?? 0 }}%</td>
                                    <td class="py-sm pr-md text-on-surface-variant">
                                        {{ $coupon->target_amount ? '₹' . number_format($coupon->target_amount) : '—' }}
                                    </td>
                                    <td class="py-sm pr-md">
                                        @if($coupon->is_active)
                                            <span class="text-xs rounded-full bg-green-100 text-green-700 px-2 py-0.5 font-medium">Active</span>
                                        @else
                                            <span class="text-xs rounded-full bg-surface-variant text-on-surface-variant px-2 py-0.5">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="py-sm pr-md">
                                        @if($coupon->for_public)
                                            <span class="text-xs rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 font-medium">Yes</span>
                                        @else
                                            <span class="text-xs rounded-full bg-surface-variant text-on-surface-variant px-2 py-0.5">No</span>
                                        @endif
                                    </td>
                                    <td class="py-sm">
                                        <div class="flex gap-2">
                                            <button type="button"
                                                    onclick="toggleEditForm({{ $coupon->id }})"
                                                    class="text-xs rounded-lg border border-outline-variant/40 px-sm py-1 hover:bg-surface-variant/30 transition-colors">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.coupons.delete', $coupon) }}" method="POST"
                                                  onsubmit="return confirm('Delete coupon {{ strtoupper($coupon->coupon_code ?? $coupon->title) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-xs rounded-lg border border-error/30 text-error px-sm py-1 hover:bg-error/5 transition-colors">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Inline edit row --}}
                                <tr id="edit-form-{{ $coupon->id }}" class="hidden bg-surface-container-low/20">
                                    <td colspan="8" class="py-md px-sm">
                                        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST"
                                              class="grid grid-cols-1 lg:grid-cols-2 gap-gutter">
                                            @csrf
                                            @method('PUT')

                                            <div class="bg-surface-container-lowest p-md rounded-xl border border-outline-variant/10 space-y-sm">
                                                <h4 class="font-semibold text-on-surface text-sm">Edit: {{ $coupon->title }}</h4>

                                                <label class="block text-sm font-medium">Offer Title</label>
                                                <input type="text" name="title" value="{{ old('title', $coupon->title) }}"
                                                       class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" required />

                                                <label class="block text-sm font-medium">Offer Type</label>
                                                <select name="type" class="offer-type-select w-full rounded-lg border border-outline-variant/40 px-sm py-xs" required>
                                                    <option value="first_order"   {{ $coupon->type === 'first_order'   ? 'selected' : '' }}>First Order</option>
                                                    <option value="daily_winning" {{ $coupon->type === 'daily_winning' ? 'selected' : '' }}>Daily Winning</option>
                                                    <option value="special_offer" {{ $coupon->type === 'special_offer' ? 'selected' : '' }}>Special Offer</option>
                                                    <option value="amount_based"  {{ $coupon->type === 'amount_based'  ? 'selected' : '' }}>Amount Based</option>
                                                </select>

                                                <label class="block text-sm font-medium">Description</label>
                                                <textarea name="description" rows="2"
                                                          class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs">{{ old('description', $coupon->description) }}</textarea>

                                                <div class="offer-field-group" data-offer-types="first_order,daily_winning,special_offer,amount_based">
                                                    <label class="block text-sm font-medium">Coupon Code</label>
                                                    <input type="text" name="coupon_code"
                                                           value="{{ old('coupon_code', $coupon->coupon_code) }}"
                                                           class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs"
                                                           style="text-transform:uppercase;" placeholder="e.g. SAVE20" />
                                                </div>

                                                <div class="offer-field-group" data-offer-types="special_offer,amount_based">
                                                    <label class="block text-sm font-medium">Discount Text</label>
                                                    <input type="text" name="discount_text"
                                                           value="{{ old('discount_text', $coupon->discount_text) }}"
                                                           class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />
                                                </div>

                                                <div class="offer-field-group" data-offer-types="first_order,daily_winning,special_offer,amount_based">
                                                    <label class="block text-sm font-medium">Discount Percentage</label>
                                                    <input type="number" name="percentage" min="0" max="100"
                                                           value="{{ old('percentage', $coupon->percentage) }}"
                                                           class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />
                                                </div>

                                                <div class="offer-field-group" data-offer-types="amount_based">
                                                    <label class="block text-sm font-medium">Target Amount (₹)</label>
                                                    <input type="number" name="target_amount"
                                                           value="{{ old('target_amount', $coupon->target_amount) }}"
                                                           class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />
                                                </div>

                                                <div class="offer-field-group" data-offer-types="first_order,daily_winning,special_offer,amount_based">
                                                    <label class="block text-sm font-medium">Sort Order</label>
                                                    <input type="number" name="sort_order"
                                                           value="{{ old('sort_order', $coupon->sort_order) }}"
                                                           class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />
                                                </div>

                                                <label class="flex items-center gap-sm text-sm">
                                                    <input type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }} />
                                                    Active
                                                </label>

                                                <label class="flex items-center gap-sm text-sm">
                                                    <input type="checkbox" name="for_public" value="1" {{ $coupon->for_public ? 'checked' : '' }} />
                                                    Show for public
                                                </label>

                                                <div class="flex gap-2 pt-xs">
                                                    <button type="submit"
                                                            class="rounded-lg bg-primary px-md py-sm text-white font-semibold text-sm">
                                                        Save Changes
                                                    </button>
                                                    <button type="button"
                                                            onclick="toggleEditForm({{ $coupon->id }})"
                                                            class="rounded-lg border border-outline-variant/40 px-md py-sm text-sm hover:bg-surface-variant/30 transition-colors">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</main>

<script>
    function toggleEditForm(id) {
        var row = document.getElementById('edit-form-' + id);
        if (row) row.classList.toggle('hidden');
    }

    // Sync offer type → show/hide conditional fields (reused from promotions page)
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('form').forEach((form) => {
            const typeSelect = form.querySelector('select[name="type"]');
            if (!typeSelect) return;

            const syncFields = () => {
                const selectedType = typeSelect.value;
                form.querySelectorAll('.offer-field-group').forEach((group) => {
                    const allowedTypes = (group.dataset.offerTypes || '').split(',').filter(Boolean);
                    const isVisible = allowedTypes.includes(selectedType);
                    group.classList.toggle('hidden', !isVisible);
                    group.querySelectorAll('input, textarea, select').forEach((input) => {
                        input.disabled = !isVisible;
                    });
                });
            };

            typeSelect.addEventListener('change', syncFields);
            syncFields();
        });

        // Auto-uppercase coupon code inputs
        document.querySelectorAll('input[name="coupon_code"]').forEach((el) => {
            el.addEventListener('input', function () {
                var pos = this.selectionStart;
                this.value = this.value.toUpperCase();
                this.setSelectionRange(pos, pos);
            });
        });
    });
</script>

@endsection
