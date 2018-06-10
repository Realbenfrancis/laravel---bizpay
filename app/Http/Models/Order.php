<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function scopeGetOrderFromSlug($query, $slug)
    {
        return $query->where('order_id', '=', $slug)->get();
    }
}
