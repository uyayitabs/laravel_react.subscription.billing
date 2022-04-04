<?php

namespace App\Observers;

use App\Models\AccountingPeriod;
use App\Models\Entry;
use App\Models\Journal;
use App\Models\SalesInvoiceLine;
use App\Models\TenantProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesInvoiceLineObserver
{
    /**
     * Listen to the SalesInvoiceLine created event.
     *
     * @param  \App\Models\SalesInvoiceLine $salesInvoiceLine
     * @return void
     */
    public function created(SalesInvoiceLine $salesInvoiceLine)
    {
        $salesInvoice = $salesInvoiceLine->salesInvoice()->first();
        if (boolval($salesInvoice->tenant->use_accounting)) {
            $subscriptionLine = $salesInvoiceLine->subscriptionLine()->first();
            $accountId = TenantProduct::getAccountId($salesInvoice->tenant_id, $subscriptionLine->product_id);
            $relation = $salesInvoice->relation()->first();
            $invoiceDate = Carbon::parse($salesInvoice->date);
            $journal = $salesInvoice->journal()->first();

            $accountingPeriod = AccountingPeriod::findByTenantIdAndDate(
                $salesInvoice->tenant_id,
                $invoiceDate->format('Y-m-d')
            );
            DB::connection()->disableQueryLog();
            Entry::create([
                'entry_no' => generateNumberFromNumberRange($salesInvoice->tenant_id, 'entry_no'),
                'date' => $salesInvoice->date,
                'description' => "Revenue entry {$salesInvoiceLine->description}",
                'journal_id' => $journal->id,
                'relation_id' => $relation->id,
                'invoice_id' => $salesInvoice->id,
                'invoice_line_id' => $salesInvoiceLine->id,
                'account_id' => $accountId,
                'period_id' => !is_null($accountingPeriod) ? $accountingPeriod->id : null,
                'credit' => $salesInvoiceLine->price,
                'vatcode_id' => $salesInvoiceLine->vat_code,
                'vat_percentage' => $salesInvoiceLine->vat_percentage,
                'vat_amount' => $salesInvoiceLine->price_vat,
            ]);
            DB::connection()->enableQueryLog();
        }
    }
}
