<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_settings', function (Blueprint $table) {
            $table->id();
            $table->string('special_offer_heading')->default('Get 25% OFF On Your First Order');
            $table->string('special_offer_discount')->default('25% OFF');
            $table->string('special_offer_coupon_code')->default('ENGIX25');
            $table->string('floating_coupon_title')->default('25% OFF your first order');
            $table->string('floating_coupon_code')->default('ENGIX25');
            $table->string('welcome_modal_title')->default('Get 15% OFF Your First Order');
            $table->text('welcome_modal_description')->nullable();
            $table->string('welcome_modal_code')->default('WELCOME15');
            $table->string('scroll_offer_title')->default('Save 40% right now');
            $table->text('scroll_offer_description')->nullable();
            $table->string('scroll_offer_code')->default('FLASH40');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_settings');
    }
};
