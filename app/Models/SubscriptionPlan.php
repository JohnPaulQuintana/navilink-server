<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'max_categories',
        'max_links_per_category',
        'price_monthly',
        'stripe_price_id',
        'is_active',
        'description',
    ];
}
