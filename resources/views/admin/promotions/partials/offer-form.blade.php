<div class="bg-surface-container-lowest p-md rounded-xl shadow-sm border border-outline-variant/10 space-y-sm">
    <h3 class="font-headline-sm text-headline-sm text-on-surface">Add / Edit Offer</h3>
    <label class="block text-sm font-medium">Offer Title</label>
    <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" required />

    <label class="block text-sm font-medium">Offer Type</label>
    <select name="type" class="offer-type-select w-full rounded-lg border border-outline-variant/40 px-sm py-xs" required>
        <option value="first_order">First Order</option>
        <option value="daily_winning">Daily Winning</option>
        <option value="special_offer">Special Offer</option>
        <option value="amount_based">Amount Based</option>
    </select>

    <label class="block text-sm font-medium">Description</label>
    <textarea name="description" rows="3" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs"></textarea>

    <div class="offer-field-group" data-offer-types="first_order,daily_winning,special_offer,amount_based">
        <label class="block text-sm font-medium">Coupon Code</label>
        <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" style="text-transform:uppercase;" placeholder="e.g. SAVE20" />
    </div>

    <div class="offer-field-group" data-offer-types="special_offer,amount_based">
        <label class="block text-sm font-medium">Discount Text</label>
        <input type="text" name="discount_text" value="{{ old('discount_text') }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />
    </div>

    <div class="offer-field-group" data-offer-types="first_order,daily_winning,special_offer,amount_based">
        <label class="block text-sm font-medium">Discount Percentage</label>
        <input type="number" name="percentage" min="0" max="100" value="{{ old('percentage') }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />
    </div>

    <div class="offer-field-group" data-offer-types="amount_based">
        <label class="block text-sm font-medium">Target Amount (₹)</label>
        <input type="number" name="target_amount" value="{{ old('target_amount') }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />
    </div>

    <div class="offer-field-group" data-offer-types="first_order,daily_winning,special_offer,amount_based">
        <label class="block text-sm font-medium">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full rounded-lg border border-outline-variant/40 px-sm py-xs" />
    </div>

    <label class="flex items-center gap-sm text-sm">
        <input type="checkbox" name="is_active" value="1" checked />
        Active
    </label>

    <label class="flex items-center gap-sm text-sm">
        <input type="checkbox" name="for_public" value="1" checked />
        Show for public
    </label>
</div>
