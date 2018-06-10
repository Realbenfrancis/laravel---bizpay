<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
|
|
*/

// enable this if you wish to apply the auth:api middleware

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::post('/login', 'APIController@login');

Route::group(['prefix' => env('API_VERSION'), 'middleware' => 'throttle:120,1'], function () {

    //graph




    //product operations
    Route::post('/products', 'APIController@createProduct');
    Route::get('/products/{id}', 'APIController@getProduct');
    Route::put('/products/{id}', 'APIController@updateProduct');
    Route::delete('/products/{id}', 'APIController@deleteProduct');
    Route::get('/products', 'APIController@allProducts');

    //plan operations
    Route::post('/plans', 'APIController@createPlan');
    Route::get('/plans/{id}', 'APIController@getPlan');
    Route::put('/plans/{id}', 'APIController@updatePlan');
    Route::delete('/plans/{id}', 'APIController@deletePlan');
    Route::get('/plans/', 'APIController@allPlans');


    //quote operations
    Route::post('/quotes', 'APIController@createQuote');
    Route::get('/quotes/{id}', 'APIController@getQuote');
    Route::put('/quotes/{id}', 'APIController@updateQuote');
    Route::delete('/quotes/{id}', 'APIController@deleteQuote');
    Route::get('/quotes/', 'APIController@allQuotes');

    //merchant settings

    Route::post('/settings', 'APIController@createQuote');
    Route::get('/settings', 'APIController@getMerchantSettings');

    // api

    Route::get('/api', 'APIController@api');
    Route::get('/maintenance', 'APIController@maintenance');



    //dynamic quote operations
    Route::post('/dynamic-quotes', 'APIController@createDynamicQuote');

    Route::get('/dynamic-quotes/{id}', 'APIController@getDynamicQuote');
    Route::put('/dynamic-quotes/{id}', 'APIController@updateDynamicQuote');
    Route::delete('/dynamic-quotes/{id}', 'APIController@deleteDynamicQuote');
    Route::get('/dynamic-quotes/', 'APIController@allDynamicQuotes');

    //notifications

    Route::post('/notify/agreements/{id}/payment-success', 'APIController@agreementPaymentSuccess');
    Route::post('/notify/agreements/{id}/payment-failure', 'APIController@agreementPaymentFailed');
    Route::post('/notify/agreements/{id}/payment-reminder', 'APIController@agreementPaymentReminder');
    Route::post('/notify/agreements/{id}/renewal', 'APIController@agreementRenewed');
    Route::post('/notify/quotes/{id}/', 'APIController@sendQuote');
    Route::post('/notify/agreements/{id}/started', 'APIController@agreementCreated');
    Route::post('/notify/agreements/{id}/cancelled', 'APIController@agreementCancelled');




    Route::post('/notify/agreements/{id}/created', 'APIController@agreementCreated');
    Route::post('/notify/agreements/{id}/renewal-cancelled', 'APIController@agreementRenewed');



    //user operations
    Route::post('/customers', 'APIController@createUser');
    Route::get('/customers/{id}', 'APIController@getUser');
    Route::put('/customers/{id}', 'APIController@updateUser');
    Route::get('/customers/', 'APIController@getAllUsersForMerchant');

    //payment related operations


    Route::get('/retry-charges', 'APIController@retryCharges');


    Route::get('/subscriptions', 'APIController@subscriptions');
    Route::post('/instalments', 'APIController@instalments');


    Route::post('/allowed-plans', 'APIController@allowedInstalmentPlans');

    Route::post('/setup-instalment', 'APIController@setUpInstalment');
    Route::post('/subscriptions/{id}', 'APIController@deleteSubscription');
    Route::post('/billing-plans', 'APIController@setUpPlan');
    Route::get('/stripe-public-credential', 'APIController@stripePublicCredential');
    Route::get('/payments', 'APIController@payments');
    Route::get('/admin/payments', 'APIController@allPayments');
    Route::get('/orders', 'APIController@orders');
    Route::post('/orders', 'APIController@createOrder');

    Route::get('/all-products', 'APIController@products');
    Route::get('/user/failed-payments', 'APIController@failedPaymentsByUser');
    Route::get('/failed-payments', 'APIController@failedPaymentsByUser');

    Route::post('/payments-against-plan', 'APIController@updateInstalments');


    Route::post('/retry-failed-payments', 'APIController@retryFailedPayments');

    Route::post('/charges', 'APIController@chargeClient');


    //agreements


    Route::get('/agreements', 'APIController@getAllAgreementsForMerchant');
    Route::post('/agreements', 'APIController@createAgreement');

    Route::post('/agreements/{ref}/cancel-payments', 'APIController@cancelDeferredCharges');
    Route::post('/agreements/{ref}/cancel', 'APIController@cancelAgreement');


    Route::post('/agreements/{ref}/refund', 'APIController@refundPayments');
    Route::get('/agreements/{ref}', 'APIController@getAgreement');

  //  Route::get('/agreements/{ref}/payments', 'APIController@getAllPaymentsForAgreement');
    Route::get('/agreements/{ref}/payments', 'APIController@getPaymentInformation');

    Route::get('/agreements/{ref}/failed-payments', 'APIController@getFailedPaymentForAgreement');
    Route::post('/agreements/{ref}/retry-payments', 'APIController@retryFailedPayments');
   // Route::post('/agreements/{ref}/payment', 'APIController@getAllPaymentsForAgreement');


    Route::post('/agreements/{ref}/payment', 'APIController@updateInstalments');



    //stripe

    Route::post('/stripe-add-user', 'APIController@addStripeCustomer');
    Route::post('/stripe-charges', 'APIController@chargeClient');
    Route::post('/stripe-charges/{id}', 'APIController@refundCharge');
    Route::post('/set-subscription', 'APIController@setUpSubscription');

    Route::post('/bizpay/stripe/user', 'APIController@addStripeCustomer');
    Route::post('/bizpay/stripe/user/update', 'APIController@addStripeCustomer');




    //gocardless

    Route::post('/gc-add-user', 'APIController@addGCCustomer');
    Route::post('/gc-charges', 'APIController@chargeGCCustomer');
    Route::post('/gc-subscribe', 'APIController@addGCSubscription');
    Route::post('/gc-unsubscribe', 'APIController@cancelGCSubscription');
    Route::post('/gc-refund', 'APIController@refundGCCharge');
    Route::post('/gc-redirectURL', 'APIController@generateGCRedirectURL');
    Route::post('/gc-add-redirect-credentials', 'APIController@addGoCardlessRedirectClientCredentials');

    Route::post('/bizpay/gc/user', 'APIController@addGCCustomer');
    Route::post('/bizpay/gc/user/update', 'APIController@addGCCustomer');
    Route::post('/bizpay/gc/redirect-url/user', 'APIController@addGoCardlessRedirectClientCredentials');
    Route::post('/bizpay/gc/redirect-url/user/update', 'APIController@addGoCardlessRedirectClientCredentials');
    Route::post('/bizpay/gc/redirect-url', 'APIController@generateGCRedirectURL');


    //finance

    Route::post('/qualify', 'APIController@qualify');
    Route::post('/custom-plan', 'APIController@customPlan');
    Route::post('/deferred-charges', 'APIController@deferredPayment');


    //rules

    Route::get('/rules/client-age', 'APIController@clientMinimumAge');
    Route::get('/rules/disallowed-countries', 'APIController@disallowedCountries');
    Route::get('/rules/trial-period', 'APIController@trialPeriod');
    Route::get('/rules/client-minimum-age', 'APIController@clientMinimumAge');
    Route::get('/rules/admin/client-minimum-age', 'APIController@clientMinimumAgeSetByAdmin');
    Route::get('/rules/client-minimum-age', 'APIController@clientMinimumAge');
    Route::get('/rules/minimum-price-for-credit', 'APIController@minimumPriceForCredit');
    Route::get('/rules/admin/minimum-price-for-credit', 'APIController@minimumPriceForCreditByAdmin');

    //admin

    Route::get('/admin/orders', 'APIController@allOrders');
 //   Route::get('/admin/products', 'APIController@allProducts');
    Route::get('/admin/users', 'APIController@allUsers');
    Route::get('/admin/subscriptions', 'APIController@allSubscriptions');
    Route::get('/admin/failed-payments', 'APIController@allFailedPayments');


   // Route::get('/test', 'APIController@test');
});




