<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SalesInvoiceReminders;
use App\Http\Resources\SalesInvoiceResource;
use App\DataViewModels\SalesInvoiceReminder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Logging;
use App\Models\SalesInvoice;
use App\Models\Subscription;
use App\Http\Requests\SalesInvoiceApiRequest;
use App\Services\SalesInvoiceService;
use App\Services\CdrUsageCostService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SalesInvoiceController extends BaseController
{
    protected $service;
    protected $cdrUsageCostService;

    public function __construct()
    {
        $this->service = new SalesInvoiceService();
        $this->cdrUsageCostService = new CdrUsageCostService();
    }

    /**
     * Return a paginated list of sales invoices.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $relationId = request('relation_id');
        return $this->sendPaginateOrResult(
            $this->service->list($relationId),
            'Sales invoice listing retrieved successfully',
            function (SalesInvoice $invoice) {
                return (new SalesInvoiceResource(
                    $invoice
                ));
            }
        );
    }

    /**
     * Store a newly created sales invoice
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SalesInvoiceApiRequest $request)
    {
        $data = jsonRecode($request->all());
        Logging::information('Create Sales Invoice', $data, 1, 1);
        $query = $this->service->create($data);
        return $this->sendSingleResult($query, 'Sales invoice created successfully.');
    }

    /**
     * Display the specified sales invoice
     *
     * @param \App\SalesInvoice $salesInvoice
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(SalesInvoice $salesInvoice)
    {
        $this->authorize('view', $salesInvoice);
        return $this->sendResult(
            $this->service->show($salesInvoice->id),
            'Sales invoice retrieved successfully.'
        );
    }

    /**
     * Update the specified sales invoice.
     *
     * @param SalesInvoice $salesInvoice
     * @param \App\Http\Requests\SalesInvoiceApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(SalesInvoice $salesInvoice, SalesInvoiceApiRequest $request)
    {
        $this->authorize('update', $salesInvoice);

        $data = jsonRecode($request->all());
        $query = $this->service->update(
            $data,
            $salesInvoice
        );

        if (!empty($query["data"])) {
            return $this->sendResult(
                $query["data"],
                'Sales invoice updated successfully.'
            );
        }

        $errorMessage =  "Error updating Sales Invoice";
        if (is_array($query) && array_key_exists("errorMessage", $query)) {
            $errorMessage = $query["errorMessage"];
        }

        return $this->sendError(
            $errorMessage,
            [
                "id" => $salesInvoice->id,
                "invoice_status" => $salesInvoice->status->status
            ],
            500
        );
    }

    /**
     * Remove the specified sales invoice
     *
     * @param SalesInvoice $salesInvoice
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SalesInvoice $salesInvoice)
    {
        $this->authorize('delete', $salesInvoice);
        if ($salesInvoice->is_updatable) {
            $query = $this->service->delete($salesInvoice);
            Logging::information('Delete Sales Invoice', $salesInvoice, 1, 1);
            return $this->sendResults($query, 'Sales invoice deleted successfully.');
        }
        Logging::error('Error deleting Sales Invoice', $salesInvoice, 1, 1);
        return $this->sendError(
            'You may only delete invoices which are Concept.',
            [
                "id" => $salesInvoice->id,
                "invoice_status" => $salesInvoice->status->status
            ],
            422
        );
    }

    /**
     * Return a listing of the customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary()
    {
        return $this->sendPaginateOrResult(
            $this->service->summary(),
            'SalesInvoice summary retrieved successfully.'
        );
    }

    /**
     * Send invoice email
     *
     * @param integer $invoiceId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendInvoiceEmail($invoiceId)
    {
        $response = $this->service->sendEmail($invoiceId);
        if ($response['success']) {
            return $this->sendResult([], 'Email sent.');
        } else {
            return $this->sendError($response['message'], [], 500);
        }
    }

    /**
     * Get invoice record counts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {
        $query = $this->service->count();
        return $this->sendResult($query, 'Invoice record counts retrieved successfully.');
    }

    /**
     * Return a paginated list of sales invoice lines.
     *
     * @param SalesInvoice $salesInvoice
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function salesInvoiceLines(SalesInvoice $salesInvoice, $queryOnly = true)
    {
        $this->authorize('view', $salesInvoice);
        return $this->sendResponse($this->service->invoiceLines($salesInvoice));
        //set cache key
        $cacheKey = 'SalesInvoice_' . $salesInvoice->id;

        //check if cache already exists. if not, generate a new cache from database
        if (!Cache::store('database')->has($cacheKey)) {
            Cache::store('database')->forever($cacheKey, $this->service->invoiceLines($salesInvoice));
        }

        return $this->sendResult(
            Cache::store('database')->get($cacheKey),
            'Sales invoice lines retrieved successfully'
        );
    }

    /**
     * Create credit invoice
     *
     * @param \App\Models\SalesInvoice $salesInvoice
     */
    public function createCreditInvoice(SalesInvoice $salesInvoice)
    {
        if ($salesInvoice->invoice_status > 0) {
            $query = $this->service->createCreditInvoice($salesInvoice);
            return $this->sendSingleResult($query, 'Sales invoice created successfully.');
        }
        Logging::error('Error creating Credit Sales Invoice', $salesInvoice, 1, 1);
        return $this->sendError(
            'Only updates are allowed on in concept invoices.',
            [
                "id" => $salesInvoice->id,
                "invoice_status" => $salesInvoice->status->status
            ],
            500
        );
    }

    /**
     * Create invoice from subscription
     *
     * @param \App\Models\SalesInvoice $salesInvoice
     */
    public function createSubscriptionInvoice($subscriptionId)
    {
        $subscription = Subscription::find($subscriptionId);
        if (!empty($subscription)) {
            $query = $this->service->createSalesInvoices($subscription, Carbon::today(), null);
            if (!empty($query)) {
                return $this->sendResult($query, 'Sales invoice created successfully.');
            }
            return $this->sendError(
                "Nothing to invoice.",
                [
                    "subscription_id" => $subscriptionId
                ],
                500
            );
        }
        return $this->sendError(
            "Error creating invoice for subscription_id={$subscriptionId}",
            [],
            500
        );
    }

    /**
     * Get invoice status
     *
     * @param SalesInvoice $salesInvoice
     * @param mixed $state
     * @return \Illuminate\Http\JsonResponse
     */
    public function invoiceState(SalesInvoice $salesInvoice, $state)
    {
        $salesInvoice->fresh();
        if ('all' == $state) {
            return $this->sendResult($salesInvoice, '');
        }
        return $this->sendResult($salesInvoice[$state], '');
    }

    /**
     * Send invoice reminder
     *
     * @param SalesInvoice $salesInvoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendReminder(SalesInvoice $salesInvoice)
    {
        if (!empty($salesInvoice->status) && strtolower($salesInvoice->status->status) !== 'paid') {
            $reminderResult = $this->service->sendReminder($salesInvoice);
            if (isset($reminderResult['success']) && !$reminderResult['success']) {
                return $this->sendError($reminderResult['message'], [], 500);
            }
            return $this->sendResult($reminderResult, '');
        } else {
            return $this->sendError('Invoice already paid.', [], 500);
        }
    }

    /**
     * Get cdr usage cost of an invoice
     *
     * @param SalesInvoice $salesInvoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function cdrUsageCosts(SalesInvoice $salesInvoice)
    {
        $cdrUsageCost = $this->cdrUsageCostService->getCdrUsageCosts($salesInvoice);
        return $this->sendResult($cdrUsageCost, '');
    }

    /**
     * Get cdr usage cost of an invoice
     *
     * @param SalesInvoice $salesInvoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function cdrUsageCostsPdf($salesInvoiceLineId)
    {
        $response = $this->cdrUsageCostService->getCdrUsageCostsPdf($salesInvoiceLineId);

        if ($response["file_exists"]) {
            return response()->file($response["file"]);
        }

        return $this->sendResponse("Invoice PDF not found.", []);
    }

    /**
     * Get invoice reminders
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reminders()
    {
        return $this->sendPaginateOrResult(
            $this->service->listReminders(),
            'Sales invoice listing retrieved successfully',
            function (SalesInvoiceReminder $salesMeta) {
                return (new SalesInvoiceReminders(
                    $salesMeta
                ));
            }
        );
    }

    /**
     * Return a listing of the customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remindersSummary()
    {
        return $this->sendPaginateOrResult(
            $this->service->listReminders(),
            'SalesInvoice summary retrieved successfully.'
        );
    }

    /**
     * Set invoice status as paid
     *
     * @param SalesInvoice $salesInvoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function paid(SalesInvoice $salesInvoice)
    {
        return $this->sendResponse($this->service->paid($salesInvoice), '');
    }

    /**
     * Get invoice gadget(s) (sub-menu(s))
     *
     * @param SalesInvoice $salesInvoice
     * @return \Illuminate\Http\JsonResponse
     */
    public function gadgets(SalesInvoice $salesInvoice)
    {
        return $this->sendResponse(
            $salesInvoice->gadgets,
            ''
        );
    }

    /**
     * List invoices for portal
     *
     * @param mixed $relationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function listPortalInvoices($relationId)
    {
        return $this->sendNewPaginate(
            $this->service->listPortalInvoices($relationId),
            'Sales invoice listing retrieved successfully'
        );
    }

    /**
     * Return a listing of the customer.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboardInvoicesSummary()
    {
        $response = $this->service->dashboardInvoicesSummary();
        if ($response) {
            return $this->sendPaginateOrResult(
                $response,
                'Dashboard sales invoice summary retrieved successfully.'
            );
        }
        return $this->sendError(
            'No sales invoice summary found.',
            ['filter' => request()->get('filter')],
            500
        );
    }
}
