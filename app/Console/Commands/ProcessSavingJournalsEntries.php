<?php

namespace App\Console\Commands;

use App\Models\NumberRange;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessSavingJournalsEntries extends Command
{
    protected $signature = 'journal:save_data {--date=YYYY-MM-DD} {--tenant_id=}';

    protected $description = 'Save journal and entries for date-specific sales_invoices & sales_invoice_lines';

    public function handle(): void
    {
        $processingDate = now();
        $invoiceDateParam = $this->option('date');
        $tenantIdParam = $this->option('tenant_id');

        if (!empty($invoiceDateParam)) {
            $processingDate = Carbon::parse($invoiceDateParam);
        }

        if (!empty($tenantIdParam)) {
            $tenant = Tenant::findOrFail($tenantIdParam);

            // Get Journal NumberRange data
            $journalNumberRange = NumberRange::where([
                ['tenant_id', '=', $tenant->id],
                ['type', '=', 'journal_no'],
            ])->first();

            $journalZeroPad = getNumberRangeZeroPad($journalNumberRange->format);
            $journalPrefixSuffix = getNumberRangePrefixSuffix($journalNumberRange->format);

            // Execute DB stored procedure - SAVE JOURNALS
            DB::select('call SaveJournals(?,?,?,?)', [
                $tenant->id,
                $processingDate->format("Y-m-d"),
                $journalZeroPad,
                $journalPrefixSuffix['prefix']
            ]);

            // Get Entry NumberRange data
            $entryNumberRange = NumberRange::where([
                ['tenant_id', '=', $tenant->id],
                ['type', '=', 'entry_no'],
            ])->first();

            $entryZeroPad = getNumberRangeZeroPad($entryNumberRange->format);
            $entryPrefixSuffix = getNumberRangePrefixSuffix($entryNumberRange->format);

            // Execute DB stored procedure - SAVE ENTRIES
            DB::select('call SaveEntries(?,?,?,?)', [
                $tenant->id,
                $processingDate->format("Y-m-d"),
                $entryZeroPad,
                $entryPrefixSuffix['prefix']
            ]);
        }
    }
}
