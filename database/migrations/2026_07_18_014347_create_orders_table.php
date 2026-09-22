<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Contact
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);

            // Shipping address
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('pincode', 10);

            // Order data
            $table->json('items');              // cart snapshot
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Payment
            $table->string('payment_method')->default('cod'); // cod | online
            $table->string('payment_status')->default('pending'); // pending | paid | failed

            // Fulfilment
            $table->string('status')->default('placed'); // placed | processing | shipped | delivered | cancelled

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
