<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->boolean('show_engix_club')->default(true)->after('scroll_offer_percentage');
            $table->boolean('show_offer_cards')->default(true)->after('show_engix_club');
        });
    }

    public function down(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->dropColumn(['show_engix_club', 'show_offer_cards']);
        });
    }
};
