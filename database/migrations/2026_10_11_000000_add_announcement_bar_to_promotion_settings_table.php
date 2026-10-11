<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->boolean('announcement_bar_enabled')->default(false)->after('show_offer_cards');
            $table->string('announcement_bar_text', 500)->nullable()->after('announcement_bar_enabled');
            $table->string('announcement_bar_bg_color', 20)->default('#1a1a1a')->after('announcement_bar_text');
            $table->string('announcement_bar_text_color', 20)->default('#ffffff')->after('announcement_bar_bg_color');
            $table->string('announcement_bar_link', 500)->nullable()->after('announcement_bar_text_color');
            $table->boolean('announcement_bar_dismissible')->default(true)->after('announcement_bar_link');
        });
    }

    public function down(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->dropColumn([
                'announcement_bar_enabled',
                'announcement_bar_text',
                'announcement_bar_bg_color',
                'announcement_bar_text_color',
                'announcement_bar_link',
                'announcement_bar_dismissible',
            ]);
        });
    }
};
