<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPaddingTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_image_padding_is_rendered_from_product_settings(): void
    {
        $product = Product::create([
            'name' => 'Glow Capsule',
            'subtitle' => 'Daily support',
            'sku' => 'GL-001',
            'category' => 'Supplements',
            'price' => 199.99,
            'stock' => 20,
            'status' => 'Active',
            'featured' => false,
            'is_visible' => true,
            'show_stock' => true,
            'image' => [],
            'description' => 'A sample product',
            'image_padding_top' => 8,
            'image_padding_right' => 12,
            'image_padding_bottom' => 6,
            'image_padding_left' => 10,
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertSee('padding-top: 8px;', false);
        $response->assertSee('padding-right: 12px;', false);
        $response->assertSee('padding-bottom: 6px;', false);
        $response->assertSee('padding-left: 10px;', false);
    }
}
