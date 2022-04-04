<?php

namespace App\Observers;

use App\Models\Journal;
use App\Models\SalesInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesInvoiceObserver
{
    /**
     * Listen to the SalesInvoice created event.
     *
     * @param  \App\Models\SalesInvoice  $salesInvoice
     * @return void
     */
    public function created(SalesInvoice $salesInvoice)
    {
        if (boolval($salesInvoice->tenant->use_accounting)) {
            $invoiceDate = Carbon::parse($salesInvoice->date);
            DB::connection()->disableQueryLog();
            // Save Journal
            Journal::create([
                'tenant_id' => $salesInvoice->tenant_id,
                'invoice_id' => $salesInvoice->id,
                'journal_no' => generateNumberFromNumberRange($salesInvoice->tenant_id, 'journal_no'),
                'date' => $salesInvoice->date,
                'description' => "Revenue {$invoiceDate->format('m-Y')}"
            ]);
            DB::connection()->enableQueryLog();
        }
    }
}
