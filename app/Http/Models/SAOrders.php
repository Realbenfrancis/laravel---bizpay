<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SAOrders extends Model
{
    public function scopeGetBySlug($query, $slug)
    {
        return $query->where('slug','=',$slug)->get();
    }
}
