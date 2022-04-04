<?php

namespace App\Services;

use App\DataViewModels\SalesInvoiceSummary;
use App\Exceptions\NoPriceFoundException;
use App\Http\Resources\PortalSalesInvoiceResource;
use App\Mail\InvoiceFinalize;
use App\Models\Product;
use App\DataViewModels\SalesInvoiceReminder;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Storage;
use Logging;
use App\Models\JsonData;
use App\Mail\InvoiceCheck;
use App\Models\PlanSubscriptionLineType;
use App\Models\Relation;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Models\Subscription;
use App\Models\SubscriptionLine;
use App\Models\Tenant;
use App\Models\TenantProduct;
use App\Models\PaymentCondition;
use App\Models\VatCode;
use App\Models\SalesInvoiceMeta;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\SalesInvoiceLineResource;
use App\Http\Resources\SalesInvoiceResource;
use App\Http\Resources\SalesInvoiceReminders;
use App\Jobs\SendInvoiceEmail;
use App\Jobs\SalesInvoiceReminderJob;
use App\Mail\GenerateInvoiceQueueMail;
use App\Mail\YourInvoiceNotificationMail;
use App\Models\QueueJob;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use DOMDocument;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Exceptions\InvalidIncludeQuery;
use Exception;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;

class SalesInvoiceService
{
    protected $noteService;

    public function __construct()
    {
        $this->noteService = new NoteService();
    }

    /**
     * Get invoice list
     *
     * @param int|null $relationId
     */
    public function list(?int $relationId)
    {
        $query = \Querying::for(SalesInvoice::class)
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->whereHas('relation', function (Builder $query) {
                $query->where('tenant_id', currentTenant('id'));
            });

        if ($relationId) {
            $query->where('relation_id', $relationId);
        }

        return $query;
    }

    /**
     * Get invoice list
     *
     * @param int|null $relationId
     */
    public function summary()
    {
        $query = \Querying::for(SalesInvoiceSummary::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->where('tenant_id', currentTenant('id'));

        return $query;
    }

    /**
     * List invoices for portal
     *
     * @param mixed $relationId
     * @return BaseResourceCollection
     */
    public function listPortalInvoices($relationId)
    {
        $statusService = new StatusService();
        $openStatusId = $statusService->getStatusId('invoice', 'Open');
        $closeStatusId = $statusService->getStatusId('invoice', 'Close');
        $paidStatusId = $statusService->getStatusId('invoice', 'Paid');

        $salesInvoicesQuery = SalesInvoice::where('relation_id', $relationId)
            ->whereIn('invoice_status', [$openStatusId, $closeStatusId, $paidStatusId]);

        // search filter
        $salesInvoicesQuery = $this->handleSearchFilters(
            $salesInvoicesQuery,
            request()->query("filter", [])
        );

        // sorting
        $salesInvoicesQuery = $this->handleSorting(
            $salesInvoicesQuery,
            request()->query('sort', '-date')
        );

        // pagination (page & limit)
        $limit = request()->query('offset', 10);
        $salesInvoicesQuery = $salesInvoicesQuery->paginate($limit);

        // JsonResource implementation
        $salesInvoicesQuery->transform(function (SalesInvoice $salesInvoice) {
            return (new PortalSalesInvoiceResource(
                $salesInvoice,
                'Sales invoice retrieved successfully.',
                true,
                true
            ));
        });

        return new BaseResourceCollection($salesInvoicesQuery);
    }

    /**
     * Handle search filters
     *
     * @param mixed $modelQuery
     * @param mixed $searchFilter
     * @return mixed
     */
    public function handleSearchFilters($modelQuery, $searchFilter)
    {
        if (array_key_exists('keyword', $searchFilter)) {
            $value = $searchFilter['keyword'];
            $modelQuery->search($value);
        }
        return $modelQuery;
    }

    /**
     * Handle sorting
     *
     * @param mixed $modelQuery
     * @param mixed $sortFilter
     * @return mixed
     */
    public function handleSorting($modelQuery, $sortFilter)
    {
        if ($sortFilter == '') {
            $sortFilter = '-date';
        }
        if ($sortFilter) {
            $sequence = Str::contains($sortFilter, '-') ? 'DESC' : 'ASC';
            $columnName = str_replace('-', '', $sortFilter);

            switch ($columnName) {
                case 'relation_primary_person':
                case 'relation_customer_number':
                    $columnName = 'relation_id';
                    break;
                case 'relation_company_name':
                    $columnName = 'tenant_id';
                    break;
                case 'price':
                    $columnName = 'price';
                    break;
                case 'price_total':
                    $columnName = 'price_total';
                    break;
            }

            $modelQuery->orderBy($columnName, $sequence);
        }
        return $modelQuery;
    }

    /**
     * Get invoice data
     *
     * @param mixed $id
     * @param string $message
     * @param bool $code
     * @return SalesInvoiceResource
     */
    public function show($id)
    {
        return new SalesInvoiceResource(
            SalesInvoice::find($id)
        );
    }

    /**
     * Get invoice lines
     *
     * @param SalesInvoice $salesInvoice
     * @return mixed
     */
    public function invoiceLines(SalesInvoice $salesInvoice)
    {
        $salesInvoiceLines = SalesInvoiceLine::where('sales_invoice_id', $salesInvoice->id)->get();
        $salesInvoiceLines->transform(function (SalesInvoiceLine $salesInvoiceLine) {
            return (new SalesInvoiceLineResource($salesInvoiceLine));
        });
        return $salesInvoiceLines;
    }

    /**
     * Create invoice
     *
     * @param array $data
     * @param bool $queryOnly
     * @return object|null
     * @throws InvalidIncludeQuery
     */
    public function create(array $data, $queryOnly = true)
    {
        $salesInvoiceAttributes = filterArrayByKeys($data, SalesInvoice::$fields);
        $relation = Relation::find($salesInvoiceAttributes['relation_id']);
        if (empty($salesInvoiceAttributes['payment_condition_id'])) {
            $salesInvoiceAttributes['payment_condition_id'] = $relation->default_payment_condition->id;
        }
        if (empty($salesInvoiceAttributes['tenant_id'])) {
            $salesInvoiceAttributes['tenant_id'] = $relation->tenant_id;
        }
        if (empty($salesInvoiceAttributes['inv_output_type'])) {
            $salesInvoiceAttributes['inv_output_type'] = $relation->inv_output_type;
        }
        $salesInvoiceAttributes['invoice_no'] = null;
        $salesInvoice = $relation->salesInvoices()->create($salesInvoiceAttributes);

        // If with invoice_lines
        if (array_key_exists("sales_invoice_lines", $data)) {
            $salesInvoiceLines = $data["sales_invoice_lines"];
            if (!empty($salesInvoiceLines) && (is_array($salesInvoiceLines) && count($salesInvoiceLines))) {
                foreach ($salesInvoiceLines as $salesInvoiceLineAttribute) {
                    $attributes = filterArrayByKeys($data, SalesInvoiceLine::$fields);
                    $salesInvoiceLine = SalesInvoiceLine::create($attributes);
                    $salesInvoice->salesInvoiceLines()->save($salesInvoiceLine);
                }
            }
        }

        // Update Sales Invoice price, price_total, price_vat
        $salesInvoice->fresh();
        $salesInvoice->updatePriceTotals();

        Logging::information(
            'INVOICING - SUCCESS',
            [
                'invoice_id' => $salesInvoice->id,
                'relation_id' => $salesInvoice->relation_id
            ],
            17,
            1,
            $salesInvoice->tenant_id,
            'invoice',
            $salesInvoice->id
        );

        return $this->getOne(['id' => $salesInvoice->id], $queryOnly);
    }

    /**
     * Create credit invoice
     *
     * @param SalesInvoice $salesInvoice
     * @return mixed
     */
    public function createCreditInvoice(SalesInvoice $salesInvoice)
    {
        $newSalesInvoice = $salesInvoice->replicate();
        $newSalesInvoice->invoice_no = null;
        $newSalesInvoice->billing_run_id = null;
        $newSalesInvoice->invoice_status = 0;

        // date / due_date calculation
        $relation = $salesInvoice->relation;
        $paymentCondition = $salesInvoice->paymentCondition()->first();
        if (!$paymentCondition) {
            $paymentCondition = $relation->paymentCondition()->first();
        }
        if (!$paymentCondition) {
            $paymentCondition = PaymentCondition::where([['tenant_id', $relation->tenant->id], ['default', 1]])->first();
        }
        $netDays = $paymentCondition->net_days;

        $date = now();
        $dueDate = $date->copy()->addDays($netDays);

        $newSalesInvoice->date = $date->copy()->format('Y-m-d');
        $newSalesInvoice->due_date = $dueDate->copy()->format('Y-m-d');

        $newSalesInvoice->save();
        Logging::information('Created credit sales invoice', $newSalesInvoice, 1, 1, $newSalesInvoice->tenant_id);

        foreach ($salesInvoice->salesInvoiceLines()->get() as $salesInvoiceLine) {
            $newSalesInvoiceLine = $salesInvoiceLine->replicate();

            $newQuantity = $newSalesInvoiceLine->quantity * -1;
            if ($salesInvoiceLine->product_id) {
                $newVatCode = TenantProduct::where([
                    ['tenant_id', $salesInvoice->relation->tenant->id],
                    ['product_id', $salesInvoiceLine->product_id]
                ])->first()->vatcode;
            }
            $price = $salesInvoiceLine->price_per_piece * $newQuantity;
            $vat = $price * $newVatCode->vat_percentage;
            $total = $price + $vat;

            $newSalesInvoiceLine->price = $price;
            $newSalesInvoiceLine->price_vat = $vat;
            $newSalesInvoiceLine->price_total = $total;
            $newSalesInvoiceLine->quantity = $newQuantity;
            $newSalesInvoiceLine->sales_invoice_id = $newSalesInvoice->id;
            $newSalesInvoiceLine->vat_code = $newVatCode->id;
            $newSalesInvoiceLine->vat_percentage = $newVatCode->vat_percentage;
            $newSalesInvoiceLine->save();

            Logging::information(
                'Created credit sales invoice line',
                $newSalesInvoiceLine,
                1,
                1,
                $newSalesInvoice->tenant_id
            );
        }

        $newSalesInvoice->updatePriceTotals();

        return $this->getOne(['id' => $newSalesInvoice->id], true);
    }

    /**
     *
     * @param array $data
     * @param SalesInvoice $salesInvoice
     * @return array
     */
    public function update(array $data, SalesInvoice $salesInvoice)
    {
        $updateAttributes = filterArrayByKeys($data, SalesInvoice::$fields);
        $proceedUpdate = false;
        if (!$salesInvoice->is_updatable) {
            Logging::error('Error updating Sales Invoice', $updateAttributes, 1, 1);
            return ["data" => null,
                "errorMessage" => "Only updates are allowed on in concept invoices."];
        }

        $log['old_values'] = $salesInvoice->getRawDBData();

        $relationIdParamExists = array_key_exists('relation_id', $updateAttributes);
        $invoiceAddressIdParamExists = array_key_exists('invoice_address_id', $updateAttributes);
        $shippingAddressIdParamExists = array_key_exists('shipping_address_id', $updateAttributes);
        $invoicePersonIdParamExists = array_key_exists('invoice_person_id', $updateAttributes);
        $shippingPersonIdParamExists = array_key_exists('shipping_person_id', $updateAttributes);

        $willValidateAddressPerson = ($invoiceAddressIdParamExists || $shippingAddressIdParamExists ||
            $invoicePersonIdParamExists || $shippingPersonIdParamExists);

        if ($willValidateAddressPerson) {
            $relationId = $salesInvoice->relation_id;
            if ($relationIdParamExists) {
                $relationId = $updateAttributes["relation_id"];
            }

            $invoiceAddressId = $salesInvoice->invoice_address_id;
            if ($invoiceAddressIdParamExists) {
                $invoiceAddressId = $updateAttributes["invoice_address_id"];
            }

            $invoicePersonId = $salesInvoice->invoice_person_id;
            if ($invoicePersonIdParamExists) {
                $invoicePersonId = $updateAttributes["invoice_person_id"];
            }

            $shippingAddressId = $salesInvoice->shipping_address_id;
            if ($shippingAddressIdParamExists) {
                $shippingAddressId = $updateAttributes["shipping_address_id"];
            }

            $shippingPersonId = $salesInvoice->shipping_person_id;
            if ($shippingPersonIdParamExists) {
                $shippingPersonId = $updateAttributes["shipping_person_id"];
            }

            $response = $this->validateShippingInvoiceAddressPerson(
                $relationId,
                $invoiceAddressId,
                $invoicePersonId,
                $shippingAddressId,
                $shippingPersonId
            );
            $proceedUpdate = $response["proceed"];
            $errorMessage = $response["errorMessage"];
        } else {
            $proceedUpdate = true;
            $errorMessage = "";
        }

        if (!$proceedUpdate) {
            Logging::error('Error updating Sales Invoice', $data, 1, 1);
            return [
                "data" => [],
                "errorMessage" => $errorMessage
            ];
        }

        if (!array_key_exists('invoice_status', $updateAttributes)) {
            $salesInvoice->update($updateAttributes);
            $log['new_values'] = $salesInvoice->getRawDBData();
            $log['changes'] = $salesInvoice->getChanges();

            Logging::information('Update Sales Invoice', $log, 1, 1, $salesInvoice->tenant_id);

            return [
                "data" => $salesInvoice,
                "errorMessage" => null
            ];
        }

        if ($updateAttributes['invoice_status'] == 1) { // Finalized
            if (!$salesInvoice->salesInvoiceLines()->count()) {
                Logging::error('Error updating Sales Invoice', $updateAttributes, 1, 1);
                return [
                    "data" => null,
                    "errorMessage" => "You need at least one (1) sales invoice line to Finalize."
                ];
            }

            $validationResult = $this->validateSalesInvoice($salesInvoice);
            if (!$validationResult['valid']) {
                Logging::warning('Warning - Finalizing Invoice', $validationResult['errors'], 7);
                return [
                    "data" => null,
                    "errorMessage" => $validationResult["errors"]
                ];
            }

            if (empty($salesInvoice->invoice_no)) {
                $salesInvoice->invoice_no = generateNumberFromNumberRange(
                    $salesInvoice->tenant_id,
                    'invoice_no'
                );
            }

            try {
                $this->generatePDFInvoiceFile($salesInvoice);
                $data['invoice_status'] = 20;
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'errorMessage' => 'Could not generate PDF upon finalizing PDF'
                ];
            }

            // Special comment for Niels; if you use a ->where() the ORM will always return a collection.
            // To get a single result (and therefore a single object reference) I have to get the first from this collection.
            // The only way to retrieve (that I know of) a single result is to use ->find(), which finds something on a primary key,
            // or ->first() which returns the first result
            $vucCode = PlanSubscriptionLineType::where('line_type', 'VUC')->first();
            $cdrLines = $salesInvoice->salesInvoiceLines()->where('sales_invoice_line_type', $vucCode->id)->get();
            if ($cdrLines->count()) {
                $cdrService = new CdrUsageCostService();
                try {
                    foreach ($cdrLines as $cdrLine) {
                        $cdrService->generateCdrSummaryPdf($cdrLine);
                    }

                    Logging::information(
                        'Invoicing - PDF generation Usage Cost',
                        [
                            'invoice_id' => $salesInvoice->id,
                            'invoice_line_id' => $cdrLine->id,
                            'relation_id' => $salesInvoice->relation_id
                        ],
                        17,
                        1,
                        $salesInvoice->tenant_id,
                        'invoice',
                        $salesInvoice->id
                    );
                } catch (Exception $e) {
                    return [
                        'success' => false,
                        'errorMessage' => 'Could not generate Usage Cost PDF (LineID: ' . $cdrLine->id . ')'
                    ];
                }
            }

            Logging::information(
                'INVOICING - OPEN STATUS (MANUAL)',
                [
                    'invoice_id' => $salesInvoice->id,
                    'relation_id' => $salesInvoice->relation_id
                ],
                17,
                1,
                $salesInvoice->tenant_id,
                'invoice',
                $salesInvoice->id
            );

            $salesInvoice->update($data);
            $log['new_values'] = $salesInvoice->getRawDBData();
            $log['changes'] = $salesInvoice->getChanges();

            Logging::information('Update Sales Invoice', $log, 1, 1, $salesInvoice->tenant_id);
        }

        return [
            "data" => $salesInvoice,
            "errorMessage" => null
        ];
    }

    /**
     * Delete invoice
     * @param SalesInvoice $salesInvoice
     */
    public function delete(SalesInvoice $salesInvoice)
    {
        $relationId = $salesInvoice->relation_id;
        $lineIds = $salesInvoice->salesInvoiceLines()->pluck('subscription_line_id')->toArray();
        $salesInvoice->delete();

        SubscriptionLine::whereIn('id', array_values($lineIds))->update(['last_invoice_stop' => \DB::raw("(select if((sales_invoice_lines.invoice_start > sales_invoice_lines.invoice_stop)
        OR (sales_invoice_lines.quantity < 0), date_sub(sales_invoice_lines.invoice_start, interval 1 day), sales_invoice_lines.invoice_stop)
from sales_invoice_lines where sales_invoice_lines.subscription_line_id = subscription_lines.id and subscription_lines.subscription_line_type not in (2,6) order by id desc limit 1)")]);

        return $this->list($relationId);
    }

    /**
     * Count invoice
     * @return mixed
     */
    public function count()
    {
        $tenant = currentTenant();
        return $tenant ? $tenant->salesInvoices()->count() : 0;
    }

    /**
     * Send invoice email
     *
     * @param mixed $invoiceId
     * @return (true|string)[]|(false|string)[]
     */
    public function sendEmail($invoiceId)
    {
        $salesInvoice = SalesInvoice::find($invoiceId);
        $customer = $salesInvoice->relation;
        $customerCompany = $salesInvoice->tenant;
        $invoicePerson = $salesInvoice->invoicePerson;

        if (!filter_var($invoicePerson->customer_email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => "Invoice person of this invoice does not have a valid email."
            ];
        }

        if (!File::exists($salesInvoice->invoice_file_full_path)) {
            return [
                'success' => false,
                'message' => "Invoice PDF does not exist, email not sent."
            ];
        }

        $cdrUsagePdf = null;
        // There should ever only be one Voice Cost Line per Invoice, so first gets the object instead of a list
        $voiceLine = $salesInvoice->salesInvoiceLines->where('product_id', 200)->first();
        if ($voiceLine && File::exists($voiceLine->call_summary_file_fullpath)) {
            $cdrUsagePdf = $voiceLine->call_summary_file_fullpath;
        }
        try {
            $email = new YourInvoiceNotificationMail(
                [
                    "company_name" => $customerCompany->name,
                    "user_fullname" => $invoicePerson->full_name,
                    "datePeriodDescription" => generateInvoiceDate($invoiceId),
                    "total_with_vat" => $salesInvoice->price_total
                ],
                $salesInvoice->invoice_file_full_path,
                $cdrUsagePdf,
                $salesInvoice->tenant_id,
                $salesInvoice->id
            );
            Logging::information(
                'Sending mail - Mail PDF',
                $invoicePerson->customer_email,
                17,
                1,
                $customerCompany->id,
                'invoice',
                $invoiceId
            );
            Mail::to($invoicePerson->customer_email)->queue($email);
        } catch (Exception $e) {
            Logging::exceptionWithMessage(
                $e,
                'Encountered an error while generating the invoice email',
                17,
                0,
                $salesInvoice->relation->tenant->id
            );
            return [
                'success' => false,
                'message' => "Encountered an error while generating the email."
            ];
        }

        $salesInvoice->update([
            'invoice_status' => 20 // Open
        ]);

        return [
            'success' => true,
            'message' => "Email sent."
        ];
    }

    /**
     * Get invoice
     *
     * @param array $where
     * @param bool $queryOnly
     * @return mixed
     */
    public function getOne($where = [], $queryOnly = false)
    {
        $query = QueryBuilder::for(SalesInvoice::where($where))
            ->allowedIncludes(SalesInvoice::$scopes);
        return $queryOnly ? $query : $query->first();
    }

    /**
     * @param Subscription $subscription
     * @param Carbon $invoiceStartDate
     * @param int|null $billingRunId
     * @return array|null
     * @throws NoPriceFoundException
     */
    public function createSalesInvoices(
        Subscription $subscription,
        Carbon $billingDate,
        ?int $billingRunId,
        bool $isMainBillingRun = false
    ): ?array {
        $tenant = $subscription->relation->tenant;

        // So treat periods like this;
        // Every day a billing could be started.
        // Start period calculation is different per period-type;
        // Month == Every month before billing_day means current period. After billing_day means next period
        // Quarter == Every quarter before billing_day in the last month of the quarter means current period
        // Year == Every year before billing_day in last month of the year means current period
        $periodType = PlanSubscriptionLineType::find($subscription->type);
        switch ($periodType->line_type) {
            case 'YRC':
                $invoiceStartDate = $billingDate->copy();
                $invoiceStartDate->month = 12;
                $invoiceStartDate->day = $tenant->billing_schedule;
                if ($billingDate->month % 3 === 0 &&  $billingDate->day < $tenant->billing_day) {
                    $invoiceStartDate->subYear();
                }
                $invoiceDuration = 12;
                break;
            case 'QRC':
                $currentQuarter = floor($billingDate->month / 3);
                $invoiceStartDate = $billingDate->copy();
                if ($billingDate->month % 3 === 0 &&  $billingDate->day < $tenant->billing_day) {
                    $currentQuarter--;
                }
                $invoiceStartDate->month = ($currentQuarter * 3) + 1;
                $invoiceStartDate->day = $tenant->billing_schedule;
                $invoiceDuration = 3;
                break;
            default:
            case 'MRC':
                $invoiceStartDate = $billingDate->copy();
                $invoiceStartDate->day = $tenant->billing_schedule;
                if ($billingDate < $invoiceStartDate && $billingDate->day < $tenant->billing_day) {
                    $invoiceStartDate->subMonth();
                } elseif ($billingDate->day >= $tenant->billing_day && $billingDate->day > $tenant->billing_schedule) {
                    $invoiceStartDate->addMonth();
                }
                $invoiceDuration = 1;
                break;
        }
        $invoiceStopDate = $invoiceStartDate->copy();
        $invoiceStopDate->addMonths($invoiceDuration);
        $invoiceStopDate->subDay();

        if (!$this->ValidateSubscriptionForInvoice($subscription, $billingRunId)) {
            return null;
        }

        return $this->generateSalesInvoices(
            $subscription,
            $billingDate,
            $invoiceStartDate,
            $invoiceStopDate,
            $tenant,
            $invoiceDuration,
            $billingRunId,
            $isMainBillingRun
        );
    }

    /**
     * @param Subscription $subscription
     * @param Carbon $invoiceStartDate
     * @param Carbon $invoiceStopDate
     * @param Tenant $tenant
     * @param int $invoiceDuration
     * @param int|null $billingRunId
     * @return array
     * @throws NoPriceFoundException|\Throwable
     */
    private function generateSalesInvoices(
        Subscription $subscription,
        Carbon $billingDate,
        Carbon $invoiceStartDate,
        Carbon $invoiceStopDate,
        Tenant $tenant,
        int $invoiceDuration,
        ?int $billingRunId,
        bool $isMainBillingRun
    ): array {
        $types = PlanSubscriptionLineType::get();
        $depositCode = $types->where('line_type', 'Deposit')->first()->id;

        $customer = $subscription->relation;
        $paymentCondition = $subscription->relation->payment_condition_id ?
            $customer->paymentCondition()->first() :
            PaymentCondition::where([['tenant_id', $tenant->id], ['default', 1]])->first();
        $dueDate = $billingDate->copy()->addDays($paymentCondition->net_days);

        $separateDeposit = SalesInvoiceLine::whereHas(
            'subscriptionLine',
            function (Builder $query) use ($subscription, $depositCode) {
                    $query->where('subscription_id', $subscription->id);
            }
        )->count() === 0;

        //Time to generate the invoice lines!
        $depositLines = [];
        $salesLines = [];

        foreach ($subscription->subscriptionLines()->get() as $subscriptionLine) {
            try {
                $lines = $this->createSalesInvoiceLines(
                    $subscription,
                    $subscriptionLine,
                    $invoiceStartDate,
                    $invoiceStopDate,
                    $invoiceDuration,
                    $types,
                    $tenant,
                    $billingRunId
                );
            } catch (NoPriceFoundException $e) {
                //No price was found, cascade throw for mail
                throw $e;
            }

            if (empty($lines)) {
                continue;
            }

            if ($separateDeposit && $subscriptionLine->subscription_line_type == $depositCode) {
                $depositLines = array_merge($depositLines, $lines);
            } else {
                $salesLines = array_merge($salesLines, $lines);
            }
        }

        //If there are voice costs, add those too. These are independent of subscriptionLines
        if ($subscription->cdrUsageCosts()->count() > 0) {
            $voiceCostsLine = $this->createTelephonySalesInvoiceLine($subscription);
            if (isset($voiceCostsLine)) {
                $salesLines[] = $voiceCostsLine;
            }
        }

        $salesInvoices = [];

        // Create a SalesInvoice if there are lines
        if (!empty($salesLines)) {
            $linesPrice = 0.0;
            $linesVat = 0.0;
            $linesTotal = 0.0;
            try {
                foreach ($salesLines as $line) {
                    $linesPrice += $line->price;
                    $linesVat += $line->price_vat;
                    $linesTotal += $line->price_total;
                }
                //This is put here because we changes the logic behind prices. Once we know this works as intended we can remove this
            } catch (Exception $exception) {
                Logging::exceptionWithData($exception, 'CHECK THIS OUT', $salesLines, 17, 0, $tenant->id);
                throw $exception;
            }

            if (!$isMainBillingRun && $billingRunId !== null && $linesPrice < 4) {
                return [];
            }

            $salesInvoices[0] = SalesInvoice::create([
                'date' => $billingDate->copy()->format("Y-m-d"),
                'description' => $subscription->description,
                'tenant_id' => $customer->tenant_id,
                'relation_id' => $subscription->relation_id,
                'price' => $linesPrice,
                'price_vat' => $linesVat,
                'price_total' => $linesTotal,
                'invoice_person_id' => $subscription->getPersonBillingAttribute()->id,
                'invoice_address_id' => $subscription->getAddressBillingAttribute()->id,
                'shipping_person_id' => $subscription->getPersonProvisioningAttribute()->id,
                'shipping_address_id' => $subscription->getAddressProvisioningAttribute()->id,
                'due_date' => $dueDate->copy()->format('Y-m-d'),
                'invoice_status' => 0,
                'payment_condition_id' => $paymentCondition->id,
                'inv_output_type' => $customer->inv_output_type,
                'billing_run_id' => $billingRunId
            ]);

            $nrcCode = $types->where('line_type', 'NRC')->first()->id;
            foreach ($salesLines as $line) {
                if ($line->subscriptionLine && $line->subscriptionLine->subscription_line_type != $nrcCode) {
                    $isCredit = $line->quantity < 0;
                    $subscriptionLine = $line->subscriptionLine;
                    if ($isCredit) {
                        $subscriptionLine->last_invoice_stop = $line->invoice_start->copy()->subDay();
                    } else {
                        $subscriptionLine->last_invoice_stop = $line->invoice_stop ?
                        $line->invoice_stop->copy()->format("Y-m-d") :
                        $line->invoice_start->copy()->format("Y-m-d");
                    }
                    $subscriptionLine->save();
                }

                $salesInvoices[0]->salesInvoiceLines()->save($line);
            }

            if (isset($voiceCostsLine)) {
                $subscription->cdrUsageCosts()
                    ->update(['sales_invoice_line_id' => $voiceCostsLine->id]);
            }
        }

        //If we had to seperate DepositLines, create the seperate invoice here.
        if (!empty($depositLines)) {
            $depositPrice = 0.0;
            $depositVat = 0.0;
            $depositTotal = 0.0;
            foreach ($depositLines as $depositLine) {
                $depositPrice += $depositLine->price;
                $depositVat += $depositLine->price_vat;
                $depositTotal += $depositLine->price_total;
            }

            $salesInvoices[1] = SalesInvoice::create([
                'date' => $billingDate->copy()->format("Y-m-d"),
                'description' => $subscription->description,
                'tenant_id' => $customer->tenant_id,
                'relation_id' => $subscription->relation_id,
                'price' => $depositPrice,
                'price_vat' => $depositVat,
                'price_total' => $depositTotal,
                'invoice_person_id' => $subscription->getPersonBillingAttribute()->id,
                'invoice_address_id' => $subscription->getAddressBillingAttribute()->id,
                'shipping_person_id' => $subscription->getPersonProvisioningAttribute()->id,
                'shipping_address_id' => $subscription->getAddressProvisioningAttribute()->id,
                'due_date' => $dueDate->copy()->format('Y-m-d'),
                'invoice_status' => 0,
                'payment_condition_id' => $paymentCondition->id,
                'inv_output_type' => $customer->inv_output_type,
                'billing_run_id' => $billingRunId
            ]);
            foreach ($depositLines as $line) {
                $salesInvoices[1]->salesInvoiceLines()->save($line);
            }
        }

        return $salesInvoices;
    }

    /**
     * @param Subscription $subscription
     * @param SubscriptionLine $subscriptionLine
     * @param Carbon $invoiceStartDate
     * @param Carbon $invoiceEndDate
     * @param $invoiceDuration
     * @param $types
     * @param $tenant
     * @param int|null $billingRunId
     * @return array
     * @throws NoPriceFoundException
     */
    public function createSalesInvoiceLines(
        Subscription $subscription,
        SubscriptionLine $subscriptionLine,
        Carbon $invoiceStartDate,
        Carbon $invoiceEndDate,
        $invoiceDuration,
        $types,
        $tenant,
        ?int $billingRunId
    ): array {
        $lastStop = $subscriptionLine->last_invoice_stop ?
            Carbon::parse($subscriptionLine->last_invoice_stop) : null;

        //If there is no invoice yet, get startDate from subscription
        $startDates = array_filter([
            $lastStop,
            $subscriptionLine->subscription_start,
            $subscription->subscription_start
        ]);
        $startDate = array_values($startDates)[0];

        //Get Unit Price, needed for validation
        $subscriptionLinePrice = $subscriptionLine->subscriptionLinePrices()
            ->where("price_valid_from", "<=", $startDate->copy()->format('Y-m-d'))->first();

        $endDates = array_filter([
            ($subscriptionLine->subscription_stop &&    //Prio 1: Take SubscriptionLine Stop if stop is before invoice stop
                $subscriptionLine->subscription_stop < $invoiceEndDate) ?
                $subscriptionLine->subscription_stop : null,
            ($subscription->subscription_stop &&        //Prio 2: Take Subscription Stop if stop is before invoice stop
                $subscription->subscription_stop < $invoiceEndDate) ?
                $subscription->subscription_stop : null,
            $invoiceEndDate                             //Prio 3: There is no Stop -> Charge until the end of current invoicing period
        ]);
        $endDate = array_values($endDates)[0];

        // Take the greater of the two billing_start
        $tenantBillingStart = Carbon::parse($tenant->invoice_start_calculation);
        $subscriptionBillingStart = $subscription->billing_start;
        if ($tenantBillingStart > $subscriptionBillingStart) {
            $billingStartDate = $tenantBillingStart;
        } else {
            $billingStartDate = $subscriptionBillingStart;
        }

        if (
            !$this->ValidateSubscriptionLineForInvoice(
                $subscriptionLine,
                $subscriptionLinePrice,
                $lastStop,
                $startDate,
                $invoiceStartDate,
                $endDate,
                $types,
                $billingStartDate,
                $billingRunId
            )
        ) {
            return [];
        }

        // If another platform billed then this Line only starts billing after that last billing_date
        // This means for recurring Lines that invoice_start will be matching this billing_date
        if (
            $subscriptionLine->subscription_line_type !== $types->where('line_type', 'NRC')->first()->id &&
            $subscriptionLine->subscription_line_type !== $types->where('line_type', 'Deposit')->first()->id &&
            $billingStartDate < $endDate && $billingStartDate > $startDate
        ) {
            $startDate = $billingStartDate;
        }

        //Get Value Added Taxes
        $tenantProduct = TenantProduct::where([
            ['product_id', $subscriptionLine->product_id],
            ['tenant_id', $subscription->relation->tenant->id]])->first();

        $vatCode = VatCode::findOrFail($tenantProduct->vat_code_id);
        $vatPercentage = $vatCode ? $vatCode->vat_percentage : Config::get('constants.options.default_vat_percentage');

        //If subscription_stop is at 2021-02-28
        //And the last invoice was at 2021-02-28
        //StartDate will be last invoice + 1, thus 2021-03-01 resulting in unnecessary credit.
        $isCredit = $endDate < $startDate;


        //If credit, EndDate is before StartDate. Therefore switch EndDate and StartDate
        if ($isCredit) {
            $tempDate = $startDate->copy();
            $startDate = $endDate->copy();
            $endDate = $tempDate->copy();
        }

        if ($lastStop) {
            $startDate->addDay();
        }

        $salesLines = [];
        $prices = $subscriptionLine->getLinePricesDuringPeriod($startDate, $endDate);
        switch ($subscriptionLine->subscription_line_type) {
            case $types->where('line_type', 'NRC')->first()->id://Nrc (static price, no end date!)
            case $types->where('line_type', 'Deposit')->first()->id://Deposit
                $price = floatval($subscriptionLinePrice->fixed_price);
                $vat = $price * $vatPercentage;
                $salesLine = new SalesInvoiceLine(); //Should probably create a method for this or something
                $salesLine->product_id = $subscriptionLine->product_id;
                $salesLine->description = $subscriptionLine->description;
                $salesLine->price_per_piece = $price;
                $salesLine->quantity = 1;
                $salesLine->price = $price;
                $salesLine->vat_code = $vatCode->id;
                $salesLine->vat_percentage = $vatPercentage;
                $salesLine->price_vat = $vat;
                $salesLine->price_total = $price + $vat;
                $salesLine->subscription_line_id = $subscriptionLine->id;
                $salesLine->subscription_id = $subscription->id;
                $salesLine->sales_invoice_line_type = $subscriptionLine->subscription_line_type;
                $salesLine->plan_line_id = $subscriptionLine->plan_line_id;
                $salesLine->invoice_start = $startDate->copy()->format("Y-m-d");
                $salesLines[] = $salesLine;
                break;
            case $types->where('line_type', 'YRC')->first()->id://Yrc
                $lineDuration = 12;
                break;
            case $types->where('line_type', 'QRC')->first()->id://Qrc
                $lineDuration = 3;
                break;
            case $types->where('line_type', 'MRC')->first()->id://Mrc
            default:
                $lineDuration = 1;
                break;
        }

        if (empty($salesLines)) {
            for ($i = 0; $i < count($prices); $i++) {
                $start = $prices[$i]->price_valid_from;
                $stop = count($prices) > $i + 1 ? $prices[$i + 1]->price_valid_from->subDay() : $endDate;
                $quantity = \getDateDiff(
                    $start->copy()->format("Y-m-d"),
                    $stop->copy()->format("Y-m-d")
                );
                $quantity = round($quantity * ($invoiceDuration / $lineDuration), 2);
                if ($isCredit) {
                    $quantity = $quantity * -1;
                }
                $unitPrice = floatval($prices[$i]->fixed_price);
                $totalPrice = $quantity * $unitPrice;
                $totalVat = $totalPrice * $vatPercentage;

                $salesLine = new SalesInvoiceLine();
                $salesLine->product_id = $subscriptionLine->product_id;
                $salesLine->description = $subscriptionLine->description;
                $salesLine->price_per_piece = $unitPrice;
                $salesLine->quantity = $quantity;
                $salesLine->price = $totalPrice;
                $salesLine->vat_code = $vatCode->id;
                $salesLine->vat_percentage = $vatPercentage;
                $salesLine->price_vat = $totalVat;
                $salesLine->price_total = $totalPrice + $totalVat;
                $salesLine->subscription_line_id = $subscriptionLine->id;
                $salesLine->subscription_id = $subscription->id;
                $salesLine->sales_invoice_line_type = $subscriptionLine->subscription_line_type;
                $salesLine->plan_line_id = $subscriptionLine->plan_line_id;
                $salesLine->invoice_start = $start->copy()->format("Y-m-d");
                $salesLine->invoice_stop = $stop;
                $salesLines[] = $salesLine;
            }
        }

        return $salesLines;
    }

    public function createTelephonySalesInvoiceLine(
        Subscription $subscription
    ) {
        //ProductId = 40 filter | Onbeperkt Bellen Vast & Mobiel pakket
        $productId40Count = $subscription->subscriptionLines()
            ->where('product_id', 40)
            ->count();

        //ProductId = 42 filter | Onbeperkt Vrij Bellen
        $productId42Count = $subscription->subscriptionLines()
            ->where('product_id', 42)
            ->count();

        //ProductId = 55 filter | Onbeperkt Bellen NL & Buitenland Vast Pakket
        $productId55Count = $subscription->subscriptionLines()
            ->where('product_id', 55)
            ->count();

        $regexPattern = null;
        if ($productId40Count) {
            $regexPattern = "^\\\\+31(1|2|3|4|5|6|7|85).*";
        }
        if ($productId42Count) {
            $regexPattern = "^\\\\+31(1|2|3|4|5|7|85).*";
        }
        if ($productId55Count) {
            $regexPattern = "^\\\\+(376|61|32|1|45|49|358|33|350|30|36|353|";
            $regexPattern .= "354|39|352|212|64|47|43|351|34|90|420|44|41|46).*";
        }
        if (!empty($regexPattern)) {
            $whereCriteria = "recipient REGEXP '{$regexPattern}'";
            $cdrUsageCostsToUpdate = $subscription->cdrUsageCosts()->whereRaw($whereCriteria);
            $cdrUsageCostsToUpdate->update([
                'total_cost' => 0
            ]);
        }
        $cdrUsage = $subscription->cdrUsageCosts()->get()->sortBy('datetime');
        $cdrUsageTotalCost = $cdrUsage->sum('total_cost');
        $vatPercentage = Config::get('constants.options.default_vat_percentage');
        $cdrUsageTotalVat = $cdrUsageTotalCost * $vatPercentage;
        $salesLine = new SalesInvoiceLine();
        $salesLine->product_id = Product::where('vendor_partcode', 'VUC')->first()->id;
        $salesLine->description = Config::get('constants.voice_usage_costs_desc');
        $salesLine->price_per_piece = $cdrUsageTotalCost;
        $salesLine->quantity = 1;
        $salesLine->price = $cdrUsageTotalCost;
        $salesLine->vat_code = 6;
        $salesLine->vat_percentage = Config::get('constants.options.default_vat_percentage');
        $salesLine->price_vat = $cdrUsageTotalVat;
        $salesLine->price_total = $cdrUsageTotalCost + $cdrUsageTotalVat;
        $salesLine->subscription_id = $subscription->id;
        $salesLine->sales_invoice_line_type = Config::get('constants.subscription_line_types.vuc');
        $salesLine->invoice_start = $cdrUsage->first()->datetime->format('Y-m-d');
        $salesLine->invoice_stop = $cdrUsage->last()->datetime->format('Y-m-d');
        return $salesLine;
    }

    /**
     * Geerate PDF invoice file
     *
     * @param SalesInvoice $salesInvoice
     * @param bool $generateHTMLFile
     * @return string
     */
    public function generatePDFInvoiceFile(SalesInvoice $salesInvoice, bool $generateHTMLFile = false): string
    {
        $now = now();
        $tenant = $salesInvoice->tenant()->first();
        $relation = $salesInvoice->relation()->first();
        $invoicePerson = $salesInvoice->invoicePerson()->first();
        $invoiceAddress = $salesInvoice->invoiceAddress()->first();
        $paymentCondition = $salesInvoice->paymentCondition()->first();

        $oneOffCosts = $periodicCosts = $usageCosts = [];
        $paymentMethod = $paymentMethodNetDays = "";
        $withDepositLine = false;

        // NRC | Deposit (One-off cost SalesInvoiceLines)
        $oneOffLines = $salesInvoice->oneOffCostLines()->orderBy('invoice_start')->get();
        $oneOffCosts["items"] = [];
        foreach ($oneOffLines as $salesInvoiceLine) {
            $description = $salesInvoiceLine->description;
            $isSubscriptionLine = !empty($salesInvoiceLine->subscriptionLine()->first());

            if (
                ($isSubscriptionLine &&
                    $salesInvoiceLine->subscriptionLine()->first()->isLineTypeDeposit()) ||
                ($salesInvoiceLine->sales_invoice_line_type == Config::get("constants.subscription_line_types.deposit"))
            ) {
                $description .= "<span class=\"note-count\">1</span>";
                $withDepositLine = true;
            }

            $oneOffCosts["items"][] = [
                'description' => $description,
                'period' => $salesInvoiceLine->period,
                'price' => $salesInvoiceLine->price,
                'price_with_vat' => $salesInvoiceLine->price_total
            ];
        }
        $oneOffCosts["with_deposit_line"] = $withDepositLine;
        $oneOffCosts['sum_price_with_vat'] = floatval($oneOffLines->sum("price_total"));
        $oneOffCosts['sum_price'] = floatval($oneOffLines->sum("price"));

        // MRC | QRC | YRC | DISCOUNT | FREE (Periodic cost SalesInvoiceLines)
        $periodicLines = $salesInvoice->periodicCostLines()->orderBy('invoice_start')->get();
        $periodicCosts["items"] = [];
        foreach ($periodicLines as $salesInvoiceLine) {
            $periodicCosts["items"][] = [
                'description' => $salesInvoiceLine->description,
                'period' => $salesInvoiceLine->period,
                'price' => $salesInvoiceLine->price,
                'price_with_vat' => $salesInvoiceLine->price_total
            ];
        }
        $periodicCosts['sum_price_with_vat'] = floatval($periodicLines->sum("price_total"));
        $periodicCosts['sum_price'] = floatval($periodicLines->sum("price"));

        // VUC (Usage cost SalesInvoiceLines)
        $usageLines = $salesInvoice->usageCostLines()->orderBy('invoice_start')->get();
        $usageCosts["items"] = [];
        foreach ($usageLines as $salesInvoiceLine) {
            $usageCosts["items"][] = [
                'description' => $salesInvoiceLine->description,
                'period' => $salesInvoiceLine->period,
                'price' => $salesInvoiceLine->price,
                'price_with_vat' => $salesInvoiceLine->price_total
            ];
        }
        $usageCosts['sum_price_with_vat'] = floatval($usageLines->sum("price_total"));
        $usageCosts['sum_price'] = floatval($usageLines->sum("price"));

        if (!empty($relation->company_name)) {
            $customerName = $relation->company_name . "<br>" . $invoicePerson->full_name;
        } else {
            $customerName = $invoicePerson->full_name;
        }

        $customerAddress1 = "{$invoiceAddress->street1} {$invoiceAddress->house_number}";
        if (!empty($invoiceAddress->house_number_suffix)) {
            $customerAddress1 .= "-{$invoiceAddress->house_number_suffix}";
        }
        $customerAddress1 .= " {$invoiceAddress->room}";
        $customerAddress2 = "{$invoiceAddress->zipcode} {$invoiceAddress->city_name}";
        $customerNumber = $relation->customer_number;

        $bankAccount = $relation->bankAccount->first();
        $customerIBAN = !empty($bankAccount) ? $bankAccount->iban : null;

        $invoiceId = $salesInvoice->id;
        $invoiceDate = $salesInvoice->date;
        $invoiceNumber = $salesInvoice->invoice_no;
        $customerVatNumber = $relation->vat_no;
        $invoiceDueDate = $salesInvoice->due_date;

        $periodicCostPeriod = $usageCostPeriod = [];

        $paymentMethodNetDays = 0;
        if (!empty($paymentCondition)) {
            if (boolval($paymentCondition->direct_debit)) {
                $paymentMethod = "direct_debit";
            }
            $paymentMethodNetDays = $paymentCondition->net_days;
        }
        $m7LineCount = $salesInvoice->getInvoiceLineCountWithBackendApi("m7");

        $invoicePrice = $salesInvoice->price;
        $invoicePriceVat = $salesInvoice->price_vat;
        $invoicePriceWithVat = $salesInvoice->price_total;

        $oneOffCostPriceWithVat = floatval($salesInvoice->oneOffCostLines()->sum("price_total"));
        $oneOffCostPrice = floatval($salesInvoice->oneOffCostLines()->sum("price"));
        $periodicCostPriceWithVat = floatval($salesInvoice->periodicCostLines()->sum("price_total"));
        $periodicCostPrice = floatval($salesInvoice->periodicCostLines()->sum("price"));

        if ($periodicLines->count() > 0) {
            $salesInvoiceLine = $periodicLines->first();
            $periodicCostPeriod['start'] = $salesInvoiceLine->invoice_start;
            $salesInvoiceLine = $periodicLines->sortByDesc('invoice_stop')->first();
            $periodicCostPeriod['stop'] = $salesInvoiceLine->invoice_stop;
        }

        $usageCostPriceWithVat = floatval($salesInvoice->usageCostLines()->sum("price_total"));
        $usageCostPrice = floatval($salesInvoice->usageCostLines()->sum("price"));
        if ($salesInvoice->usageCostLines()->count()) {
            $salesInvoiceLine = $usageLines->first();
            $usageCostPeriod['start'] = $salesInvoiceLine->invoice_start;
            $salesInvoiceLine = $usageLines->sortByDesc('invoice_stop')->first();
            $usageCostPeriod['stop'] = $salesInvoiceLine->invoice_stop;
        }

        $data = compact(
            "invoiceId",
            "invoiceDate",
            "invoiceDueDate",
            "invoiceNumber",
            "invoicePrice",
            "invoicePriceVat",
            "invoicePriceWithVat",
            "customerIBAN",
            "customerName",
            "customerAddress1",
            "customerAddress2",
            "customerNumber",
            "customerVatNumber",
            "oneOffCostPriceWithVat",
            "periodicCostPriceWithVat",
            "periodicCostPeriod",
            "usageCostPriceWithVat",
            "usageCostPeriod",
            "oneOffCosts",
            "periodicCosts",
            "usageCosts",
            "oneOffCostPrice",
            "periodicCostPrice",
            "usageCostPrice",
            "paymentMethod",
            "paymentMethodNetDays",
            "m7LineCount",
        );

        // Get invoice_pdf_template from DB
        $templateName = 'invoice';
        if ($salesInvoice->is_deposit_invoice) {
            $templateName = 'deposit_invoice';
        }
        if ($relation->isBusiness()) {
            $templateName = 'invoice.bus';
        }
        $pdfTemplate = $tenant->getPdfTemplate($templateName)->first();
        if ($pdfTemplate) {
            $invoiceHTML = getStringBladeView($pdfTemplate->main_html, $data);
            if ($invoiceHTML) {
                $monthDir = Carbon::parse($salesInvoice->date)->format("Y/m");
                $dirPath = Storage::path("private/invoices/{$salesInvoice->tenant_id}/{$monthDir}/");
                if (!File::isDirectory($dirPath)) {
                    try {
                        File::makeDirectory($dirPath, 0775, true, true);
                    } catch (Exception $exception) {
                        Logging::exceptionWithData(
                            $exception,
                            "INVOICING EXCEPTION - FOLDER CREATION",
                            [
                                'invoice_id' => $salesInvoice->id,
                                'relation_id' => $salesInvoice->relation_id,
                            ],
                            17,
                            0,
                            $salesInvoice->tenant_id,
                            'invoice',
                            $salesInvoice->relation_id
                        );
                        return "Could not create directory";
                    }
                }

                if ($generateHTMLFile) {
                    $htmlOutputFile = str_replace(".pdf", ".html", $dirPath . $salesInvoice->invoice_filename);
                    if (file_exists($htmlOutputFile)) {
                        File::delete($htmlOutputFile);
                    }
                    file_put_contents($htmlOutputFile, $invoiceHTML);
                }

                // PDF name
                $pdfOutputFile = $dirPath . $salesInvoice->invoice_filename;
                if (file_exists($pdfOutputFile)) {
                    File::delete($pdfOutputFile);
                }

                try {
                    SnappyPdf::loadHTML($invoiceHTML)
                        ->setPaper('a4')
                        ->setOptions([
                            'no-background' => false,
                            'background' => true,
                            'disable-javascript' => true,
                            'print-media-type' => false,
                            'disable-smart-shrinking' => true,
                            'lowquality' => false,
                            'header-html' => $pdfTemplate->header_html,
                            'margin-top' => "3.81cm",
                            'margin-right' => 0,
                            'footer-html' => $pdfTemplate->footer_html,
                            'margin-bottom' => "2.54cm",
                            'margin-left' => 0,
                        ])
                        ->save($pdfOutputFile);

                    $salesInvoice->update(['invoice_status' => 20]);

                    Logging::information('Success Invoicing - Pdf successfully generated', [
                        'month_dir' => $monthDir,
                        'filename' => $salesInvoice->invoice_filename,
                        'time_taken' => $now->diff(Carbon::now())
                    ], 17, 1, $salesInvoice->tenant_id, 'invoice', $salesInvoice->id);

                    return $pdfOutputFile;
                } catch (Exception $exception) {
                    Logging::exceptionWithData(
                        $exception,
                        "INVOICING EXCEPTION - PDF CREATION",
                        [
                            'invoice_id' => $salesInvoice->id,
                            'relation_id' => $salesInvoice->relation_id,
                        ],
                        17,
                        0,
                        $salesInvoice->tenant_id,
                        'invoice',
                        $salesInvoice->id
                    );
                    return "";
                }
            }
        }
        return "";
    }

    /**
     * Send invoice reminder
     */
    public function sendReminder(SalesInvoice $salesInvoice)
    {
        $salesInvoiceMeta = $salesInvoice->salesInvoiceMetas()->where('key', 'reminder_status')->first();

        $key = 'first_reminder';
        if ($salesInvoiceMeta) {
            switch ($salesInvoiceMeta->value) {
                case 'first_reminder_sent':
                    $key = 'second_reminder';
                    break;

                case 'second_reminder_sent':
                    $key = 'warning';
                    break;

                case 'warning_sent':
                    $key = 'final_notice';
                    break;
            }
        }

        $tenant = $salesInvoice->tenant;
        $emailTemplateExists = $tenant->emailTemplatesByType("sales_invoice.$key")->exists();

        if (!$emailTemplateExists) {
            $message = "Sorry, we can\'t send this email. ";
            $message .= "Contact your administrator for support.<br />";
            $message .= '[Missing email template: ' . $key . ' / ' . $tenant->id . ']';
            $response = [
                'success' => false,
                'message' => $message,
            ];

            return $response;
        }

        if (!$salesInvoiceMeta || $salesInvoiceMeta->value != 'sent_to_collection_agency') {
            if ('local' != config('app.env')) {
                SalesInvoiceReminderJob::dispatchNow($salesInvoice);
            }

            if (!$salesInvoiceMeta) {
                $salesInvoiceMeta = $salesInvoice->salesInvoiceMetas()
                    ->create([
                        'key' => 'reminder_status',
                        'value' => 'first_reminder_sent'
                    ]);
                $text = 'Herinnering verstuurd voor factuur ?';
            } else {
                switch ($salesInvoiceMeta->value) {
                    case 'first_reminder_sent':
                        $value = 'second_reminder_sent';
                        $text = 'Tweede herinnering verstuurd voor factuur ?';
                        break;
                    case 'second_reminder_sent':
                        $value = 'warning_sent';
                        $text = 'Aanmaning verstuurd voor factuur ?';
                        break;
                    case 'warning_sent':
                        $value = 'final_notice_sent';
                        $text = 'Ingebrekestelling verstuurd voor factuur ?';
                        break;
                    case 'final_notice_sent':
                        $value = 'sent_to_collection_agency';
                        $text = "Factuur ? uit handen gegeven aan incassobureau.";
                        break;
                }
                $salesInvoiceMeta->value = $value;
                $salesInvoiceMeta->save();
            }

            if ($text) {
                $data = ['text' => Str::replaceArray('?', [$salesInvoice->invoice_no], $text)];
                $this->noteService->create($salesInvoice->relation_id, 'relations', $data);
            }
        }

        $reminder = null;
        if ($salesInvoiceMeta) {
            $reminder = [
                'reminder_status' => $salesInvoiceMeta->value,
                'date' => dateFormat($salesInvoiceMeta->updated_at, 'Y-m-d')
            ];
        }
        return $reminder;
    }

    /**
     * Get invoice reminders
     */
    public function listReminders()
    {
        $query = \Querying::for(SalesInvoiceReminder::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('id')
            ->make()
            ->getQuery();

        $query->where('tenant_id', currentTenant('id'));

        return $query;
    }

    /**
     * Set invoice status as paid
     *
     * @param SalesInvoice $salesInvoice
     * @return mixed
     * @throws BindingResolutionException
     */
    public function paid(SalesInvoice $salesInvoice)
    {
        $salesInvoiceMeta = $salesInvoice->salesInvoiceMetas()->where('key', 'reminder_status')->first();
        $data = ['text' => Str::replaceArray('?', [$salesInvoice->invoice_no], 'Factuur ? is alsnog betaald.')];
        $this->noteService->create($salesInvoice->relation_id, 'relations', $data);
        $salesInvoice->invoice_status = 50;
        $salesInvoice->save();
        return $salesInvoiceMeta->delete();
    }

    public function sendInvoiceCheckMail(
        $userId,
        $billingRun,
        $invoiceLists,
        $invoiceCount,
        $subscriptionCount,
        $sumExcludingVat,
        $sumIncludingVat,
        $noPriceFoundMessages
    ) {
        $user = User::find($userId);
        Mail::to($user->username)
            ->send(new InvoiceCheck(
                $billingRun,
                $invoiceLists,
                $invoiceCount,
                $subscriptionCount,
                $sumExcludingVat,
                $sumIncludingVat,
                $noPriceFoundMessages
            ));
    }

    public function sendInvoiceFinalizeMail($userId, $billingRunId, $pdfFileCount, $invoiceCount, $startedAt, $endedAt)
    {
        $user = User::find($userId);
        Mail::to($user->username)
            ->send(new InvoiceFinalize(
                $billingRunId,
                $pdfFileCount,
                $invoiceCount,
                $startedAt->copy()->format('Y-m-d'),
                $startedAt->diff($endedAt)->format('%H:%i:%s.%f')
            ));
    }

    /**
     * Create invoice mail queue
     *
     * @param mixed $queueJobId
     * @param mixed $billingRunid
     * @param array $params
     * @param bool $isSuccess
     */
    public function sendGenerateInvoiceErrorMail($userId, $billingRunId, $params = [], $isSuccess = false)
    {
        $user = User::find($userId);
        $message = (new GenerateInvoiceQueueMail(
            $params,
            $billingRunId,
            $isSuccess
        ));

        if (filter_var($user->username, FILTER_VALIDATE_EMAIL)) {
            Mail::to($user->username)->queue($message);
        }
    }

    private function ValidateSubscriptionForInvoice(Subscription $subscription, $billRunId)
    {
        $isValid = true;

        //Subscription MUST have a start date
        if (empty($subscription->subscription_start)) {
            Logging::error(
                'INVOICING (NO subscription_start)',
                [
                    'subscription_id' => $subscription->id,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            $isValid = false;
        }

        //Subscription MUST be Ongoing
        if (!$subscription->statusSubscription->id == 1) {
            Logging::error(
                'INVOICING (Status is not ONGOING)',
                [
                    'subscription_id' => $subscription->id,
                    'subscription_status' => $subscription->statusSubscription->label,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            $isValid = false;
        }

        //Subscription MUST have a person and address to bill to
        if (is_null($subscription->getPersonBillingAttribute()) || is_null($subscription->getAddressBillingAttribute())) {
            Logging::error(
                'INVOICING (NO BILLING PERSON/ADDRESS)',
                [
                    'subscription_id' => $subscription->id,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * @param SubscriptionLine $line
     * @param $price --the price must exist for the line
     * @param $lastStop --the last known automated invoice date of the line
     * @param $startDateLine --the date on which the line will be validated
     * @param $startDateInvoice --the date on which the invoice will be validated
     * @param $endDateLine --the date until which the line will be validated
     * @param $types --contains the list of types (NRC, MRC, Deposit)
     * @param $billingStartDate --any processing for invoices should be after this date (tenant->invoice_start_calculation)
     * @param $billRunId --ID of the Billing Run
     * @return bool
     * @throws NoPriceFoundException
     */
    private function ValidateSubscriptionLineForInvoice(
        SubscriptionLine $line,
        $price,
        $lastStop,
        $lineStartDate,
        $invoiceStartDate,
        $lineEndDate,
        $types,
        $billingStartDate,
        $billRunId
    ): bool {
        $isValid = true;

        $nrcCode = $types->where('line_type', 'NRC')->first()->id;
        $depositCode = $types->where('line_type', 'Deposit')->first()->id;

        $hasInvoiceLine = SalesInvoiceLine::where('subscription_line_id', $line->id)->count() > 0;
        $isInvoiceOnce =
            $line->subscription_line_type == $nrcCode ||
            $line->subscription_line_type == $depositCode;

        //endDate of recurring subscriptionLine MUST be before or equal to Billing Start of Tenant/Subscription
        if (
            !$isInvoiceOnce &&
            $billingStartDate > $lineEndDate
        ) {
            $subscription = $line->subscription;
            Logging::information(
                'INVOICING (BillingStartDate (' . $billingStartDate->format('Y-m-d') . ') after StopDate of recurring line  (' . $lineEndDate->format('Y-m-d') . '))',
                [
                    'subscription_line_id' => $line->id,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            $isValid = false;
        }

        //startDate of non-recurring subscriptionLine MUST be after Billing Start of Tenant/Subscription
        if (
            $isInvoiceOnce &&
            $billingStartDate > $lineStartDate
        ) {
            $subscription = $line->subscription;
            Logging::information(
                'INVOICING (BillingStartDate(' . $billingStartDate->format('Y-m-d') . ') after StartDate of non-recurring line  (' . $lineStartDate->format('Y-m-d') . '))',
                [
                    'subscription_line_id' => $line->id,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            $isValid = false;
        }

        // If this is the first invoice of this subscriptionLine:
        // startDate of subscriptionLine MUST be before startDate of Invoice IF
        // the line has a product with an api
        if (
            !$hasInvoiceLine &&
            $lineStartDate > $invoiceStartDate &&
            ($line->product && $line->product->backend_api)
        ) {
            $subscription = $line->subscription;
            Logging::information(
                'INVOICING (StartDate of line with API (' . $lineStartDate->format('Y-m-d') . ') after StartDate Invoice (' . $invoiceStartDate->format('Y-m-d') . '))',
                [
                    'subscription_line_id' => $line->id,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            $isValid = false;
        }

        // Cannot attempt a credit if there has been no invoice line
        if (
            !$hasInvoiceLine &&
            $lineEndDate < $lineStartDate
        ) {
            $subscription = $line->subscription;
            Logging::information(
                'INVOICING (StartDate of line (' . $lineStartDate->format('Y-m-d') . ') before EndDate of line (' . $lineEndDate->format('Y-m-d') . ') no credit)',
                [
                    'subscription_line_id' => $line->id,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            $isValid = false;
        }

        //If the endDate of last invoice and current invoice are the same, stop
        if (!$isInvoiceOnce && $lastStop && $lastStop == $lineEndDate) {
            $subscription = $line->subscription;
            Logging::information(
                'INVOICING (Already Invoiced)',
                [
                    'subscription_line_id' => $line->id,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            $isValid = false;
        }

        //Non-Recurring costs and Deposit lines are only invoiced once
        if ($isInvoiceOnce && $hasInvoiceLine) {
            $subscription = $line->subscription;
            Logging::information(
                'INVOICING (Already billed NRC/Deposit)',
                [
                    'subscription_line_id' => $line->id,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            $isValid = false;
        }

        //SubscriptionLine MUST have at least one price
        if (!$price || ($price->fixed_price === null && $price->margin === null)) {
            $subscription = $line->subscription;
            Logging::error(
                'INVOICING (No valid subscription line price)',
                [
                    'subscription_line_id' => $line->id,
                    'relation_id' => $subscription->relation_id,
                    'billing_run_id' => $billRunId
                ],
                17,
                0,
                $subscription->relation->tenant_id,
                'subscription',
                $subscription->id
            );
            throw new NoPriceFoundException('Invoicing - No valid price; ' .
                'relation: ' . $subscription->relation_id . ' subscription: ' . $subscription->id . ' subscription_line: ' . $line->id);
        }
        return $isValid;
    }

    /**
     *
     * @param mixed $inputRelationId
     * @param mixed $inputInvoiceAddressId
     * @param mixed $inputInvoicePersonId
     * @param mixed $inputShippingAddressId
     * @param mixed $inputShippingPersonId
     * @return array
     */
    public static function validateShippingInvoiceAddressPerson(
        $inputRelationId,
        $inputInvoiceAddressId,
        $inputInvoicePersonId,
        $inputShippingAddressId,
        $inputShippingPersonId
    ) {
        $proceed = false;
        $errorMessage = "";
        $invoiceAddressIds = $shippingAddressIds = $relationPersonIds = $errors = [];
        $relation = Relation::find($inputRelationId);

        if ($relation) {
            $invoiceAddressIds = $relation->billingAddresses()
                ->pluck("id")
                ->toArray();
            $relationPersonIds = $relation->persons()
                ->pluck("id")
                ->toArray();
            $shippingAddressIds = $relation->provisioningAddresses()
                ->pluck("id")
                ->toArray();

            $isInputAddressIdExisting = in_array(
                $inputInvoiceAddressId,
                $invoiceAddressIds
            );
            if (!$isInputAddressIdExisting) {
                $errors[] = "billing address";
            }

            $isInputInvoicePersonIdExisting = in_array(
                $inputInvoicePersonId,
                $relationPersonIds
            );
            if (!$isInputInvoicePersonIdExisting) {
                $errors[] = "billing person";
            }

            $isInputShippingAddressIdExisting = in_array(
                $inputShippingAddressId,
                $shippingAddressIds
            );
            if (!$isInputShippingAddressIdExisting) {
                $errors[] = "shipping address";
            }

            $isInputShippingPersonIdExisting = in_array(
                $inputShippingPersonId,
                $relationPersonIds
            );
            if (!$isInputShippingPersonIdExisting) {
                $errors[] = "shipping person";
            }

            $proceed = $isInputAddressIdExisting && $isInputInvoicePersonIdExisting &&
                $isInputShippingAddressIdExisting && $isInputShippingPersonIdExisting;
            if (!$proceed) {
                $errorMessage = "Invalid " . join(", ", $errors);
                $errorMessage .= " for customer number `{$relation->customer_number}`.";
            }
        }

        return compact("proceed", "errorMessage");
    }

    private function validateSalesInvoice(SalesInvoice $invoice)
    {
        $result = [
            'valid' => true,
            'errors' => []
        ];

        $price = 0.0;
        $vat = 0.0;

        $pc = $invoice->paymentCondition;
        if (!$pc) {
            $result['valid'] = false;
            $result['errors'][] = 'PaymentCondition is not set.';
        } elseif (!$invoice->due_date->diffInDays($invoice->date) == $pc->net_days) {
            $result['valid'] = false;
            $result['errors'][] = 'PaymentCondition ' . $pc->net_days . ' net_days does not equal difference in invoice date - due_date. (' . dateFormat($invoice->date) . ')';
        }

        foreach ($invoice->salesInvoiceLines as $line) {
            $price += $line->price;
            $vat += $line->price_vat;
            if (!isset($line->product_id)) {
                $result['valid'] = false;
                $result['errors'][] = 'SalesInvoiceLine ' . $line->id . ' does not have a Product';
            }

            $tenantProduct = $line->product->tenantProducts()->where('tenant_id', $invoice->relation->tenant->id)->first();
            if (!isset($tenantProduct) && !isset($tenantProduct->vatcode)) {
                $result['valid'] = false;
                $result['errors'][] = 'Product ' . $line->product->id . ' does not have a tenantProduct for this invoice. (' . dateFormat($invoice->relation->tenant->id) . ')';
            } else {
                $vatcode = $tenantProduct->vatcode;
                if ($vatcode->active_from > $invoice->date && ($vatcode->active_to && $vatcode->active_to < $invoice->date)) {
                    $result['valid'] = false;
                    $result['errors'][] = 'VatCode ' . $vatcode->id . ' is not active at the date of invoice. (' . dateFormat($invoice->date) . ')';
                }
            }
        }

        if (round($price, 2, PHP_ROUND_HALF_DOWN) != round($invoice->price, 2, PHP_ROUND_HALF_DOWN)) {
            $result['valid'] = false;
            $result['errors'][] = 'Price of invoice does not equal all line prices. (' . $price . ' != ' . $invoice->price . ')';
        }

        if (round($vat, 2, PHP_ROUND_HALF_DOWN) != round($invoice->price_vat, 2, PHP_ROUND_HALF_DOWN)) {
            $result['valid'] = false;
            $result['errors'][] = 'VatPrice of invoice does not equal all line vatprices. (' . $vat . ' != ' . $invoice->price_vat . ')';
        }

        return $result;
    }

    /**
     * Get dashboard sales_invoices summary data
     *
     * @return SalesInvoice|\Illuminate\Database\Query\Builder
     */
    public function dashboardInvoicesSummary()
    {
        // Filter handling
        $filterParam = request()->get('filter');
        $whereParams = [];
        $groupByParam = 'day(`date`)';
        $filters = explode(',', $filterParam);
        foreach ($filters as $filter) {
            preg_match('/[><=%]/', $filter, $operatorMatch);
            if (count($operatorMatch)) {
                $explodedFilter = explode($operatorMatch[0], $filter);
                switch ($explodedFilter[0]) {
                    case 'tenant_id':
                        $whereParams[] = "`$explodedFilter[0]` $operatorMatch[0] $explodedFilter[1]";
                        break;
                    case 'date':
                        $whereParams[] = "`$explodedFilter[0]` $operatorMatch[0] '$explodedFilter[1]'";
                        break;
                    case 'period':
                        if (in_array($explodedFilter[1], ['day', 'week', 'month', 'quarter', 'year'])) {
                            $groupByParam = "$explodedFilter[1](`date`)";
                            break;
                        }
                        return null;
                    default:
                        return null;
                }
            }
        }
        $query = SalesInvoice::selectRaw("`tenant_id`, min(`date`) `date`, count(*) `count`, sum(price) `price_exc_vat`, sum(price_vat) `price_vat`, sum(price_total) `price_inc_vat`");
        if (count($whereParams)) {
            $query->whereRaw(implode(" and ", $whereParams));
        }
        return  $query->groupByRaw($groupByParam)->orderByRaw("min(`date`)");
    }
}
