<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SAPlan extends Model
{
    public function scopeGetBySlug($query, $slug)
    {
        return $query->where('slug','=',$slug)->get();
    }

    public function scopeGetPlan($query, $slug, $merchantId)
    {
        return $query->where('slug','=',$slug)->where('merchant_id','=',$merchantId)->get();
    }

    public function pricing()
    {
        return $this->hasMany('App\Http\Models\SAPlanPricing');
    }

    public function scopeGetAllPlansByMerchant($query, $merchantId)
    {
        return $query->where('merchant_id','=',$merchantId)->paginate();
    }

}
