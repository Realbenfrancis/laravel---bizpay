<?php

namespace App\Http\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeferredCharge
 * @package App\Http\Models
 */
class DeferredCharge extends Model
{

    /**
     * @param $query
     * @return mixed
     */
    public function scopePaymentsDueToday($query)
    {
        $now = Carbon::now();
        $date = $now->toDateString();
        return $query->where('payment_date', '=', $date)->get();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeAllFailedPayments($query)
    {
        return $query->where('status', '=', -1)->get();
    }

    /**
     * @param $query
     * @param $merchantId
     * @return mixed
     */
    public function scopeFailedPaymentsByMerchant($query, $merchantId)
    {
        return $query->where('status', '=', -1)->where('merchant_id', '=', $merchantId)->get();
    }

    /**
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeFailedPaymentsByUser($query, $userId)
    {
        return $query->where('status', '=', -1)->where('user_id', '=', $userId)->get();
    }

    /**
     * @param $query
     * @param $orderId
     * @return mixed
     */
    public function scopePaymentsByOrderId($query, $orderId)
    {
        return $query->where('order_id', '=', $orderId)->orderBy('id', 'desc')->get();
    }


    public function scopePaymentsByRef($query, $orderRef, $merchantId)
    {
        return $query->where('order_ref', '=', $orderRef)->where('merchant_id', '=', $merchantId)->get();
    }

}
