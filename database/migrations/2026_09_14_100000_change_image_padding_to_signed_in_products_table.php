<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('image_padding_top')->default(0)->change();
            $table->integer('image_padding_right')->default(0)->change();
            $table->integer('image_padding_bottom')->default(0)->change();
            $table->integer('image_padding_left')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('image_padding_top')->default(0)->change();
            $table->unsignedInteger('image_padding_right')->default(0)->change();
            $table->unsignedInteger('image_padding_bottom')->default(0)->change();
            $table->unsignedInteger('image_padding_left')->default(0)->change();
        });
    }
};
