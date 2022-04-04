<?php

namespace App\Services;

use App\DataViewModels\M7SubscriptionLine;
use App\DataViewModels\PaymentReport;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\PaymentInvoiceResource;
use App\Http\Resources\PaymentListResource;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Relation;
use App\Models\SalesInvoice;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PaymentService
{
    protected $statusService;

    /**
     * Service class constructor
     */
    public function __construct()
    {
        $this->statusService = new StatusService();
    }

    /**
     * Get payment list
     *
     * @param mixed|null $relation
     * @return BaseResourceCollection
     * @throws BindingResolutionException
     */
    public function list($tenantId = null)
    {
        if (!$tenantId) {
            $tenantId = currentTenant('id');
        }
        $date = request()->query('date', '');
        $accountHolder = request()->query('account_holder', '');
        $amount = request()->query('amount', '');
        $iban = request()->query('iban', '');
        $paymentType = request()->query('payment_type', '');
        $descr = request()->query('descr', '');

        $query = Payment::whereNull('relation_id')
            ->where('status_id', 0);

        if (!blank($date)) {
            $query = $query->where('date', '=', Carbon::createFromFormat("Y-m-d", $date)->format("Y-m-d"));
        }

        if (!blank($amount)) {
            $query->where('amount', '=', intval($amount));
        }

        if (!blank($iban)) {
            $query->where('account_iban', 'LIKE', '%' . $iban . '%');
        }

        if (!blank($paymentType)) {
            $query->where('type', '=', $paymentType);
        }

        if (!blank($accountHolder)) {
            $query->where('account_name', 'LIKE', '%' . $accountHolder . '%');
        }

        if (!blank($descr)) {
            $query->where('descr', 'LIKE', '%' . $descr . '%');
        }

        // Sorting
        $sort = request()->query('sort', 'date');
        $sequence = Str::contains($sort, ['-']) ? 'DESC' : 'ASC';
        $columnName = str_replace('-', '', $sort);

        switch ($columnName) {
            case 'account_holder':
                $query->orderBy('account_name', $sequence);
                break;
            case 'account_iban':
                $query->orderBy('account_iban', $sequence);
                break;
            case 'payment_type':
                $query->orderBy('type', $sequence);
                break;
            default:
                $query->orderBy($columnName, $sequence);
                break;
        }

        // Pagination
        $limit = request()->query('offset', 10);
        $payments = $query->paginate($limit);

        return PaymentListResource::collection($payments);
    }

    /**
     * Get payment list
     *
     * @param mixed|null $relation
     * @return BaseResourceCollection
     * @throws BindingResolutionException
     */
    public function listRelationPayments($relation = null)
    {
        if ($relation) {
            $paymentQuery = Payment::where('relation_id', $relation);
        } else {
            $paymentQuery = Payment::all();
        }

        $paymentQuery->orderBy('date', 'DESC');

        // search filter
        $paymentQuery = $this->handleSearchFilters(
            $paymentQuery,
            request()->query("filter", [])
        );

        // sorting
        $paymentQuery = $this->handleSorting(
            $paymentQuery,
            request()->query('sort', '-date')
        );

        return $paymentQuery;
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
                case 'invoice_no':
                    $columnName = 'sales_invoice_id';
                    break;
            }

            $modelQuery->orderBy($columnName, $sequence);
        }
        return $modelQuery;
    }
    /**
     * Show payment data
     *
     * @param mixed $id
     * @param string $message
     * @param bool $code
     * @return PaymentResource
     */
    public function show($id, $message = '', $code = true)
    {
        return new PaymentResource(
            Payment::find($id),
            $message,
            $code
        );
    }

    /**
     * Get invoices with no payment
     *
     * @param mixed $relation
     * @return BaseResourceCollection
     * @throws BindingResolutionException
     */
    public function invoicesNoPayment($relation)
    {
        $openStatusId = $this->statusService->getStatusId('invoice', 'Open');
        $paymentInvoiceIds = Payment::where('relation_id', $relation)
            ->pluck('sales_invoice_id')
            ->toArray();
        $invoicesQuery = SalesInvoice::where([
            ['relation_id', '=', $relation],
            ['invoice_status', '=', $openStatusId]
        ]);
        if (count($paymentInvoiceIds)) {
            $invoicesQuery->whereNotIn('id', array_unique(array_filter($paymentInvoiceIds)));
        }
        $invoicesQuery->orderBy('date', 'DESC');
        addToLaravelLog("query", [
            'sql' => $invoicesQuery->toSql(),
            'paid_invoice_ids' => $paymentInvoiceIds,
            'relation_id' => $relation,
            'invoice_status' => $openStatusId
        ]);

        // search filter
        $invoicesQuery = $this->handleSearchFilters(
            $invoicesQuery,
            request()->query("filter", [])
        );

        // sorting
        $invoicesQuery = $this->handleSorting(
            $invoicesQuery,
            request()->query('sort', '-date')
        );

        // pagination (page & limit)
        $limit = request()->query('offset', 10);
        $invoicesQuery = $invoicesQuery->paginate($limit);

        // JsonResource implementation
        $invoicesQuery->transform(function (SalesInvoice $invoice) {
            return (new PaymentInvoiceResource(
                $invoice,
                'Sales invoice(s) retrieved successfully.',
                true,
                true
            ));
        });

        return new BaseResourceCollection($invoicesQuery);
    }

    /**
     * Update payment, set sales_invoice_id and status
     * @param mixed $paymentId
     * @param mixed $salesInvoiceId
     * @return mixed
     */
    public function setPaymentInvoice($paymentId, $salesInvoiceId)
    {
        $payment = Payment::find($paymentId);
        $salesInvoice = SalesInvoice::find($salesInvoiceId);
        if (!blank($payment) && !blank($salesInvoiceId)) {
            $payment->update([
                'sales_invoice_id' => $salesInvoiceId,
                'status_id' => 100 // processed
            ]);
            return $payment;
        }
        return null;
    }

    /**
     * Returns all direct debit payments that were reversed since the last time it was reported.
     *
     * @param Carbon $lastCheckedDate   The date the last check was done
     */
    public function getDirectDebitReversals($lastCheckedDate)
    {
        $rows = \Querying::for(PaymentReport::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->where('created_at', '>', $lastCheckedDate->format('Y-m-d H:i:s'))
            ->where('type', 'direct_debit_reversal')
            ->orderBy('created_at', 'ASC')
            ->get();

        // Split by tenant
        $list = [];
        $paidCounter = 0;
        foreach ($rows as $row) {
            if (!isset($list[$row->tenant_id])) {
                $obj = new \stdClass();
                $obj->name = $row->tenant_name;
                $obj->payments = [];
                $obj->count = 0;
                $obj->total = 0;
                $list[$row->tenant_id] = $obj;
            }

            $customerNumber = '';
            $invoiceNumber = '';

            // Find the customer and invoice for this payment
            // Assume this format for the description: 59452/FF1949314 September 2020 Fiber NL
            $customerNumberMatches = [];
            preg_match('/^[A-Z0-9]+(?=\/)/', $row->descr, $customerNumberMatches);
            $customerNumber = count($customerNumberMatches) ? $customerNumberMatches[0] : null;
            if (!empty($customerNumber)) {
                $row->customer_number = $customerNumber;
                $invoiceNumberMatches = [];
                preg_match('/(?<=\/)[A-Z0-9]+/', $row->descr, $invoiceNumberMatches);
                $invoiceNumber = count($invoiceNumberMatches) ? $invoiceNumberMatches[0] : null;
            }

            $row->relation_link = '';
            $row->invoice_link = '';
            //$row->status = 'Open';
            if (!empty($invoiceNumber)) {
                $row->invoice_number = $invoiceNumber;
                $relationId = $this->getRelationId($invoiceNumber);
                $invoiceId = $this->getInvoiceId($invoiceNumber);
                $row->invoice_link = config('app.front_url') . '/#/relations/' . $relationId . '/' . $invoiceId . '/invoices';
                $row->relation_link = config('app.front_url') . '/#/relations/' . $relationId . '/details';
            }

            $amount = $row->amount;
            $row->amount = number_format($row->amount, 2, ',', '.');
            $row->fmtd_date = Carbon::parse($row->date)->format('d-m-Y');
            $list[$row->tenant_id]->payments[] = $row;
            $list[$row->tenant_id]->count++;
            $list[$row->tenant_id]->total += $amount;
        }

        return $list;
    }

    /**
     * Returns all manual payments that were reversed since the last time it was reported.
     *
     * @param Carbon $lastCheckedDate   The date the last check was done
     */
    public function getManualPayments($lastCheckedDate)
    {
        $rows = \Querying::for(PaymentReport::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->where('created_at', '>', $lastCheckedDate->format('Y-m-d H:i:s'))
            ->where('type', 'credit_transfer')
            ->orderBy('created_at', 'ASC')
            ->get();

        // Split by tenant
        $list = [];
        foreach ($rows as $row) {
            if (!isset($list[$row->tenant_id])) {
                $obj = new \stdClass();
                $obj->name = $row->tenant_name;
                $obj->payments = [];
                $obj->count = 0;
                $obj->total = 0;
                $list[$row->tenant_id] = $obj;
            }

            // Find the invoice for this payment
            $invoiceNumbers = [];
            $customerNumber = '';
            $customerId = '';
            $row->invoice_link = '';
            $row->relation_link = '';

            if (!empty($row->descr)) {
                // 1: Try finding regular invoice number (FF/FS...)
                preg_match_all('/(FF|FS)[0-9]+/i', $row->descr, $invoiceNumbers);
                $invoiceNumbers = count($invoiceNumbers) ? array_unique(array_filter($invoiceNumbers[0])) : [];
                if (!empty($invoiceNumbers)) {
                    $invoiceIds = [];
                    $invoiceUrls = [];
                    foreach ($invoiceNumbers as $invoiceNumber) {
                        $relationId = $this->getRelationId($invoiceNumber);
                        $invoiceId = $this->getInvoiceId($invoiceNumber);
                        if ($relationId && $invoiceId) {
                            $invoiceUrls[] = config('app.front_url') . '/#/relations/' . $relationId . '/' . $invoiceId . '/invoices';
                            if (empty($row->relation_id)) {
                                $row->relation_id = $relationId;
                            }
                            if (empty($row->customerNumber)) {
                                $row->customerNumber = $this->getCustomerNumberByRelationId($relationId);
                            }
                        }
                        $invoiceIds[$this->getInvoiceId($invoiceNumber)] = $invoiceNumber;
                    }
                    $invoiceNumbers = $invoiceIds;
                    $invoiceUrls = array_unique(array_filter($invoiceUrls));
                    $row->invoice_link = implode(", ", $invoiceUrls);
                }

                // 2: Try finding invoice by matching on customer number, then matching invoices of customer on amount (only when there is a single invoice with the amount)
                if (empty($invoiceNumbers)) {
                    $customerNumberMatches = [];
                    preg_match('/FP[0-9]+/i', $row->descr, $customerNumberMatches);
                    $customerNumber = count($customerNumberMatches) ? $customerNumberMatches[0] : null;
                    $customerId = $this->getCustomerIdByCustomerNumber($customerNumber);

                    if (!empty($customerNumber) && !empty($customerId)) {
                        $row->customerNumber = $customerNumber;
                        $row->relation_id = $customerId;
                        $invoiceNumber = $this->getInvoiceNumberUsingCustomerNumberAndAmount($customerNumber, $row->amount);
                        $invoiceNumbers[$this->getInvoiceId($invoiceNumber)] = $invoiceNumber;
                    }
                }

                // 3: Try sucking spaces out and matching on invoice number
                if (empty($invoiceNumbers)) {
                    $denseDescr = str_replace(' ', '', $row->descr);
                    preg_match_all('/(FF|FS)[0-9]+/i', $denseDescr, $invoiceNumbers);
                    $invoiceNumbers = count($invoiceNumbers) ? $invoiceNumbers[0] : [];
                    $invoiceNumbers = $this->checkInvoiceNumbersExist($invoiceNumbers);
                }

                // 4: Try sucking spaces out and matching on customer number
                if (empty($invoiceNumbers)) {
                    $denseDescr = str_replace(' ', '', $row->descr);
                    $customerNumberMatches = [];
                    preg_match('/FP[0-9]+/i', $denseDescr, $customerNumberMatches);
                    $customerNumber = count($customerNumberMatches) ? $customerNumberMatches[0] : null;
                    $customerId = $this->getCustomerIdByCustomerNumber($customerNumber);
                    if (!empty($customerNumber) && !empty($customerId)) {
                        $row->customerNumber = $customerNumber;
                        $row->relation_id = $customerId;
                        $invoiceNumber = $this->getInvoiceNumberUsingCustomerNumberAndAmount($customerNumber, $row->amount);
                        if (!empty($invoiceNumber)) {
                            $invoiceNumbers[$this->getInvoiceId($invoiceNumber)] = $invoiceNumber;
                        }
                    }
                }

                // 5: Try finding invoice number-like numbers + check them
                if (empty($invoiceNumbers)) {
                    preg_match_all('/\d{6,}/', $row->descr, $invoiceNumbers);
                    $invoiceNumbers = count($invoiceNumbers) ? $invoiceNumbers[0] : [];
                    $invoiceNumbers = array_unique($invoiceNumbers);
                    if (!empty($invoiceNumbers)) {
                        $invoiceNumbers = $this->checkInvoiceNumbersExist($invoiceNumbers);
                    }
                }
            }

            $amount = $row->amount;
            $row->amount = number_format($row->amount, 2, ',', '.');
            $row->fmtd_date = Carbon::parse($row->date)->format('d-m-Y');
            $row->invoiceNumbers = $invoiceNumbers;
            $list[$row->tenant_id]->payments[] = $row;
            $list[$row->tenant_id]->count++;
            $list[$row->tenant_id]->total += $amount;
        }

        return $list;
    }

    private function getInvoiceId($invoiceNumber)
    {
        return DB::table('sales_invoices')
            ->where('invoice_no', '=', $invoiceNumber)
            ->pluck('id')
            ->first();
    }

    private function getRelationId($invoiceNumber)
    {
        return DB::table('sales_invoices')
            ->where('invoice_no', '=', $invoiceNumber)
            ->pluck('relation_id')
            ->first();
    }

    public function generateCsvFileReversals($filename, $data)
    {
        $filename = storage_path("app/private/reports/$filename");
        if (!File::isDirectory(File::dirname($filename))) {
            File::makeDirectory(File::dirname($filename), 0775, true, true);
        }


        $columns = ['Datum', 'Klant', 'Klant nr', 'Factuur nr', 'Bedrag', 'Reden', 'Omschrijving', 'Factuur link'];

        $fh = fopen($filename, 'a');
        fputcsv($fh, $columns, ';');

        foreach ($data as $row) {
            $values = [
                $row->fmtd_date,
                $row->account_name,
                (isset($row->customer_number) ? $row->customer_number : ''),
                (isset($row->invoice_number) ? $row->invoice_number : ''),
                $row->amount,
                $row->return_code . ' / ' . $row->return_reason,
                $row->descr,
                (isset($row->invoice_link) ? $row->invoice_link : ''),
            ];
            fputcsv($fh, $values, ';');
        }
        fclose($fh);

        return $filename;
    }

    public function generateCsvFileManualPayments($filename, $data)
    {
        $filename = storage_path("app/private/reports/$filename") ;
        if (!File::isDirectory(File::dirname($filename))) {
            File::makeDirectory(File::dirname($filename), 0775, true, true);
        }

        $columns = ['Datum', 'Klant', 'Klant nr', 'Factuur nr', 'Bedrag', 'IBAN', 'Omschrijving', 'Factuur link'];

        $fh = fopen($filename, 'a');
        fputcsv($fh, $columns, ';');
        info(json_encode(file_exists($filename)));

        foreach ($data as $row) {
            $values = [
                $row->date->format("d-m-Y"),
                $row->account_name,
                (isset($row->customerNumber) ? $row->customerNumber : ''),
                (isset($row->invoiceNumbers) ? implode(', ', $row->invoiceNumbers) : ''),
                $row->amount,
                $row->descr,
                $row->iban,
                (isset($row->invoice_link) ? $row->invoice_link : ''),
            ];
            fputcsv($fh, $values, ';');
        }
        fclose($fh);

        return $filename;
    }

    public function getPaymentsByIban($iban, $type = '', $amount = '')
    {
        $query = \DB::table('payments');
        //->where('account_iban', '=', $iban);
        if (!empty($type)) {
            $query->where('type', '=', $type);
        }
        if (!empty($amount)) {
            $query->where('amount', '=', $amount);
        }
        return $query->get()->toArray();
    }

    public function getRelatedPayments($invoiceNumber, $amount)
    {
        return DB::table('payments')
            ->where('descr', 'LIKE', '%' . $invoiceNumber . '%')
            ->where('type', '=', 'credit_transfer')
            ->where('amount', '=', $amount)
            ->get()->toArray();
    }

    public function checkInvoiceNumberExists($invoiceNumber)
    {
        return DB::table('sales_invoices')
            ->where('invoice_no', '=', $invoiceNumber)
            ->exists();
    }

    /**
     * Returns the invoice number if a relation with the given customer number has a single invoice with the given amount.
     * @param $customerNumber
     * @param $amount
     */
    public function getInvoiceNumberUsingCustomerNumberAndAmount($customerNumber, $amount)
    {
        $res = DB::table('sales_invoices')
            ->select('sales_invoices.id as id')
            ->join('relations', 'relations.id', '=', 'sales_invoices.relation_id')
            ->where('relations.customer_number', '=', $customerNumber)
            ->whereRaw('ROUND(sales_invoices.price_total, 2) = ' . round($amount, 2))
            ->get()
            ->toArray();
        if (count($res) === 1) {
            return $res[0]->id;
        }
        return null;
    }

    /**
     * Checks if the given invoice numbers exist and returns a list of those that exist.
     *
     * @param $list
     * @return array
     */
    private function checkInvoiceNumbersExist($list)
    {
        $invoiceList = [];
        foreach ($list as $invoiceNumber) {
            if ($this->checkInvoiceNumberExists($invoiceNumber)) {
                $invoiceList[$this->getInvoiceId($invoiceNumber)] = $invoiceNumber;
            }
        }
        return $invoiceList;
    }

    private function checkCustomerNumberExist($customerNumber)
    {
        return DB::table('relations')
            ->where('customer_number', '=', $customerNumber)
            ->whereIn('tenant_id', [7, 8])
            ->exists();
    }

    private function getCustomerIdByCustomerNumber($customerNumber)
    {
        return DB::table('relations')
            ->select('id')
            ->where('customer_number', '=', $customerNumber)
            ->whereIn('tenant_id', [7, 8])
            ->pluck('id')
            ->first();
    }

    private function getCustomerNumberByRelationId($relationId)
    {
        return DB::table('relations')
            ->select('customer_number')
            ->where('id', '=', $relationId)
            ->pluck('customer_number')
            ->first();
    }
}
