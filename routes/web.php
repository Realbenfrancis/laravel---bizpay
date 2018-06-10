<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
|   All the web routes for Bizpay API are configured here!
|
*/



Auth::routes();

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::get('/index', 'HomeController@index');
Route::get('/customers', 'HomeController@customers');
Route::get('/tickets', 'HomeController@tickets');
Route::post('/cancel-ticket', 'HomeController@cancelTicket');
Route::post('/payment-failed', 'HomeController@paymentFailed');
Route::post('/make-payment', 'HomeController@makePayment');
Route::get('/test', 'HomeController@test');
Route::get('/admin/merchants', 'AdminController@merchants');
Route::get('admin/merchant-details/{slug}', 'AdminController@merchantDetails');

Route::get('/admin/api-explorer', 'ApiExplorer@index');
Route::post('/admin/api-explorer', 'ApiExplorer@submit');



Route::get('/admin/add-merchant', 'AdminController@addMerchant');
Route::post('/admin/add-merchant', 'AdminController@processAddMerchant');
Route::post('/admin/delete-merchant', 'AdminController@deleteMerchant');
Route::post('/admin/disable-merchant', 'AdminController@disableMerchant');
Route::post('/admin/enable-merchant', 'AdminController@enableMerchant');
Route::get('/admin/users', 'AdminController@users');
Route::get('/admin/settings', 'AdminController@settings');
Route::post('/admin/settings', 'AdminController@saveSettings');
Route::get('/admin/orders', 'AdminController@orders');
Route::get('/admin/agreements', 'AdminController@agreements');
Route::get('/admin/subscriptions', 'AdminController@subscriptions');
Route::get('/admin/payments', 'AdminController@payments');
Route::get('/admin/bizpay-subscriptions', 'AdminController@bizpaySubscriptions');
Route::get('/admin/test', 'AdminController@api');
Route::get('/admin/rules', 'AdminController@rules');
Route::get('/admin/rules-added-by-admin', 'AdminController@rulesAddedByBizpay');
Route::get('/admin/add-rule', 'AdminController@addRule');
Route::post('/admin/add-rule', 'AdminController@processAddRule');
Route::post('/admin/rules', 'AdminController@deleteRule');
Route::get('/admin/profile', 'AdminController@profile');
Route::post('/admin/profile', 'AdminController@saveProfile');
Route::get('/admin/change-password', 'AdminController@changePassword');
Route::post('/admin/change-password', 'AdminController@saveChangePassword');
Route::post('/admin/disable-direct-merchant', 'AdminController@disableDirectMerchant');
Route::post('/admin/enable-direct-merchant', 'AdminController@enableDirectMerchant');
Route::get('/merchant-admin/clients', 'MerchantAdminController@clients');
Route::get('/merchant-admin/managers', 'MerchantAdminController@managers');
Route::get('/merchant-admin/test', 'MerchantAdminController@test');
Route::get('/merchant-admin/add-client', 'MerchantAdminController@addClient');
Route::post('/merchant-admin/add-client', 'MerchantAdminController@processAddClient');
Route::post('/merchant-admin/add-card', 'MerchantAdminController@addCard');

Route::post('/merchant-admin/subscribe', 'MerchantAdminController@subscribe');
Route::get('/merchant-admin/bizpay-subscription', 'MerchantAdminController@bizpaySubscription');
Route::post('/merchant-admin/cancel-bizpay-subscription', 'MerchantAdminController@cancelBizpaySubscription');

Route::get('/merchant-admin/add-card', 'MerchantAdminController@card');
Route::get('/merchant-admin/add-manager', 'MerchantAdminController@addManager');
Route::post('/merchant-admin/add-manager', 'MerchantAdminController@processAddManager');
Route::post('/merchant-admin/managers', 'MerchantAdminController@deleteManager');
Route::get('/merchant-admin/products', 'MerchantAdminController@products');
Route::get('/merchant-admin/add-product', 'MerchantAdminController@addProduct');
Route::post('/merchant-admin/add-product', 'MerchantAdminController@processAddProduct');
Route::get('/merchant-admin/rules', 'MerchantAdminController@rules');
Route::get('/merchant-admin/add-rule', 'MerchantAdminController@addRule');
Route::post('/merchant-admin/add-rule', 'MerchantAdminController@processAddRule');
Route::post('/merchant-admin/rules', 'MerchantAdminController@deleteRule');
Route::get('/merchant-admin/settings', 'MerchantAdminController@settings');
Route::post('/merchant-admin/settings', 'MerchantAdminController@saveSettings');
Route::get('/merchant-admin/orders', 'MerchantAdminController@orders');
Route::get('/merchant-admin/subscriptions', 'MerchantAdminController@subscriptions');
Route::get('/merchant-admin/payments', 'MerchantAdminController@payments');
Route::post('/merchant-admin/cancel-subscription', 'MerchantAdminController@cancelClientSubscription');
Route::get('/merchant-admin/add-installment', 'MerchantAdminController@AddInstallment');
Route::post('/merchant-admin/add-installment', 'MerchantAdminController@processAddInstallment');
Route::get('/merchant-admin/profile', 'MerchantAdminController@profile');
Route::post('/merchant-admin/profile', 'MerchantAdminController@saveProfile');
Route::get('/merchant-admin/change-password', 'MerchantAdminController@changePassword');
Route::post('/merchant-admin/change-password', 'MerchantAdminController@saveChangePassword');
Route::post('/merchant-admin/refund', 'MerchantAdminController@refund');
Route::get('/merchant-manager/clients', 'ManagerController@clients');
Route::get('/merchant-manager/add-client', 'ManagerController@addClient');
Route::post('/merchant-manager/add-client', 'ManagerController@processAddClient');
Route::get('/merchant-manager/products', 'ManagerController@products');
Route::get('/merchant-manager/add-product', 'ManagerController@addProduct');
Route::post('/merchant-manager/add-product', 'ManagerController@processAddProduct');
Route::get('/merchant-manager/orders', 'ManagerController@orders');
Route::get('/merchant-manager/subscriptions', 'ManagerController@subscriptions');
Route::get('/merchant-manager/payments', 'ManagerController@payments');
Route::post('/merchant-manager/cancel-subscription', 'ManagerController@cancelClientSubscription');
Route::get('/merchant-manager/add-installment', 'ManagerController@AddInstallment');
Route::post('/merchant-manager/add-installment', 'ManagerController@processAddInstallment');
Route::get('/merchant-manager/profile', 'ManagerController@profile');
Route::post('/merchant-manager/profile', 'ManagerController@saveProfile');
Route::get('/merchant-manager/change-password', 'ManagerController@changePassword');
Route::post('/merchant-manager/change-password', 'ManagerController@saveChangePassword');
Route::get('/client/subscriptions', 'ClientController@subscriptions');
Route::get('/client/payments', 'ClientController@payments');
Route::get('/client/card', 'ClientController@card');
Route::post('client/add-card', 'ClientController@addCard');
Route::get('/client/cart', 'ClientController@cart');
Route::get('/client/orders', 'ClientController@orders');
Route::get('/client/update-card', 'ClientController@updateCard');
Route::get('/client/shop', 'ClientController@shop');
Route::post('/client/shop', 'ClientController@buy');
Route::post('/client/cancel-subscription', 'ClientController@cancelClientSubscription');
Route::post('/client/instalment', 'ClientController@plans');
Route::post('/client/order-plan', 'ClientController@processPlan');
Route::get('/client/profile', 'ClientController@profile');
Route::post('/client/profile', 'ClientController@saveProfile');
Route::get('/client/change-password', 'ClientController@changePassword');
Route::post('/client/change-password', 'ClientController@saveChangePassword');
Route::get('/client/gocardless-setup', 'ClientController@goCardlessAddCustomer');

Route::get('/admin/api-usage', 'AdminController@apiUsage');
Route::get('/admin/api-requests', 'AdminController@apiRequests');
Route::get('/admin/api-performance', 'AdminController@apiPerformance');
Route::get('/admin/business-intelligence', 'AdminController@businessIntelligence');



Route::post('/webhook/stripe', 'WebHookController@stripe');


Route::get('/logout', function () {
    Auth::logout();
    return redirect('login');
});

Route::group(array('prefix' => 'api'), function () {
    Route::get('/users', 'UserController@index');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
