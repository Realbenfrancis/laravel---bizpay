<?php

namespace App\Http\Bizpay;

use App\Http\Models\CustomerSubscriptions;
use App\Http\Models\DeferredCharge;
use App\Http\Models\DynamicProduct;
use App\Http\Models\Merchant;
use App\Http\Models\Order;
use App\Http\Models\Payment;
use App\Http\Models\PaymentGateway;
use App\Http\Models\PaymentGatewayCustomerDetail;
use App\Http\Models\Product;
use App\Http\Models\Rule;
use App\Http\Models\SAAgreement;
use App\Notifications\PaymentFailedNotification;
use App\Notifications\SendUserAccount;
use App\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Nexmo\Laravel\Facade\Nexmo;
use Stripe\Error\Card;
use Stripe\Token;

/**
 * Class Bizpay
 *
 * For all the shared functions
 * If there are not qualifiers for a payment gateway related function, please note that
 * it refers to Stripe as it was the only supported gateway originally.
 *
 * All functions needed for API are also here!
 *
 *
 * @package App\Http\Bizpay
 */
class Bizpay
{
    private $stripe;
    private $goCardless;
    private $stripeCredential;
    private $stripePublicCredential;


    /**
     * Set the stripe secret credential
     * @param $merchantId
     */
    public function setStripeCredential($merchantId)
    {
        $gatewayCredentials = PaymentGateway::GetStripeCredentialsForMerchant($merchantId)[0];
        $this->stripePublicCredential = $gatewayCredentials->credential_1; // public
        $this->stripeCredential = $gatewayCredentials->credential_2; // secret
        $this->stripe = new Stripe($this->stripeCredential);
    }

    /**
     * Get the access key for GoCardless
     *
     * @param $merchantId
     */
    public function setGoCardlessCredential($merchantId)
    {
        $gatewayCredentials = PaymentGateway::GetGoCardlessCredentialsForMerchant($merchantId)[0];
        $this->goCardless = new GoCardless($gatewayCredentials->credential_1); // access key
    }


    /**
     * Save the new rule
     *
     * @param Request $request
     */
    public function processAddRule(Request $request)
    {
        $user = Auth::user();
        $rule = new Rule();
        $rule->rule = $request->get('name');
        $rule->check_type = $request->get('check_type');
        $rule->apply_rule_on = $request->get('apply_rule_on');
        $rule->description = $request->get('description');
        $rule->data_type = $request->get('data_type');
        $rule->limit1 = $request->get('limit1');
        $rule->limit2 = $request->get('limit2');
        $rule->limit3 = $request->get('limit3');
        $rule->action_on = $request->get('action_on');
        $rule->action_type = $request->get('action_type');
        $rule->action_value = mb_strtolower($request->get('action_value'));
        $rule->merchant_id = $user->merchant_id;
        $rule->merchant_id_on = $request->get('merchant_id');
        $rule->user_id = $user->id;
        $rule->rule_id = str_random(20);
        $rule->status = 1;

        $testCheck = env('API_ENV');

        if ($testCheck == "test") {
            $rule->test_check = 1;
        } else {
            $rule->test_check = 0;
        }

        $rule->save();
    }

    /**
     *  Add merchant to database
     *
     * @param Request $request
     */
    public function processAddMerchant(Request $request)
    {
        $merchant = new Merchant();
        $merchant->merchant_name = $request->get('merchant_name');
        $merchant->merchant_website = $request->get('merchant_website');
//        $merchant->merchant_phone_number= $request->get('merchant_phone_number'); // use if we need to store this info
        $merchant->merchant_id = str_random(30);
        $merchant->gateway = 1;  // change this to use a different default gateway //TODO - app stage

        if ($request->has('direct_client')) {
            $merchant->direct_client = 1;
        }

        if ($request->has('bizpay_credit')) {
            $merchant->bizpay_credit = 1;
        }

        $merchant->status = 1;
        $merchant->save();


        //DO NOT UNCOMMENT MIDDLEWARE COMMANDS
        // THIS WILL AUTOMATICALLY BE EXECUTED WHEN DEPLOYED ON MIDDLEWARE

        //MW:COMMAND
//        if($request->has('resource_check')){
//            exec("mw:add-server local env --id".$merchant->id);
//        }


        $user = new User(); // create user for this merchant
        $password = str_random(12);
        $user->password = Hash::make($password);
        $user->name = $request->get('merchant_name');
        $user->email = $request->get('merchant_email');
        $user->user_type = 1;
        $user->merchant_id = $merchant->id;
        $user->user_id = str_random(20);
        $user->api_token = str_random(30);
        $user->status = 1;
        $user->confirmation_code = str_random(6);

        if ($request->has('direct_client')) {
            $user->api_limit = 50000;
        }


        $user->save();

    //    Notification::send($user, new SendUserAccount($password));

        Session::flash('message', 'Merchant has been created!');
        Session::flash('alert-class', 'alert-success');
    }

    /**
     * Save settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveSettings(Request $request)
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $merchant->merchant_name = $request->get('merchant_name'); // change default gateway post MVP
        $merchant->tax = $request->get('tax');
        $merchant->gateway = $request->get('default-gateway');
        $merchant->save();

        //   $gateways = $merchant->gateways->all();

        $stripe = PaymentGateway::GetStripeCredentialsForMerchant($user->merchant_id);
        $goCardless = PaymentGateway::GetGoCardlessCredentialsForMerchant($user->merchant_id);


        if (count($stripe) < 1) {
            $paymentGateway = new PaymentGateway();
            $paymentGateway->gateway = 1;
            $paymentGateway->credential_1 = $request->get('stripe_credential_1');
            $paymentGateway->credential_2 = $request->get('stripe_credential_2');
            $paymentGateway->status = 1;
            $paymentGateway->merchant_id = $user->merchant_id;
            $paymentGateway->save();

        } else {
            $stripe[0]->credential_1 = $request->get('stripe_credential_1');
            $stripe[0]->credential_2 = $request->get('stripe_credential_2');
            $stripe[0]->save();
        }

        if (count($goCardless) < 1) {
            $paymentGateway = new PaymentGateway();
            $paymentGateway->gateway = 2;
            $paymentGateway->credential_1 = $request->get('gocardless_credential');
            $paymentGateway->status = 1;
            $paymentGateway->merchant_id = $user->merchant_id;
            $paymentGateway->save();

        } else {
            $goCardless[0]->credential_1 = $request->get('gocardless_credential');
            $goCardless[0]->save();
        }

        return redirect()->back();
    }

    /**
     * Cancel a customer subscription
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelClientSubscription(Request $request)
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $gateways = ($merchant->gateways->all());

        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential1 = $gateway->credential_1;
                $credential2 = $gateway->credential_2;
            }
        }

        $stripe = new Stripe($credential2);
        $stripe->cancelSubscription($request->get('id'));

        $sub = CustomerSubscriptions::GetSubscriptionFromSlug($request->get('id'))[0];
        $sub->delete();

        return redirect()->back();
    }


    /**
     * Do necessary checks and save password
     * Currently password needs to be at least 6 letters long.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveChangePassword(Request $request)
    {
        $user = Auth::user();
        $password = $request->get('password');

        if (Hash::check($password, $user->password)) {
            if ($request->get('password_new') == $request->get('password_confirm')) {
                if (strlen($request->get('password_new')) > 5) {
                    Session::flash('message', 'Password Changed!');
                    Session::flash('alert-class', 'alert-info');
                    $cUser = User::findorFail($user->id);
                    $cUser->password = Hash::make($request->get('password_new'));
                    $cUser->save();

                    return redirect('/home');

                } else {
                    Session::flash('message', 'Password has to be at least 6 characters long!');
                    Session::flash('alert-class', 'alert-danger');

                    return redirect()->back();
                }
            } else {
                Session::flash('message', 'Passwords do not match!');
                Session::flash('alert-class', 'alert-danger');

                return redirect()->back();
            }
        } else {
            Session::flash('message', 'Please check your old password!');
            Session::flash('alert-class', 'alert-danger');

            return redirect()->back();
        }
    }

    /**
     * @param $customerAge
     * @param $customerCountry
     * @param $price
     * @return bool
     *
     */
    public function financeCheck($customerAge, $customerCountry, $price)
    {
        $check = false;
        if ($customerAge > 17 && $customerCountry != "India" && $price > 10000) {
            $check = true;
        }

        return $check;
    }

    /**
     * @param $price
     * @param $tax
     * @param $priceHike
     * @param $firstPayment
     * @return mixed
     */
    public function instalments($price, $tax, $priceHike, $firstPayment)
    {
        $client = new Client();
        $total = ($price + $price * $tax) * $priceHike;

        $response3 = $client->request('POST', 'https://bpapi.flymycloud.com/api/plans', [
            'form_params' => [
                'price' => $total * 100,
                'months' => 13,
                'percent' => $firstPayment / 100,
            ]
        ]);

        return (\GuzzleHttp\json_decode($response3->getBody()->getContents()));
    }

    /**
     * Add a user to the platform
     *
     * @param $merchantId
     * @param $name
     * @param $email
     * @return User
     */
    public function addUser(
        $merchantId,
        $name,
        $email,
        $firstName = null,
        $lastName = null,
        $addressLine1 = null,
        $addressLine2 = null,
        $city = null,
        $postCode = null,
        $country = null,
        $organisationName = null,
        $companyNumber = null,
        $role = null,
        $phoneNumber = null,
        $jobTitle = null
    )
    {
        $userManagement = new UserManagement();
        $password = str_random(12);
        $user = $userManagement->addUser(
            $merchantId,
            $name,
            $email,
            Hash::make($password),
            $firstName,
            $lastName,
            $addressLine1,
            $addressLine2,
            $city,
            $postCode,
            $country,
            $organisationName,
            $companyNumber,
            $role,
            $phoneNumber,
            $jobTitle
        );

        // Notification::send($user, new SendUserAccount($password));

        return $user;
    }


    /**
     * Get User from user id
     *
     * @param $merchantId
     * @param $userId
     * @return mixed
     */
    public function getUser($merchantId, $userId)
    {
        try {
            $user = User::GetUserForMerchant($merchantId, $userId);
            return $user;
        } catch (\Exception $exception) {

        }


    }

    public function updateUser(
        $userId,
        $merchantId,
        $name,
        $fname,
        $lname,
        $addressl1,
        $addressl2,
        $addressl3,
        $city,
        $postcode,
        $country,
        $org,
        $companyNo,
        $phoneNo,
        $job,
        $role=null
    )
    {

        $user = User::GetUserForMerchant($userId,$merchantId)[0];
        $user->name = $name;
        $user->first_name = $fname;
        $user->last_name = $lname;
        $user->address_line1 = $addressl1;
        $user->address_line2 = $addressl2;
        $user->address_line3 = $addressl3;
        $user->city = $city;
        $user->postcode = $postcode;
        $user->country = $country;
        $user->organisation_name = $org;
        $user->company_no = $companyNo;
        $user->phone_number = $phoneNo;
        $user->job_title = $job;
        $user->role = $role;
        $user->save();

        return $user;
    }

    /**
     * @param $merchantId
     * @return mixed
     */
    public function getAllUsersForMerchant($merchantId)
    {
        $users = User::GetAllUsersForMerchant($merchantId);
        return $users;
    }


    /**
     * Returns all orders for the merchant
     *
     * @param $merchantId
     * @return mixed
     */
    public function orders($merchantId)
    {
        return Merchant::findorFail($merchantId)->orders->all();
    }

    /**
     * Super Admin only!
     * Returns all orders on the platform
     * Only a super admin should be able to view this. Please add that check when calling this.
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allOrders()
    {
        return Order::all();
    }

    /**
     * Suggest custom plans based on the params passed by the client
     *
     * @param $amount
     * @param $initialPayPercent
     * @param $months
     * @return array
     * @internal param $initialPaymentPercent
     */
    public function customPlans($amount, $initialPayPercent, $months)
    {

        $plan = array();
        $amountinPence = $amount; //TODO check
        $initalPayment = $amount * ($initialPayPercent / 100);
        $initalPayment = round($initalPayment);
        $balance = $amountinPence - $initalPayment;
        $remainder = $balance % $months;
        $roundedBalance = $balance - $remainder;
        $instalmentAmount = $roundedBalance / $months;

        if ($initalPayment > 0) {
            $initalPayment = $initalPayment + $remainder;
            $firstInstalment = $instalmentAmount;
        } else {
            $firstInstalment = $instalmentAmount + $remainder;
        }

        $plan['initial_payment'] = number_format($initalPayment, 2);
        $plan['recurring_instalment_amount'] = number_format($instalmentAmount, 2) ;
        $plan['first_instalment'] = number_format($firstInstalment, 2) ;
        $plan['total_instalments'] = $months+1;

        return $plan;
    }

    /**
     * STRIPE
     * Charge the customer using Stripe payment gateway
     *
     * @param $customer
     * @param $amount
     * @param $currency
     * @param $description
     * @param int $selectedGateway
     * @param int $taxPerCharge
     * @param int $deferredPaymentRef
     * @return null
     */
    public function chargeClient(
        $customer,
        $amount,
        $currency,
        $description,
        $selectedGateway = 1,
        $taxPerCharge = 0,
        $deferredPaymentRef = 0,
        $orderRef = 0
    )
    {
        $gatewayCredentials = $customer->customerDetail->all();
        $merchant = Merchant::findorFail($customer->merchant_id);
        $tax = ($merchant->tax / 100);
        $gateways = ($merchant->gateways->all());
        $gocardlessCredential = null;
        $credential2 = null;

        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential2 = $gateway->credential_2;
            }
            if ($gateway->gateway == 2) {
                $gocardlessCredential = $gateway->credential_1;
            }
        }

        if (!is_null($credential2)) {
            $stripe = new Stripe($credential2);
        }

        if (!is_null($gocardlessCredential)) {
            $goCardless = new GoCardless($gocardlessCredential);
        }


        $order = new Order();


        if ($taxPerCharge > 0) {
            $total = $amount + $amount * $taxPerCharge / 100;
            $order->tax = $amount * $taxPerCharge / 100;
            $taxPercent = $taxPerCharge;
        } else {
            if ($tax > 0) {
                $total = $amount + $amount * $tax;
                $order->tax = $amount * $tax;
                $taxPercent = $tax * 100;
            } else {
                $total = $amount;
                $order->tax = 0.00;
                $taxPercent = 0.00;
            }
        }

        $order->order_id = str_random(20);
        $payment = new Payment();
        $payment->charge_id = ""; // default
        $payment->order_ref = $orderRef;

        if (count($gatewayCredentials) < 1) {
            return 0;
        }


        //  dd($selectedGateway);

        if ($selectedGateway == 1) {
            try {

                if ($currency != "JYP") {
                    $total = $total * 100;
                }

                $resp = $stripe->chargeCustomer(
                    $gatewayCredentials[0]->stripe_customer_id,
                    $total,
                    $currency,
                    $description,
                    $deferredPaymentRef
                );

                $payment->charge_id = $resp->id; // charge_id from gateway for refund
                $payment->gateway = 1;

                $order->installments = 1;
                $order->price = $amount;
                $order->adjusted_price = $total;
                $order->initial_payment = $total;
                $payment->amount = $total;
                $payment->currency_code = $currency;

                $order->balance = 0.00;
                $order->user_id = $customer->id;
                $order->merchant_id = $customer->merchant_id;
                $order->status = 1;
                $order->save();

                $dynamo = new DynamoDbAccess();
                $dynamo->addData($order->id, $order->toArray());

                $payment->order_id = $order->id;
                $payment->user_id = $customer->id;
                $payment->merchant_id = $customer->merchant_id;
                $payment->status = 1;
                $payment->save();

//                Nexmo::message()->send([
//                    'to' => $customer->phone_number,
//                    'from' => "Bizpay",
//                    'text' => 'Thank you for buying from ' . $merchant->merchant_name
//                ]);

                return $resp->id;


            } catch (Card $e) {
                return null;
                // POST MVP - send the right notification
                //  Notification::send($customer,new PaymentFailedNotification());
            }
        } elseif ($selectedGateway == 2) {

            if ($currency != "JYP") {
                $total = $total * 100;
            }

            $gatewayCredentials = $customer->customerDetail->all();
            $id = $goCardless->chargeGCCustomer($amount, $currency, $gatewayCredentials[0]->gc_mandate_id, "");

            $payment->charge_id = $id; // charge_id from gateway for refund
            $payment->gateway = 2;
            $order->installments = 1;
            $order->price = $amount;
            $order->adjusted_price = $total;
            $order->initial_payment = $total;
            $payment->amount = $total;
            $payment->currency_code = $currency;

            $order->balance = 0.00;
            $order->user_id = $customer->id;
            $order->merchant_id = $customer->merchant_id;
            $order->status = 1;
            $order->save();

            $dynamo = new DynamoDbAccess();
            $dynamo->addData($order->id, $order->toArray());

            $payment->order_id = $order->id;
            $payment->user_id = $customer->id;
            $payment->merchant_id = $customer->merchant_id;
            $payment->status = 1;
            $payment->save();

            return $id;

        } else {
            return null;
        }
    }

    /**
     * Add stripe subscription
     *
     * @param $user
     * @param $planId
     * @param $taxPercent
     * @param $recurringPayment
     * @param $currency
     * @return mixed|null
     */
    public function addSubscription($user, $planId, $taxPercent, $recurringPayment, $currency)
    {
        $gatewayCredentials = $user->customerDetail->all();
        $merchant = Merchant::findorFail($user->merchant_id);
        $gateways = ($merchant->gateways->all());

        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential1 = $gateway->credential_1;
                $credential2 = $gateway->credential_2;
            }
        }

        $stripe = new Stripe($credential2);
        $order = new Order();
        $order->order_id = str_random(20);
        $payment = new Payment();
        $payment->charge_id = ""; // default

        $response = $stripe->subscribe(
            $gatewayCredentials[0]->stripe_customer_id,
            $planId,
            $taxPercent
        );

        $subscription = new CustomerSubscriptions();
        $subscription->subscription_id = $response->id;
        $subscription->start_date = $response->current_period_start;
        $subscription->end_date = $response->current_period_end;
        $subscription->payment_gateway_id = 1;
        $subscription->status = 1;
        $subscription->user_id = $user->id;
        $subscription->merchant_id = $user->merchant_id;
        $subscription->save();

        $order->tax = $taxPercent * $recurringPayment;
        $order->price = $recurringPayment;
        $order->adjusted_price = $recurringPayment;
        $order->initial_payment = $recurringPayment;
        $order->installments = -1;
        $order->currency_code = $currency;
        $payment->currency_code = $currency;
        $payment->amount = $recurringPayment;

        $order->balance = 0.00;
        $order->user_id = $user->id;
        $order->merchant_id = $user->merchant_id;
        $order->status = 1;
        $order->save();

        $dynamo = new DynamoDbAccess();
        $dynamo->addData($order->id, $order->toArray());

        $payment->order_id = $order->id;
        $payment->user_id = $user->id;
        $payment->merchant_id = $user->merchant_id;
        $payment->status = 1;
        $payment->save();

//        Nexmo::message()->send([
//            'to' => $user->phone_number,
//            'from' => "Bizpay", // change to $merchant->merchant_name if merchant name has to be shown
//            'text' => 'Thank you for buying from ' . $merchant->merchant_name
//        ]);

        return $subscription->subscription_id;
    }

    /**
     * Add a plan to Stripe
     *
     * @param $user
     * @param $price
     * @param $currency
     * @param $planId
     * @param $name
     * @param $duration
     * @param $trialDays
     */
    public function addPlan($user, $price, $currency, $planId, $name, $duration, $trialDays)
    {
        $this->setStripeCredential($user->merchant_id);
        $this->stripe->plan(
            $price,
            $currency,
            $planId,
            $name,
            $duration,
            $trialDays
        );
    }

    /**
     * Add product to database and return product id
     *
     * @param $currencyCode
     * @param $type
     * @param $name
     * @param $description
     * @param $price
     * @param $tax
     * @param $merchantId
     * @param $userId
     * @return mixed|string
     */
    public function addProduct($currencyCode, $type, $name, $description, $price, $tax, $merchantId, $userId)
    {
        $product = new Product();
        $product->currency_code = $currencyCode;
        $product->product_id = str_random(20);
        $product->name = $name;
        $product->description = $description;
        $product->type = $type;
        $product->price = $price;
        $product->tax = $tax;
        $product->merchant_id = $merchantId;
        $product->user_id = $userId;
        $product->status = 1;
        $product->save();

        return $product->product_id;
    }

    /**
     * Cancel a stripe subscription
     *
     * @param $user
     * @param $subscriptionId
     */
    public function cancelSubscription($user, $subscriptionId)
    {
        $this->setStripeCredential($user->merchant_id);
        $this->stripe->cancelSubscription($subscriptionId);
    }

    /**
     * Return the public stripe credential
     *
     * @param $user
     * @return mixed
     */
    public function stripePublicCredential($user)
    {
        $this->setStripeCredential($user->merchant_id);
        return $this->stripePublicCredential;
    }

    /**
     * Return a stripe charge
     *
     * @param $user
     * @param $chargeId
     */
    public function refund($user, $chargeId)
    {
        $this->setStripeCredential($user->merchant_id);
        $this->stripe->refundCharge($chargeId);
    }

    /**
     * Add a card or update it on Stripe
     *
     * @param $user
     * @param $stripeToken
     */
    public function addCardToStripe($user, $stripeToken)
    {
        $this->setStripeCredential($user->merchant_id);
        $gatewayCredentials = $user->customerDetail->all();


        if (count($gatewayCredentials) < 1) {
            $response = $this->stripe->addCustomer($stripeToken, $user->name, $user->email);
            $token = Token::retrieve($stripeToken, ['api_key' => $this->stripeCredential]);
            $customerDetail = new PaymentGatewayCustomerDetail();
            $customerDetail->stripe_customer_id = $response->id;
            $customerDetail->stripe_card_id = $token->card->id;
            $customerDetail->card_exp_month = $token->card->exp_month;
            $customerDetail->card_exp_year = $token->card->exp_year;
            $customerDetail->card_last_four = $token->card->last4;
            $customerDetail->card_brand = $token->card->brand;
            $customerDetail->card_country = $token->card->country;
            $customerDetail->payment_gateway_id = 1;
            $customerDetail->user_id = $user->id;
            $customerDetail->save();

        } else {
            $customerDetail = PaymentGatewayCustomerDetail::findorFail($gatewayCredentials[0]->id);
            $customer = \Stripe\Customer::retrieve($customerDetail->stripe_customer_id);
        //    $customer->sources->retrieve($customerDetail->stripe_card_id)->delete();
            $customer = \Stripe\Customer::retrieve($customerDetail->stripe_customer_id);
            $customer->sources->create(array("source" => $stripeToken));
            $token = Token::retrieve($stripeToken, ['api_key' => $this->stripeCredential]);
            $customerDetail->stripe_card_id = $token->card->id;
            $customerDetail->card_exp_month = $token->card->exp_month;
            $customerDetail->card_exp_year = $token->card->exp_year;
            $customerDetail->card_last_four = $token->card->last4;
            $customerDetail->card_brand = $token->card->brand;
            $customerDetail->card_country = $token->card->country;
            $customerDetail->payment_gateway_id = 1;
            $customerDetail->user_id = $user->id;
            $customerDetail->save();
        }
    }

    /**
     * Returns all payments for the merchant
     *
     * @param $merchantId
     * @return mixed
     */
    public function payments($merchantId)
    {
        return Merchant::findorFail($merchantId)->payments->all();
    }

    /**
     * Super Admin only!
     * Returns all payments on the platform
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allPayments()
    {
        return Payment::all();
    }

    /**
     * Returns all products for the merchant
     *
     * @param $merchantId
     * @return mixed
     */
    public function products($merchantId)
    {
        return Merchant::findorFail($merchantId)->products->all();
    }

    /**
     * Super admin only!
     * Returns all products on the platform
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allProducts()
    {
        return Product::all();
    }

    /**
     * Get all subscriptions for the merchant
     *
     * @param $merchantId
     * @return mixed
     */
    public function subscriptions($merchantId)
    {
        return Merchant::findorFail($merchantId)->subscriptions->all();
    }

    /**
     * Get all subscriptions
     * \
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allSubscriptions()
    {
        return CustomerSubscriptions::all();
    }

    public function deferredCharges()
    {
    }

    public function addOrder()
    {
    }

    public function addPayment()
    {
    }

    /**
     * POST-MVP : add generic notifcation system
     *
     * @param $user
     * @param $subject
     * @param $body
     */
    public function sendNotfication($user, $subject, $body)
    {
    }

    /**
     * Setup deferred payment
     *
     * @param $user
     * @param $date
     * @param $amount
     * @param $tax
     * @param $gateway
     * @param $merchantId
     * @param $currencyCode
     * @param $description
     * @param int $orderId
     * @param int $orderType
     * @param null $duration
     */
    public function deferredPayment(
        $user,
        $date,
        $amount,
        $tax,
        $gateway,
        $merchantId,
        $currencyCode,
        $description,
        $orderType = 0,
        $instalments,
        $orderRef,
        $duration = null,
        $orderId = null,
        $testCheck = null
    )
    {
        $deferredCharge = new DeferredCharge();
        $deferredCharge->amount = $amount;
        $deferredCharge->tax = $tax;
        $deferredCharge->payment_gateway = $gateway;
        $deferredCharge->payment_date = Carbon::parse($date)->toDateString();
        $deferredCharge->merchant_id = $merchantId;
        $deferredCharge->user_id = $user->id;
        $deferredCharge->status = 1;
        $deferredCharge->currency_code = $currencyCode;
        $deferredCharge->description = $description;
        $deferredCharge->order_id = $orderId;
        $deferredCharge->order_type = $orderType;
        $deferredCharge->duration = $duration;
        $deferredCharge->instalments = $instalments;
        $deferredCharge->instalments_remaining = $instalments;
        $deferredCharge->order_ref = $orderRef;
        $deferredCharge->test_check = $testCheck;
        $deferredCharge->s_a_agreement_id = $orderId;
        $deferredCharge->save();
    }


    /**
     *
     *
     * Apply a payment to a plan
     *
     * @param $orderId
     * @param $instalments
     */
    public function applyPaymentToPlan($orderId, $instalments)
    {
        $payments = DeferredCharge::paymentsByOrderId($orderId);

        foreach ($payments as $payment) {
            while ($instalments > 0) {
                $payment->delete();
                --$instalments;
            }
            if ($instalments == 0) {
                break;
            }
        }
    }

    public function updateInstalments($orderRef, $merchantId, $instalments)
    {
        $payments = DeferredCharge::PaymentsByRef($orderRef, $merchantId);
        foreach ($payments as $payment) {
            if ($payment->order_type == 2) {

                $duration = $payment->duration;
                $formattedFirstDate = Carbon::parse($payment->payment_date);

                for ($i = 0; $i < $instalments; $i++) {


                    if ($duration == "day") {
                        $formattedFirstDate->addDay();
                    }

                    if ($duration == "2-days") {
                        $formattedFirstDate->addDays(2);
                    }

                    if ($duration == "3-days") {
                        $formattedFirstDate->addDays(3);
                    }

                    if ($duration == "4-days") {
                        $formattedFirstDate->addDays(4);
                    }

                    if ($duration == "5-days") {
                        $formattedFirstDate->addDays(5);
                    }

                    if ($duration == "6-days") {
                        $formattedFirstDate->addDays(6);
                    }

                    if ($duration == "week") {
                        $formattedFirstDate->addWeek();
                    }

                    if ($duration == "2-weeks") {
                        $formattedFirstDate->addWeeks(2);
                    }

                    if ($duration == "3-weeks") {
                        $formattedFirstDate->addWeeks(3);
                    }

                    if ($duration == "month") {
                        $formattedFirstDate->addMonth();
                    }

                    if ($duration == "2-months") {
                        $formattedFirstDate->addMonths(2);
                    }

                    if ($duration == "3-months") {
                        $formattedFirstDate->addMonths(3);
                    }

                    if ($duration == "4-months") {
                        $formattedFirstDate->addMonths(4);
                    }

                    if ($duration == "5-months") {
                        $formattedFirstDate->addMonths(5);
                    }

                    if ($duration == "6-months") {
                        $formattedFirstDate->addMonths(6);
                    }

                    if ($duration == "year") {
                        $formattedFirstDate->addYear();
                    }

                    if ($duration == "2-years") {
                        $formattedFirstDate->addYears(2);
                    }

                    if ($duration == "3-years") {
                        $formattedFirstDate->addYears(3);
                    }

                    if ($duration == "4-years") {
                        $formattedFirstDate->addYears(4);
                    }

                    if ($duration == "5-years") {
                        $formattedFirstDate->addYears(5);
                    }

                    if ($duration == "10-years") {
                        $formattedFirstDate->addYears(10);
                    }

                    if ($duration == "15-years") {
                        $formattedFirstDate->addYears(15);
                    }

                    if ($duration == "20-years") {
                        $formattedFirstDate->addYears(20);
                    }

                    if ($duration == "25-years") {
                        $formattedFirstDate->addYears(25);
                    }

                    $payment->payment_date = $formattedFirstDate->toDateString();

                }

                $payment->instalments_remaining = $payment->instalments_remaining - $instalments;
                $payment->save();
            }
        }

    }

    public function updateFirstInstalment($orderRef, $merchantId)
    {
        $payments = DeferredCharge::PaymentsByRef($orderRef, $merchantId);
        foreach ($payments as $payment) {
            if ($payment->order_type == 1) {
                $payment->instalments_remaining = 0;
                $payment->status = 0;
                $payment->save();
            }
        }
    }

    public function retryFailedPayments($orderRef, $merchantId)
    {
        $payments = DeferredCharge::PaymentsByRef($orderRef, $merchantId);
        $retrial = true;


        foreach ($payments as $payment) {

            $user = User::findorFail($payment->user_id);
            $this->setStripeCredential($user->merchant_id);

            if ($payment->order_type == 1 && $payment->status == -1) {

                $resp = $this->chargeClient(
                    $user,
                    $payment->amount / 100,
                    $payment->currency_code,
                    $payment->description,
                    $payment->payment_gateway,
                    $payment->tax,
                    "dc-" . $payment->id,
                    $payment->order_ref

                );

                if (!is_null($resp)) {
                    $payment->status = 0;
                    $payment->instalments_remaining = 0;
                    $payment->save();
                } else {
                    $retrial = false;
                    $payment->status = -1;
                    $payment->save();
                }


            }


            if ($payment->order_type == 2 && $payment->status == -1) {


                $now = Carbon::now();
                $formattedFirstDate = Carbon::parse($payment->payment_date);
                $duration = $payment->duration;

                while ($now > $formattedFirstDate && $payment->instalments_remaining > 0) {

                    $resp = $this->chargeClient(
                        $user,
                        $payment->amount / 100,
                        $payment->currency_code,
                        $payment->description,
                        $payment->payment_gateway,
                        $payment->tax,
                        "dc-" . $payment->id,
                        $payment->order_ref

                    );

                    if (is_null($resp)) {
                        $payment->status = -1;
                        $payment->save();
                        $retrial = false;
                        exit();
                        break;
                    }

                    if ($duration == "day") {
                        $formattedFirstDate->addDay();
                    }

                    if ($duration == "2-days") {
                        $formattedFirstDate->addDays(2);
                    }

                    if ($duration == "3-days") {
                        $formattedFirstDate->addDays(3);
                    }

                    if ($duration == "4-days") {
                        $formattedFirstDate->addDays(4);
                    }

                    if ($duration == "5-days") {
                        $formattedFirstDate->addDays(5);
                    }

                    if ($duration == "6-days") {
                        $formattedFirstDate->addDays(6);
                    }

                    if ($duration == "week") {
                        $formattedFirstDate->addWeek();
                    }

                    if ($duration == "2-weeks") {
                        $formattedFirstDate->addWeeks(2);
                    }

                    if ($duration == "3-weeks") {
                        $formattedFirstDate->addWeeks(3);
                    }

                    if ($duration == "month") {
                        $formattedFirstDate->addMonth();
                    }

                    if ($duration == "2-months") {
                        $formattedFirstDate->addMonths(2);
                    }

                    if ($duration == "3-months") {
                        $formattedFirstDate->addMonths(3);
                    }

                    if ($duration == "4-months") {
                        $formattedFirstDate->addMonths(4);
                    }

                    if ($duration == "5-months") {
                        $formattedFirstDate->addMonths(5);
                    }

                    if ($duration == "6-months") {
                        $formattedFirstDate->addMonths(6);
                    }

                    if ($duration == "year") {
                        $formattedFirstDate->addYear();
                    }

                    if ($duration == "2-years") {
                        $formattedFirstDate->addYears(2);
                    }

                    if ($duration == "3-years") {
                        $formattedFirstDate->addYears(3);
                    }

                    if ($duration == "4-years") {
                        $formattedFirstDate->addYears(4);
                    }

                    if ($duration == "5-years") {
                        $formattedFirstDate->addYears(5);
                    }

                    if ($duration == "10-years") {
                        $formattedFirstDate->addYears(10);
                    }

                    if ($duration == "15-years") {
                        $formattedFirstDate->addYears(15);
                    }

                    if ($duration == "20-years") {
                        $formattedFirstDate->addYears(20);
                    }

                    if ($duration == "25-years") {
                        $formattedFirstDate->addYears(25);
                    }

                    $payment->payment_date = $formattedFirstDate->toDateString();

                    if ($payment->instalments_remaining > 0) {
                        $payment->instalments_remaining = ($payment->instalments_remaining) - 1;
                        $payment->status = 1;
                        $agreement = SAAgreement::GetBySlug($payment->order_ref)[0];
                        $agreement->status = 1;
                        $agreement->save();

                    }

                    if ($payment->instalments_remaining == 0) {
                        $payment->status = 0;
                        $agreement = SAAgreement::GetBySlug($payment->order_ref)[0];
                        $agreement->status = 0;
                        $agreement->save();
                    }
                }


                $payment->save();
            }
        }

        return $retrial;
    }


    /**
     * Get all failed payments
     *
     * @return mixed
     */
    public function allFailedPayments()
    {
        return DeferredCharge::AllFailedPayments();
    }

    /**
     * Get all failed payment for a merchant
     *
     * @return mixed
     */
    public function failedPaymentsByMerchant()
    {
        return DeferredCharge::FailedPaymentsByMerchant();
    }

    /**
     * Get all failed payments for a user
     *
     * @return mixed
     */
    public function failedPaymentsByUser()
    {
        return DeferredCharge::FailedPaymentsByUser();
    }

    /**
     * Get all users on the platform
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function allUsers()
    {
        $users = User::all();
        return $users;
    }

    /**
     * Change the default payment gateway for the merchant
     *
     * @param $paymentGateway
     * @param $merchantId
     */
    public function defaultPaymentGateway($paymentGateway, $merchantId)
    {
        $merchant = Merchant::findorFail($merchantId)[0];
        $merchant->payment_gateway = $paymentGateway;
        $merchant->save();
    }

    /**
     * Gocardless Pro only!
     *
     * Add customer and add their bank account to go cardless
     * @param $user
     * @param $countryCode
     * @param $orderId
     * @param $accountNumber
     * @param $sortCode
     * @return array
     */
    public function addGCCustomerAndBankAccount(
        $user,
        $countryCode,
        $orderId,
        $accountNumber,
        $sortCode,
        $iban = null
    )
    {
        $goCardless = array();
        $this->setGoCardlessCredential($user->merchant_id);
        $customerId = $this->goCardless->addGCCustomer($user->email, $user->name, $countryCode);
        $backAccountId = $this->goCardless->addBankAccount(
            $accountNumber,
            $sortCode,
            $user->name,
            $countryCode,
            $customerId,
            $iban
        );

        $mandateId = $this->goCardless->addGCMandate($orderId, $backAccountId);
        $goCardless['customer_id'] = $customerId;
        $goCardless['mandate_id'] = $mandateId;

        $gatewayCredentials = $user->customerDetail->all();

        if (count($gatewayCredentials) < 1) {
            $customerDetail = new PaymentGatewayCustomerDetail();
            $customerDetail->gc_customer_id = $customerId;
            $customerDetail->gc_mandate_id = $mandateId;
            // add this if same customer id column is used for different gateways
            // $customerDetail->payment_gateway_id = 2;
            $customerDetail->user_id = $user->id;
            $customerDetail->save();

        } else {
            $customerDetail = PaymentGatewayCustomerDetail::findorFail($gatewayCredentials[0]->id);
            $customerDetail->gc_customer_id = $customerId;
            $customerDetail->gc_mandate_id = $mandateId;
            // $customerDetail->payment_gateway_id = 2;
            $customerDetail->user_id = $user->id;
            $customerDetail->save();
        }

        return $goCardless;
    }

    /**
     * Charge a GoCardless customer
     *
     * @param $customer
     * @param $amount
     * @param $currencyCode
     * @param $orderId
     * @param int $deferredPaymentRef
     * @return mixed
     */
    public function chargeGCCustomer($customer, $amount, $currencyCode, $orderId, $deferredPaymentRef = 0)
    {
        $gatewayCredentials = $customer->customerDetail->all();
        $this->setGoCardlessCredential($customer->merchant_id);
        $paymentId = $this->goCardless->chargeGCCustomer(
            $amount,
            $currencyCode,
            $gatewayCredentials[0]->gc_mandate_id,
            $orderId,
            $deferredPaymentRef
        );

        return $paymentId;
    }

    /**
     * @param $customer
     * @param $refundAmount
     * @param $totalAmount
     * @param $paymentId
     */
    public function processGCRefund($customer, $refundAmount, $totalAmount, $paymentId)
    {
        $this->setGoCardlessCredential($customer->merchant_id);
        $this->goCardless->processGCRefund($refundAmount, $totalAmount, $paymentId);
    }

    /**
     * Cancel Gocardless subscription
     *
     * @param $subscriptionId
     */
    public function cancelGCSubscription($subscriptionId)
    {
        $this->goCardless->cancelGCSubscription($subscriptionId);
    }

    /**
     * @param $customer
     * @param $amount
     * @param $currencyCode
     * @param $duration
     * @param $day
     * @param $count
     * @param $orderId
     * @param int $deferredPaymentRef
     * @return mixed
     */
    public function addGCSubscription(
        $customer,
        $amount,
        $currencyCode,
        $duration,
        $day,
        $count,
        $orderId,
        $deferredPaymentRef = 0
    )
    {
        $gatewayCredentials = $customer->customerDetail->all();
        $this->setGoCardlessCredential($customer->merchant_id);
        $subscriptionId = $this->goCardless->addGCSubscription(
            $amount,
            $currencyCode,
            $duration,
            $day,
            $count,
            $gatewayCredentials[0]->gc_mandate_id,
            $orderId,
            $deferredPaymentRef
        );

        return $subscriptionId;
    }

    /**
     * Get the redirect URL for Gocardless
     * @param $customer
     * @param $description
     * @param $userId
     * @param $successURL
     * @return mixed
     */
    public function getGCRedirectURL($customer, $description, $successURL)
    {
        $this->setGoCardlessCredential($customer->merchant_id);
        $url = $this->goCardless->generateGCRedirectURL(
            $description,
            $successURL,
            $customer
        );

        return $url;
    }

    /**
     * @param $ref
     * @param $merchantId
     * @return mixed
     */
    public function getPaymentInformation($ref, $merchantId)
    {
        return DeferredCharge::PaymentsByRef($ref, $merchantId);
    }

    /**
     * @param $orderRef
     * @param $merchantId
     */
    public function cancelDeferredCharges($orderRef, $merchantId)
    {
        $payments = DeferredCharge::PaymentsByRef($orderRef, $merchantId);
        foreach ($payments as $payment) {
            $payment->status = 0;
            $payment->save();
        }

    }

    /**
     * @param $ref
     * @param $merchantId
     * @param $percent
     */
    public function refundAgreement($ref, $merchantId, $percent)
    {
        $payments = Payment::GetPaymentFromOrderRef($ref, $merchantId);
        $merchant = Merchant::findorFail($merchantId);
        $gateways = ($merchant->gateways->all());

        $gocardlessCredential = null;
        $credential2 = null;

        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential2 = $gateway->credential_2;
            }
            if ($gateway->gateway == 2) {
                $gocardlessCredential = $gateway->credential_1;
            }
        }

        if (!is_null($credential2)) {
            $stripe = new Stripe($credential2);
        }

        if (!is_null($gocardlessCredential)) {
            $goCardless = new GoCardless($gocardlessCredential);
        }

        foreach ($payments as $payment) {

            $chargeId = $payment->charge_id;

            if ($chargeId[0] == "c") {
                $stripe->refundCharge($payment->charge_id, round($payment->amount * $percent));
            } else {
                $goCardless->processGCRefund($payment->amount * $percent, $payment->amount * 100, $payment->charge_id);
            }
        }

    }


    /**
     * @param $redirectId
     * @param $merchantId
     * @param $customer
     * @return array
     */
    public function goCardlessCredentials($redirectId, $merchantId, $customer)
    {

        $goCardless = array();

        $this->setGoCardlessCredential($merchantId);
        $redirectFlow = $this->goCardless->createRedirectFlow($redirectId, $customer->user_id);

        $customerId = $redirectFlow->links->customer;
        $mandateId = $redirectFlow->links->mandate;
        $gatewayCredentials = $customer->customerDetail->all();

        if (count($gatewayCredentials) < 1) {
            $customerDetail = new PaymentGatewayCustomerDetail();
            $customerDetail->gc_customer_id = $customerId;
            $customerDetail->gc_mandate_id = $mandateId;
            // add this if same customer id column is used for different gateways
            // $customerDetail->payment_gateway_id = 2;
            $customerDetail->user_id = $customer->id;
            $customerDetail->save();

        } else {
            $customerDetail = PaymentGatewayCustomerDetail::findorFail($gatewayCredentials[0]->id);
            $customerDetail->gc_customer_id = $customerId;
            $customerDetail->gc_mandate_id = $mandateId;
            // $customerDetail->payment_gateway_id = 2;
            $customerDetail->user_id = $customer->id;
            $customerDetail->save();
        }

        return $goCardless;

    }

    /**
     * @param $planDetails
     * @param $vendorRef
     * @param $currencyCode
     * @param $tax
     * @param $price
     * @param $firstPayment
     * @param $balance
     * @param $instalments
     * @param $products
     * @param $userId
     * @param $merchantId
     * @return mixed|string
     */
    public function createOrder($planDetails, $vendorRef, $currencyCode, $tax, $price, $firstPayment, $balance, $instalments, $products, $userId, $merchantId)
    {
        $order = new Order();
        $order->order_id = str_random(20);
        $order->plan_id = $planDetails;
        $order->vendor_reference = $vendorRef;
        $order->currency_code = $currencyCode;
        $order->tax = $tax;
        $order->adjusted_price = $price;
        $order->initial_payment = $firstPayment;
        $order->balance = $balance;
        $order->installments = $instalments;
        $order->user_id = $userId;
        $order->merchant_id = $merchantId;
        $order->status = 1;
        $order->save();

        $data = array();
        array_push($data, $order);
        $productsArray = ['products' => $products];
        array_push($data, $productsArray);

        $dynamo = new DynamoDbAccess();
        $dynamo->addData($order->order_id, $data->toArray());

        return $order->order_id;
    }


    public function createDynamicQuote()
    {

    }

    public function updateDynamicQuote()
    {

    }

    public function deleteDynamicQuote()
    {

    }

    public function allDynamicQuotes()
    {

    }

    /**
     * @param $slug
     * @return bool
     */
    public function getAgreement($slug)
    {

        $agreement = SAAgreement::GetBySlug($slug);

        if (count($agreement) > 0) {
            return $agreement[0];
        } else {
            return false;
        }

    }

    /**
     * @param $merchantId
     * @return mixed
     */
    public function GetAllAgreementsForMerchant($merchantId)
    {
       return  SAAgreement::GetAllAgreementsForMerchant($merchantId);
    }

    /**
     * @param $plan
     * @param $currencyCode
     * @param $duration
     * @param $quoteTotal
     * @param $gatewayId
     * @param $customerId
     * @param $qProducts
     * @param $paymentCheck
     */
    public function createAgreement(
        $plan,
        $currencyCode,
        $duration,
        $gatewayId,
        $dynamicProducts,
        $paymentCheck,
        $firstPayment,
        $recurringPayment,
        $quoteId,
        $customerId,
        $merchantId
    )
    {


        $billingStartDate = $plan->billing_start;
        $firstPaymentDateDelay = $plan->first_payment_date;
        $frequency = $plan->billing_period;


        $now = Carbon::now();



        if ($billingStartDate == 1) {
            $firstInvoiceDate = $now;
        } elseif ($billingStartDate == 2) {
            if ($firstPaymentDateDelay > 1) {
                $firstInvoiceDate = $now->addDays($firstPaymentDateDelay);
            } elseif ($firstPaymentDateDelay == 1) {
                $firstInvoiceDate = $now->addDay(1);
            } else {
                $firstInvoiceDate = $now;
            }

        } else {

            if ($firstPaymentDateDelay > 1) {
                $firstInvoiceDate = $now->addDays($firstPaymentDateDelay);
            } elseif ($firstPaymentDateDelay == 1) {
                $firstInvoiceDate = $now->addDay(1);
            } else {
                $firstInvoiceDate = $now;
            }

            $firstInvoiceDate = $this->getFirstInvoiceDate($frequency, $firstInvoiceDate);
        }


        $firstRecurringBillingDate = $this->getRecurringInvoiceDate($frequency, $firstInvoiceDate);




        $agreement = new SAAgreement();
        $agreement->slug = str_random(30);
        $agreement->merchant_slug = str_random(30);

        $agreement->structure = $plan->structure;
        $agreement->billing_start = $plan->billing_start;
        $agreement->billing_period = $plan->billing_period;
        $agreement->payment_info_required = $plan->payment_info_required;
        $agreement->different_first_payment = $plan->different_first_payment;
        $agreement->can_cancel = $plan->can_cancel;
        $agreement->cancellation_days = $plan->cancellation_days;
        $agreement->refund_check = $plan->refund_check;
        $agreement->refund_percent = $plan->refund_percent;
        $agreement->renewal = $plan->renewal;
        $agreement->currency_code = $currencyCode;
        $agreement->terms = $plan->terms;
        $agreement->agreement_term= $plan->agreement_term;

        $agreement->instalments = $duration;
        $agreement->instalments_remaining =$duration;

        $agreement->first_payment = $firstPayment;
        $agreement->first_payment_date = $firstInvoiceDate;
        $agreement->recurring_payment = $recurringPayment;
        $agreement->recurring_payment_date = $firstRecurringBillingDate;

        $agreement->quote_id = $quoteId;


        $agreement->user_id_string = $customerId;
        $agreement->merchant_id = $plan->merchant_id;
        $agreement->gateway = $gatewayId;


        $userId = User::GetUserForMerchant($customerId,$plan->merchant_id)[0];

        $agreement->user_id = $userId->id;


        $agreement->merchant_id = $merchantId;



        if ($paymentCheck) {
            $agreement->status = 1;
        } else {
            $agreement->status = -2;
        }

        $agreement->save();


        if(is_array($dynamicProducts)){
            if(count($dynamicProducts)>0){
                foreach ($dynamicProducts as $dynamicProduct) {
                    $dProduct = new DynamicProduct();
                    $dProduct->price= $dynamicProduct->price;
                    $dProduct->tax= $dynamicProduct->tax;
                    $dProduct->currency= $dynamicProduct->currency;
                    $dProduct->details= $dynamicProduct->details;
                    $dProduct->s_a_agreement_id= $agreement->id;
                }
            }
        }


        return $agreement;



    }


    /**
     * @param $ref
     * @return bool
     */
    public function cancelAgreement($ref)
    {

        $agreement = SAAgreement::GetAAgreementForMerchant($ref);

        if (count($agreement) > 0) {

            $agreement = SAAgreement::findorFail($agreement[0]->id);
            $agreement->status =2;
            $agreement->save();
            return true;
        } else {
            return false;
        }


    }

    /**
     * @param $agreementSlug
     * @param $merchantId
     * @return array
     */
    public function failedPayments($agreementSlug,$merchantId)
    {
        $payments = DeferredCharge::PaymentsByRef($agreementSlug, $merchantId);
        $failedPayments = array();

        foreach ($payments as $payment) {

            $user = User::findorFail($payment->user_id);
            $this->setStripeCredential($user->merchant_id);

            if ($payment->order_type == 1 && $payment->status == -1) {
                array_push($failedPayments,$payment);
            }
        }

        return $failedPayments;
    }



    /**
     * Converts frequency integer to string
     *
     * @param $frequency
     * @return string
     */
    private function getFrequencyString($frequency)
    {
        switch ($frequency) {
            case 1:
                $frequency = "daily";
                break;
            case 2:
                $frequency = "weekly";
                break;
            case 3:
                $frequency = "monthly";
                break;
            case 4:
                $frequency = "quarterly";
                break;
            case 5:
                $frequency = "bi-annually";
                break;
            case 6:
                $frequency = "yearly";
                break;
        }

        return $frequency;
    }


    private function getFrequencyStringForAPI($frequency)
    {
        switch ($frequency) {
            case 1:
                $frequency = "day";
                break;
            case 2:
                $frequency = "week";
                break;
            case 3:
                $frequency = "month";
                break;
            case 4:
                $frequency = "3-months";
                break;
            case 5:
                $frequency = "6-months";
                break;
            case 6:
                $frequency = "year";
                break;
        }

        return $frequency;
    }

    /**
     * Returns the first payment date
     *
     * @param $frequency
     * @param $firstInvoiceDate
     * @return mixed
     */
    private function getFirstInvoiceDate($frequency, $firstInvoiceDate)
    {
        switch ($frequency) {
            case 1:
                $firstInvoiceDate = $firstInvoiceDate->addDay(1);
                break;
            case 2:
                $firstInvoiceDate = $firstInvoiceDate->addWeek(1);
                break;
            case 3:
                $firstInvoiceDate = $firstInvoiceDate->addMonth(1);
                break;
            case 4:
                $firstInvoiceDate = $firstInvoiceDate->addMonths(3);
                break;
            case 5:
                $firstInvoiceDate = $firstInvoiceDate->addMonths(6);
                break;
            case 6:
                $firstInvoiceDate = $firstInvoiceDate->addYear(1);
                break;
        }

        return $firstInvoiceDate;
    }

    /**
     * Returns the recurring payment date
     *
     * @param $frequency
     * @param $firstInvoiceDate
     * @return mixed
     */
    private function getRecurringInvoiceDate($frequency, $firstInvoiceDate)
    {
        switch ($frequency) {
            case 1:
                $firstRecurringBillingDate = (clone $firstInvoiceDate)->addDay(1);
                break;
            case 2:
                $firstRecurringBillingDate = (clone $firstInvoiceDate)->addWeek(1);
                break;
            case 3:
                $firstRecurringBillingDate = (clone $firstInvoiceDate)->addMonth(1);
                break;
            case 4:
                $firstRecurringBillingDate = (clone $firstInvoiceDate)->addMonths(3);
                break;
            case 5:
                $firstRecurringBillingDate = (clone $firstInvoiceDate)->addMonths(6);
                break;
            case 6:
                $firstRecurringBillingDate = (clone $firstInvoiceDate)->addYear(1);
                break;
        }

        return $firstRecurringBillingDate;
    }

    /**
     * DEMO ONLY - USE stripe.js to create this token on merchant side
     * Return stripe token
     *
     * @param $stripePrivateKey
     * @param $request
     * @return Token
     */
    private function generateStripeToken($stripePrivateKey, $request)
    {

        Stripe::setApiKey($stripePrivateKey);
        $token = Token::create(array(
            "card" => array(
                "number" => str_replace(" ", "", $request->get('number')),
                "exp_month" => $request->get('month'),
                "exp_year" => $request->get('year'),
                "cvc" => $request->get('cvc')
            )
        ));

        return $token;
    }
}