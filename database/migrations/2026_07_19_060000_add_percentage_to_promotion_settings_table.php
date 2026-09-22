<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('special_offer_percentage')->default(0)->after('special_offer_coupon_code');
            $table->unsignedTinyInteger('floating_coupon_percentage')->default(0)->after('floating_coupon_code');
            $table->unsignedTinyInteger('welcome_modal_percentage')->default(0)->after('welcome_modal_code');
            $table->unsignedTinyInteger('scroll_offer_percentage')->default(0)->after('scroll_offer_code');
        });
    }

    public function down(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->dropColumn([
                'special_offer_percentage',
                'floating_coupon_percentage',
                'welcome_modal_percentage',
                'scroll_offer_percentage',
            ]);
        });
    }
};
