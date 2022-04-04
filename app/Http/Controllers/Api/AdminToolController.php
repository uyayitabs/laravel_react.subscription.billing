<?php

namespace App\Http\Controllers\Api;

use App\Models\BillingRun;
use App\DataViewModels\BillingRunSummary;
use App\Services\BillingRunService;
use App\Services\CdrUsageCostService;
use App\Services\StatusService;
use App\Models\Status;
use Illuminate\Http\Request;

class AdminToolController extends BaseController
{
    protected $billingRunService;
    protected $cdrUsageService;


    public function __construct()
    {
        parent::__construct();
        $this->billingRunService = new BillingRunService();
        $this->cdrUsageService = new CdrUsageCostService();
    }

    public function cdr()
    {
        $resultSaveCSV = $this->cdrUsageService->saveCSV(request()->all());
        if (!$resultSaveCSV['success']) {
            return response()->json($resultSaveCSV, 422);
        }

        $user = request()->user();
        $resultProcessCSV = $this->cdrUsageService->startCsvImportJob(request('filename'), $user->last_tenant_id, $user->id);
        return response()->json($resultProcessCSV, $resultProcessCSV['success'] ? 200 : 422);
    }

    public function billingRunsByStatus(Request $request, $statusId)
    {
        return $this->sendPaginateOrResult(
            $this->billingRunService->getBillingRunDatesByStatusId($statusId, $request)
        );
    }

    public function billingRuns(Request $request)
    {
        return $this->sendPaginateOrResult(
            $this->billingRunService->getBillingRunDatesByStatusId(null, $request),
            'Billing runs retrieved successfully'
        );
    }

    public function billingRunsSummary(Request $request)
    {
        $statusService = new StatusService();
        $statusCreateId = $statusService->getStatusId('billing_run', 'creating_invoices');
        $statusFinalizeId = $statusService->getStatusId('billing_run', 'finalizing_invoices');

        return $this->sendPaginateOrResult(
            $this->billingRunService->billingRunsSummary(null, $request),
            'Billing runs retrieved successfully',
            function (BillingRunSummary $billingRun) use ($statusCreateId, $statusFinalizeId) {
                $billingRun->is_removable = $billingRun->status_id !== $statusCreateId && $billingRun->status_id < $statusFinalizeId;
                return $billingRun;
            }
        );
    }

    public function createInvoiceQueueJob()
    {
        $billingRun = BillingRun::find(request('billing_run_id'));
        if (!$billingRun) {
            return $this->sendError('Given BillingRun does not exist');
        }
        if ($billingRun->status_id != 0) {
            return $this->sendError("Given BillingRun is not of status new");
        }

        $queueJob = $this->billingRunService->createInvoiceQueueJob(
            $billingRun,
            request()->user()->id
        );

        if ($queueJob) {
            return response()->json([
                'success' => true,
                'message' => "When done, you will receive an email notification",
                'data' => $queueJob,
            ]);
        }

        return $this->sendResponse("No invoice to create", []);
    }

    public function createFinalizeInvoicesQueueJob()
    {
        $billingRun = BillingRun::find(request('billing_run_id'));
        if (!$billingRun) {
            return $this->sendError('Given BillingRun does not exist');
        }
        if ($billingRun->status_id != 12) {
            return $this->sendError("Given BillingRun is not of status invoice_created");
        }

        $queueJob = $this->billingRunService->createFinalizeInvoicesQueueJob(
            $billingRun,
            request()->user()->id
        );

        if ($queueJob) {
            return response()->json([
                'success' => true,
                'message' => "When done, you will receive an email notification",
                'data' => $queueJob,
            ]);
        }

        return $this->sendResponse("No invoices to finalize", []);
    }

    public function createSendEmailQueueJob()
    {
        $billingRun = BillingRun::find(request('billing_run_id'));
        if (!$billingRun) {
            return $this->sendError('Given BillingRun does not exist');
        }
        if ($billingRun->status_id != 15) {
            return $this->sendError("Given BillingRun is not of status invoices_finalized");
        }

        $queueJob = $this->billingRunService->createSendEmailQueueJob(
            $billingRun,
            request()->user()->id
        );

        if ($queueJob) {
            return response()->json([
                'success' => true,
                'message' => "A job has been scheduled to send out the invoice emails. The job will soon be processed.",
                'data' => $queueJob,
            ]);
        }

        return $this->sendResponse("No invoice emails to send", []);
    }
}
