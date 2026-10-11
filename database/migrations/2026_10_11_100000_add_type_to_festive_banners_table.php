<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('festive_banners', function (Blueprint $table) {
            // 'custom'     = builder se design karo (existing behaviour)
            // 'image_only' = seedha ek image upload karo, full width dikhao
            $table->string('banner_type')->default('custom')->after('name');

            // Full-width image path for 'image_only' type
            $table->string('full_image_path')->nullable()->after('accent_image_path');

            // Optional click URL for image_only banners
            $table->string('full_image_url')->nullable()->after('full_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('festive_banners', function (Blueprint $table) {
            $table->dropColumn(['banner_type', 'full_image_path', 'full_image_url']);
        });
    }
};
