<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSubscriptions extends Model
{
    public function scopeGetSubscriptionFromSlug($query, $slug)
    {
        return $query->where('subscription_id', '=', $slug)->get();
    }
}
