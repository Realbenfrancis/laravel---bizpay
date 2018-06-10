<?php

namespace App\Http\Bizpay;

class Paypal
{

    public static function instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new self();
        }
        return $inst;
    }

    private function addCustomer($token,$name)
    {
        $customerId="";
        return $customerId;
    }


    private function updateCustomerCard($customerId)
    {

    }


    private function chargeCustomer($customerId,$amount)
    {
        $chargeId="";
        return $chargeId;
    }

    private function refundCharge($customerId,$amount)
    {
        $transactionId="";
        return $transactionId;
    }

    private function setupPlanAndAddCustomer($planTerm,$customerId)
    {
        $planId="";
        return $planId;
    }

}