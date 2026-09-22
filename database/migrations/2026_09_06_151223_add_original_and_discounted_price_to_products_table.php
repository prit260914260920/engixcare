<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // MRP — shown with a strikethrough
            $table->decimal('original_price', 10, 2)->nullable()->after('price');
            // Final sale price — shown prominently
            $table->decimal('discounted_price', 10, 2)->nullable()->after('original_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'discounted_price']);
        });
    }
};
