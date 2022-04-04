<?php

namespace App\Services;

use App\Filters\JournalOutstandingBalanceSortFilter;
use Logging;
use App\Models\Journal;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class JournalService
{
    protected $model;

    public function __construct()
    {
        $this->model = new Journal();
    }
    /**
     * Display a listing of the Journal.
     */
    public function list($tenantId)
    {
        if (is_null($tenantId)) {
            $tenantId = currentTenant('id');
        }

        $query = QueryBuilder::for($this->model::where('tenant_id', $tenantId))
            ->allowedFields($this->model::$fields)
            ->allowedIncludes($this->model::$includes)
            ->defaultSort('-id')
            ->allowedSorts([
                'tenant_id',
                'invoice_id',
                'journal_no',
                'date',
                'description',
                AllowedSort::custom(
                    'outstanding_balance',
                    new JournalOutstandingBalanceSortFilter($tenantId),
                    ''
                )
            ]);

        if (request()->has('filter') && isset(request()->filter['keyword'])) {
            $query->search(request()->filter['keyword']);
        }

        return $query;
    }

    /**
     * Display the specified Journal.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        return QueryBuilder::for($this->model::where("id", $id))
            ->allowedFields($this->model::$fields)
            ->allowedIncludes($this->model::$includes);
    }

    public function listEntries($journal)
    {
        return QueryBuilder::for($this->model::where('journal_id', $journal->id))
            ->allowedFields($this->model::$fields)
            ->allowedIncludes($this->model::$includes)
            ->defaultSort('-id')
            ->allowedSorts($this->model::$sorts);
    }

    /**
     * Store a newly created Journal in storage.
     *
     * @param  \Illuminate\Http\Request $request
     */
    public function create(array $data)
    {
        $attributes = filterArrayByKeys($data, $this->model::$fields);
        Journal::create($attributes);
        return $this->list(request(), currentTenant('id'));
    }

    /**
     * Update the specified Journal in storage.
     *
     * @param  \App\Models\Journal $journal
     */
    public function update(array $data, Journal $journal)
    {
        $attributes = filterArrayByKeys($data, $this->model::$fields);
        if (!is_null($journal)) {
            $log['old_values'] = $journal->getRawDBData();

            $journal->update($attributes);
            $log['new_values'] = $journal->getRawDBData();
            $log['changes'] = $journal->getChanges();

            Logging::information('Update Journal', $log, 1, 1);
        }
        return $this->list(request(), currentTenant('id'));
    }

    /**
     * Remove the specified Journal from storage.
     *
     * @param  \App\Models\Journal $journal
     */
    public function delete(Journal $journal)
    {
        $journal->delete();
        return $this->list(request(), currentTenant('id'));
    }

    public function count()
    {
        return $this->model::count();
    }
}
