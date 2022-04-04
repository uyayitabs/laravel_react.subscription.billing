<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\JournalApiRequest;
use App\Models\Journal;
use App\Models\Tenant;
use App\Services\JournalService;
use Illuminate\Http\Request;

class JournalsController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new JournalService();
    }

    /**
     * Get tenant-specific fiscal years
     *
     * @return \Illuminate\Http\Response
     */
    public function my($tenantId)
    {
        $tenant =  Tenant::find($tenantId);
        $this->authorize('view', $tenant);
        return $this->sendPaginate(
            $this->service->list($tenantId),
            'Journals retrieved successfully.'
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(currentTenant('id')),
            'Journals retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(JournalApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendPaginate(
            $this->service->create($data),
            'Journal saved successfully.'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Journal retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Journal $journal
     * @return \Illuminate\Http\Response
     */
    public function update(Journal $journal, JournalApiRequest $request)
    {
        $data = jsonRecode($request->all());
        return $this->sendPaginate(
            $this->service->update($data, $journal),
            'Journal updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Journal $journal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Journal $journal)
    {
        // $this->authorize('delete', $journal);
        return $this->sendPaginate(
            $this->service->delete($journal),
            'Journal deleted successfully.'
        );
    }

    public function entries(Journal $journal)
    {
        return $this->sendPaginate(
            $this->service->listEntries($journal),
            'Entries retrieved successfully.'
        );
    }
}
