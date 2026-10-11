<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('festive_banners', function (Blueprint $table) {
            $table->id();

            // ── Identity ──────────────────────────────────────────────────────
            $table->string('name');                       // Internal label, e.g. "Navratri 2026"

            // ── Left / Text side ─────────────────────────────────────────────
            $table->string('tag_line')->nullable();       // Small pill above headline, e.g. "This Navratri"
            $table->string('title')->nullable();          // Main headline, e.g. "Make Wellness Your Celebration"
            $table->string('offer_label')->nullable();    // Bold label left of discount, e.g. "FESTIVE WELLNESS, NOW"
            $table->string('discount_text')->nullable();  // Big discount pill, e.g. "15% OFF"
            $table->string('sub_text')->nullable();       // Body copy line below discount
            $table->string('fine_print')->nullable();     // T&C / asterisk note at the bottom-left

            // ── CTA Button ────────────────────────────────────────────────────
            $table->string('cta_label')->default('SHOP NOW');
            $table->string('cta_url')->default('#products');

            // ── Images / Decoration ──────────────────────────────────────────
            $table->string('bg_image_path')->nullable();  // Optional full-width background image
            $table->string('product_image_path')->nullable(); // Product group image on the right
            $table->string('accent_image_path')->nullable();  // Optional festive decoration image (dancer etc.)

            // ── Styling ───────────────────────────────────────────────────────
            $table->string('bg_color_from')->default('#c0392b');  // Gradient start (rich red)
            $table->string('bg_color_to')->default('#8b1a1a');    // Gradient end (dark red)
            $table->string('text_color')->default('#ffffff');
            $table->string('discount_bg_color')->default('#ffffff');
            $table->string('discount_text_color')->default('#c0392b');

            // ── Visibility ────────────────────────────────────────────────────
            $table->boolean('is_active')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('festive_banners');
    }
};
