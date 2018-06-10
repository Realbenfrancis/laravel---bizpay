<?php

namespace App\Http\Bizpay;

use Stripe\Charge;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Refund;
use Stripe\Subscription;

/**
 * Class Stripe
 * Stripe Module
 *
 * @package App\Http\Bizpay
 */
class Stripe
{

    /**
     * Stripe constructor.
     * @param $key
     */
    public function __construct($key)
    {
        \Stripe\Stripe::setApiKey($key);
    }


    /**
     * @param $token Token from Stripe
     * @param $name Customer Name
     * @param $email Customer Email
     * @return Customer customer object from Stripe containing customer id
     */
    public function addCustomer($token, $name, $email)
    {
        $json = Customer::create(array(
            "description" => $name . " : " . $email,
            "source" => $token
        ));

        return $json;
    }


    public function updateCustomerCard($customerId)
    {

    }

    /**
     * Charge $amount
     *
     * @param $customerId
     * @param $amount
     * @param $currency
     * @param $description
     * @return Charge
     */
    public function chargeCustomer($customerId, $amount, $currency, $description, $deferredPaymentRef = 0)
    {

        // $deferredPaymentRef
        // orderid-date

        $header = array();

        if (strlen($deferredPaymentRef) > 7) {
            $header = ["idempotency_key" => $deferredPaymentRef];
        }

        $json = Charge::create(array(
            "amount" => $amount,
            "currency" => $currency,
            "customer" => $customerId,
            "description" => $description
        ), $header);


        return $json;
    }

    /**
     * Refund the payment
     *
     * @param $chargeId
     */
    public function refundCharge($chargeId,$amount)
    {
        $transactionId = "";

        $re = Refund::create(array(
            "charge" => $chargeId,
            "amount" => $amount
        ));
        // return $transactionId;
    }


    public function setupPlanAndAddCustomer($planTerm, $customerId)
    {
        $planId = "";
        return $planId;
    }

    /**
     *
     * @param $customerId
     * @param $plan
     * @param $taxPercent
     * @return Subscription
     */
    public function subscribe($customerId, $plan, $taxPercent)
    {
        $subscription = Subscription::create(array(
            "customer" => $customerId,
            "plan" => $plan,
            "tax_percent" => $taxPercent,
        ));

        return ($subscription);
    }

    public function subscribetoInstallment($customerId, $plan, $taxPercent)
    {

        //TODO: add end date for subscription

        $subscription = Subscription::create(array(
            "customer" => $customerId,
            "plan" => $plan,
            "tax_percent" => $taxPercent,
        ));

        return ($subscription);

    }

    /**
     * Cancel Subscription
     *
     * @param $subscriptionId
     */
    public function cancelSubscription($subscriptionId)
    {
        $subscription = Subscription::retrieve($subscriptionId);
        $subscription->cancel();
    }

    /**
     *
     * Create a plan for subscription or instalment
     *
     *
     * @param $amount
     * @param $currency
     * @param $id
     * @param $name
     * @param $frequency
     */

    public function plan($amount, $currency, $id, $name, $frequency, $trialDays = 0)
    {
        Plan::create(array(
                "amount" => $amount * 100,
                "interval" => $frequency, // "month"
                "name" => $name,
                "currency" => $currency,
                "id" => $id,
                "trial_period_days" => $trialDays)
        );
    }

}