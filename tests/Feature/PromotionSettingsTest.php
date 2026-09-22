<?php

namespace Tests\Feature;

use App\Models\PromotionOffer;
use App\Models\PromotionSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionSettingsTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_promotions_page_renders(): void
    {
        $response = $this->get('/admin/promotions');

        $response->assertStatus(200);
        $response->assertSee('Promotion Settings');
        $response->assertSee('Special Offer');
    }

    public function test_home_page_uses_dynamic_promotion_settings(): void
    {
        PromotionSetting::query()->truncate();

        PromotionSetting::create([
            'special_offer_heading' => 'Get 40% OFF On Your First Order',
            'special_offer_discount' => '40% OFF',
            'special_offer_coupon_code' => 'ENGIX40',
            'floating_coupon_title' => '40% OFF your first order',
            'floating_coupon_code' => 'ENGIX40',
            'welcome_modal_title' => 'Get 20% OFF Your First Order',
            'welcome_modal_description' => 'Free shipping and bonus points.',
            'welcome_modal_code' => 'WELCOME20',
            'scroll_offer_title' => 'Save 30% right now',
            'scroll_offer_description' => 'Ends in 10 minutes.',
            'scroll_offer_code' => 'FLASH30',
        ]);

        PromotionOffer::create([
            'title' => 'First Order Offer',
            'type' => 'first_order',
            'description' => 'Get a bonus coupon on your first order.',
            'discount_text' => '10% OFF',
            'coupon_code' => 'FIRST10',
            'percentage' => 10,
            'target_amount' => 499,
            'is_active' => true,
            'for_public' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Get 40% OFF On Your First Order');
        $response->assertSee('ENGIX40');
        $response->assertSee('WELCOME20');
        $response->assertSee('FLASH30');
        $response->assertSee('First Order Offer');
        $response->assertSee('FIRST10');
    }

    public function test_checkout_applies_percentage_coupon_discount(): void
    {
        $user = User::factory()->create([
            'cart_data' => [
                ['name' => 'Product A', 'price' => 400, 'qty' => 1, 'img' => ''],
                ['name' => 'Product B', 'price' => 200, 'qty' => 1, 'img' => ''],
            ],
        ]);

        PromotionOffer::create([
            'title' => 'Summer Savings',
            'type' => 'special_offer',
            'description' => 'Apply the code for 15% off.',
            'discount_text' => '15% OFF',
            'coupon_code' => 'SUMMER15',
            'percentage' => 15,
            'is_active' => true,
            'for_public' => true,
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->post('/checkout', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '9876543210',
            'address_line1' => '123 Test St',
            'address_line2' => 'Apt 1',
            'city' => 'Mumbai',
            'state' => 'Maharashtra',
            'pincode' => '400001',
            'payment_method' => 'cod',
            'coupon_code' => 'SUMMER15',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'discount' => 90.00,
            'total' => 510.00,
        ]);
    }
}
