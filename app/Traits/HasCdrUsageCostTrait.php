<?php

namespace App\Traits;

use App\Models\CdrUsageCost;
use Carbon\Carbon;

trait HasCdrUsageCostTrait
{
    public function cdrUsageCosts()
    {
        return $this->hasMany(CdrUsageCost::class, 'sales_invoice_line_id', 'id');
    }

    /**
     * Get invoice filename
     *
     * @return string
     */
    public function getCallSummaryFilenameAttribute()
    {
        $salesInvoice = $this->salesInvoice;
        $relation = $salesInvoice->relation;
        if (!empty($relation)) {
            return "Gespreksspecificatie-{$relation->customer_number}-{$salesInvoice->invoice_no}.pdf";
        }
        return null;
    }

    /**
     * Get invoice file dir path
     *
     * @return string
     */
    public function getCallSummaryFileDirPathAttribute(): string
    {
        $salesInvoice = $this->salesInvoice;
        $monthDir = Carbon::parse($salesInvoice->due_date)->format("Y/m");
        return storage_path("app/private/cdr_summary/{$salesInvoice->tenant_id}/{$monthDir}");
    }

    /**
     * Get invoice file dir path
     *
     * @return string
     */
    public function getCallSummaryFileFullPathAttribute(): string
    {
        return "{$this->call_summary_file_dir_path}/{$this->call_summary_filename}";
    }
}
