<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_admin_dashboard_page_renders(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Overview');
        $response->assertSee('EngixCare Admin Dashboard');
    }

    public function test_admin_products_page_renders(): void
    {
        $response = $this->get('/admin/products');

        $response->assertStatus(200);
        $response->assertSee('Product Management');
        $response->assertSee('Add New Product');
    }
}
