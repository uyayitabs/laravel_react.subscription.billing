<?php

namespace App\Models;

use App\Traits\HasLineProductBackendApiTrait;
use App\Traits\HasInvoiceGadgetTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SalesInvoice extends BaseModel
{
    use HasLineProductBackendApiTrait;
    use HasInvoiceGadgetTrait;

    protected $table = 'sales_invoices';

    protected $fillable = [
        'invoice_no',
        'date',
        'description',
        'tenant_id',
        'relation_id',
        'invoice_address_id',
        'shipping_address_id',
        'price',
        'price_vat',
        'price_total',
        'due_date',
        'shipping_person_id',
        'invoice_person_id',
        'invoice_status',
        'payment_status',
        'payment_condition_id',
        'inv_output_type',
        'billing_run_id'
    ];

    protected $appends = [];

    public static $fields = [
        'id',
        'invoice_no',
        'date',
        'description',
        'tenant_id',
        'relation_id',
        'invoice_address_id',
        'shipping_address_id',
        'price',
        'price_vat',
        'price_total',
        'due_date',
        'shipping_person_id',
        'invoice_person_id',
        'invoice_status',
        'payment_status',
        'payment_condition_id',
        'inv_output_type',
        'billing_run_id'
    ];

    public static $sortables = [
        'id','relation|customer_number','invoice_no','date','description','price','due_date','inv_output_type',
        'company_name','addresses:street1.house_number.room.zipcode.city','name.country','name'
    ];

    protected $searchable = [
        'id,invoice_no,invoice_date,description,price,due_date,inv_output_type',
        'relation|customer_number,company_name,addresses:street1.house_number.room.zipcode.city!name.country!name',
        'invoicePerson|first_name,last_name'
    ];

    public static $searchableCols = [
        'id',
        'invoice_no',
        'invoice_date',
        'description',
        'price',
        'due_date',
        'customer_number',
        'company_name',
        'street',
        'house_number',
        'house_number_suffix',
        'room',
        'zipcode',
        'city',
        'country',
        'status',
        'first_name',
        'last_name'
    ];

    public static $scopes = [
        'relation',
        'invoice-address',
        'invoice-person',
        'shipping-address',
        'shipping-person',
        'journal',
        'sales-invoice-lines',
        'sales-invoice-lines.subscription-line',
        'sales-invoice-lines.subscription-line.line-type',
        'sales-invoice-lines.plan-line',
        'sales-invoice-lines.plan-line.line-type',
        'tenant',
        'paymentCondition'
    ];

    public static $withScopes = [
        'tenant',
        'relation',
        'invoiceAddress',
        'invoicePerson',
        'shippingAddress',
        'shippingPerson',
        'salesInvoiceLines',
        'salesInvoiceLines.subscriptionLine',
        'salesInvoiceLines.subscriptionLine.lineType',
        'salesInvoiceLines.planLine',
        'salesInvoiceLines.planLine.lineType',
        'invoiceAddress.city',
        'invoiceAddress.country',
        'shippingAddress.city',
        'shippingAddress.country',
        'paymentCondition'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'due_date' => 'datetime:Y-m-d',
        'price' => 'float',
        'price_vat' => 'float',
        'price_total' => 'float',
        'price_inc_vat' => 'float', // dashboard.sales_invoices_summary
        'price_exc_vat' => 'float', // dashboard.sales_invoices_summary
    ];

    /**
     * Boot method
     *
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public static function boot()
    {
        parent::boot();
    }

    public function paymentCondition()
    {
        return $this->belongsTo(PaymentCondition::class)->active();
    }

    /**
     * getRelationEmailAttribute function
     *
     * @return String
     */
    public function getRelationEmailAttribute()
    {
        $relation = $this->relation()->first();
        if (!empty($relation)) {
            return $relation->email;
        }
        return null;
    }

    /**
     * Get relation.customer_number
     *
     * @return string
     */
    public function getRelationCustomerNumberAttribute()
    {
        $relation = $this->relation()->first();
        if (!empty($relation)) {
            return $relation->customer_number;
        }
        return null;
    }

    /**
     * Get invoice.person.full_name
     *
     * @return string
     */
    public function getRelationPrimaryPersonAttribute()
    {
        $relation = $this->relation()->first();
        if (!empty($relation)) {
            $person = $relation->primaryPerson()->first();
            if (!empty($person)) {
                return $person->full_name;
            }
            return null;
        }
        return null;
    }

    /**
     * Get invoice.person.full_name
     *
     * @return string
     */
    public function getRelationPrimaryPersonEmailAttribute()
    {
        $relation = $this->relation()->first();
        if (!empty($relation)) {
            $person = $relation->primaryPerson()->first();
            if (!empty($person)) {
                return $person->email;
            }
            return null;
        }
        return null;
    }


    /**
     * Get invoice relation.address.full_name
     *
     * @return string
     */
    public function getRelationPrimaryAddressAttribute()
    {
        $relation = $this->relation()->first();
        if (!empty($relation)) {
            $address = $relation->addresses()->first();
            if (!empty($address)) {
                return $address->full_address;
            }
        }
        return null;
    }

    /**
     * Get relation.company_name
     *
     * @return string
     */
    public function getRelationCompanyNameAttribute()
    {
        $relation = $this->relation()->first();
        if (!empty($relation)) {
            return $relation->company_name;
        }
        return null;
    }


    /**
     * getTenantNameAttribute function
     *
     * @return String
     */
    public function getTenantNameAttribute()
    {
        $tenant = $this->tenant()->first();
        if (!empty($tenant)) {
            return $tenant->name;
        }
        return null;
    }

    /**
     * Get invoice filename
     *
     * @return string
     */
    public function getInvoiceFilenameAttribute()
    {
        if (empty($this->invoice_no)) {
            return null;
        }

        $relation = $this->relation;
        if (!empty($relation)) {
            $filename = 'invoice';
            if ($this->is_deposit_invoice) {
                $filename = 'Borg';
            }
            return "$filename-{$relation->customer_number}-{$this->invoice_no}.pdf";
        }
        return null;
    }

    /**
     * Get invoice file dir path
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function getInvoiceFileDirPathAttribute(): string
    {
        // In the past invoices have been generated using the due_date.
        // This caused the PDF file to be located in a different folder because of the month
        // Until that is resolved, we have to check both locations
        // You could theoretically look when the invoice was made, and then base the location of that
        // That is a minor performance improvement however, I much prefer the mindless 'delete if problem resolved' fix
        $monthDir1 = Carbon::parse($this->date)->format("Y/m");
        if (File::exists(storage_path("app/private/invoices/{$this->tenant_id}/{$monthDir1}/{$this->invoice_filename}"))) {
            return storage_path("app/private/invoices/{$this->tenant_id}/{$monthDir1}");
        }
        $monthDir2 = Carbon::parse($this->due_date)->format("Y/m");
        if (File::exists(storage_path("app/private/invoices/{$this->tenant_id}/{$monthDir2}/{$this->invoice_filename}"))) {
            return storage_path("app/private/invoices/{$this->tenant_id}/{$monthDir2}");
        }
        return $monthDir1;
    }

    /**
     * Get invoice file dir path
     *
     * @return string
     */
    public function getInvoiceFileFullPathAttribute(): string
    {
        return "{$this->invoice_file_dir_path}/{$this->invoice_filename}";
    }


    /**
     * Check if invoice pdf exists
     *
     * @return bool
     */
    public function getInvoiceFileExistsAttribute(): bool
    {
        return File::exists($this->invoice_file_full_path);
    }

    /**
     * Get Subscription function
     *
     * @return \Subscription
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get SalesInvoiceLine[] function
     *
     * @return \SalesInvoiceLine
     */
    public function salesInvoiceLines()
    {
        return $this->hasMany(SalesInvoiceLine::class, "sales_invoice_id");
    }

    /**
     * Belongs to a BillingRun
     *
     * @return \BillingRun
     */
    public function billingRun()
    {
        return $this->belongsTo(BillingRun::class, 'billing_run_id');
    }

    /**
     * Get one-off cost SalesInvoiceLines
     */
    public function oneOffCostLines()
    {
        return $this->salesInvoiceLines()
            ->whereIn('sales_invoice_line_type', [
                Config::get("constants.subscription_line_types.nrc"),
                Config::get("constants.subscription_line_types.deposit")
            ]);
    }

    /**
     * Get periodic cost SalesInvoiceLines function
     */
    public function periodicCostLines()
    {
        return $this->salesInvoiceLines()
            ->whereNotIn('sales_invoice_line_type', [
                Config::get("constants.subscription_line_types.nrc"),
                Config::get("constants.subscription_line_types.deposit"),
                Config::get("constants.subscription_line_types.vuc")
            ]);
    }

    /**
     * Get usage cost SalesInvoiceLines function
     */
    public function usageCostLines()
    {
        return $this->salesInvoiceLines()
            ->whereIn('sales_invoice_line_type', [
                Config::get("constants.subscription_line_types.vuc")
            ]);
    }

    /**
     * Get Journal function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function journal()
    {
        return $this->hasOne(Journal::class, 'invoice_id', 'id');
    }

    /**
     * Get tenant
     *
     * @return \Tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get Relation function
     *
     * @return \Relation
     */
    public function relation()
    {
        return $this->belongsTo(Relation::class)->with('persons');
    }


    /**
     * Shipping Person function
     *
     * @return Person
     */
    public function shippingPerson()
    {
        return $this->hasOne(Person::class, 'id', 'shipping_person_id');
    }

    /**
     * Shipping Address function
     *
     * @return Address
     */
    public function shippingAddress()
    {
        return $this->hasOne(Address::class, 'id', 'shipping_address_id');
    }


    /**
     * Invoice Person function
     *
     * @return Person
     */
    public function invoicePerson()
    {
        return $this->hasOne(Person::class, 'id', 'invoice_person_id');
    }

    /**
     * Shipping Address function
     *
     * @return Address
     */
    public function invoiceAddress()
    {
        return $this->hasOne(Address::class, 'id', 'invoice_address_id');
    }


    /**
     * NumberRange relationship
     *
     * @return \NumberRange
     */
    public function numberRange()
    {
        return $this->hasOne(NumberRange::class, 'tenant_id', 'tenant_id');
    }


    /**
     * Get Invoice Status function
     *
     * @return \Status
     */
    public function status()
    {
        return $this->hasOne(Status::class, 'id', 'invoice_status')
            ->where('status_type_id', 1);
    }

    public function salesInvoiceMetas()
    {
        return $this->hasMany(SalesInvoiceMeta::class, 'sales_invoice_id', 'id');
    }

    /**
     * Scopes to return all the relationships
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }

    /**
     * Check whether a SubscriptionLine is a non-recurring type
     *
     * @param integer $subscriptionLineId
     * @param string $invoicingDate
     * @return boolean
     */
    public static function isToInvoiceNonRecurringCost(int $relationId, int $subscriptionLineId)
    {
        $relation = Relation::find($relationId);
        $invoiceIds = $relation->salesInvoices()
            ->pluck("id")
            ->toArray();

        if (count($invoiceIds)) {
            return boolval(
                SalesInvoiceLine::whereIn("sales_invoice_id", $invoiceIds)
                    ->where("subscription_line_id", $subscriptionLineId)
                    ->doesntExist()
            );
        }
        return true;
    }

    /**
     * Get generated invoice_no without the format
     *
     * @param int $tenantId
     * @return array
     */
    public static function getGeneratedInvoiceNos($tenantId)
    {
        $invoiceNos = SalesInvoice::select("invoice_no")
            ->where("tenant_id", $tenantId)
            ->orderBy("invoice_no", "ASC")
            ->get()
            ->pluck('invoice_no')
            ->toArray();

        $results = array_map(
            function ($invoiceNo) {
                $digitResults = [];
                $withMatch = preg_match("/\d{1,}/", $invoiceNo, $digitResults);
                if ($withMatch) {
                    return intval($digitResults[0]);
                }
                return null;
            },
            $invoiceNos
        );
        return $results;
    }

    /**
     * Update sales_invoice price, price_vat, price_total
     *
     */
    public function updatePriceTotals()
    {
        $this->fresh();
        $totalPrice = $this->salesInvoiceLines()->sum('price');
        $totalPriceVat = $this->salesInvoiceLines()->sum('price_vat');
        $totalPriceTotal = $this->salesInvoiceLines()->sum('price_total');
        $this->update([
            'price' => $totalPrice,
            'price_vat' => $totalPriceVat,
            'price_total' => $totalPriceTotal,
        ]);
    }

    public function getIsUpdatableAttribute()
    {
        return intval($this->invoice_status) == 0;
    }

    public function getLineCountAttribute()
    {
        return $this->salesInvoiceLines()->count();
    }

    public function getIsDepositInvoiceAttribute()
    {
        return $this->salesInvoiceLines()
                ->where('sales_invoice_line_type', '=', Config::get("constants.subscription_line_types.deposit"))
                ->exists() && $this->getAttribute('line_count') == 1;
    }

    public static function invoicesByBillingRun($billingRun)
    {
        $query = $billingRun->salesInvoices();

        return $query->where('price_total', '>', 0)
            ->whereHas(
                'tenant.paymentConditions',
                function ($paymentConditionsQuery) {
                    // filter sales_invoices.payment_condition_id =
                    // tenant's payment_condition.id (w/c is direct_debit=1 and status=1)
                    $paymentConditionsQuery->where([
                        ['direct_debit', '=', 1],
                        ['status', '=', 1],
                        ['id', '=', DB::raw('sales_invoices.payment_condition_id')]
                    ]);
                }
            )
            ->whereHas(
                'relation.bankAccounts',
                function ($bankAccountQuery) {
                    $bankAccountQuery->where([
                        ['dd_default', '=', 1],
                        ['status', '=', 1]
                    ])
                    ->whereRaw("CAST(UPPER(REPLACE(iban,' ', '')) AS BINARY) REGEXP BINARY '[A-Z]{2,2}[0-9]{2,2}[a-zA-Z0-9]{1,30}'");
                }
            );
    }
}
