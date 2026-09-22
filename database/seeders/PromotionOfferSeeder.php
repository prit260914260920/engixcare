<?php

namespace Database\Seeders;

use App\Models\PromotionOffer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotionOfferSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First-order welcome offer
        PromotionOffer::firstOrCreate(
            ['coupon_code' => 'ECFOF0001'],
            [
                'title'         => 'First Order Offer',
                'type'          => 'first_order',
                'description'   => '25% OFF on your first order',
                'discount_text' => null,
                'percentage'    => 25,
                'target_amount' => null,
                'is_active'     => true,
                'for_public'    => true,
                'sort_order'    => 1,
            ]
        );

        // 80% Limited time offer (amount-based)
        PromotionOffer::firstOrCreate(
            ['coupon_code' => 'ECPROMO80'],
            [
                'title'         => 'Limited Time Offer',
                'type'          => 'special_offer',
                'description'   => 'Get up to 80% off',
                'discount_text' => '80% OFF',
                'percentage'    => 0,
                'target_amount' => null,
                'is_active'     => false,
                'for_public'    => true,
                'sort_order'    => 2,
            ]
        );
    }
}
