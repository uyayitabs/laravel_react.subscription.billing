<?php

namespace App\Filters;

use App\Models\Entry;
use App\Models\Journal;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class JournalOutstandingBalanceSortFilter implements Sort
{
    protected $tenantId;

    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $sumStatement = "(SUM(debit) - round(SUM(credit) + SUM(vat_amount), 2)) as 'outstanding_balance'";
        $subQuery = Entry::select('entries.journal_id', DB::raw($sumStatement))->groupBy('entries.journal_id');

        $query->joinSub(
            $subQuery,
            'entries',
            function ($query) {
                $query->on('journals.id', '=', 'entries.journal_id');
            }
        )
            ->select(["journals.*", "entries.outstanding_balance"])
            ->orderBy("entries.outstanding_balance", $direction);
    }
}
