<?php

namespace App\Console\Commands;

use App\Jobs\CreateCDRUsageCostsPDF;
use App\Models\PlanSubscriptionLineType;
use App\Models\SalesInvoice;
use Illuminate\Console\Command;

class CreateCDRUsageCostsPDFs extends Command
{
    protected $signature = 'cdr:generate_pdfs
                                {--billing_run_id=}
                                {--overwrite_pdf=false}
                                {--tenant_id=}
                                {--sales_invoice_id=*}
                                {--relation_id=*}';
    protected $description = 'Create CDR usage costs PDFs';

    public function handle(): void
    {
        // Parameters
        $billingRunId = filter_var($this->option('billing_run_id'), FILTER_VALIDATE_INT);
        $overwritePdf = filter_var($this->option('overwrite_pdf'), FILTER_VALIDATE_BOOLEAN);
        $tenantId = filter_var($this->option('tenant_id'), FILTER_VALIDATE_INT);
        $salesInvoiceIds = array_map('intval', $this->option('sales_invoice_id'));
        $relationIds = array_map('intval', $this->option('relation_id'));
        $salesInvoices = null;
        if (!is_null($billingRunId)) {
            $salesInvoices = SalesInvoice::where('billing_run_id', $billingRunId);
        }
        if (!is_null($tenantId)) {
            $salesInvoices = SalesInvoice::where('tenant_id', $tenantId);
        }
        if ($salesInvoiceIds) {
            $salesInvoices = SalesInvoice::whereIn('id', $salesInvoiceIds);
        }
        if ($relationIds) {
            $salesInvoices = SalesInvoice::whereIn('relation_id', $relationIds);
        }
        if (!is_null($salesInvoices)) {
            $vucLineTypeId = PlanSubscriptionLineType::where('line_type', 'VUC')->pluck('id')->first();
            // Filter sales_invoices those with vuc lines only
            $salesInvoices = $salesInvoices->whereHas('salesInvoiceLines', function ($query) use ($vucLineTypeId) {
                $query->where('sales_invoice_line_type', $vucLineTypeId);
            });
            foreach ($salesInvoices->get() as $salesInvoice) {
                $cdrLines = $salesInvoice->salesInvoiceLines()->where('sales_invoice_line_type', $vucLineTypeId);
                foreach ($cdrLines->get() as $cdrInvoiceLine) {
                    CreateCDRUsageCostsPDF::dispatch($cdrInvoiceLine, $overwritePdf);
                }
            }
        }
    }
}
