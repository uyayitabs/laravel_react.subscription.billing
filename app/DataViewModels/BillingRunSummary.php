<?php

namespace App\DataViewModels;

use App\Models\BaseModel;

class BillingRunSummary extends BaseModel
{
    protected $table = 'v_billing_run_summaries';

    protected $fillable = [
        'billing_run_id',
        'tenant_id',
        'date',
        'status_id',
        'status_label',
        'sales_invoice_count',
        'price_sum',
        'price_vat_sum',
        'price_total_sum'
    ];

    public static $fields = [
        'billing_run_id',
        'tenant_id',
        'date',
        'status_id',
        'status_label',
        'sales_invoice_count',
        'price_sum',
        'price_vat_sum',
        'price_total_sum'
    ];

    public static $filters = [
        'billing_run_id',
        'tenant_id',
        'date',
        'status_id',
        'status_label',
        'sales_invoice_count',
        'price_sum',
        'price_vat_sum',
        'price_total_sum'
    ];

    public static $sortables = [
        'billing_run_id',
        'tenant_id',
        'date',
        'status_id',
        'status_label',
        'sales_invoice_count',
        'price_sum',
        'price_vat_sum',
        'price_total_sum'
    ];
}
