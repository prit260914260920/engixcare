<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();

            // Pill / badge text above the title
            $table->string('pill_text')->default('WHO-GMP & HACCP Certified · FSSAI Approved · Effervescent Technology');

            // Main headline — plain part and gradient part stored separately
            $table->string('title_main')->default('Hydrate. Restore.');
            $table->string('title_gradient')->default('Recharge.');

            // Sub-description paragraph
            $table->text('subtitle')->nullable();

            // CTA buttons
            $table->string('cta_primary_label')->default('Buy Now');
            $table->string('cta_primary_url')->default('#products');
            $table->string('cta_secondary_label')->default('Explore Products');
            $table->string('cta_secondary_url')->default('#products');

            // Minerals strip — stored as JSON array of {label, value} objects
            $table->json('minerals')->nullable();

            // Trust badges — stored as JSON array of {icon, label} objects
            $table->json('trust_badges')->nullable();

            // Floating badges on the hero image
            $table->string('badge_rating')->default('4.9/5 · 12k+ Reviews');
            $table->string('badge_lab')->default('Lab Tested');

            // Hero product image path (public storage)
            $table->string('image_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_sections');
    }
};
