<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->boolean('show_lab_journey')->default(true)->after('show_wellness_quiz');
            $table->boolean('show_formulated_by')->default(true)->after('show_lab_journey');
        });
    }

    public function down(): void
    {
        Schema::table('promotion_settings', function (Blueprint $table) {
            $table->dropColumn(['show_lab_journey', 'show_formulated_by']);
        });
    }
};
