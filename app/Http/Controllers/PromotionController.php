<?php

namespace App\Http\Controllers;

use App\Models\PromotionOffer;
use App\Models\PromotionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    public function index()
    {
        $settings = PromotionSetting::current();
        $offers = PromotionOffer::active()->get();

        return view('admin.promotions.index', compact('settings', 'offers'));
    }

    public function coupons()
    {
        // All offers (active + inactive) that have a coupon code set
        $coupons = PromotionOffer::orderBy('sort_order')->orderBy('id')->get();

        // Widget coupon codes from PromotionSetting
        $settings = PromotionSetting::current();

        return view('admin.coupons.index', compact('coupons', 'settings'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'special_offer_heading'      => 'required|string|max:255',
            'special_offer_discount'     => 'required|string|max:100',
            'special_offer_coupon_code'  => 'required|string|max:100',
            'special_offer_percentage'   => 'nullable|integer|min:0|max:100',
            'floating_coupon_title'      => 'required|string|max:255',
            'floating_coupon_code'       => 'required|string|max:100',
            'floating_coupon_percentage' => 'nullable|integer|min:0|max:100',
            'welcome_modal_title'        => 'required|string|max:255',
            'welcome_modal_description'  => 'nullable|string',
            'welcome_modal_code'         => 'required|string|max:100',
            'welcome_modal_percentage'   => 'nullable|integer|min:0|max:100',
            'scroll_offer_title'         => 'required|string|max:255',
            'scroll_offer_description'   => 'nullable|string',
            'scroll_offer_code'          => 'required|string|max:100',
            'scroll_offer_percentage'    => 'nullable|integer|min:0|max:100',
            'spin_to_win_title'          => 'nullable|string|max:255',
            'spin_to_win_description'    => 'nullable|string',
            'spin_to_win_code'           => 'nullable|string|max:100',
            'spin_to_win_percentage'     => 'nullable|integer|min:0|max:100',
            'show_engix_club'            => 'boolean',
            'show_offer_cards'           => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Please fix the errors below.');
        }

        $settings = PromotionSetting::current();
        $settings->update(array_merge(
            $request->only([
                'special_offer_heading',
                'special_offer_discount',
                'special_offer_coupon_code',
                'special_offer_percentage',
                'floating_coupon_title',
                'floating_coupon_code',
                'floating_coupon_percentage',
                'welcome_modal_title',
                'welcome_modal_description',
                'welcome_modal_code',
                'welcome_modal_percentage',
                'scroll_offer_title',
                'scroll_offer_description',
                'scroll_offer_code',
                'scroll_offer_percentage',
                'spin_to_win_title',
                'spin_to_win_description',
                'spin_to_win_code',
                'spin_to_win_percentage',
            ]),
            [
                // checkboxes are absent from the request when unchecked
                'show_engix_club'  => $request->boolean('show_engix_club'),
                'show_offer_cards' => $request->boolean('show_offer_cards'),
            ]
        ));

        return back()->with('success', 'Promotion settings updated successfully.');
    }

    public function storeOffer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'discount_text' => 'nullable|string|max:100',
            'coupon_code' => 'nullable|string|max:100',
            'percentage' => 'nullable|integer|min:0|max:100',
            'target_amount' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'for_public' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Please fix the offer fields below.');
        }

        PromotionOffer::create($request->only([
            'title',
            'type',
            'description',
            'discount_text',
            'coupon_code',
            'percentage',
            'target_amount',
            'is_active',
            'for_public',
            'sort_order',
        ]));

        return back()->with('success', 'Offer created successfully.');
    }

    public function updateOffer(Request $request, PromotionOffer $offer)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'discount_text' => 'nullable|string|max:100',
            'coupon_code' => 'nullable|string|max:100',
            'percentage' => 'nullable|integer|min:0|max:100',
            'target_amount' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'for_public' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Please fix the offer fields below.');
        }

        $offer->update($request->only([
            'title',
            'type',
            'description',
            'discount_text',
            'coupon_code',
            'percentage',
            'target_amount',
            'is_active',
            'for_public',
            'sort_order',
        ]));

        return back()->with('success', 'Offer updated successfully.');
    }

    public function deleteOffer(PromotionOffer $offer)
    {
        $offer->delete();

        return back()->with('success', 'Offer deleted successfully.');
    }

    /**
     * Show all public offers/coupons to users.
     */
    public function publicOffers()
    {
        // The active() scope already includes where('for_public', true) and ordering
        $offers = PromotionOffer::active()->get();

        // Compute coupon codes already used by the authenticated user (case-insensitive)
        $usedCodes = [];
        if (auth()->check()) {
            $usedCodes = \App\Models\Order::where('user_id', auth()->id())
                ->whereNotNull('coupon_code')
                ->pluck('coupon_code')
                ->map(fn($c) => strtoupper(trim($c)))->unique()->values()->all();
        }

        return view('offers.index', compact('offers', 'usedCodes'));
    }
}
