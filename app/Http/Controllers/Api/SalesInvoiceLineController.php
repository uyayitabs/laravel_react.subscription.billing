<?php

namespace App\Http\Controllers\Api;

use App\Models\CdrUsageCost;
use Logging;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Http\Requests\SalesInvoiceLineApiRequest;
use App\Services\CdrUsageCostService;
use App\Services\SalesInvoiceService;
use App\Services\SalesInvoiceLineService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SalesInvoiceLineController extends BaseController
{
    protected $service;
    protected $salesInvoiceService;

    public function __construct()
    {
        $this->salesInvoiceService = new SalesInvoiceService();
        $this->service = new SalesInvoiceLineService();
    }

    /**
     * Return a paginated list of sales invoice lines.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = $this->service->list();
        return $this->sendPaginate($query, 'Sales invoice line listing retrieved successfully');
    }

    /**
     * Store a newly created sales invoice line
     *
     * @return \Illuminate\Http\Response
     */
    public function store(SalesInvoiceLineApiRequest $request)
    {
        $data = jsonRecode($request->all());
        $salesInvoice = SalesInvoice::find($data["sales_invoice_id"]);

        if ($salesInvoice->is_updatable) {
            $query = $this->service->create($data);

            $this->saveToCache($salesInvoice);
            Logging::information('Create Sales Invoice Line', $request, 1, 1);

            return $this->sendSingleResult($query, 'Sales invoice line created successfully.');
        }

        Logging::error('Error creating Sales Invoice Line', $salesInvoice, 1, 1);
        return $this->sendError(
            'Only updates are allowed on in concept invoices.',
            [
                "id" => $salesInvoice->id,
                "line_id" => null,
                "invoice_status" => $salesInvoice->status->status
            ],
            500
        );
    }

    /**
     * Return the specified sales invoice line
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(SalesInvoiceLine $salesInvoiceLine)
    {
        $query = $this->service->show($salesInvoiceLine->id);
        return $this->sendSingleResult($query, 'Sales invoice line retrieved successfully.');
    }

    /**
     * Update the specified sales invoice line.
     *
     * @param \App\Models\SalesInvoiceLine $salesInvoiceLine
     *
     * @return \Illuminate\Http\Response
     */
    public function update(SalesInvoiceLine $salesInvoiceLine, SalesInvoiceLineApiRequest $request)
    {
        $data = jsonRecode($request->all());
        $salesInvoice = $salesInvoiceLine->salesInvoice;
        if ($salesInvoice->is_updatable) {
            $query = $this->service->update($data, $salesInvoiceLine);
            $this->saveToCache($salesInvoice);
            return $this->sendSingleResult($query, 'Sales invoice line updated successfully.');
        }

        Logging::error('Error updating Sales Invoice Line', $salesInvoice, 1, 1);
        return $this->sendError(
            'Only updates are allowed on in concept invoices.',
            [
                "id" => $salesInvoice->id,
                "line_id" => $salesInvoiceLine->id,
                "invoice_status" => $salesInvoice->status->status
            ],
            500
        );
    }

    /**
     * Remove the specified sales invoice line
     *
     * @param \App\Models\SalesInvoiceLine $salesInvoiceLine
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalesInvoiceLine $salesInvoiceLine)
    {
        $salesInvoice = $salesInvoiceLine->salesInvoice;

        if ($salesInvoice->is_updatable) {
            $query = $this->service->delete($salesInvoiceLine);

            $this->saveToCache($salesInvoice);
            Logging::information('Delete Sales Invoice Line', $salesInvoiceLine, 1, 1);

            return $this->sendResponse($query, 'Sales invoice line deleted successfully.');
        }

        return $this->sendError(
            'Only updates are allowed on in concept invoices.',
            [
                "id" => $salesInvoice->id,
                "line_id" => $salesInvoiceLine->id,
                "invoice_status" => $salesInvoice->status->status
            ],
            500
        );
    }

    private function saveToCache(SalesInvoice $salesInvoice)
    {
        Cache::store('database')->forget('SalesInvoice_' . $salesInvoice->id);
        Cache::store('database')->forever(
            'SalesInvoice_' . $salesInvoice->id,
            $this->salesInvoiceService->invoiceLines($salesInvoice)
        );
    }

    public function gadgets(SalesInvoiceLine $salesInvoiceLine)
    {
        return $this->sendResponse($salesInvoiceLine->gadgets, '');
    }

    public function processGadget(SalesInvoiceLine $salesInvoiceLine, $gadgetType, $action)
    {
        $salesInvoice = $salesInvoiceLine->salesInvoice;
        $primaryPersonEmail = $salesInvoice->relation_primary_person_email;

        $responseErrorMessage = 'An error occurred while sending the call details';
        $responseData = [];
        $responseCode = 500;

        switch ($gadgetType) {
            case 'cdr':
                switch ($action) {
                    case 'SendEmail':
                        if (filter_var($primaryPersonEmail, FILTER_VALIDATE_EMAIL)) {
                            $cdrUsageCostService = new CdrUsageCostService();

                            $pdfFile = $salesInvoiceLine->call_summary_file_fullpath;
                            if (File::exists($pdfFile)) {
                                // Send cdr summary email
                                $responseData = $cdrUsageCostService->sendCdrSummaryEmail($salesInvoiceLine);
                                if ($responseData['success']) {
                                    $responseCode = 200;
                                }
                            } else {
                                $responseData = [];
                                $responseErrorMessage = 'The Usage Cost PDF does not exist.';
                                $responseCode = 500;
                            }
                        } else {
                            $responseData = [];
                            $responseCode = 500;
                            $responseErrorMessage = 'The person selected for this invoice has an invalid email address.';
                        }
                        break;
                }
                break;
        }

        if ($responseCode == 200) {
            return $this->sendResponse($responseData, 'Successfully sent call details', $responseCode);
        }
        return $this->sendError($responseErrorMessage, $responseData, $responseCode);
    }
}
