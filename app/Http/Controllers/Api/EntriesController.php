<?php

namespace App\Http\Controllers\Api;

use App\Models\Entry;
use App\Http\Requests\EntryApiRequest;
use App\Services\EntryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\QueryBuilder\Exceptions\AllowedFieldsMustBeCalledBeforeAllowedIncludes;

class EntriesController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new EntryService();
    }

    /**
     * Get list of Entries
     *
     * @return \Illuminate\Http\Response
     */
    public function my($journalId)
    {
        return $this->sendPaginate(
            $this->service->list(request(), $journalId),
            'Journals retrieved successfully.'
        );
    }

    /**
     * Display a listing of the Entries.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request(), null),
            'Entries retrieved successfully.'
        );
    }

    /**
     *
     * Store a newly created Entries in storage.
     *
     * @param EntryApiRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(EntryApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendPaginate(
            $this->service->create($data),
            'Entry saved successfully.'
        );
    }

    /**
     * Display the specified Entries.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Entry retrieved successfully.'
        );
    }

    /**
     * Update the specified Entries in storage.
     *
     * @param  \App\Models\Entry $entry
     * @return \Illuminate\Http\Response
     */
    public function update(Entry $entry, EntryApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendPaginate(
            $this->service->update($data, $entry),
            'Entry updated successfully.'
        );
    }

    /**
     * Remove the specified Entries from storage.
     *
     * @param  \App\Models\Entry $entry
     * @return \Illuminate\Http\Response
     */
    public function destroy(Entry $entry)
    {
        // $this->authorize('delete', $entry);
        return $this->sendPaginate(
            $this->service->delete($entry),
            'Entry deleted successfully.'
        );
    }
}
