<?php

namespace App\DataViewModels;

use App\Models\BaseModel;

class RelationSummary extends BaseModel
{
    protected $table = 'v_relation_summaries';

    protected $fillable = [
        'id',
        'tenant_id',
        'customer_number',
        'full_name',
        'full_address',
        'subscription_status',
        'subscription_description',
        'sales_invoice_date',
        'sales_invoice_invoice_number',
        'sales_invoice_excl_price',
        'sales_invoice_incl_price'
    ];

    public static $fields = [
        'id',
        'tenant_id',
        'customer_number',
        'full_name',
        'full_address',
        'subscription_status',
        'subscription_description',
        'sales_invoice_date',
        'sales_invoice_invoice_number',
        'sales_invoice_excl_price',
        'sales_invoice_incl_price'
    ];

    public static $filters = [
        'id',
        'tenant_id',
        'customer_number',
        'full_name',
        'full_address',
        'subscription_status',
        'subscription_description',
        'sales_invoice_date',
        'sales_invoice_invoice_number',
        'sales_invoice_excl_price',
        'sales_invoice_incl_price'
    ];

    public static $sortables = [
        'id',
        'tenant_id',
        'customer_number',
        'full_name',
        'full_address',
        'subscription_status',
        'subscription_description',
        'sales_invoice_date',
        'sales_invoice_invoice_number',
        'sales_invoice_excl_price',
        'sales_invoice_incl_price'
    ];

    public static $searchables = [
        'customer_number',
        'full_name',
        'full_address',
        'subscription_description',
        'sales_invoice_invoice_number'
    ];
}
