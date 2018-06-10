<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function scopeGetPaymentFromSlug($query, $slug)
    {
        return $query->where('payment_id', '=', $slug)->get();
    }

    public function scopeGetPaymentFromChargeId($query, $chargeId)
    {
        return $query->where('charge_id', '=', $chargeId)->get();
    }

    public function scopeGetPaymentFromOrderRef($query, $orderRef, $merchantId)
    {
        return $query->where('order_ref', '=', $orderRef)->where('merchant_id', '=', $merchantId)->get();
    }
}
