<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    public function scopeGetMerchantFromSlug($query, $slug)
    {
        return $query->where('merchant_id', '=', $slug)->get();
    }

    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function gateways()
    {
        return $this->hasMany('App\Http\Models\PaymentGateway');
    }

    public function plans()
    {
        return $this->hasMany('App\Http\Models\SAPlan');
    }

    public function products()
    {
        return $this->hasMany('App\Http\Models\SAProduct');
    }

    public function terms()
    {
        return $this->hasMany('App\Http\Models\SAClientTerm');
    }

    public function quotes()
    {
        return $this->hasMany('App\Http\Models\SAQuote');
    }

    public function agreements()
    {
        return $this->hasMany('App\Http\Models\SAAgreement');
    }

    public function rules()
    {
        return $this->hasMany('App\Http\Models\Rule');
    }

    public function orders()
    {
        return $this->hasMany('App\Http\Models\Order');
    }

    public function payments()
    {
        return $this->hasMany('App\Http\Models\Payment');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Http\Models\CustomerSubscriptions');
    }
}
