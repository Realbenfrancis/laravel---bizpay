<?php

namespace App\Http\Controllers;

use App\Http\Bizpay\Stripe;
use App\Http\Bizpay\UserManagement;
use App\Http\Models\CustomerSubscriptions;
use App\Http\Models\Merchant;
use App\Http\Models\Product;
use App\Notifications\SendUserAccount;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;

class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('merchant.manager');
        $this->middleware('merchant.check');
    }

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
        $user=$userManagement->addUser($cUser->merchant_id,
            $request->get('name'),
            $request->get('email'),Hash::make($password));

        Notification::send($user, new SendUserAccount($password));

        return redirect('/merchant-manager/clients');

    }

    public function products()
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $products = $merchant->products->all();
        return view('dashboard.merchant.products', compact('products'));

    }

    public function addProduct()
    {
        return view('dashboard.merchant.add-product');
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
            $credential1 = "";
            $credential2 = "";

            //TODO: check for default

            foreach ($gateways as $gateway) {
                if ($gateway->gateway == 1) {
                    $credential1 = $gateway->credential_1;
                    $credential2 = $gateway->credential_2;
                }
            }

            $stripe = new Stripe($credential2);

            $stripe->plan($request->get('price'),
                $request->get('currency_code'),
                $product->product_id, $request->get('name'),
                $request->get('duration'));

        }

        $product->name = $request->get('name');
        $product->description = $request->get('description');
        $product->type = $request->get('type');
        $product->price = $request->get('price');
        $product->merchant_id = $user->merchant_id;
        $product->user_id = $user->id;
        $product->status = 1;
        $product->save();

        return redirect('/merchant-manager/products');


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

        return redirect('/merchant-manager/products');

    }

    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.shared.profile', compact('user'));

    }

    public function saveProfile(Request $request)
    {

        $user = User::findorFail(Auth::user()->id);
        $user->name= $request->get('name');
        $user->email= $request->get('email');
        $user->phone_number= $request->get('phone_number');
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

}
