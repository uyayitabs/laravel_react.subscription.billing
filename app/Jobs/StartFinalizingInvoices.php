<?php

namespace App\Jobs;

use App\Models\BillingRun;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Env;
use Logging;
use App\Models\SalesInvoice;
use App\Models\Subscription;
use App\Services\SalesInvoiceService;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class StartFinalizingInvoices implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $billingRunId;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($billingRunId, $userId)
    {
        $this->billingRunId = $billingRunId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $billingRun = BillingRun::find($this->billingRunId);
        $salesInvoices = SalesInvoice::where('billing_run_id', $this->billingRunId)->get();
        $successCount = 0;
        $service = new SalesInvoiceService();

        $now = Carbon::now();
        if ($salesInvoices->count() > 0) {
            foreach ($salesInvoices as $invoice) {
                try {
                    if (!isset($invoice->invoice_no)) { //No number == Concept status
                        $service->update([
                            'invoice_status' => 1 //Finalized (also generates Invoice Number)
                        ], $invoice);
                    }

                    if ($invoice->invoice_file_exists) {
                        $successCount++;
                    }
                } catch (Exception $e) {
                    Logging::exceptionWithMessage($e, "Error creating PDF for " . $invoice->id, 17);
                }

                SnappyPdf::snappy()->removeTemporaryFiles();
            }
        }

        $end = Carbon::now();

        Logging::information(
            'Finished finalizing salesInvoices: successes ' . $successCount,
            [
                'count' => $salesInvoices->count(), 'total_time_taken' => $now->diff($end),
                'billingRun' => $billingRun
            ],
            17
        );
        $billingRun->update(['status_id' => 15]);
        $service->sendInvoiceFinalizeMail($this->userId, $this->billingRunId, $successCount, $salesInvoices->count(), $now, $end);
        $this->delete();
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        Logging::exceptionWithData(
            $exception,
            'Exception - Invoicing - Finalize Invoices',
            [
                'billing_run_id' => $this->billingRunId
            ],
            17
        );
        BillingRun::find($this->billingRunId)->update(['status_id' => 14]);
        $this->delete();
    }
}
