<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeGetUserForMerchant($query,$userId,$merchantId)
    {
        return $query->where('user_id', '=', $userId)->where('merchant_id', '=', $merchantId)->get();
    }

    public function scopeCheckUserExistsForMerchant($query,$email,$merchantId)
    {
        return $query->where('email', '=', $email)->where('merchant_id', '=', $merchantId)->get();

    }

    public function scopeGetUserForMerchantById($query,$userId,$merchantId)
    {
        return $query->where('user_id', '=', $userId)->where('merchant_id', '=', $merchantId)->get();

    }


    public function scopeGetClients($query)
    {
        $user = Auth::user();
        return $query->where('user_type', '=', 3)->where('merchant_id', '=', $user->merchant_id)->get();
    }

    public function scopeGetAllUsersForMerchant($query,$merchantId)
    {
        return $query->where('user_type', '=', 3)->where('merchant_id', '=', $merchantId)->paginate();
    }

    public function scopeGetMerchantManagers($query)
    {
        $user = Auth::user();
        return $query->where('user_type', '=', 2)->where('merchant_id', '=', $user->merchant_id)->get();

    }

    public function scopeGetUserFromAPIKey($query,$key)
    {
        return $query->where('api_token', '=', $key)->get();
    }


    public function customerSubscriptions()
    {
        return $this->hasMany('App\Http\Models\CustomerSubscriptions');
    }


    public function customerDetail()
    {
        return $this->hasMany('App\Http\Models\PaymentGatewayCustomerDetail');
    }

    public function orders()
    {
        return $this->hasMany('App\Http\Models\Order');
    }

    public function rules()
    {
        return $this->hasMany('App\Http\Models\Rule');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Http\Models\CustomerSubscriptions');
    }

    public function payments()
    {
        return $this->hasMany('App\Http\Models\Payment');
    }


    public function scopeGetUserFromEmail($query,$email)
    {
        return $query->where('email', '=', $email)->get();
    }

    public function scopeGetUserFromUserId($query,$userId)
    {
        return $query->where('user_id', '=', $userId)->get();
    }

    public function scopeGetMerchantAdmin($query,$merchantId)
    {
        return $query->where('user_type', '=', 1)->where('merchant_id', '=', $merchantId)->get();
    }




}
