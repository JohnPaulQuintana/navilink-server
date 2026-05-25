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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            // Plan info
            $table->string('name');
            $table->string('slug')->unique();

            // Limits
            $table->unsignedInteger('max_categories')->default(10);
            $table->unsignedInteger('max_links_per_category')->default(20);

            // Pricing
            $table->decimal('price_monthly', 10, 2)->default(199);

            // Stripe
            $table->string('stripe_price_id')->nullable();

            // Optional extras (future-proofing)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
