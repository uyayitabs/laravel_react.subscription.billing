<?php

namespace App\Models;

use App\Traits\HasOutstandingBalanceTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Journal extends BaseModel
{
    use HasOutstandingBalanceTrait;

    protected $table = 'journals';

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'journal_no',
        'date',
        'description',
    ];

    protected $appends = [
        'outstanding_balance'
    ];

    public static $fields = [
        'tenant_id',
        'invoice_id',
        'journal_no',
        'date',
        'description',
    ];

    protected $searchable = [
        'journal_no,date,description',
        'salesInvoice|invoice_no'
    ];

    public static $searchableCols = [
        'journal_no',
        'date',
        'description',
        'invoice_no'
    ];

    public static $includes = [
        'entries',
        'sales-invoice'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    public static $sorts = [
        'id',
        'tenant_id',
        'invoice_id',
        'journal_no',
        'date',
        'description',
    ];

    public static $withScopes = [
        'entries'
    ];

    /**
     * Get binding Tenant
     *
     * @return \Tenant
     */

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Sales Invoice relationship
     *
     * @return \SalesInvoice
     */
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'invoice_id', 'id');
    }


    /**
     * Get entries under period
     *
     * @return Entry[]
     */
    public function entries()
    {
        return $this->hasMany(Entry::class, 'journal_id', 'id');
    }

    /**
     * Scopes to return all the relationships
     *
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }

    public function scopeWith($query, $scopes)
    {
        return $query->with($scopes);
    }

    public static function getJournalIds($tenantId)
    {
        return Journal::where('tenant_id', $tenantId)->pluck('id')->toArray();
    }

    /**
     * Get generated journal_no without the format
     *
     * @param int $tenantId
     * @return array
     */
    public static function getGeneratedJournalNos($tenantId)
    {
        $journalNos = Journal::select("journal_no")
            ->where("tenant_id", $tenantId)
            ->orderBy("journal_no", "ASC")
            ->get()
            ->pluck('journal_no')
            ->toArray();

        $results = array_map(
            function ($journalNo) {
                $digitResults = [];
                $withMatch = preg_match("/\d{1,}/", $journalNo, $digitResults);
                if ($withMatch) {
                    return intval($digitResults[0]);
                }
                return null;
            },
            $journalNos
        );
        return $results;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = dateFormat($value);
    }
}
