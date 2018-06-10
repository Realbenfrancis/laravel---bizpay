<?php


namespace App\Http\Bizpay;

use GoCardlessPro\Client;
use GoCardlessPro\Environment;

/**
 * Class GoCardless
 * @package App\Http\Bizpay
 */
class GoCardless
{

    private $environment;
    private $client;

    /**
     * GoCardless constructor.
     * @param $key
     * @param string $environment
     * LIVE , SANDBOX
     */
    public function __construct($key)
    {
        $testCheck = env('API_ENV');

        if($testCheck=="test"){
            $this->client = new Client(array(
                'access_token' => $key,
                'environment' => Environment::SANDBOX
            ));
        } else {
            $this->client = new Client(array(
                'access_token' => $key,
                'environment' => Environment::LIVE
            ));
        }


    }

    /**
     * @param $email
     * @param $givenName
     * @param $familyName
     * @param $countryCode
     * @return mixed
     */
    public function addGCCustomer($email, $name, $countryCode)
    {
        $names = explode(" ",$name);
        $customer = $this->client->customers()->create([
            "params" => ["email" => $email,
                "given_name" => $names[0],
                "family_name" => $names[1],
                "country_code" => $countryCode]
        ]);

        return $customer->id;
    }

    /**
     * @param $accountNumber
     * @param $sortCode
     * @param $accountHolderName
     * @param $countryCode
     * @param $customerId
     * @return mixed
     */
    public function addBankAccount($accountNumber, $sortCode, $accountHolderName, $countryCode, $customerId, $iban=null)
    {

        if(is_null($iban)){
            $bankAccount = $this->client->customerBankAccounts()->create([
                "params" => ["account_number" => $accountNumber,
                    "branch_code" => $sortCode,
                    "account_holder_name" => $accountHolderName,
                    "country_code" => $countryCode,
                    "links" => ["customer" => $customerId]]
            ]);


        } else {
            $bankAccount = $this->client->customerBankAccounts()->create([
                "params" => ["iban" => $iban,
                    "account_holder_name" => $accountHolderName,
                    "country_code" => $countryCode,
                    "links" => ["customer" => $customerId]]
            ]);

        }

        return $bankAccount->id;


    }

    /**
     * @param $orderId
     * @param $backAccountId
     * @return mixed
     */
    public function addGCMandate($orderId, $backAccountId)
    {
        $mandate = $this->client->mandates()->create([
            "params" => ["scheme" => "bacs",
                "metadata" => ["contract" => $orderId],
                "links" => ["customer_bank_account" => $backAccountId]]
        ]);

        return $mandate->id;
    }


    /**
     * @param $refundAmount
     * @param $totalAmount
     * @param $paymentId
     */
    public function processGCRefund($refundAmount, $totalAmount, $paymentId)
    {
        $this->client->refunds()->create([
            "params" => ["amount" => (int) $refundAmount,
                "total_amount_confirmation" => (int) $totalAmount,
                "links" => ["payment" => $paymentId]]
        ]);
    }


    /**
     * @param $subscriptionId
     */
    public function cancelGCSubscription($subscriptionId)
    {

        $this->client->subscriptions()->cancel($subscriptionId);
    }

    /**
     * @param $amount
     * @param $currencyCode
     * @param $duration
     * @param $day
     * @param $count
     * @param $mandateId
     * @param $orderId
     * @param int $deferredPaymentRef
     * @return mixed
     */
    public function addGCSubscription(
        $amount,
        $currencyCode,
        $duration,
        $day,
        $count,
        $mandateId,
        $orderId,
        $deferredPaymentRef = 0
    )
    {

        $header = array();

        if (strlen($deferredPaymentRef) > 3) {
            $header = ["Idempotency-Key" => $deferredPaymentRef];
        }

        $params = [
            "amount" => $amount, // 15 GBP in pence
            "currency" => $currencyCode,
            "interval_unit" => $duration, //weekly, monthly or yearly
            "day_of_month" => $day,
            "links" => [
                "mandate" => $mandateId
            ],
            "metadata" => [
                "subscription_number" => $orderId
            ]
        ];

        if ($count > 0) {
            $params["count"] = $count;
        }

        $subscription = $this->client->subscriptions()->create([
            "params" => $params,
            "headers" => [
                $header
            ]
        ]);

        return $subscription->id;
    }


    /**
     * @param $amount
     * @param $currencyCode
     * @param $mandateId
     * @param $orderId
     * @param int $deferredPaymentRef
     * @return mixed
     */
    public function chargeGCCustomer(
        $amount,
        $currencyCode,
        $mandateId,
        $orderId,
        $deferredPaymentRef = 0
    ) {
        $header = array();

        if (strlen($deferredPaymentRef) > 3) {
            $header = ["Idempotency-Key" => $deferredPaymentRef];
        }

        $payment = $this->client->payments()->create([
            "params" => ["amount" => (int) $amount*100,
                "currency" => $currencyCode,
                "metadata" => [
                    "order_id" => $orderId
                ],
                "links" => [
                    "mandate" => $mandateId
                ]],
            "headers" => [
                $header
            ]
        ]);

        return $payment->id;
    }

    /**
     * @param $description
     * @param $userId
     * @param $successURL
     * @param $user
     * @return mixed
     */
    public function generateGCRedirectURL(
        $description,
        $successURL,
        $user
    ) {
        $redirectFlow = $this->client->redirectFlows()->create([
            "params" => [
                "description" => $description,
                "session_token" => $user->user_id,
                "success_redirect_url" => $successURL,
//                "prefilled_customer" => [
//                    "given_name" => $user->first_name,
//                    "family_name" => $user->last_name,
//                    "email" => $user->email,
//                    "address_line1" => $user->address_line1,
//                    "city" => $user->city,
//                    "postal_code" => $user->postcode
//                ]
            ]
        ]);

        return $redirectFlow->redirect_url;
    }


    /**
     * @param $redirectId
     * @return \GoCardlessPro\Resources\RedirectFlow
     */
    public function createRedirectFlow($redirectId, $userId)
    {
        $redirectFlow =  $this->client->redirectFlows()->complete(
            $redirectId,
            ["params" => ["session_token" => $userId]]
        );

        return $redirectFlow;
    }
}