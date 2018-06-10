<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    public function scopeGetStripeCredentialsForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', '=', $merchantId)->where('gateway', '=', 1)->get();
    }

    public function scopeGetGoCardlessCredentialsForMerchant($query, $merchantId)
    {
        return $query->where('merchant_id', '=', $merchantId)->where('gateway', '=', 2)->get();
    }

}
