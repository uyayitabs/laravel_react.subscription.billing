<?php

namespace App\Http\Controllers\Portal;

use App\Models\SalesInvoice;
use App\Http\Resources\PortalSalesInvoiceResource;
use App\Services\SalesInvoiceService;
use App\Services\CdrUsageCostService;
use App\Services\SubscriptionService;

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
     * List invoices for portal
     *
     * @param mixed $relationId
     */
    public function listInvoices($relationId)
    {
        if (isPortalRelationIdAuthorized($relationId)) {
            return $this->sendNewPaginate(
                $this->service->listPortalInvoices($relationId),
                'Sales invoice listing retrieved successfully'
            );
        }
        return $this->sendError('Unauthorized access', [], 403);
    }

    /**
     * Get invoice details
     *
     * @param SalesInvoice $invoice
     * @return PortalSalesInvoiceResource
     */
    public function show($salesInvoice)
    {
        $invoice = SalesInvoice::find($salesInvoice);
        if (isPortalRelationIdAuthorized($invoice->relation_id)) {
            return new PortalSalesInvoiceResource(
                $invoice,
                'Successfully retrieved invoice.',
                true
            );
        }
        return $this->sendError('Unauthorized access', [], 403);
    }

    /**
     * Download PDF
     *
     * @param mixed $salesInvoice
     */
    public function downloadInvoicePdf($salesInvoice)
    {
        $invoice = SalesInvoice::find($salesInvoice);
        if (isPortalRelationIdAuthorized($invoice->relation_id)) {
            $subscriptionService = new SubscriptionService();
            $response = $subscriptionService->getInvoicePdf($salesInvoice);
            if ($response["file_exists"]) {
                return response()->file($response["file"]);
            }
            return $this->sendError("Invoice PDF not found.", [], 500);
        }
        return $this->sendError('Unauthorized access', [], 403);
    }

    /**
     * Download usage cost PDF
     *
     * @param mixed $salesInvoice
     */
    public function downloadUsageCostPdf($salesInvoice)
    {
        $invoice = SalesInvoice::find($salesInvoice);
        if (isPortalRelationIdAuthorized($invoice->relation_id)) {
            $usageCostInvoiceLine = $invoice->usageCostLines()->first();
            $pdfFile = $usageCostInvoiceLine->getAttribute('call_summary_file_fullpath');
            if (file_exists($pdfFile)) {
                return response()->file($pdfFile);
            }
            return $this->sendError("Usage cost PDF not found.", [], 500);
        }
        return $this->sendError('Unauthorized access', [], 403);
    }
}
