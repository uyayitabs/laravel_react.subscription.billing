<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends BaseModel
{
    protected $fillable = [
        'id',
        'bank_file_id',
        'tenant_bank_account_id',
        'relation_id',
        'sales_invoice_id',
        'date',
        'descr',
        'amount',
        'batch_id',
        'batch_trx',
        'account_iban',
        'account_name',
        'bank_code',
        'type',
        'return_code',
        'return_reason',
    ];

    public static $fields = [
        'id',
        'bank_file_id',
        'tenant_bank_account_id',
        'relation_id',
        'sales_invoice_id',
        'date',
        'descr',
        'amount',
        'batch_id',
        'batch_trx',
        'account_iban',
        'account_name',
        'bank_code',
        'type',
        'return_code',
        'return_reason',
    ];

    protected $searchable = [
        'date',
        'amount',
        'batch_id',
        'batch_trx',
        'account_iban',
        'account_name',
    ];

    public static $searchableCols = [
        'date',
        'amount',
        'batch_id',
        'batch_trx',
        'account_iban',
        'account_name',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d'
    ];

    /**
     * Get belonged TenantBankAccount
     *
     * @return BelongsTo
     */
    public function tenantBankAccount()
    {
        return $this->belongsTo(TenantBankAccount::class);
    }

    /**
     * Get belonged Relation
     *
     * @return BelongsTo
     */
    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    /**
     * Get belonged SalesInvoice
     * @return BelongsTo
     */
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }
}
