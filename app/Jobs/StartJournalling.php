<?php

namespace App\Jobs;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StartJournalling implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $processingDate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($processingDate)
    {
        $this->processingDate = $processingDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Log::info("*** Saving Journals start=" . now()->format('d/m/Y H:i:s'));
        $salesInvoices = SalesInvoice::where("date", $this->processingDate)->cursor();
        foreach ($salesInvoices as $salesInvoice) {
            SaveJournalFromSalesInvoice::dispatchNow($salesInvoice);
        }
        //Log::info("*** Saved Journals stop=" . now()->format('d/m/Y H:i:s'));

        //Log::info("*** Saving Entries start=" . now()->format('d/m/Y H:i:s'));
        $salesInvoiceIds = SalesInvoice::where("date", $this->processingDate)->get()->pluck("id");
        $salesInvoiceLines =  SalesInvoiceLine::whereIn("sales_invoice_id", $salesInvoiceIds)->cursor();
        foreach ($salesInvoiceLines as $salesInvoiceLine) {
            SaveEntryDataFromSalesInvoiceLine::dispatchNow($salesInvoiceLine);
        }
        //Log::info("*** Saved Entries stop=" . now()->format('d/m/Y H:i:s'));
    }
}
