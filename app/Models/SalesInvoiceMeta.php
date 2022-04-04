<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceMeta extends BaseModel
{
    protected $fillable = [
        'sales_invoice_id',
        'key',
        'value',
    ];

    public static $fields = [
        'id',
        'sales_invoice_id',
        'key',
        'value',
        'updated_at',
        'SalesInvoice.id',
        'SalesInvoice.invoice_no',
        'SalesInvoice.Relation.id'
    ];

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }
}
