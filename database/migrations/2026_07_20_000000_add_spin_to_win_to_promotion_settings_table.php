<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->boolean('is_spin_to_win_enabled')->default(true)->after('scroll_offer_percentage');
            $table->string('spin_to_win_title')->nullable()->default('Spin To Win')->after('is_spin_to_win_enabled');
            $table->string('spin_to_win_description')->nullable()->after('spin_to_win_title');
            $table->string('spin_to_win_code')->nullable()->default('SPINWIN')->after('spin_to_win_description');
            $table->integer('spin_to_win_percentage')->nullable()->default(20)->after('spin_to_win_code');
        });
    }

    public function down(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->dropColumn(['is_spin_to_win_enabled', 'spin_to_win_title', 'spin_to_win_description', 'spin_to_win_code', 'spin_to_win_percentage']);
        });
    }
};
