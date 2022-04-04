<?php

namespace App\DataViewModels;

use App\Models\BaseModel;

class SalesInvoiceReminder extends BaseModel
{
    protected $table = 'v_sales_invoice_reminders';

    protected $fillable = [
        'id',
        'sales_invoice_id',
        'value',
        'date',
        'relation_id',
        'price_total',
        'invoice_no',
        'customer_number',
        'city_name',
        'full_name'
    ];

    public static $fields = [
        'id',
        'sales_invoice_id',
        'value',
        'date',
        'relation_id',
        'price_total',
        'invoice_no',
        'customer_number',
        'city_name',
        'full_name'
    ];

    public static $filters = [
        'id',
        'sales_invoice_id',
        'value',
        'date',
        'relation_id',
        'price_total',
        'invoice_no',
        'customer_number',
        'city_name',
        'full_name'
    ];

    public static $sortables = [
        'id',
        'sales_invoice_id',
        'value',
        'date',
        'relation_id',
        'price_total',
        'invoice_no',
        'customer_number',
        'city_name',
        'full_name'
    ];

    public static $searchables = [
        'invoice_no',
        'customer_number',
        'city_name',
        'full_name',
        'value'
    ];
}
