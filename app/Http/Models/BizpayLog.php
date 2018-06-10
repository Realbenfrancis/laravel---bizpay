<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class BizpayLog extends Model
{

    public function scopeGetAvgTime($query,$merchantId)
    {
        return $query->where('merchant_id', '=', $merchantId)->avg('response_time');
    }

}
