<?php

namespace App\Models;

use Carbon\Carbon;

class Entry extends BaseModel
{
    protected $fillable = [
        'entry_no',
        'date',
        'description',
        'journal_id',
        'relation_id',
        'invoice_id',
        'invoice_line_id',
        'account_id',
        'period_id',
        'credit',
        'debit',
        'vatcode_id',
        'vat_percentage',
        'vat_amount',
    ];

    public static $fields = [
        'date',
        'description',
        'journal_id',
        'relation_id',
        'invoice_id',
        'invoice_line_id',
        'account_id',
        'period_id',
        'credit',
        'debit',
        'vatcode_id',
        'vat_percentage',
        'vat_amount',
    ];

    public static $includes = [
        'relation',
        'sales-invoice-line',
        'account',
        'accounting-period'
    ];

    public static $scopes = [];

    public static $sorts = [
        'entry_no',
        'date',
        'description',
        'journal_id',
        'relation_id',
        'invoice_id',
        'invoice_line_id',
        'account_id',
        'period_id',
        'credit',
        'debit',
        'vatcode_id',
        'vat_percentage',
        'vat_amount',
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];

    public static $withScopes = [];

    /**
     * Get relation function
     *
     * @return \Relation
     */
    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    /**
     * Get SalesInvoiceLine function
     *
     * @return \SalesInvoiceLine
     */
    public function salesInvoiceLine()
    {
        return $this->belongsTo(SalesInvoiceLine::class, "invoice_line_id", "id");
    }

    /**
     * Get Account function
     *
     * @return \Account
     */
    public function account()
    {
        return $this->belongsTo(Account::class, "account_id", "id");
    }

    /**
     * Get AccountingPeriod function
     *
     * @return \AccountingPeriod
     */
    public function accountingPeriod()
    {
        return $this->belongsTo(AccountingPeriod::class, "period_id", "id");
    }

    /**
     * Get generated entry_no without the format
     *
     * @param int $tenantId
     * @return array
     */
    public static function getGeneratedEntryNos($journalIds)
    {
        $entryNos = Entry::select("entry_no")
            ->whereIn("journal_id", $journalIds)
            ->orderBy("entry_no", "ASC")
            ->get()
            ->pluck('entry_no')
            ->toArray();

        $results = array_map(
            function ($entryNo) {
                $digitResults = [];
                $withMatch = preg_match("/\d{1,}/", $entryNo, $digitResults);
                if ($withMatch) {
                    return intval($digitResults[0]);
                }
                return null;
            },
            $entryNos
        );
        return $results;
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = dateFormat($value);
    }
}
