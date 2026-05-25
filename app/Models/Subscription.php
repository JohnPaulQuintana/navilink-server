<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = 
    ['user_id', 'subscription_plan_id', 'status', 
    'stripe_customer_id', 'stripe_subscription_id', 
    'current_period_end'];
}
