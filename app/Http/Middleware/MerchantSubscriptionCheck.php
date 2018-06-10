<?php

namespace App\Http\Middleware;

use App\Http\Models\Merchant;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MerchantSubscriptionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $subscriptions = $user->customerSubscriptions->all();
        $merchant = Merchant::findorFail($user->merchant_id);
        if(count($subscriptions)<1 && $merchant->direct_client!=1){
            Session::flash('message', 'Please subscribe to use the portal!');
            Session::flash('alert-class', 'alert-danger');
            return redirect('/merchant-admin/bizpay-subscription');
        } else {
            return $next($request);
        }

    }
}
