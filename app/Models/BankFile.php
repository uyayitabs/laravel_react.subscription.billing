<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankFile extends BaseModel
{
    protected $fillable = [
        'id',
        'tenant_bank_account_id',
        'filename',
        'status',
    ];

    public static $fields = [
        'id',
        'tenant_bank_account_id',
        'filename',
        'status',
    ];

    protected $searchable = [
        'filename'
    ];

    public static $searchableCols = [
        'filename',
    ];


    protected $casts = [
        // 'dt_of_sgntr' => 'datetime:Y-m-d'
    ];
}
