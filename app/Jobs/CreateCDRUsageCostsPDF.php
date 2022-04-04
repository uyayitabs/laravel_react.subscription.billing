<?php

namespace App\Jobs;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use App\Services\CdrUsageCostService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class CreateCDRUsageCostsPDF implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $salesInvoiceLine;
    protected $overwriteExistingPdf;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SalesInvoiceLine $salesInvoiceLine, $overwriteExistingPdf = false)
    {
        $this->salesInvoiceLine = $salesInvoiceLine;
        $this->overwriteExistingPdf = $overwriteExistingPdf;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $salesInvoice = $this->salesInvoiceLine->salesInvoice;
        if (File::exists($this->salesInvoiceLine->call_summary_file_fullpath)) {
            if ($this->overwriteExistingPdf) {
                $this->generatePdf($salesInvoice, $this->salesInvoiceLine);
            }
        } else {
            $this->generatePdf($salesInvoice, $this->salesInvoiceLine);
        }
    }

    private function generatePdf(SalesInvoice $salesInvoice, SalesInvoiceLine $salesInvoiceLine)
    {
        $cdrService = new CdrUsageCostService();
        $cdrService->generateCdrSummaryPdf($salesInvoiceLine);
        \Logging::information(
            'Invoicing - PDF generation Usage Cost',
            [
                'invoice_id' => $salesInvoice->id,
                'invoice_line_id' => $salesInvoiceLine->id,
                'relation_id' => $salesInvoice->relation_id
            ],
            17,
            1,
            $salesInvoice->tenant_id,
            'invoice',
            $salesInvoice->id
        );
    }
}
