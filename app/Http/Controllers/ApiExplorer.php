<?php

namespace App\Http\Controllers;

use App\Http\Models\Merchant;
use App\Http\Models\PaymentGateway;
use App\User;
use GuzzleHttp\Client;
use Http\Client\Exception\HttpException;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Token;

class ApiExplorer extends Controller
{

    private $merchantAPI;
    private $url = "https://api.bizpay.co.uk";


    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }


    public function index()
    {

        $merchants = Merchant::all();


        return view('dashboard.admin.explorer.index', compact('merchants'));

    }

    public function submit()
    {
        $this->merchantAPI = (User::GetMerchantAdmin(request()->get('merchant-id'))[0])->api_token;

        $action=(request()->get('btn-action'));

        if($action=="AddPayment"){
            $result = $this->addStripeCredentials();
        }

        if($action=="createPlan"){
            $result = $this->createPlan();
        }


        if($action=="fetchPlans"){
            $result = $this->plans();
        }


        if($action=="createDynamicQuote"){
            $result = $this->createDynamicQuote();
        }

        if($action=="createAgreement"){
            $result = $this->createAgreement();
        }

        if($action=="createScheduledPayments"){
            $result = $this->createPayment();
        }

        if($action=="cancelAgreement"){
            $result = $this->cancelAgreement();
        }

        if($action=="createUser"){
            $result = $this->createUser();
        }


        if($action=="getUsers"){
            $result = $this->users();
        }

        if($action=="createProduct"){
            $result = $this->createProduct();
        }

        if($action=="getProducts"){
            $result = $this->products();
        }

        if($action=="viewScheduledPayments"){
            $result = $this->viewScheduledPayments();
        }

        if($action=="refundAgreement"){
            $result = $this->refundAgreement();
        }




        return view('dashboard.admin.explorer.result', compact('result'));

    }

    private function users()
    {




        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->get($this->url.'/api/v0.3/customers'
        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;

        $this->addStripeKeys();

        return $resp;


    }

    private function createUser()
    {
        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->post($this->url.'/api/v0.3/customers',
            ['form_params' => array(

                    'first_name' => 'Test1',
                    'last_name' => 'User2',
                    'email' => 'test9@test.com',
                    'phone' => '132321321',
                    'company_name' => 'Test Company',
                    'company_number' => 'Company Number',
                    'position' => 'CEO',
                    'address_line_1' => 'Address l1',
                    'address_line_2' => 'l2',
                    'city' => 'Test city',
                    'postcode' => 'TTCC',
                    'country' => 'GB',
                    'occupation' => ''

            )]
        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;
        return $resp;


    }


    private function products()
    {

        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->get($this->url.'/api/v0.3/products'
        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;
        return $resp;


    }

    private function createProduct()
    {

        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->post($this->url.'/api/v0.3/products',
            ['form_params' => array(

                'name' => 'product',
                'product_sku' => '121221',
                'quantity' => '2',
                'description' => 'Test',
                'tags' => 'test1,test2',
                'currency_code' => 'GBP',
                'price' => '10',
                'tax_percent' => '20'

            )]
        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;
        return $resp;

    }


    private function createPlan()
    {
        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->post($this->url.'/api/v0.3/plans',
            ['form_params' => array(

                'name' => 'Test plan',
                'plan_type' => 'instalment',
                'durations' => '[{"duration": 3, "change": 10, "default": true},{"duration": 6, "change": 20, "default": false}]',
                'billing_period' => 'monthly',
                'billing_start' => '2',
                'agreement_term' => '',
                'payment_info_required' => 'true',
                'first_payment_delay' => '10',
                'first_payment_percent' => '10',
                'cancellation_check' => 'true',
                'cancellation_days' => '10',
                'refund_check' => 'true',
                'refund_percent' => '10',
                'recurring_billing_check' => 'true',
                'terms_file' => 'GOOGLE'

            )]
        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;
        return $resp;
    }

    private function plans()
    {

          $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->get($this->url.'/api/v0.3/plans'
        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;
        return $resp;

    }


    private function createAgreement()
    {

        $merchant = Merchant::findOrfail((request()->get('merchant-id')));
        $user = $merchant->users->get(1);
        $plan = $merchant->plans->get(0);


        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->post($this->url.'/api/v0.3/agreements',
            ['form_params' => array(
                'plan_id' => $plan->slug,
                'selected_duration' => 6,
                'currency_code' => "USD",
                'amount' => 100,
                'gateway' => "stripe",
                'payment_check' => 1,
              //  'quote_id' => null,
                'customer_id' => $user->user_id,
                'products' => '[{"price":100.00, "tax":"20.00", "currency":"GBP", "details":""}]'

            )]
        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;
        return $resp;

    }

    private function cancelAgreement()
    {

        $merchant = Merchant::findOrfail((request()->get('merchant-id')));
        $agreement = $merchant->agreements->get(0);

        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->post($this->url.'/api/v0.3/agreements/'.$agreement->slug.'/cancel'

        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;
        return $resp;

    }

    private function refundAgreement()
    {

        $merchant = Merchant::findOrfail((request()->get('merchant-id')));
        $agreement = $merchant->agreements->get(0);

        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->post($this->url.'/api/v0.3/agreements/'.$agreement->slug.'/refund',
                    ['form_params' => array(
                'percent' => 10
            )]
        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;
        return $resp;

    }


    private function createPayment()
    {

        $merchant = Merchant::findOrfail((request()->get('merchant-id')));
        $user = $merchant->users->get(1);

        $agreement = $merchant->agreements->get(0);

        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->post($this->url.'/api/v0.3/deferred-charges',
            ['form_params' => array(
                'customer_id' => $user->user_id,
                'amount' => '1000',
                'tax' => '',
                'currency_code' => 'USD',
                'description' => 'Test',
                'gateway' => 'stripe',
                'instalments' => '6',
                'payment_date' => '2018/04/05',
                'frequency' => 'monthly',
                'order_ref' => $agreement->slug,
                'order_type' => 'instalments'
            )]
        );


        $resp = json_decode( $response->getBody()->getContents(),true) ;
        return $resp;
    }


    private function addStripeCredentials()
    {

        $token = $this->generateStripeToken();

        $merchant = Merchant::findOrfail((request()->get('merchant-id')));
        $user = $merchant->users->get(1);

        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->post($this->url.'/api/v0.3/bizpay/stripe/user',
            ['form_params' => array(
                'stripe_token' => $token->id,
                'customer_id' => $user->user_id
            )]
        );

        $resp = json_decode( $response->getBody()->getContents(),true) ;

        return $resp;

    }

    public function addStripeKeys()
    {
                $merchant = Merchant::findorFail(request()->get('merchant-id')) ;

                $gateway = new PaymentGateway();
                $gateway->gateway = 1;
                $gateway->credential_1 = "";
                $gateway->credential_2 = env('STRIPE_SECRET_KEY_BIZPAY');
                $gateway->additional_charge = 0;
                $gateway->additional_charge_single_price = 0;
                $gateway->account_type = 1;
                $gateway->other_info = "";
                $gateway->merchant_id=$merchant->id;
                $gateway->status=1;
                $gateway->save();

        return redirect()->back();
    }

    private function createDynamicQuote()
    {
        $merchant = Merchant::findOrfail((request()->get('merchant-id')));
        $plan = $merchant->plans->get(0);

        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->post($this->url.'/api/v0.3/dynamic-quotes',
            ['form_params' => array(
                'plan_id' => $plan->slug,
                'price' => '1000',

            )]
        );

        $resp = json_decode( $response->getBody()->getContents(),true) ;

        return $resp;
    }

    private function viewScheduledPayments()
    {
        $merchant = Merchant::findOrfail((request()->get('merchant-id')));
        $agreement = $merchant->agreements->get(0);

        $client = new Client([
            'headers' => [   'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'x-bizpay-key' => $this->merchantAPI ]
        ]);

        $response = $client->get($this->url.'/api/v0.3/agreements/'.$agreement->slug.'/payments');

        $resp = json_decode( $response->getBody()->getContents(),true) ;

        return $resp;
    }




    private function generateStripeToken()
    {

        Stripe::setApiKey(env('STRIPE_SECRET_KEY_BIZPAY'));
        $token = Token::create(array(
            "card" => array(
                "number" => "4000056655665556",
                "exp_month" => "10",
                "exp_year" => "2020",
                "cvc" => "123"
            )
        ));

        return $token;


    }

}
