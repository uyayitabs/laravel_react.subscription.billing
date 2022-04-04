<?php

namespace App\Services;

use App\Models\Entry;
use Logging;
use App\Models\Journal;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class EntryService
{
    /**
     * Display a listing of the Entries.
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $journalId)
    {
        $params = [];
        if (!is_null($journalId)) {
            $params[] = ['journal_id', '=', $journalId];
        }

        $query = Entry::where($params);

        return QueryBuilder::for($query, $request)
            ->allowedFields(Entry::$fields)
            ->allowedIncludes(Entry::$includes)
            ->defaultSort('-id')
            ->allowedSorts([
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
                AllowedSort::field('credit', 'credit'),
                AllowedSort::field('debit', 'debit'),
                AllowedSort::field('vat_amount', 'vat_amount'),
            ]);
    }

    /**
     * Store a newly created Entries in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {

        $journal = Journal::findOrFail($data['journal_id']);
        if (!is_null($journal)) {
            $entry = $journal->entries()->first();
            $data['tenant_id'] = $journal->tenant_id;
            $data['relation_id'] = $entry->relation_id;
            $data['invoice_id'] = $entry->invoice_id;
            $data['period_id'] = $entry->period_id;
        }
        $data['entry_no'] = generateNumberFromNumberRange(currentTenant('id'), 'entry_no');
        $attributes = filterArrayByKeys($data, Entry::$fields);
        Entry::create($attributes);
        Logging::information('Create Journal Entry', $attributes, 1, 1);

        return $this->list(request(), null);
    }

    /**
     * Display the specified Entries.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        return QueryBuilder::for(Entry::where("id", $id))
            ->allowedFields(Entry::$fields)
            ->allowedIncludes(Entry::$includes);
    }

    /**
     * Update the specified Entries in storage.
     *
     * @param  \App\Models\Entry $entry
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, Entry $entry)
    {
        $attributes = filterArrayByKeys($data, Entry::$fields);
        if (!is_null($entry)) {
            $log['old_values'] = $entry->getRawDBData();

            $entry->update($attributes);
            $log['new_values'] = $entry->getRawDBData();
            $log['changes'] = $entry->getChanges();

            Logging::information('Update Journal Entry', $log, 1, 1);
        }
        return $this->list(request(), null);
    }

    /**
     * Remove the specified Entries from storage.
     *
     * @param  \App\Models\Entry $entry
     * @return \Illuminate\Http\Response
     */
    public function delete(Entry $entry)
    {
        $entry->delete();
        return $this->list(request(), null);
    }

    /**
     * Count total number of Entries from storage.
     *
     * @param  \App\Models\Entry $entry
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        return Entry::count();
    }
}
