<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SAPlanPricing extends Model
{
    public function scopeGetBySlug($query, $slug)
    {
        return $query->where('slug','=',$slug)->get();
    }

}
