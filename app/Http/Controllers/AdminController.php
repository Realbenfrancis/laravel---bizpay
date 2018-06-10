<?php

namespace App\Http\Controllers;

use App\Http\Bizpay\Bizpay;
use App\Http\Models\BizpayLog;
use App\Http\Models\CustomerSubscriptions;
use App\Http\Models\Merchant;
use App\Http\Models\Order;
use App\Http\Models\Payment;
use App\Http\Models\PaymentGateway;
use App\Http\Models\Rule;
use App\Http\Models\SAAgreement;
use App\Http\Models\SAProduct;
use App\Http\Models\SAQuote;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * Class AdminController
 * All functions associated with Bizpay Admin goes here!
 *
 * @package App\Http\Controllers
 */
class AdminController extends Controller
{

    /**
     * AdminController constructor.
     */
    public function __construct()
    {
        //['except' => ['chart']

        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Return all the rules for this merchant
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rules()
    {
        $user = Auth::user();
        $merchant = Merchant::findorfail($user->merchant_id);
        $rules = $merchant->rules->all();

        return view('dashboard.merchant.rules', compact('rules'));
    }

    /**
     * Lists all rules added by Bizpay Admin
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rulesAddedByBizpay()
    {
        $user = Auth::user();
        $rules = $user->rules->all();

        return view('dashboard.merchant.rules', compact('rules'));
    }

    /**
     * Displays form to add a new rule
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addRule()
    {
        $merchants = Merchant::all();

        return view('dashboard.merchant.create-rule', compact('merchants'));

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function processAddRule(Request $request)
    {
        $bizpay = new Bizpay();
        $bizpay->processAddRule($request);

        return redirect('/admin/rules');
    }

    /**
     * Delete a rule by rule id
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteRule(Request $request)
    {
        $rule = Rule::GetRuleFromSlug($request->get('id'))[0];
        $rule->delete();

        return redirect('/admin/rules');
    }

    /**
     * Returns list of all merchants
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function merchants()
    {
        $merchants = Merchant::all();

        return view('dashboard.admin.merchants', compact('merchants'));
    }

    /**
     * Returns the details associated with a merchant account.
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function merchantDetails($slug)
    {
        $merchant = Merchant::GetMerchantFromSlug($slug)[0];

        return view('dashboard.admin.merchant-detail', compact('merchant'));
    }

    public function addMerchant()
    {
        return view('dashboard.admin.add-merchant');
    }


    /**
     * Creates a merchant and assigns a merchant id
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function processAddMerchant(Request $request)
    {
        $bizpay = new Bizpay();
        $bizpay->processAddMerchant($request);
        return redirect('/admin/merchants');

    }


    /**
     * Disabling the merchant will stop the merchant from accessing most critical parts
     * of the platform. Merchant clients won't be able to the platform either.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disableMerchant(Request $request)
    {
        $merchant = Merchant::findorFail($request->get('id'));
        $merchant->status = 0;
        $merchant->save();

        Session::flash('message', 'Merchant has been disabled');
        Session::flash('alert-class', 'alert-success');

        return redirect()->back();
    }

    /**
     * This will restore the merchant status
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enableMerchant(Request $request)
    {
        $merchant = Merchant::findorFail($request->get('id'));
        $merchant->status = 1;
        $merchant->save();

        Session::flash('message', 'Merchant has been enabled');
        Session::flash('alert-class', 'alert-success');

        return redirect()->back();
    }

    /**
     * Remove merchant from the platform
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteMerchant(Request $request)
    {
        $merchant = Merchant::findorFail($request->get('id'));
        $merchant->delete();
        Session::flash('message', 'Merchant has been deleted');
        Session::flash('alert-class', 'alert-success');

        return redirect()->back();
    }

    /**
     * Add a new merchant
     * Middleware commands are commented out for localhost testing.
     * Enable them before pushing to production
     *
     * @param Request $request
     */
    public function processCreateMerchantAdmin(Request $request)
    {
        $user = new User();
        $user->email = $request->get('email');
    }

    /**
     * List of all users
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function users()
    {
        $users = User::all();

        return view('dashboard.admin.users', compact('users'));
    }

    /**
     * Settings page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settings()
    {
        $user = Auth::user();

        $stripe = PaymentGateway::GetStripeCredentialsForMerchant($user->merchant_id);
        $goCardless = PaymentGateway::GetGoCardlessCredentialsForMerchant($user->merchant_id);
        $merchant = Merchant::findorFail($user->merchant_id);


        return view(
            'dashboard.admin.settings',
            compact(
                'merchant',
                'stripe',
                'goCardless'
            )
        );
    }

    /**
     * Save settings
     *
     * @param Request $request
     */
    public function saveSettings(Request $request)
    {
        $bizpay = new Bizpay();
        $bizpay->saveSettings($request);
        return redirect()->back();
    }

    /**
     * Returns all subscriptions on the platform
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscriptions()
    {
        $subscriptions = CustomerSubscriptions::all();

        return view('dashboard.shared.subscriptions', compact('subscriptions'));
    }

    /**
     * Returns all merchants subscribed to Bizpay Platform
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bizpaySubscriptions()
    {
        $user = Auth::user();
        $merchant = Merchant::findorFail($user->merchant_id);
        $subscriptions = $merchant->subscriptions->all();

        return view('dashboard.shared.subscriptions', compact('subscriptions'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payments()
    {
        $payments = Payment::all();

        return view('dashboard.shared.payments', compact('payments'));
    }

    /**
     * Returns all orders
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orders()
    {
        $orders = Order::all();

        return view('dashboard.shared.orders', compact('orders'));
    }

    public function agreements()
    {
        $agreements = SAAgreement::all();
        return view('dashboard.shared.agreements', compact('agreements'));
    }

    /**
     * Cancel one of client's subscription
     *
     * @param Request $request
     */
    public function cancelClientSubscription(Request $request)
    {
        $bizpay = new Bizpay();
        $bizpay->cancelClientSubscription($request);
    }


    /**
     * Returns the profile page - values from users table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();

        return view('dashboard.shared.profile', compact('user'));
    }

    /**
     * Save the data to users table
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
        $bizpay = new Bizpay();
        $bizpay->saveChangePassword($request);
    }

    /**
     * Mark a merchant account as "direct" - this means the merchant will be able to use
     * Bizpay portal without a subscription. This can also be used to offer a trial
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enableDirectMerchant(Request $request)
    {
        $merchant = Merchant::findorFail($request->get('id'));
        $merchant->direct_client = 1;
        $merchant->save();

        Session::flash('message', 'Merchant has been given full access!');
        Session::flash('alert-class', 'alert-success');

        return redirect()->back();
    }

    /**
     * Remove the "direct client" tag from merchant
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disableDirectMerchant(Request $request)
    {
        $merchant = Merchant::findorFail($request->get('id'));
        $merchant->direct_client = 0;
        $merchant->save();

        Session::flash('message', 'Full access has been revoked!');
        Session::flash('alert-class', 'alert-success');

        return redirect()->back();
    }

    public function apiPerformance()
    {

//        $viewer = BizpayLog::select(DB::raw("(response_time) as count, month(created_at) as mon"))
//            ->orderBy("created_at")
//            ->groupBy(DB::raw("year(created_at)"))
//            ->get()->toArray();


        //api response time - by  merchant

        //MYSQL - change!

//        $data = BizpayLog::select(DB::raw("(response_time) as count,strftime('%m',created_at) as mon"))
//            ->orderBy("created_at")
////            ->groupBy(DB::raw("strftime('%m',created_at)"))
//            ->get()->toArray();


        $data = BizpayLog::select(DB::raw("(response_time) as count,month(created_at) as mon"))
            ->orderBy("created_at")
//            ->groupBy(DB::raw("strftime('%m',created_at)"))
            ->get()->toArray();


        //api response time - by  merchant

        if (request()->has('merchant-id')) {

//            $data = BizpayLog::select(DB::raw("(response_time) as count,strftime('%m',created_at) as mon"))
//                ->where('merchant_id', '=', request()->get('merchant-id'))
//                ->orderBy("created_at")
////            ->groupBy(DB::raw("strftime('%m',created_at)"))
//                ->get(10)->toArray();

            $data = BizpayLog::select(DB::raw("(response_time) as count,month(created_at) as mon"))
                ->where('merchant_id', '=', request()->get('merchant-id'))
                ->orderBy("created_at")
//            ->groupBy(DB::raw("strftime('%m',created_at)"))
                ->get(10)->toArray();
        }


        $viewer = array_column($data, 'count');
        $dates = array_column($data, 'mon');

        $merchants = Merchant::all();


        return view('dashboard.admin.chart')->with(compact('merchants'))
            ->with('viewer', json_encode($viewer, JSON_NUMERIC_CHECK))
            ->with('dates', json_encode($dates, JSON_NUMERIC_CHECK));
    }

    public function apiRequests()
    {
        $logs = BizpayLog::all();

        return view('dashboard.admin.api-response', compact('logs'));

    }

    public function apiUsage()
    {

        $users = User::all();

        return view('dashboard.admin.api-usage', compact('users'));
    }

    public function businessIntelligence()
    {

        $merchants = Merchant::all();
        $productCount = 0;
        $agreementCount = 0;
        $product = array();
        $quote = array();
        $avgResponseTime = 0;


        if (request()->has('merchant-id')) {

            $merchantId = request()->get('merchant-id');
            $avgResponseTime = BizpayLog::GetAvgTime($merchantId);
            $productCount = SAProduct::GetProductsCountForMerchant($merchantId);
            $agreementCount = SAAgreement::GetAgreementsCountForMerchant($merchantId);
            $product = SAProduct::GetProductWithMaxSales($merchantId);
            $quote = SAQuote::GetQuoteWithMaxSales($merchantId);

            if (count($product) > 0) {
                $product = $product[0];
            }

            if (count($quote) > 0) {
                $quote = $quote[0];
            }

        }



        return view('dashboard.admin.business-intelligence', compact('merchants',
            'productCount', 'agreementCount', 'product', 'quote', 'avgResponseTime'));

    }


}
