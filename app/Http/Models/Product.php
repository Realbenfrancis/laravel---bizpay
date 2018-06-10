<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function scopeGetProductFromSlug($query, $slug)
    {
        return $query->where('product_id', '=', $slug)->get();
    }
}
