<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('badge_label')->nullable()->after('image');   // e.g. "#1 BESTSELLER"
            $table->string('badge_color')->default('orange')->after('badge_label'); // orange|green|blue|red
            $table->decimal('rating', 3, 1)->default(0)->after('badge_color');
            $table->unsignedInteger('review_count')->default(0)->after('rating');
            $table->json('bullet_points')->nullable()->after('review_count'); // ["Drop in 200ml","2 tablets daily",...]
            $table->string('warning_text')->nullable()->after('bullet_points'); // "Contains sucralose..."
            $table->string('stock_label')->nullable()->after('warning_text');   // "Only 8 left at this price!"
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'badge_label', 'badge_color', 'rating', 'review_count',
                'bullet_points', 'warning_text', 'stock_label',
            ]);
        });
    }
};
