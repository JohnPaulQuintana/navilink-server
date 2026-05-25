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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // Relation
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('subscription_plan_id')
                ->constrained('subscription_plans')
                ->cascadeOnDelete();

            // Status (active, canceled, past_due, trialing)
            $table->string('status')->default('active');

            // Stripe
            $table->string('stripe_customer_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();

            // Billing period
            $table->timestamp('current_period_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
