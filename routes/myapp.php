<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('signup', 'Portal\AuthController@signup')->name('portal.signup');
    Route::post('login', 'Portal\AuthController@login')->name('portal.login');
    Route::get('logout', 'Portal\AuthController@logout')->name('portal.logout');
    Route::get('verify/{code}', 'Portal\AuthController@verify')->name('portal.verify');
    Route::get('forgot_password/{username}', 'Portal\AuthController@forgotPassword')->name('portal.forgot_password');
    Route::post('set_password/{code}', 'Portal\AuthController@resetPassword')->name('portal.set_password');

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('change_password/{relation_id}', 'Portal\AuthController@changePassword')->name('portal.change_password');
    });
});

Route::get('tenants/{id}', 'Portal\TenantController@show')->name('portal.tenants');

Route::group(['middleware' => ['auth:api']], function () {
    // TODO: change to Route::apiResource() if ADD/EDIT/DELETE functionalities will be added
    Route::get('subscriptions/{relation_id}', 'Portal\SubscriptionController@listSubscriptions')->name('portal.subscriptions');
    Route::get('invoices/{relation_id}', 'Portal\SalesInvoiceController@listInvoices')->name('portal.invoices');
    Route::get('invoices/{sales_invoice}/details', 'Portal\SalesInvoiceController@show')->name('portal.invoice_details');
    Route::get('invoices/{sales_invoice}/pdf', 'Portal\SalesInvoiceController@downloadInvoicePdf')->name("portal.invoice_pdf");
    Route::get('invoices/{sales_invoice}/usage_cost', 'Portal\SalesInvoiceController@downloadUsageCostPdf')->name("portal.invoice_usage_cost_pdf");

    Route::get('profiles/{relation_id}', 'Portal\RelationController@getUserData')->name('portal.customers');
});
