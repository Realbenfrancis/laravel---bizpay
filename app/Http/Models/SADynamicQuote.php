<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SADynamicQuote extends Model
{
    public function scopeGetBySlug($query, $slug)
    {
        return $query->where('slug','=',$slug)->get();
    }

    public function scopeGetDynamicQuoteByMerchant($query, $slug, $merchantId)
    {
        return $query->where('slug','=',$slug)->where('merchant_id','=',$merchantId)->get();
    }

    public function scopeGetAllDynamicQuotesByMerchant($query, $merchantId)
    {
        return $query->where('merchant_id','=',$merchantId)->get();
    }
}
