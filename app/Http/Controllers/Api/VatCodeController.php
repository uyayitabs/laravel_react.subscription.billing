<?php

namespace App\Http\Controllers\Api;

use App\Models\Tenant;
use App\Models\VatCode;
use App\Services\VatCodeService;
use App\Http\Requests\VatCodeApiRequest;

class VatCodeController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new VatCodeService();
    }

    /**
     * Return the list products with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return $this->sendPaginate(
            $this->service->list(null),
            'VatCodes retrieved successfully.'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VatCode  $vatCode
     * @return \Illuminate\Http\Response
     */
    public function show(VatCode $vatCode)
    {
        return $this->sendSingleResult(
            $this->service->show($vatCode->id),
            'Vat code retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(VatCodeApiRequest $request)
    {
        $datas = jsonRecode($request->all(VatCode::$fields));

        return $this->sendSingleResult(
            $this->service->create($datas),
            'Vat code created successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\VatCode  $vatCode
     * @return \Illuminate\Http\Response
     */
    public function update(VatCode $vatCode, VatCodeApiRequest $request)
    {
        $datas = jsonRecode($request->all(VatCode::$fields));

        return $this->sendSingleResult(
            $this->service->update($vatCode, $datas),
            'VAT code updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VatCode  $vatCode
     * @return \Illuminate\Http\Response
     */
    public function destroy(VatCode $vatCode)
    {
        $this->authorize('delete', $vatCode);

        $vatCode->delete();

        return $this->sendResponse($vatCode, 'Vat code deleted successfully.');
    }
}
