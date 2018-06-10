<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SAProduct
 * @package App\Http\Models
 */
class SAProduct extends Model
{
    public function scopeGetBySlug($query, $slug)
    {
        return $query->where('slug','=',$slug)->get();
    }

    public function scopeGetProduct($query, $slug,$merchantId)
    {
        return $query->where('slug','=',$slug)->where('merchant_id','=',$merchantId)->get();
    }

    public function scopeGetAllProductForMerchant($query,$merchantId)
    {
        return $query->where('merchant_id','=',$merchantId)->paginate();
    }

    public function scopeGetProductsCountForMerchant($query,$merchantId)
    {
        return $query->where('merchant_id','=',$merchantId)->count();
    }


    public function scopeGetProductWithMaxSales($query,$merchantId)
    {
        return $query->where('merchant_id','=',$merchantId)->orderBy('purchase_count', 'desc')->take(1)->get();
    }

}
