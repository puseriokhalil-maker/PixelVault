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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('artist_id')->constrained()->restrictOnDelete();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('subtotal_amount');
            $table->unsignedBigInteger('platform_fee_amount');
            $table->unsignedBigInteger('total_amount');
            $table->char('currency', 3)->default('php');
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->index(['user_id', 'status']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
