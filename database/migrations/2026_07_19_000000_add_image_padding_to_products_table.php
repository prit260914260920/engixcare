<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('image_padding_top')->default(0)->after('image');
            $table->unsignedInteger('image_padding_right')->default(0)->after('image_padding_top');
            $table->unsignedInteger('image_padding_bottom')->default(0)->after('image_padding_right');
            $table->unsignedInteger('image_padding_left')->default(0)->after('image_padding_bottom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image_padding_top', 'image_padding_right', 'image_padding_bottom', 'image_padding_left']);
        });
    }
};
