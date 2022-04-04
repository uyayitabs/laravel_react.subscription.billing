<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasRelationTrait;

class BankAccount extends BaseModel
{
    use HasRelationTrait;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'relation_id',
        'description',
        'status',
        'bank_name',
        'iban',
        'bic',
        'dd_default',
        'mndt_id',
        'dt_of_sgntr',
        'amdmnt_ind'
    ];

    public static $fields = [
        'id',
        'relation_id',
        'description',
        'status',
        'bank_name',
        'iban',
        'bic',
        'dd_default',
        'mndt_id',
        'dt_of_sgntr',
        'amdmnt_ind'
    ];

    protected $searchable = [
        'description,bank_name,iban,bic,mndt_id,dt_of_sgntr,amdmnt_ind,status,dd_default'
    ];

    public static $searchableCols = [
        'description',
        'bank_name',
        'iban',
        'bic',
        'mndt_id',
        'dt_of_sgntr',
        'amdmnt_ind',
        'default',
        'status'
    ];

    protected $appends = ['dd_default_label', 'status_label'];

    protected $casts = [
        'dt_of_sgntr' => 'datetime:Y-m-d'
    ];

    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    public function getDdDefaultLabelAttribute()
    {
        return $this->dd_default ? 'Y' : 'N';
    }

    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function setDtOfSgntrAttribute($value)
    {
        $this->attributes['dt_of_sgntr'] = dateFormat($value);
    }
}
