<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\BillingRun;
use App\Models\CdrUsageCost;
use App\Models\PaymentCondition;
use App\Models\Person;
use App\Models\Product;
use App\Models\Relation;
use App\Models\RelationsPerson;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Services\SalesInvoiceService;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\VatCode;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class GenerateInvoiceTest extends TestCase
{
    use DatabaseTransactions;

    protected static $service;
    protected static $tenant;
    protected static $paymentCondition;
    protected static $relation;
    protected static $person;
    protected static $relationPerson;
    protected static $address;
    protected static $products;
    protected static $billingRun;

    public function setUp(): void
    {
        parent::setUp();

        self::$service = new SalesInvoiceService();

        self::$tenant = factory(Tenant::class)->create(['billing_schedule' => 1]);
        self::$paymentCondition = PaymentCondition::create([
            'tenant_id' => self::$tenant->id,
            'status' => 1,
            'pay_in_advance' => null,
            'net_days' => 3,
            'default' => 1
        ]);

        self::$relation = factory(Relation::class)->create([
            'tenant_id' => self::$tenant->id,
            'payment_condition_id' => self::$paymentCondition->id]);

        self::$person = factory(Person::class)->create();
        self::$relationPerson = factory(RelationsPerson::class)->create([
            'relation_id' => self::$relation->id,
            'person_id' => self::$person->id
        ]);
        self::$address = factory(Address::class)->create(['relation_id' => self::$relation->id]);

        self::$products = factory(Product::class, 20)->create()->each(function ($p) {
            factory(TenantProduct::class)->create([
                'product_id' => $p->id,
                'tenant_id' => Tenant::latest()->first()->id
            ]);
        });

        self::$billingRun = factory(BillingRun::class)->create([
            'tenant_id' => self::$tenant->id
        ]);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_jan()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2019-12-11'),
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2019-12-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id,
                'description' => $i
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2019-12-31');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2019-12-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-01-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2019-12-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-01-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_feb()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-01-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-01-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-01-31');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-01-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-02-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-01-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-02-29'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_mar()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-02-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-02-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-02-29');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-02-29'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-03-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-02-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-03-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_apr()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-03-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-03-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-03-31');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-03-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-04-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-03-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-04-30'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_may()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-04-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-04-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-04-30');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-04-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-05-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-04-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-05-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_jun()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-05-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-05-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-05-31');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-05-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-06-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-05-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-06-30'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_jul()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-06-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-06-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-06-30');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-06-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-07-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-06-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-07-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_aug()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-07-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-07-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-07-31');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-07-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-08-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-07-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-08-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_sep()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-08-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-08-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-08-31');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-08-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-09-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-08-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-09-30'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_okt()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-09-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-09-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-09-30');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-09-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-10-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-09-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-10-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_nov()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-10-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-10-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-10-31');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-10-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-11-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-10-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-11-30'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * Testing a regular case for a first invoice.
     * This means that the startDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_firstInvoice_dec()
    {

        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-11-11'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 3
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-11-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }
        $subscription->save();

        $now = Carbon::parse('2020-11-30');
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue($result->salesInvoiceLines->count() == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-11-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-12-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-11-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-12-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 2);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_jan()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2019-11-12'),
            'subscription_end' => Carbon::parse('2020-01-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2019-11-12'),
                'subscription_stop' => Carbon::parse('2020-01-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2019-11-30');
        //Generate first invoice
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2019-12-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-01-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-01-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-01-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_feb()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2019-12-12'),
            'subscription_end' => Carbon::parse('2020-02-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2019-12-12'),
                'subscription_stop' => Carbon::parse('2020-02-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2019-12-31');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-01-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-02-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-02-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-02-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_mar()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-01-12'),
            'subscription_end' => Carbon::parse('2020-03-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-01-12'),
                'subscription_stop' => Carbon::parse('2020-03-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-01-31');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-02-29'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-03-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-03-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-03-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_apr()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-02-12'),
            'subscription_end' => Carbon::parse('2020-04-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-02-12'),
                'subscription_stop' => Carbon::parse('2020-04-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-02-29');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-03-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-04-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-04-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-04-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_may()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-03-12'),
            'subscription_end' => Carbon::parse('2020-05-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-03-12'),
                'subscription_stop' => Carbon::parse('2020-05-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-03-31');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-04-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-05-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-05-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-05-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_jun()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-04-12'),
            'subscription_end' => Carbon::parse('2020-06-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-04-12'),
                'subscription_stop' => Carbon::parse('2020-06-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-04-30');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-05-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-06-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-06-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-06-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_jul()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-05-12'),
            'subscription_end' => Carbon::parse('2020-07-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-05-12'),
                'subscription_stop' => Carbon::parse('2020-07-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-05-31');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-06-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-07-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-07-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-07-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_aug()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-06-12'),
            'subscription_end' => Carbon::parse('2020-08-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-06-12'),
                'subscription_stop' => Carbon::parse('2020-08-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-06-30');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-07-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-08-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-08-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-08-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_sep()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-07-12'),
            'subscription_end' => Carbon::parse('2020-09-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-07-12'),
                'subscription_stop' => Carbon::parse('2020-09-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-07-31');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-08-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-09-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-09-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-09-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_okt()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-08-12'),
            'subscription_end' => Carbon::parse('2020-10-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-08-12'),
                'subscription_stop' => Carbon::parse('2020-10-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-08-31');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-09-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-10-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-10-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-10-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_nov()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-09-12'),
            'subscription_end' => Carbon::parse('2020-11-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-09-12'),
                'subscription_stop' => Carbon::parse('2020-11-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-09-30');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-10-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-11-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-11-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-11-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular case for a final invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_regular_lastInvoice_dec()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-10-12'),
            'subscription_end' => Carbon::parse('2020-12-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-10-12'),
                'subscription_stop' => Carbon::parse('2020-12-15'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-10-31');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-11-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-12-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-12-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-12-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 1);
    }

    /**
     * A regular set of cases for invoicing a full ongoing month.
     *
     * @return void
     */
    public function test_regular_year2020()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2019-11-12'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2019-11-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2019-11-30');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);

        //Jan
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2019-12-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-01-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-01-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-01-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Feb
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-01-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-02-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-02-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-02-29'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Mar
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-02-29'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-03-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-03-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-03-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Apr
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-03-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-04-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-04-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-04-30'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //May
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-04-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-05-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-05-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-05-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Jun
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-05-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-06-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-06-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-06-30'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Jul
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-06-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-07-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-07-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-07-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Aug
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-07-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-08-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-08-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-08-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Sep
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-08-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-09-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-09-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-09-30'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Okt
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-09-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-10-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-10-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-10-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Nov
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-10-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-11-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-11-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-11-30'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);

        //Dec
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-11-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-12-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-12-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-12-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);
    }

    /**
     * A special case for lines that have been skipped in previous runs, or ended up being delayed for several months.
     *
     * @return void
     */
    public function test_special_catchUp()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-01-12'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $numberOfLines = 5;
        for ($i = 0; $i < $numberOfLines; $i++) {
            $product = self::$products[rand(0, self::$products->count() - 1)];
            $line = $subscription->subscriptionLines()->create([
                'subscription_start' => Carbon::parse('2020-01-12'),
                'subscription_line_type' => 3,
                'product_id' => $product->id
            ]);

            $line->subscriptionLinePrices()->create([
                'subscription_line_id' => $line->id,
                'fixed_price' => $product->price,
                'price_valid_from' => $line->subscription_start
            ]);
        }

        $now = Carbon::parse('2020-02-29');
        //Generate first invoice
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-02-29'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-03-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-01-12'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-03-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 2);
        $now = $now->addMonthsNoOverflow(2)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 5);
        $this->assertTrue($result->date == Carbon::parse('2020-04-30'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-05-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-04-01'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-05-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 2);
    }

    /**
     * A regular case for deposit lines. These should be separate for the first invoice only.
     *
     * @return void
     */
    public function test_regular_DepositSeperateOnFirstInvoice()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-01-12'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $product = self::$products[rand(0, self::$products->count() - 1)];
        $line = $subscription->subscriptionLines()->create([
            'subscription_start' => Carbon::parse('2020-01-12'),
            'subscription_line_type' => 2, //Nrc
            'product_id' => $product->id
        ]);

        $line->subscriptionLinePrices()->create([
            'subscription_line_id' => $line->id,
            'fixed_price' => $product->price,
            'price_valid_from' => $line->subscription_start
        ]);

        $product = self::$products[rand(0, self::$products->count() - 1)];
        $line = $subscription->subscriptionLines()->create([
            'subscription_start' => Carbon::parse('2020-01-12'),
            'subscription_line_type' => 6, //Deposit
            'product_id' => $product->id
        ]);

        $line->subscriptionLinePrices()->create([
            'subscription_line_id' => $line->id,
            'fixed_price' => $product->price,
            'price_valid_from' => $line->subscription_start
        ]);

        $now = Carbon::parse('2020-01-31');
        //Generate first invoice
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $this->assertTrue(count($result[0]->salesInvoiceLines) == 1);
        $this->assertTrue(count($result[1]->salesInvoiceLines) == 1);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $product = self::$products[rand(0, self::$products->count() - 1)];
        $line = $subscription->subscriptionLines()->create([
            'subscription_start' => Carbon::parse('2020-01-12'),
            'subscription_line_type' => 2, //Nrc
            'product_id' => $product->id
        ]);

        $line->subscriptionLinePrices()->create([
            'subscription_line_id' => $line->id,
            'fixed_price' => $product->price,
            'price_valid_from' => $line->subscription_start
        ]);

        $product = self::$products[rand(0, self::$products->count() - 1)];
        $line = $subscription->subscriptionLines()->create([
            'subscription_start' => Carbon::parse('2020-01-12'),
            'subscription_line_type' => 2, //Nrc
            'product_id' => $product->id
        ]);

        $line->subscriptionLinePrices()->create([
            'subscription_line_id' => $line->id,
            'fixed_price' => $product->price,
            'price_valid_from' => $line->subscription_start
        ]);

        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 2);
    }

    /**
     * A regular case for Non-Recurring and Deposit to appear only once.
     *
     * @return void
     */
    public function test_regular_NrcAndDepositOnlyOnce_DepositSeparate()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-01-12'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $product = self::$products[rand(0, self::$products->count() - 1)];
        $line = $subscription->subscriptionLines()->create([
            'subscription_start' => Carbon::parse('2020-01-12'),
            'subscription_line_type' => 2, //Nrc
            'product_id' => $product->id
        ]);

        $line->subscriptionLinePrices()->create([
            'subscription_line_id' => $line->id,
            'fixed_price' => $product->price,
            'price_valid_from' => $line->subscription_start
        ]);

        $product = self::$products[rand(0, self::$products->count() - 1)];
        $line = $subscription->subscriptionLines()->create([
            'subscription_start' => Carbon::parse('2020-01-12'),
            'subscription_line_type' => 6, //Deposit
            'product_id' => $product->id
        ]);

        $line->subscriptionLinePrices()->create([
            'subscription_line_id' => $line->id,
            'fixed_price' => $product->price,
            'price_valid_from' => $line->subscription_start
        ]);

        $now = Carbon::parse('2020-01-31');
        //Generate first invoice
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $this->assertTrue(count($result[0]->salesInvoiceLines) == 1);
        $this->assertTrue(count($result[1]->salesInvoiceLines) == 1);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $this->assertTrue(count($result) == 0);
    }

    /**
     * A regular case for discounts, both Monthly and Non-Recurring over a broken period and a full month.
     *
     * @return void
     */
    public function test_regular_Discount_NrcAndMrc_BrokenAndWholeMonth()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-01-12'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $product = self::$products[rand(0, self::$products->count() - 1)];
        $line = $subscription->subscriptionLines()->create([
            'subscription_start' => Carbon::parse('2020-01-15'),
            'subscription_line_type' => 3, //Mrc
            'product_id' => $product->id
        ]);

        $line->subscriptionLinePrices()->create([
            'subscription_line_id' => $line->id,
            'fixed_price' => -10,
            'price_valid_from' => $line->subscription_start
        ]);

        $product = self::$products[rand(0, self::$products->count() - 1)];
        $line = $subscription->subscriptionLines()->create([
            'subscription_start' => Carbon::parse('2020-01-15'),
            'subscription_line_type' => 2, //Nrc
            'product_id' => $product->id
        ]);

        $line->subscriptionLinePrices()->create([
            'subscription_line_id' => $line->id,
            'fixed_price' => -20,
            'price_valid_from' => $line->subscription_start
        ]);

        $now = Carbon::parse('2020-01-31');
        //Generate first invoice
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 2);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity > 1);
        $this->assertTrue($result->salesInvoiceLines[0]->price < -15);
        $this->assertTrue($result->salesInvoiceLines[1]->quantity == 1); //Nrc is always 1
        $this->assertTrue($result->salesInvoiceLines[1]->price == -20);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 1);
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);
        $this->assertTrue($result->salesInvoiceLines[0]->price == -10);
    }

    /**
     * A special case for a credit invoice.
     * This means that the endDate of the subscriptionLine should be taken into account.
     *
     * @return void
     */
    public function test_special_credit()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-01-12'),
            'subscription_end' => Carbon::parse('2020-04-16'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        $product = self::$products[rand(0, self::$products->count() - 1)];
        $line = $subscription->subscriptionLines()->create([
            'subscription_start' => Carbon::parse('2020-01-12'),
            'subscription_line_type' => 3,
            'product_id' => $product->id
        ]);

        $line->subscriptionLinePrices()->create([
            'subscription_line_id' => $line->id,
            'fixed_price' => $product->price,
            'price_valid_from' => $line->subscription_start
        ]);

        $now = Carbon::parse('2020-02-29');
        //Generate first invoice
        self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id);
        $now = $now->addMonthsNoOverflow(1)->endOfMonth();
        $subscription->subscriptionLines->first()->subscription_stop = Carbon::parse('2020-03-15');
        $subscription->push();
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 1);
        $this->assertTrue($result->date == Carbon::parse('2020-03-31'));
        $this->assertTrue($result->due_date == Carbon::parse('2020-04-03'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2020-03-16'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-03-31'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity < 0);
        $this->assertTrue($result->salesInvoiceLines[0]->price < 0);
    }

    /**
     * A regular case for discounts, both Monthly and Non-Recurring over a broken period and a full month.
     *
     * @return void
     */
    public function test_regular_CdrCosts()
    {
        $subscription = Subscription::create([
            'tenant_id' => self::$tenant->id,
            'relation_id' => self::$relation->id,
            'subscription_start' => Carbon::parse('2020-01-12'),
            'billing_person' => self::$person->id,
            'billing_address' => self::$address->id,
            'provisioning_person' => self::$person->id,
            'provisioning_address' => self::$address->id,
            'status' => 1,
            'type' => 2
        ]);

        factory(CdrUsageCost::class, 19)->create([
            'subscription_id' => $subscription->id,
            'relation_id' => self::$relation->id,
            'date' => Carbon::parse('2019-11-15')
        ]);

        factory(CdrUsageCost::class, 19)->create([
            'subscription_id' => $subscription->id,
            'relation_id' => self::$relation->id,
            'date' => Carbon::parse('2020-01-15')
        ]);

        $now = Carbon::parse('2020-01-31');
        //Generate first invoice
        $result = self::$service->createSalesInvoicesWithAtLeastOneMonth($subscription, $now, self::$billingRun->id)[0];
        $this->assertTrue(count($result->salesInvoiceLines) == 1);
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_start == Carbon::parse('2019-11-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->invoice_stop == Carbon::parse('2020-01-15'));
        $this->assertTrue($result->salesInvoiceLines[0]->quantity == 1);
        $this->assertTrue($result->salesInvoiceLines[0]->price > 0);
    }
}
