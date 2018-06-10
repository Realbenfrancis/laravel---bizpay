<?php

namespace App\Http\Controllers;

use App\Http\Bizpay\Bizpay;
use App\Http\Bizpay\Stripe;
use App\Http\Bizpay\UserManagement;
use App\Http\Models\CustomerSubscriptions;
use App\Http\Models\Merchant;
use App\Http\Models\Payment;
use App\Http\Models\PaymentGateway;
use App\Http\Models\PaymentGatewayCustomerDetail;
use App\Http\Models\Product;
use App\Http\Models\Rule;
use App\Notifications\SendSMSAlert;
use App\Notifications\SendUserAccount;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Nexmo\Laravel\Facade\Nexmo;
use Stripe\Subscription;
use Stripe\Token;

/**
 * Class MerchantAdminController
 * @package App\Http\Controllers
 */
class MerchantAdminController extends Controller
{

    /**
     * MerchantAdminController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('merchant.admin');
        $this->middleware(
            'merchant.subscription',
            ['except' =>
                ['bizpaySubscription',
                    'subscribe',
                    'addCard',
                    'card']
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function clients()
    {
        $clients = User::GetClients();

        return view('dashboard.merchant.clients', compact('clients'));
    }

    public function addClient()
    {
        return view('dashboard.merchant.create-client');
    }

    public function processAddClient(Request $request)
    {
        $cUser = Auth::user();

        $userManagement = new UserManagement();
        $password = str_random(12);
        $user = $userManagement->addUser(
            $cUser->merchant_id,
            $request->get('name'),
            $request->get('email'),
            Hash::make($password)
        );

        Notification::send($user, new SendUserAccount($password));

        return redirect('/merchant-admin/clients');
    }


    public function managers()
    {
        $managers = User::GetMerchantManagers();

        return view('dashboard.merchant.managers', compact('managers'));
    }


    public function addManager()
    {
        return view('dashboard.merchant.create-manager');
    }

    public function processAddManager(Request $request)
    {
        $cUser = Auth::user();
        $userManagement = new UserManagement();
        $password = str_random(12);
        $user = $userManagement->addManager(
            $cUser->merchant_id,
            $request->get('name'),
            $request->get('email'),
            Hash::make($password)
        );

        Notification::send($user, new SendUserAccount($password));

        return redirect('/merchant-admin/managers');
    }

    public function deleteManager(Request $request)
    {
        $user = User::GetUserFromEmail($request->get('email'));
        $user[0]->delete();

        return redirect('/merchant-admin/managers');
    }

    /**
     * Add these to enable these features
     */
    public function disableManager()
    {
    }

    public function enableManager()
    {
    }

    /**
     * Add or Update Card for paying for Bizpay Subscriptions
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function card()
    {
        $bizpay = Merchant::findorFail(1); // hard coded to avoid another lookup
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
     * Display the Bizpay Subscriptions pages accordingly
     * - if there is no card - show add card page
     * - if there is card and no active subscription - show subscribe button or plans available
     * - if there is an active subscription - show subscription details or cancel subscription button
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bizpaySubscription()
    {
        $user = Auth::user();
        $gatewayCredentials = $user->customerDetail->all();

        if (count($gatewayCredentials) < 1) {
            $bizpay = Merchant::findorFail(1);
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

            //  Add or Update Card for paying for Bizpay Subscriptions
            return view('dashboard.merchant.create', compact('credential1', 'credential2'));

        } else {
            $subscriptions = $user->customerSubscriptions->all();
            if (count($subscriptions) < 1) {
                // show plans or subscribe button
                // pass plan ids here if different plans need to be shown
                return view('dashboard.merchant.subscription');
            } else {
                //return details for active subscription / cancel button
                // Get subscription details by calling Stripe function
                return view('dashboard.merchant.active-subscription', compact('subscriptions'));
            }
        }
    }

    /**
     * Returns all products added by this merchant
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function products()
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $products = $merchant->products->all();

        return view('dashboard.merchant.products', compact('products'));
    }

    /**
     * Show form to add a new product
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addProduct()
    {
        return view('dashboard.merchant.add-product');
    }


    /**
     * Show form to edit  product
     * Not part of spec
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editProduct()
    {
        // return view('dashboard.merchant.edit-product');
    }


    public function processAddProduct(Request $request)
    {

        $user = Auth::user();
        $product = new Product();
        $product->currency_code = $request->get('currency_code');
        $product->product_id = str_random(20);

        if ($request->get('type') == 2) {
            $product->duration = $request->get('duration');
            $bizpay = Merchant::findorFail($user->merchant_id);
            $gateways = ($bizpay->gateways->all());
            $credential2 = "";

            //TODO: check for default

            foreach ($gateways as $gateway) {
                if ($gateway->gateway == 1) {
                    $credential1 = $gateway->credential_1;
                    $credential2 = $gateway->credential_2;
                }
            }

            $stripe = new Stripe($credential2);
            $stripe->plan(
                $request->get('price'),
                $request->get('currency_code'),
                $product->product_id,
                $request->get('name'),
                $request->get('duration')
            );
        }

        $product->name = $request->get('name');
        $product->description = $request->get('description');
        $product->type = $request->get('type');
        $product->price = $request->get('price');
        $product->merchant_id = $user->merchant_id;
        $product->user_id = $user->id;
        $product->status = 1;
        $product->save();

        return redirect('/merchant-admin/products');
    }

    public function addCard(Request $request)
    {
        $user = Auth::user();
        $bizpay = Merchant::findorFail(1);
        $gateways = ($bizpay->gateways->all());

        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential1 = $gateway->credential_1;
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

        return redirect('/merchant-admin/bizpay-subscription');
    }

    public function subscribe()
    {
        $user = Auth::user();
        $gatewayCredentials = $user->customerDetail->all();

        $bizpay = Merchant::findorFail(1);
        $gateways = ($bizpay->gateways->all());

        if ($bizpay->tax > 0) {
            $tax = $bizpay->tax;
        } else {
            $tax = 0.00;
        }


        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential1 = $gateway->credential_1;
                $credential2 = $gateway->credential_2;
            }
        }

        $stripe = new Stripe($credential2);
        $response = $stripe->subscribe($gatewayCredentials[0]->stripe_customer_id, "plan1", $tax);

        $subscription = new CustomerSubscriptions();
        $subscription->subscription_id = $response->id;
        $subscription->start_date = $response->current_period_start;
        $subscription->end_date = $response->current_period_end;
        $subscription->merchant_id = $user->merchant_id;
        $subscription->payment_gateway_id = 1;
        $subscription->status = 1;
        $subscription->merchant_id = 1;
        $subscription->user_id = $user->id;
        $subscription->save();

        $merchant = Merchant::findorFail($user->merchant_id);
        $merchant->status = 1;
        $merchant->save();

        Nexmo::message()->send([
            'to' => '447852534842',
            'from' => 'BIZPAY',
            'text' => 'You have subscribed to Bizpay!'
        ]);

        return redirect('/merchant-admin/bizpay-subscription');


    }

    public function cancelBizpaySubscription(Request $request)
    {
        $user = Auth::user();
        $bizpay = Merchant::findorFail(1);
        $gateways = ($bizpay->gateways->all());


        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential1 = $gateway->credential_1;
                $credential2 = $gateway->credential_2;
            }
        }

        $stripe = new Stripe($credential2);
        $stripe->cancelSubscription($request->get('id'));
        $subscriptions = $user->customerSubscriptions->all();

        $sub = CustomerSubscriptions::findorFail($subscriptions[0]->id);
        $sub->delete();

        $merchant = Merchant::findorFail($user->merchant_id);
        $merchant->status = 0;
        $merchant->save();

        return redirect('/merchant-admin/bizpay-subscription');


    }

    public function rules()
    {
        $user = Auth::user();
        $merchant = Merchant::findorfail($user->merchant_id);
        $rules = $merchant->rules->all();
        return view('dashboard.merchant.rules', compact('rules'));

    }

    public function addRule()
    {

        return view('dashboard.merchant.create-rule');

    }

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
        $rule->action_value = $request->get('action_value');
        $rule->merchant_id = $user->merchant_id;
        $rule->user_id = $user->id;
        $rule->rule_id = str_random(20);
        $rule->status = 1;
        $rule->save();


        return redirect('/merchant-admin/rules');


    }

    public function deleteRule(Request $request)
    {
        $rule = Rule::GetRuleFromSlug($request->get('id'))[0];
        $rule->delete();
        return redirect('/merchant-admin/rules');
    }

    public function settings()
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);

        $stripe = PaymentGateway::GetStripeCredentialsForMerchant($user->merchant_id);
        $goCardless = PaymentGateway::GetGoCardlessCredentialsForMerchant($user->merchant_id);

        return view(
            'dashboard.admin.settings',
            compact(
                'gateways',
                'merchant',
                'stripe',
                'goCardless'
            )
        );
    }

    public function saveSettings(Request $request)
    {
        $bizpay = new Bizpay();
        $bizpay->saveSettings($request);

        return redirect()->back();
    }

    public function subscriptions()
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $subscriptions = $merchant->subscriptions->all();

        return view('dashboard.shared.subscriptions', compact('subscriptions'));
    }

    public function payments()
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $payments = $merchant->payments->all();

        return view('dashboard.shared.payments', compact('payments'));
    }


    public function orders()
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $orders = $merchant->orders->all();
        return view('dashboard.shared.orders', compact('orders'));
    }


    public function test()
    {
        $user = Auth::user();
//        $test= Notification::send($user, new SendSMSAlert());

        $test = Nexmo::message()->send([
            'to' => '447985226596',
            'from' => 'FLYMYCLOUD',
            'text' => 'Hewo'
        ]);
        dd($test);

    }


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

    public function installments()
    {

    }

    public function AddInstallment()
    {

        return view('dashboard.merchant.add-installment');

    }

    public function processAddInstallment(Request $request)
    {

        $user = Auth::user();
        $product = new Product();
        $product->currency_code = $request->get('currency_code');
        $product->product_id = str_random(20);
        $product->name = $request->get('name');
        $product->description = $request->get('description');
        $product->type = 3;
        $product->price = $request->get('price');
        $product->merchant_id = $user->merchant_id;
        $product->user_id = $user->id;
        $product->status = 1;
        $product->save();

        return redirect('/merchant-admin/products');

    }


    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.shared.profile', compact('user'));

    }

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

    public function refund(Request $request)
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $gateways = ($merchant->gateways->all());


        foreach ($gateways as $gateway) {
            if ($gateway->gateway == 1) {
                $credential2 = $gateway->credential_2;
            }
        }

        $gateways = $merchant->gateways->all();
        $stripe = new Stripe($credential2);
        $stripe->refundCharge($request->get('id'));

        $payment = Payment::GetPaymentFromChargeId($request->get('id'))[0];
        $payment->charge_id = "";
        $payment->save();

        return redirect()->back();

    }




}
