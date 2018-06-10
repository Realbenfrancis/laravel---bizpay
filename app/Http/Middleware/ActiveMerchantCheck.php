<?php

namespace App\Http\Middleware;

use App\Http\Models\Merchant;
use Closure;
use Illuminate\Support\Facades\Auth;

class ActiveMerchantCheck
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
        $merchant = Merchant::findorFail($user->merchant_id);
        if($merchant->status==1){
            return $next($request);
        } else {
            exit("Merchant Account Inactive!");
        }

    }
}
