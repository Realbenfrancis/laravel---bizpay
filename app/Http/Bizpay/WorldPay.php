<?php

namespace App\Http\Bizpay;

/**
 * Class WorldPay
 * @package App\Http\Bizpay
 */
class WorldPay
{

    private function addCustomer($token, $name)
    {
        $customerId = "";
        return $customerId;
    }


    private function updateCustomerCard($customerId)
    {
    }


    private function chargeCustomer($customerId, $amount)
    {
        $chargeId = "";
        return $chargeId;
    }

    private function refundCharge($customerId, $amount)
    {
        $transactionId = "";
        return $transactionId;
    }

    private function setupPlanAndAddCustomer($planTerm, $customerId)
    {
        $planId = "";
        return $planId;
    }

}