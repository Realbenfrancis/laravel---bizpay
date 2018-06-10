<?php

namespace App\Http\Controllers;

use App\Http\Models\Payment;
use App\Notifications\PaymentFailedNotification;
use App\Notifications\PaymentSuccededNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class WebHookController extends Controller
{

    public function stripe()
    {

    }


    private function payment()
    {

        $payment = new Payment();
        Notification::send("",new PaymentSuccededNotification());


    }

    private function paymentFailed()
    {
        Notification::send("",new PaymentFailedNotification());
    }
}
