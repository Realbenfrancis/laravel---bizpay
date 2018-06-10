<?php

namespace App\Http\Controllers;

use App\Http\Bizpay\DynamoDbAccess;
use App\Http\Bizpay\Stripe;
use App\Http\Models\CustomerSubscriptions;
use App\Http\Models\Merchant;
use App\Http\Models\Order;
use App\Http\Models\Payment;
use App\Http\Models\PaymentGatewayCustomerDetail;
use App\Http\Models\Product;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Nexmo\Laravel\Facade\Nexmo;
use Stripe\Token;

/**
 * Class ClientController
 * @package App\Http\Controllers
 */
class ClientController extends Controller
{
    /**
     * ClientController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('client');
        $this->middleware('merchant.check'); // check merchant is active
    }


    /**
     * Returns all  subscriptions for this client
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscriptions()
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions->all();

        return view('dashboard.shared.subscriptions', compact('subscriptions'));
    }

    /**
     * Returns payments made by this client
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payments()
    {
        $user = Auth::user();
        $payments = $user->payments->all();

        return view('dashboard.shared.payments', compact('payments'));
    }

    /**
     * Merchant Shop
     * All products are currently listed here - including instalments
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function shop()
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $products = $merchant->products->all();

        return view('dashboard.client.shop', compact('products'));
    }

    /**
     * Display form to add a new payment card or current card if the client has already
     * added the card
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function card()
    {
        $user = Auth::user();
        $bizpay = Merchant::findorFail($user->merchant_id);
        $gateways = ($bizpay->gateways->all());
        $gatewayCredentials = $user->customerDetail->all();

        if (count($gatewayCredentials) < 1) {
            $credential1 = "";
            $credential2 = "";

            //TODO: Multi-gateway: check for default

            foreach ($gateways as $gateway) {
                if ($gateway->gateway == 1) {
                    $credential1 = $gateway->credential_1;
                    $credential2 = $gateway->credential_2;
                }
            }

            return view('dashboard.merchant.create', compact('credential1', 'credential2'));
        } else {
            $card = ($gatewayCredentials[0]);

            return view('dashboard.client.card', compact('card'));
        }

    }

    /**
     * Same form - used for updating
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateCard()
    {
        $user = Auth::user();
        $bizpay = Merchant::findorFail($user->merchant_id);
        $gateways = ($bizpay->gateways->all());
        $credential1 = "";
        $credential2 = "";

        //TODO: check for default

        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential1 = $gateway->credential_1;
                $credential2 = $gateway->credential_2;
            }
        }

        return view('dashboard.merchant.create', compact('credential1', 'credential2'));
    }

    /**
     * Add the card details obtained from the payment gateway to db
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function addCard(Request $request)
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $gateways = ($merchant->gateways->all());

        //TODO: add support for multiple gateways
        // look for $merchant->gateway

        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential2 = $gateway->credential_2;
            }
        }

        $stripe = new Stripe($credential2);
        $gatewayCredentials = $user->customerDetail->all();

        if (count($gatewayCredentials) < 1) {
            $response = $stripe->addCustomer($request->get('stripeToken'), $user->name, $user->email);
            $token = Token::retrieve($request->get('stripeToken'), ['api_key' => $credential2]);
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
            $customer->sources->retrieve($customerDetail->stripe_card_id)->delete();
            $customer = \Stripe\Customer::retrieve($customerDetail->stripe_customer_id);
            $customer->sources->create(array("source" => $request->get('stripeToken')));
            $token = Token::retrieve($request->get('stripeToken'), ['api_key' => $credential2]);
            $customerDetail->stripe_card_id = $token->card->id;
            $customerDetail->card_expiry_month = $token->card->exp_month;
            $customerDetail->card_expiry_year = $token->card->exp_year;
            $customerDetail->card_last_four = $token->card->last4;
            $customerDetail->card_brand = $token->card->brand;
            $customerDetail->card_country = $token->card->country;
            $customerDetail->payment_gateway_id = 1;
            $customerDetail->user_id = $user->id;
            $customerDetail->save();
        }

        return redirect('/client/card');
    }

    /**
     * Returns cart
     * Not part of spec
     *
     */
    public function cart()
    {
    }

    /**
     * List of all orders by this client
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders->all();
        return view('dashboard.shared.orders', compact('orders'));
    }

    /**
     * Process the order request and take payment
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function buy(Request $request)
    {

        $user = Auth::user();
        $gatewayCredentials = $user->customerDetail->all();
        $merchant = Merchant::findorFail($user->merchant_id);
        $tax = ($merchant->tax / 100);
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

        if (count($gatewayCredentials) < 1) {
            return redirect('/client/card');
        }

        // change to support cart - loop

        //TODO: get charge id

        $product = Product::GetProductFromSlug($request->get('id'))[0];
        if ($tax > 0) {
            $total = $product->price + $product->price * $tax;
            $order->tax = $product->price * $tax;
            $taxPercent = $tax * 100;
        } else {
            $total = $product->price;
            $order->tax = 0.00;
            $taxPercent = 0.00;
        }

        if ($product->type == 1) {
            $resp = $stripe->chargeCustomer(
                $gatewayCredentials[0]->stripe_customer_id,
                $total * 100,
                $product->currency_code,
                $product->name
            );

            $payment->charge_id = $resp->id; // charge_id from gateway for refund
            $order->installments = 1;
            $order->price = $product->price;
            $order->adjusted_price = $product->price;
            $order->initial_payment = $product->price;
            $payment->amount = $product->price;
            $payment->currency_code = $product->currency_code;

        } elseif ($product->type == 2) {
            $response = $stripe->subscribe(
                $gatewayCredentials[0]->stripe_customer_id,
                $product->product_id,
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

            $order->price = $product->price;
            $order->adjusted_price = $product->price;
            $order->initial_payment = $product->price;
            $order->installments = -1;
            $order->currency_code = $product->currency_code;
            $payment->currency_code = $product->currency_code;
            $payment->amount = $product->price;
        }
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

        Nexmo::message()->send([
            'to' => $user->phone_number,
            'from' => "Bizpay", // change to $merchant->merchant_name if merchant name has to be shown
            'text' => 'Thank you for buying from ' . $merchant->merchant_name
        ]);

        return redirect('/client/orders');
    }

    /**
     * Cancel one of the active subscriptions
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

    //Not part of spec

    public function acceptInstallment()
    {
    }

    public function processAcceptInstallment()
    {
    }

    public function installments()
    {
    }


    /**
     * Returns all plans for an instalment product
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function plans(Request $request)
    {
        $product = Product::GetProductFromSlug($request->get('id'))[0];
        $merchant = Merchant::findorFail(Auth::user()->merchant_id);
        $tax = $merchant->tax / 100;
        $rules = $merchant->rules->all();
        $firstPaymentPercent = 1.00;
        $priceHike1 = 1.3; // default values
        $priceHike2 = 1.3;
        $priceHike3 = 1.3;

        foreach ($rules as $rule) {
            if ($rule->apply_rule_on == "instalment" && $rule->action_on == "first_payment") {
                if ($rule->action_type == "Percentage") {
                    $firstPaymentPercent = ($rule->action_value / 100);
                }
            }

            if ($rule->apply_rule_on == "plan" && $rule->action_on == "price") {
                if ($rule->action_type == "Percentage") {
                    if ($rule->limit1 == "0" && $rule->limit2 == 4) {
                        $priceHike1 = 1 + ($rule->action_value / 100);
                    }

                    if ($rule->limit1 == "3" && $rule->limit2 == 7) {
                        $priceHike2 = 1 + ($rule->action_value / 100);
                    }

                    if ($rule->limit1 == "6" && $rule->limit2 == 13) {
                        $priceHike3 = 1 + ($rule->action_value / 100);
                    }
                }
            }
        }

        $client = new Client();

        $response1 = $client->request('POST', 'https://bpapi.flymycloud.com/api/plans', [
            'form_params' => [
                'price' => ($product->price + $product->price * $tax) * $priceHike1 * 100,
                'months' => 4,
                'percent' => $firstPaymentPercent
            ]
        ]);


        $response2 = $client->request('POST', 'https://bpapi.flymycloud.com/api/plans', [
            'form_params' => [
                'price' => ($product->price + $product->price * $tax) * $priceHike2 * 100,
                'months' => 7,
                'percent' => $firstPaymentPercent
            ]
        ]);

        $response3 = $client->request('POST', 'https://bpapi.flymycloud.com/api/plans', [
            'form_params' => [
                'price' => ($product->price + $product->price * $tax) * $priceHike3 * 100,
                'months' => 13,
                'percent' => $firstPaymentPercent
            ]
        ]);

        $plans1 = (\GuzzleHttp\json_decode($response1->getBody()->getContents()));
        $plans2 = (\GuzzleHttp\json_decode($response2->getBody()->getContents()));
        $plans3 = (\GuzzleHttp\json_decode($response3->getBody()->getContents()));
        $plans = collect([['plan_1' => $plans1->plan_1], ['plan_2' => $plans2->plan_2], ['plan_3' => $plans3->plan_3]]);

        return view('dashboard.client.select-plan', compact('product', 'plans'));
    }

    /**
     * Accept, process, take payment for a selected plan
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function processPlan(Request $request)
    {
        $user = Auth::user();
        $gatewayCredentials = $user->customerDetail->all();
        $merchant = Merchant::findorFail(Auth::user()->merchant_id);
        $tax = $merchant->tax / 100;
        $rules = $merchant->rules->all();
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

        if (count($gatewayCredentials) < 1) {
            return redirect('/client/card');
        }

        // change to support cart - loop
        //TODO: get charge id

        $product = Product::GetProductFromSlug($request->get('id'))[0];
        $firstPaymentPercent = 1.00;
        $priceHike1 = 1.3;
        $priceHike2 = 1.3;
        $priceHike3 = 1.3;


        foreach ($rules as $rule) {
            if ($rule->apply_rule_on == "instalment" && $rule->action_on == "first_payment") {
                if ($rule->action_type == "Percentage") {
                    $firstPaymentPercent = ($rule->action_value / 100);
                }
            }


            if ($rule->apply_rule_on == "plan" && $rule->action_on == "price") {
                if ($rule->action_type == "Percentage") {
                    if ($rule->limit1 == "0" && $rule->limit2 == 4) {
                        $priceHike1 = 1 + ($rule->action_value / 100);
                    }

                    if ($rule->limit1 == "3" && $rule->limit2 == 7) {
                        $priceHike2 = 1 + ($rule->action_value / 100);
                    }

                    if ($rule->limit1 == "6" && $rule->limit2 == 13) {
                        $priceHike3 = 1 + ($rule->action_value / 100);
                    }
                }
            }
        }

        $client = new Client();

        if ($request->get('plan') == 1) {
            if ($tax > 0) {
                $total = ($product->price + $product->price * $tax) * $priceHike1;
                $order->tax = $product->price * $tax;
                $taxPercent = $tax * 100;
            } else {
                $total = $product->price * $priceHike1;
                $order->tax = 0.00;
                $taxPercent = 0.00;
            }

            $response = $client->request('POST', 'https://bpapi.flymycloud.com/api/plans', [
                'form_params' => [
                    'price' => ($product->price + $product->price * $tax) * $priceHike1 * 100,
                    'months' => 4,
                    'percent' => $firstPaymentPercent
                ]
            ]);
        }

        if ($request->get('plan') == 2) {
            if ($tax > 0) {
                $total = ($product->price + $product->price * $tax) * $priceHike2;
                $order->tax = $product->price * $tax;
                $taxPercent = $tax * 100;
            } else {
                $total = $product->price * $priceHike2;
                $order->tax = 0.00;
                $taxPercent = 0.00;
            }

            $response = $client->request('POST', 'https://bpapi.flymycloud.com/api/plans', [
                'form_params' => [
                    'price' => ($product->price + $product->price * $tax) * $priceHike2 * 100,
                    'months' => 7,
                    'percent' => $firstPaymentPercent
                ]
            ]);
        }

        if ($request->get('plan') == 3) {
            if ($tax > 0) {
                $total = ($product->price + $product->price * $tax) * $priceHike3;
                $order->tax = $product->price * $tax;
                $taxPercent = $tax * 100;
            } else {
                $total = $product->price * $priceHike3;
                $order->tax = 0.00;
                $taxPercent = 0.00;
            }

            $response = $client->request('POST', 'https://bpapi.flymycloud.com/api/plans', [
                'form_params' => [
                    'price' => ($product->price + $product->price * $tax) * $priceHike3 * 100,
                    'months' => 13,
                    'percent' => $firstPaymentPercent
                ]
            ]);
        }

        $plans = (\GuzzleHttp\json_decode($response->getBody()->getContents()));
        $planId = "plan_" . $request->get('plan');
        $plan = ($plans->$planId);
        $resp = $stripe->chargeCustomer(
            $gatewayCredentials[0]->stripe_customer_id,
            $total * 100,
            $product->currency_code,
            $product->name
        );

        $payment->charge_id = $resp->id;
        $order->installments = $plan->instalments;
        $order->price = $product->price;
        $order->adjusted_price = $total;
        $order->initial_payment = $plan->initial_payment_amount / 100;
        $order->currency_code = $product->currency_code;
        $payment->amount = $plan->initial_payment_amount / 100;
        $payment->currency_code = $product->currency_code;
        $order->balance = ($plan->recurring_payment_amount / 100) * $plan->instalments;
        $order->user_id = $user->id;
        $order->merchant_id = $user->merchant_id;
        $order->status = 1;
        $order->save();

        //save to DynamoDB
        $dynamo = new DynamoDbAccess();
        $dynamo->addData($order->id, $order);

        $payment->order_id = $order->id;
        $payment->user_id = $user->id;
        $payment->merchant_id = $user->merchant_id;
        $payment->status = 1;
        $payment->save();

        Nexmo::message()->send([
            'to' => '447852534842', //$user->phone_number
            'from' => "Bizpay", //$merchant->merchant_name
            'text' => 'Thank you for buying from ' . $merchant->merchant_name
        ]);

        return redirect('/client/orders');
    }

    /**
     * Return profile details for client
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();

        return view('dashboard.shared.profile', compact('user'));
    }

    /**
     * Save profile details
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveProfile(Request $request)
    {
        $user = User::findorFail(Auth::user()->id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->phone_number = $request->get('phone_number');
        $user->save();

        return redirect()->back();
    }


    /**
     * Display change password form
     *
     * @return mixed
     */
    public function changePassword()
    {
        $user = Auth::user();

        return view('dashboard.shared.change-password', compact('user'));
    }

    /**
     * Save the new passport if it passes validation.
     *
     * @param Request $request
     * @return mixed
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


    public function goCardlessAddCustomer(Request $request)
    {

        $redirectId= $request->get('redirect_flow_id');

        $client = new \GoCardlessPro\Client([
            'access_token' => "sandbox_rZjViUG38AkoWFD8jJT4MKU_-6FXJsvGaDsEmbWY",
            'environment' => \GoCardlessPro\Environment::SANDBOX
        ]);

        $redirectFlow = $client->redirectFlows()->complete(
            $redirectId, //The redirect flow ID from above.
            ["params" => ["session_token" => "dummy_session_token"]]
        );

        print("Mandate: " . $redirectFlow->links->mandate . "<br />");
        print("Customer: " . $redirectFlow->links->customer . "<br />");

        //   Mandate: MD00022N28ZK4C
       //  Customer: CU00026X6Q13C9
    }
}
