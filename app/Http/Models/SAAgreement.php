<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SAAgreement
 * @package App\Http\Models
 */
class SAAgreement extends Model
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
     * @param $merchantId
     * @return mixed
     */
    public function scopeGetAllAgreementsForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', '=', $merchantId)->paginate();
    }

    /**
     * @param $query
     * @param $merchantSlug
     * @return mixed
     */
    public function scopeGetAAgreementsForMerchant($query, $merchantSlug)
    {
        return $query->where('merchant_slug', '=', $merchantSlug)->paginate();
    }

    /**
     * @param $query
     * @param $merchantSlug
     * @return mixed
     */
    public function scopeGetAAgreementForMerchant($query, $slug)
    {
        return $query->where('slug', '=', $slug)->get();
    }


    /**
     * @param $query
     * @param $merchantId
     * @return mixed
     */
    public function scopeGetAgreementsCountForMerchant($query,$merchantId)
    {
        return $query->where('merchant_id','=',$merchantId)->count();
    }
}
