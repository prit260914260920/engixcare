<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionSetting extends Model
{
    protected $table = 'promotion_settings';

    protected $fillable = [
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
        'is_spin_to_win_enabled',
        'spin_to_win_title',
        'spin_to_win_description',
        'spin_to_win_code',
        'spin_to_win_percentage',
        'show_engix_club',
        'show_offer_cards',
    ];

    protected $casts = [
        'special_offer_percentage'   => 'integer',
        'floating_coupon_percentage' => 'integer',
        'welcome_modal_percentage'   => 'integer',
        'scroll_offer_percentage'    => 'integer',
        'is_spin_to_win_enabled'     => 'boolean',
        'spin_to_win_percentage'     => 'integer',
        'show_engix_club'            => 'boolean',
        'show_offer_cards'           => 'boolean',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'special_offer_heading'    => 'Get 25% OFF On Your First Order',
            'special_offer_discount'   => '25% OFF',
            'special_offer_coupon_code' => 'ENGIX25',
            'floating_coupon_title'    => '25% OFF your first order',
            'floating_coupon_code'     => 'ENGIX25',
            'welcome_modal_title'      => 'Get 15% OFF Your First Order',
            'welcome_modal_description' => 'Plus free shipping, Cash on Delivery and 500 ENGIX points instantly.',
            'welcome_modal_code'       => 'WELCOME15',
            'scroll_offer_title'       => 'Save 40% right now',
            'scroll_offer_description' => 'Ends in 5 minutes. Use this code at checkout.',
            'scroll_offer_code'        => 'FLASH40',
            'is_spin_to_win_enabled'   => true,
            'spin_to_win_title'        => 'Spin To Win',
            'spin_to_win_description'  => 'Spin the wheel and win exciting discounts!',
            'spin_to_win_code'         => 'SPINWIN',
            'spin_to_win_percentage'   => 20,
            'show_engix_club'          => true,
            'show_offer_cards'         => true,
        ]);
    }
}
