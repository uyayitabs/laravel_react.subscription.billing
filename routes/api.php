<?php

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;

Route::post('maintenance', 'Api\MaintenanceController@maintenance')->name('maintenance');

Route::group([
    'middleware' => ['maintenance']
], function () {
    // Route::middleware('auth:api')->get('/user', 'AuthController@user');

    Route::group([
        'prefix' => 'auth'
    ], function () {
        Route::post('login', 'AuthController@login')->name('login');
        Route::post('signup', 'AuthController@signup');

        Route::post('reset-password', 'AuthController@resetPassword');
        Route::post('forgot-password', 'AuthController@forgotPassword');

        Route::group([
            'middleware' => 'auth:api'
        ], function () {
            Route::post('change-password', 'AuthController@changePassword');
            Route::get('logout', 'AuthController@logout');
            Route::get('user', 'AuthController@user');
        });
    });


    Route::group([
        'middleware' => ['auth:api', 'vso']
    ], function () {

        Route::get('subscriptions/latest', 'Api\SubscriptionController@latest')->name("subscriptions.latest");

        Route::get('dependencies/relations', 'Api\DependencyControler@relations');
        Route::get('dependencies/persons', 'Api\DependencyControler@persons');
        Route::get('dependencies/address', 'Api\DependencyControler@address');
        Route::get('dependencies/plan_lines', 'Api\DependencyControler@planLines');
        Route::get('dependencies/plans', 'Api\DependencyControler@plans');
        Route::get('dependencies/subscription_lines', 'Api\DependencyControler@subscriptionLines');
        Route::get('dependencies/subscriptions', 'Api\DependencyControler@subscriptions');
        Route::get('dependencies/countries', 'Api\DependencyControler@countries');
        Route::get('dependencies/cities/{country}', 'Api\DependencyControler@cities');

        // List endpoints
        Route::get('products/list', 'Api\ProductController@list')->name("products.list");
        Route::get('relations/list', 'Api\RelationController@list')->name("relations.list");
        Route::get('plans/list', 'Api\PlanController@list')->name("plans.list");
        Route::get('tenants/list', 'Api\TenantController@list')->name("tenants.list");
        Route::get('product_types/list', 'Api\ProductTypeController@list')->name("product_types.list");
        Route::get('address_types/list', 'Api\AddressTypeController@list')->name("address_types.list");
        Route::get('person_types/list', 'Api\PersonTypeController@list')->name("person_types.list");
        Route::get('countries/list', 'Api\CountryController@list')->name("countries.list");
        Route::get('cities/list', 'Api\CountryController@list')->name("cities.list");
        Route::get('vat_codes/list', 'Api\VatCodeController@list')->name("vat_codes.list");
        Route::get('contract_periods/list', 'Api\ContractPeriodsController@list')->name("contract_periods.list");
        Route::get('statuses/{statusType}/list', 'Api\StatusesController@statusList')->name("status.list");
        Route::get('network_operators/list', 'Api\NetworkOperatorsController@list')->name("network_operator.list");
        Route::get('networks/list', 'Api\NetworkOperatorsController@networkOpts')->name("networks.network_opts");
        Route::get('operators/list/{network_operator}', 'Api\NetworkOperatorsController@operators')->name("operator.operators_opts");
        Route::get('products/backend_apis', 'Api\ProductController@backendApis')->name("products.backendApis");

        // Summary endpoints
        Route::get('relations/summary', 'Api\RelationController@summary')->name("relations.summary");
        Route::get('subscriptions/summary', 'Api\SubscriptionController@summary')->name("subscriptions.summary");
        Route::get('sales_invoices/summary', 'Api\SalesInvoiceController@summary')->name("sales_invoices.summary");
        Route::get('reminders/summary', 'Api\SalesInvoiceController@remindersSummary')->name("reminders.summary");
        Route::get('billing_runs/summary', 'Api\AdminToolController@billingRunsSummary')->name("billing_runs.summary");

        Route::group(['prefix' => 'dashboard'], function() {
            Route::get('sales_invoice_summary', 'Api\SalesInvoiceController@dashboardInvoicesSummary')->name('dashboard.invoice_summary');
        });

        // Count endpoints
        Route::get('tenants/count', 'Api\TenantController@count')->name("tenants.count");
        Route::get('persons/count', 'Api\PersonController@count')->name("persons.count");
        Route::get('products/count', 'Api\ProductController@count')->name("products.count");
        Route::get('subscriptions/count', 'Api\SubscriptionController@count')->name("subscriptions.count");
        Route::get('sales_invoices/count', 'Api\SalesInvoiceController@count')->name("sales_invoices.count");
        Route::get('sales_invoices/{sales_invoice}/cdr_usage_costs', 'Api\SalesInvoiceController@cdrUsageCosts')->name("sales_invoices.cdrUsageCosts");
        Route::get('sales_invoices/{sales_invoice}/cdr_usage_pdf', 'Api\SalesInvoiceController@cdrUsageCostsPdf')->name("sales_invoices.cdrUsageCostsPdf");

        // ApiKey endpoints
        Route::apiResource('api_keys', 'Api\ApiKeyController');
        Route::get('api_keys/tenant/{tenantId}', 'Api\ApiKeyController@showForTenant');
        Route::get('api_keys/user/{userId}', 'Api\ApiKeyController@showForUser');

        // Persons Endpoints
        Route::get('persons/checkemail', 'Api\PersonController@checkemail')->name("persons.checkemail");
        Route::post('persons/linkperson', 'Api\PersonController@linkperson')->name("persons.linkperson");

        Route::get('tenants/{tenant}/switch', 'Api\TenantController@doSwitch')->name("tenants.doSwitch");

        Route::get('mynumberranges/{tenant}', 'Api\NumberRangesController@my')->name("number_ranges.my");

        // Addreses Endpoints
        Route::apiResource('addresses', 'Api\AddressController');
        Route::post('addresses/{relation}', 'Api\AddressController@store')->name("address.create");

        // Country Endpoints
        Route::apiResource('countries', 'Api\CountryController');
        Route::get('country/{country}/states', 'Api\CountryController@states')->name("country.states");

        Route::get('state/{country}/cities', 'Api\StateController@cities')->name("state.cities");

        Route::get('tenants/{tenant}/groups', 'Api\TenantController@groups')->name("tenants.groups");

        Route::post('tenants/{tenant}/createGroup', 'Api\TenantController@createGroup')->name("group.create");
        Route::match(['put', 'patch'], 'tenants/{tenant}/groups/{group}', [
            'as' => 'group.update',
            'uses' => 'Api\TenantController@updateGroup'
        ]);

        // Relation endpoints
        Route::get('relations/{relation}/subscriptions', 'Api\RelationController@subscriptionsUnpaginated')->name("relation.subscriptions");
        Route::get('relations/{relation}/addresses', 'Api\RelationController@addresses')->name("relation.addresses");

        // RelationsPersons endpoints
        Route::get('relations/{relation}/persons', 'Api\RelationController@indexPersons')->name("relations_persons.index");
        Route::get('relations/{relation}/persons/{person}', 'Api\RelationController@showPerson')->name("relations_persons.show");
        Route::post('relations/{relation}/persons', 'Api\RelationController@storePerson')->name('relations_persons.store');
        Route::put('relations/{relation}/persons/{person}', 'Api\RelationController@updatePerson')->name('relations_persons.update');
        Route::delete('relations/{relation}/persons/{person}', 'Api\RelationController@destroyPerson')->name('relations_persons.delete');

        Route::get('relations/{relation}/invoices', 'Api\RelationController@salesInvoices')->name("relations.invoices");
        Route::post('relations/createcs', 'Api\RelationController@storeCs')->name("relations.storeCs");

        Route::get('relations/{relation}/bank-accounts', 'Api\RelationController@bankAccounts')->name("relations.bankAccounts");
        Route::post('relations/{relation}/bank-accounts', 'Api\RelationController@storeBankAccount')->name("relations.storeBankAccount");
        Route::match(
            ['put', 'patch'],
            'relations/{relation}/bank-accounts/{bankAccount}',
            [
                'as' => 'relations.updateBankAccount',
                'uses' => 'Api\RelationController@updateBankAccount'
            ]
        );
        Route::get('relations/{relation}/bank-accounts/next-mndt-id', 'Api\RelationController@nextMndtId')->name("relations.nextMndtId");
        Route::get('relations/{relation}/payments', 'Api\RelationController@relationPayments')->name("relations.payments");
        Route::get('relations/{relation}/payments/invoices', 'Api\RelationController@relationPaymentInvoices')->name("relations.payments.invoice_list");
        Route::post('relations/{relation}/payments/invoices', 'Api\RelationController@setPaymentInvoice')->name("relations.payments.set_invoice");

        Route::apiResource('brands', 'Api\BrandController');
        Route::apiResource('warehouses', 'Api\WarehouseController');
        Route::apiResource('billing_runs', 'Api\BillingRunController');
        Route::apiResource('network_operators', 'Api\NetworkOperatorsController');

        // Person endpoints
        Route::get('persons', 'Api\PersonController@index')->name('persons.index');
        Route::get('persons/{person}', 'Api\PersonController@show')->name('persons.show');
        Route::post('persons', 'Api\PersonController@store')->name('persons.store');
        Route::put('persons/{person}', 'Api\PersonController@update')->name('persons.update');
        Route::delete('persons/{person}', 'Api\PersonController@destroy')->name('persons.destroy');

        // Subscription endpoints
        Route::get('subscriptions/{subscription}/addresses', 'Api\SubscriptionController@addresses')->name("subscriptions.addresses");
        Route::get('subscriptions/{subscription}/persons', 'Api\SubscriptionController@persons')->name("subscriptions.persons");
        Route::get('subscriptions/{subscription}/subscription_lines', 'Api\SubscriptionController@subscriptionLines')->name('subscriptions.subscription_lines');
        Route::get('subscriptions/{subscription}/json_data', 'Api\SubscriptionController@subscriptionLines')->name('subscriptions.json_data');
        // SubscriptionLines endpoints
        Route::apiResource('subscription_lines', 'Api\SubscriptionLineController');
        Route::get('subscriptions_lines/{subscriptionLine}/gadgets', 'Api\SubscriptionLineController@gadgets')->name('subscriptions_lines.gadgets');
        Route::get('subscriptions/{subscription}/subscription_lines/{subscription_line}/subscription_line_prices', 'Api\SubscriptionLineController@subscriptionLinePrices')->name('subscriptions.subscription_line.prices');
        Route::post('subscription_lines/{subscription_line}/subscription_line_prices', 'Api\SubscriptionLinePriceController@store')->name('subscription_line_prices.create');
        Route::post('subscriptions_lines/{subscription}', 'Api\SubscriptionLineController@store')->name('subscriptions.subscription_lines.create');
        Route::post('subscription_lines/{subscription}', 'Api\SubscriptionLineController@store')->name('subscription.create');
        Route::post('subscription_lines/{subscription_line}/network_operator', 'Api\SubscriptionLineController@processNetworkOperator')->name('subscription_lines.network_operator');
        // SubscriptionLinePrices endpoints
        Route::apiResource('subscription_line_prices', 'Api\SubscriptionLinePriceController');

        //Plans Endpoints
        Route::get('plan/{plan}/plan_lines', 'Api\PlanController@planLines')->name('plan.plan_lines');
        Route::post('plan_lines/{plan}', 'Api\PlanLineController@store')->name('plan_lines.create');
        Route::apiResource('plan_lines', 'Api\PlanLineController');
        Route::get('plans/{plan}/plan_lines/{plan_line}/plan_line_prices', 'Api\PlanLineController@planLinePrices')->name('plans.plan_lines');
        Route::apiResource('plan_line_prices', 'Api\PlanLinePriceController');
        Route::post('plan_lines/{plan_line}/plan_line_prices', 'Api\PlanLinePriceController@store')->name('plan_line_prices.create');

        Route::apiResource('sales_invoices', 'Api\SalesInvoiceController');
        Route::get('sales_invoices/{sales_invoice}/sales_invoice_lines', 'Api\SalesInvoiceController@salesInvoiceLines')->name('sales_invoices.sales_invoice_lines');
        Route::get('sales_invoices/{salesInvoice}/gadgets', 'Api\SalesInvoiceController@gadgets')->name('sales_invoices.gadgets');
        Route::apiResource('sales_invoice_lines', 'Api\SalesInvoiceLineController');
        Route::get('sales_invoice_lines/{salesInvoiceLine}/gadgets', 'Api\SalesInvoiceLineController@gadgets')->name('sales_invoice_lines.gadgets');
        Route::get('sales_invoice_lines/{salesInvoiceLine}/gadget/{gadgetType}/{action}', 'Api\SalesInvoiceLineController@processGadget')->name('sales_invoice_lines.process_gadgets');
        Route::apiResource('plan_subscription_line_types', 'Api\PlanSubscriptionLineTypeController');
        Route::apiResource('vat_codes', 'Api\VatCodeController');

        Route::apiResource('person_types', 'Api\PersonTypeController');
        Route::apiResource('address_types', 'Api\AddressTypeController');
        Route::apiResource('product_types', 'Api\ProductTypeController');
        Route::apiResource('stocks', 'Api\StockController');
        Route::apiResource('serials', 'Api\SerialController');

        Route::get('sales_invoices/email/{invoice_id}', 'Api\SalesInvoiceController@sendInvoiceEmail')->name("subscriptions.email_invoice");
        Route::get('sales_invoices/credit/{sales_invoice}', 'Api\SalesInvoiceController@createCreditInvoice')->name('sales_invoices.create_credit_invoice');
        Route::get('sales_invoices/subscription/{subscriptionId}', 'Api\SalesInvoiceController@createSubscriptionInvoice')->name('sales_invoices.create_subscription_invoice');
        Route::get('sales_invoices/{sales_invoice}/{state}', 'Api\SalesInvoiceController@invoiceState')->name('sales_invoices.invoiceState');
        Route::get('sales_invoices/{sales_invoice}/send/reminder', 'Api\SalesInvoiceController@sendReminder')->name('sales_invoices.send_reminder');
        Route::get('reminders/sales_invoices/{sales_invoice}/paid', 'Api\SalesInvoiceController@paid')->name("sales_invoices.paid");

        Route::get('subscriptions/invoice/{id}', 'Api\SubscriptionController@generateInvoiceFile')->name("subscriptions.generate_invoices");
        Route::get('subscriptions/recreate_invoice/{id}', 'Api\SubscriptionController@reCreateInvoiceFile')->name("subscriptions.recreate_invoices");

        // Journalling
        Route::apiResource('journals', 'Api\JournalsController');
        Route::get('journals/{journal}/entries', 'Api\JournalsController@entries');

        Route::apiResource('entries', 'Api\EntriesController');
        Route::apiResource('fiscal_years', 'Api\FiscalYearsController');
        Route::apiResource('accounts', 'Api\AccountsController');
        Route::apiResource('accounting_periods', 'Api\AccountingPeriodsController');
        Route::apiResource('pdf_templates', 'Api\PdfTemplatesController');

        Route::get('myaccounts/{tenant}', 'Api\AccountsController@my')->name("accounts.my");
        Route::get('myfiscalyears/{tenant}', 'Api\FiscalYearsController@my')->name("fiscal_years.my");
        Route::get('myaccountingperiods/{tenant}/{fiscalYearId}', 'Api\AccountingPeriodsController@my')->name("accounting_periods.my");
        Route::get('myjournals/{tenant}', 'Api\JournalsController@my')->name("journals.my");
        Route::get('myentries/{journalId}', 'Api\EntriesController@my')->name("entries.my");
        Route::get('mypdftemplates/{tenant}', 'Api\PdfTemplatesController@my')->name("pdf_templates.my");

        Route::get('accounts/list/{tenant}', 'Api\AccountsController@list')->name("accounts.list");

        //Logs
//        Route::apiResource('activity_logs', 'Api\LogActivitiesController');
        Route::get('activity_logs', 'Api\LogActivitiesController@index');
        Route::get('activity_logs/recent', 'Api\LogActivitiesController@recent')->name("activity_logs.recent");

        Route::get('tenants/products', 'Api\TenantController@products')->name("tenants.products");
        Route::get('tenants/{type}/has-email-templates', 'Api\TenantController@hasEmailTemplates')->name("tenants.hasEmailTemplates");

        // Tenant Payment conditions
        Route::get('tenants/{tenant}/payment-conditions', 'Api\PaymentConditionController@index')->name("tenants.paymentConditions.index");
        Route::post('tenants/{tenant}/payment-conditions', 'Api\PaymentConditionController@create')->name("tenants.paymentConditions.create");
        Route::match(
            ['put', 'patch'],
            'tenants/payment-conditions/{paymentCondition}',
            [
                'as' => 'tenants.paymentConditions.update',
                'uses' => 'Api\PaymentConditionController@update'
            ]
        );
        Route::get('tenants/{tenant}/vatcodes', 'Api\TenantController@vatCodes')->name("tenants.vatcodes");
        // Route::post('tenants/finance/upload_cdrs', 'Api\TenantController@invoiceStats')->name("tenants.finance_upload_cdrs");
        Route::post('tenants/invoice_stats', 'Api\TenantController@invoiceStats')->name("tenants.invoice_stats");

        Route::apiResource('statuses', 'Api\StatusesController');

        // Notes
        Route::get('notes/{related}/{type}', 'Api\NoteController@index')->name("notes.index");
        Route::post('notes/{related}/{type}', 'Api\NoteController@create')->name("notes.create");
        Route::put('notes/{note}', 'Api\NoteController@update')->name("notes.update");

        Route::group([
            'middleware' => ['roles']
        ], function () {

            Route::apiResource('relations', 'Api\RelationController');

            Route::apiResource('tenants', 'Api\TenantController');
            Route::get('mytenants/', 'Api\TenantController@my')->name("tenants.my");
            Route::get('list_threes', 'Api\TenantController@listThrees')->name("tenants.listThrees");
            Route::apiResource('number_ranges', 'Api\NumberRangesController');

            Route::apiResource('plans', 'Api\PlanController');

            Route::apiResource('invoices', 'Api\SalesInvoiceController');
            Route::apiResource('sales_invoices', 'Api\SalesInvoiceController');

            Route::apiResource('subscriptions', 'Api\SubscriptionController');
            Route::get('provisioning_subscriptions', 'Api\SubscriptionController@provisioningSubscriptions')->name("tenants.provisioning_subscriptions");
            Route::get('provisioning_subscriptions_count', 'Api\SubscriptionController@provisioningSubscriptionsCount')->name("tenants.provisioning_subscriptions_count");

            Route::apiResource('products', 'Api\ProductController');

            // Product Hierarchy end-points
            Route::get('product_hierarchy_relation_types', 'Api\ProductHierarchyController@showHierarchyRelationTypesOpts')->name("product_hierarchy_relation_types.show");
            Route::get('products/{product}/hierarchies', 'Api\ProductHierarchyController@show')->name("products.hierarchies.show");
            Route::post('products/{product}/hierarchies/{related_product}', 'Api\ProductHierarchyController@store')->name("products.hierarchies.store");
            Route::put('products/{product}/hierarchies/{related_product}', 'Api\ProductHierarchyController@update')->name("products.hierarchies.update");
            Route::delete('products/{product}/hierarchies/{related_product}', 'Api\ProductHierarchyController@destroy')->name("products.hierarchies.delete");


            Route::apiResource('users', 'Api\UserController');

            Route::post('admin_tools/job/cdr', 'Api\AdminToolController@cdr')->name('admin_tools.job.cdr');
            Route::post('admin_tools/generate_invoices', 'Api\AdminToolController@createInvoiceQueueJob')->name("admin_tools.generate_invoices");
            Route::post('admin_tools/finalize_invoices', 'Api\AdminToolController@createFinalizeInvoicesQueueJob')->name("admin_tools.finalize_invoices");
            Route::post('admin_tools/send_invoice_emails', 'Api\AdminToolController@createSendEmailQueueJob')->name("admin_tools.send_invoice_emails");
            Route::get('admin_tools/billing_run_dates/{statusId}', 'Api\AdminToolController@billingRunsByStatus')->name("admin_tools.billing_run_dates");
            Route::get('admin_tools/billing_run_dates', 'Api\AdminToolController@billingRuns')->name("admin_tools.billing_run_dates_all");
            //These reside on the admin-tools page, but is debatable whether to move them into that controller too(?)
            Route::get('tenants/finance/validate_ibans/{tenant}', 'Api\TenantController@validateIbans')->name("tenants.finance_validate_ibans");
            Route::post('tenants/create_pain_dd_file', 'Api\TenantController@createPainDDFile')->name("tenants.create_pain_dd_file");
            Route::get('tenants/dd_file/{id}', 'Api\TenantController@downloadPainDDFile')->name("tenants.download_pain_dd_file");

            Route::post('power_tools/validateSubscription', 'Api\PowerToolController@validateSubscription')->name('power_tools.validateSubscription');
            Route::post('power_tools/resetM7', 'Api\PowerToolController@resetM7')->name('power_tools.resetM7');
            Route::post('power_tools/closeAccount', 'Api\PowerToolController@closeAccount')->name('power_tools.closeAccount');
            Route::post('power_tools/deprovisionStb', 'Api\PowerToolController@deprovisionStb')->name('power_tools.deprovisionStb');
            Route::post('power_tools/fixSubscription', 'Api\PowerToolController@fixSubscription')->name('power_tools.fixSubscription');
        });

        Route::get('dependencies/roles', 'Api\DependencyControler@roles')->name("dependencies.roles");
        Route::get('dependencies/groups', 'Api\DependencyControler@groups')->name("dependencies.groups");

        Route::get('users/tenant/{tenant_id}', 'Api\UserController@indexForTenant');
        Route::post('user/resend-code', 'Api\UserController@resendEmail');
        Route::post('user', 'Api\UserController@updatePassword');
        Route::delete('user/{user}', 'Api\UserController@destroy');
        Route::get('subscriptions/{subscription}/log_activities', 'Api\SubscriptionController@logActivities')->name('subscriptions.logActivities');
        Route::get('subscriptions/{provider}/provision/{transaction}/{status}/{limit}', 'Api\SubscriptionController@provision');

        Route::get('l2fiber/{cmd}/{action?}', 'Api\L2FiberController@cmd');
        Route::get('subscription_lines/{provider}/{method}/{subscription_line}', 'Api\SubscriptionLineController@processRequest');
        Route::post('subscription_lines/{provider}/{method}/{subscription_line}', 'Api\SubscriptionLineController@processRequest');
        Route::post('subscription_lines/{subscription_line}/serial', 'Api\SubscriptionLineController@serial');
        Route::get('prices/{subscriptionLine}/subscription_lines', 'Api\SubscriptionLineController@prices');
        Route::get('prices/{subscription}/subscription', 'Api\SubscriptionController@subscriptionLinePrices');

        Route::get('payments', 'Api\PaymentsController@index')->name("payments.index");
    });

    Route::post('deploy', 'DeployController@deploy');
    Route::get('verify/{code}', 'Api\UserController@validateCode');
    Route::post('user/{code}/password', 'Api\UserController@updatePassword');
    Route::post('user/forgot-password', 'AuthController@forgotPassword');

    Route::group(
        ['prefix' => 'ext', 'as' => 'ext.'],
        function () {
            Route::apiResource('orders', 'Api\OrderController');
        }
    );
});
