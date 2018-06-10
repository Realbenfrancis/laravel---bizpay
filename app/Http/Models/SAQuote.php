<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SAQuote
 * @package App\Http\Models
 */
class SAQuote extends Model
{
    /**
     * @param $query
     * @param $slug
     * @return mixed
     */
    public function scopeGetBySlug($query, $slug)
    {
        return $query->where('slug', '=', $slug)->get();
    }

    /**
     * @param $query
     * @param $slug
     * @param $merchantId
     * @return mixed
     */
    public function scopeGetQuoteByMerchant($query, $slug, $merchantId)
    {
        return $query->where('slug', '=', $slug)->where('merchant_id', '=', $merchantId)->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('App\Http\Models\SAQuoteProduct');
    }

    public function scopeGetAllQuotesByMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', '=', $merchantId)->paginate();
    }

    public function scopeGetQuoteWithMaxSales($query, $merchantId)
    {
        return $query->where('merchant_id', '=', $merchantId)
            ->orderBy('purchase_count')->orderBy('purchase_count', 'desc')->take(1)->get();
    }

}
