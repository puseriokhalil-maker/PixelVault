<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropUnique(['stripe_account_id']);
            $table->dropColumn([
                'stripe_account_id',
                'stripe_transfers_enabled',
                'onboarding_complete',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['stripe_checkout_session_id']);
            $table->dropColumn([
                'stripe_checkout_session_id',
                'stripe_payment_intent_id',
            ]);
            $table->foreignId('artist_id')->nullable()->change();
            $table->string('status')->default('simulated')->change();
            $table->timestamp('completed_at')->nullable();
        });

        Schema::dropIfExists('stripe_webhook_events');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('orders')->whereNull('artist_id')->exists()) {
            throw new RuntimeException(
                'Cannot restore Stripe-era order constraints while multi-artist simulated orders exist.',
            );
        }

        Schema::create('stripe_webhook_events', function (Blueprint $table) {
            $table->string('event_id')->primary();
            $table->string('type');
            $table->timestamp('processed_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->foreignId('artist_id')->nullable(false)->change();
            $table->string('status')->default('pending')->change();
            $table->dropColumn('completed_at');
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->string('stripe_account_id')->nullable()->unique();
            $table->boolean('stripe_transfers_enabled')->default(false);
            $table->boolean('onboarding_complete')->default(false);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
