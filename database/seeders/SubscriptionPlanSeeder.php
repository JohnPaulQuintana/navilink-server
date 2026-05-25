<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('subscription_plans')->insert([
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'max_categories' => 15,
                'max_links_per_category' => 25,
                'price_monthly' => 0.00,
                'stripe_price_id' => null,
                'is_active' => true,
                'description' => 'Free plan for new users',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'max_categories' => 50,
                'max_links_per_category' => 100,
                'price_monthly' => 9.99,
                'stripe_price_id' => 'price_pro_xxxxxx',
                'is_active' => true,
                'description' => 'For power users',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'max_categories' => 200,
                'max_links_per_category' => 1000,
                'price_monthly' => 19.99,
                'stripe_price_id' => 'price_premium_xxxxxx',
                'is_active' => true,
                'description' => 'Unlimited productivity',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
