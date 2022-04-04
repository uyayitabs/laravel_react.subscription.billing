<?php

namespace App\DataViewModels;

use App\Models\BaseModel;

class SalesInvoiceSummary extends BaseModel
{
    protected $table = 'v_sales_invoice_summaries';

    protected $fillable = [
        'id',
        'billing_run_id',
        'date',
        'price_excl_vat',
        'price_incl_vat',
        'invoice_status',
        'invoice_status_label',
        'invoice_no',
        'relation_id',
        'customer_number',
        'full_name',
        'email',
        'full_address',
        'sales_invoice_reminder'
    ];

    public static $fields = [
        'id',
        'billing_run_id',
        'date',
        'price_excl_vat',
        'price_incl_vat',
        'invoice_status',
        'invoice_status_label',
        'invoice_no',
        'relation_id',
        'customer_number',
        'full_name',
        'email',
        'full_address',
        'sales_invoice_reminder'
    ];

    public static $filters = [
        'id',
        'billing_run_id',
        'date',
        'price_excl_vat',
        'price_incl_vat',
        'invoice_status',
        'invoice_status_label',
        'invoice_no',
        'customer_number',
        'full_name',
        'email',
        'full_address',
        'sales_invoice_reminder'
    ];

    public static $searchables = [
        'invoice_no',
        'customer_number',
        'full_name',
        'email',
        'full_address'
    ];

    public static $sortables = [
        'id',
        'billing_run_id',
        'date',
        'price_excl_vat',
        'price_incl_vat',
        'invoice_status',
        'invoice_status_label',
        'invoice_no',
        'customer_number',
        'full_name',
        'email',
        'full_address',
        'sales_invoice_reminder'
    ];

    protected $casts = [
        'price_excl_vat' => 'float',
        'price_incl_vat' => 'float'
    ];
}
