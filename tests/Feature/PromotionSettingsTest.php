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
        $admin = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($admin)->get('/admin/promotions');

        $response->assertStatus(200);
        $response->assertSee('Promotion Settings');
        $response->assertSee('Special Offer');
        $response->assertSee('Welcome Modal');
        $response->assertSee('welcome_modal_enabled');
        $response->assertSee('show_articles');
        $response->assertSee('show_wellness_quiz');
        $response->assertSee('show_lab_journey');
        $response->assertSee('show_formulated_by');
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
        $response->assertDontSee('data-testid="offer-section"', false);
        $response->assertSee('WELCOME20');
        $response->assertSee('FLASH30');
        $response->assertSee('First Order Offer');
        $response->assertSee('FIRST10');
    }

    public function test_admin_can_disable_and_enable_the_welcome_offer_modal(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $settings = PromotionSetting::current();
        $promotionFields = [
            'special_offer_heading' => 'Special Offer',
            'special_offer_discount' => '25% OFF',
            'special_offer_coupon_code' => 'ENGIX25',
            'floating_coupon_title' => '25% OFF your first order',
            'floating_coupon_code' => 'ENGIX25',
            'welcome_modal_title' => 'Welcome Offer',
            'welcome_modal_description' => 'A welcome discount.',
            'welcome_modal_code' => 'WELCOME15',
            'scroll_offer_title' => 'Flash Sale',
            'scroll_offer_code' => 'FLASH40',
        ];

        $this->actingAs($admin)
            ->put(route('admin.promotions.update'), $promotionFields)
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_settings', [
            'id' => $settings->id,
            'welcome_modal_enabled' => false,
        ]);
        $this->get('/')->assertDontSee('id="welcomeModal"');

        $this->actingAs($admin)
            ->put(route('admin.promotions.update'), $promotionFields + ['welcome_modal_enabled' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_settings', [
            'id' => $settings->id,
            'welcome_modal_enabled' => true,
        ]);
        $this->get('/')->assertSee('id="welcomeModal"');
    }

    public function test_admin_can_disable_and_enable_the_wellness_quiz(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $settings = PromotionSetting::current();
        $promotionFields = [
            'special_offer_heading' => 'Special Offer',
            'special_offer_discount' => '25% OFF',
            'special_offer_coupon_code' => 'ENGIX25',
            'floating_coupon_title' => '25% OFF your first order',
            'floating_coupon_code' => 'ENGIX25',
            'welcome_modal_title' => 'Welcome Offer',
            'welcome_modal_description' => 'A welcome discount.',
            'welcome_modal_code' => 'WELCOME15',
            'scroll_offer_title' => 'Flash Sale',
            'scroll_offer_code' => 'FLASH40',
        ];

        $this->actingAs($admin)
            ->put(route('admin.promotions.update'), $promotionFields)
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_settings', [
            'id' => $settings->id,
            'show_wellness_quiz' => false,
        ]);
        $this->get('/')->assertDontSee('data-testid="quiz-section"', false);

        $this->actingAs($admin)
            ->put(route('admin.promotions.update'), $promotionFields + ['show_wellness_quiz' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_settings', [
            'id' => $settings->id,
            'show_wellness_quiz' => true,
        ]);
        $this->get('/')->assertSee('data-testid="quiz-section"', false);
    }

    public function test_admin_can_disable_and_enable_the_articles_section(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $settings = PromotionSetting::current();
        $promotionFields = [
            'special_offer_heading' => 'Special Offer',
            'special_offer_discount' => '25% OFF',
            'special_offer_coupon_code' => 'ENGIX25',
            'floating_coupon_title' => '25% OFF your first order',
            'floating_coupon_code' => 'ENGIX25',
            'welcome_modal_title' => 'Welcome Offer',
            'welcome_modal_description' => 'A welcome discount.',
            'welcome_modal_code' => 'WELCOME15',
            'scroll_offer_title' => 'Flash Sale',
            'scroll_offer_code' => 'FLASH40',
        ];

        $this->actingAs($admin)
            ->put(route('admin.promotions.update'), $promotionFields)
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_settings', [
            'id' => $settings->id,
            'show_articles' => false,
        ]);
        $this->get('/')->assertDontSee('data-testid="blog-section"', false);

        $this->actingAs($admin)
            ->put(route('admin.promotions.update'), $promotionFields + ['show_articles' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_settings', [
            'id' => $settings->id,
            'show_articles' => true,
        ]);
        $this->get('/')->assertSee('data-testid="blog-section"', false);
    }

    public function test_admin_can_manage_lab_journey_and_formulated_by_visibility_independently(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $settings = PromotionSetting::current();
        $promotionFields = [
            'special_offer_heading' => 'Special Offer',
            'special_offer_discount' => '25% OFF',
            'special_offer_coupon_code' => 'ENGIX25',
            'floating_coupon_title' => '25% OFF your first order',
            'floating_coupon_code' => 'ENGIX25',
            'welcome_modal_title' => 'Welcome Offer',
            'welcome_modal_description' => 'A welcome discount.',
            'welcome_modal_code' => 'WELCOME15',
            'scroll_offer_title' => 'Flash Sale',
            'scroll_offer_code' => 'FLASH40',
        ];

        $this->actingAs($admin)
            ->put(route('admin.promotions.update'), $promotionFields + ['show_formulated_by' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_settings', [
            'id' => $settings->id,
            'show_lab_journey' => false,
            'show_formulated_by' => true,
        ]);
        $this->get('/')
            ->assertDontSee('data-testid="lab-section"', false)
            ->assertDontSee('Formulated by');

        $this->actingAs($admin)
            ->put(route('admin.promotions.update'), $promotionFields + ['show_lab_journey' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('promotion_settings', [
            'id' => $settings->id,
            'show_lab_journey' => true,
            'show_formulated_by' => false,
        ]);
        $this->get('/')
            ->assertSee('data-testid="lab-section"', false)
            ->assertDontSee('Formulated by');

        $this->actingAs($admin)
            ->put(route('admin.promotions.update'), $promotionFields + [
                'show_lab_journey' => '1',
                'show_formulated_by' => '1',
            ])
            ->assertRedirect();

        $this->get('/')
            ->assertSee('data-testid="lab-section"', false)
            ->assertSee('Formulated by');
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
