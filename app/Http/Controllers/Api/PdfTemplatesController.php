<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\PdfTemplate;
use App\Services\PdfTemplateService;

class PdfTemplatesController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new PdfTemplateService();
    }

    /**
     * Get tenant-specific pdf templates
     *
     * @return \Illuminate\Http\Response
     */
    public function my($tenantId)
    {
        return $this->sendPaginate(
            $this->service->list(request(), $tenantId),
            'Pdf Templates retrieved successfully.'
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
            $this->service->list(request(), currentTenant('id')),
            'Pdf Templates retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->sendPaginate(
            $this->service->create(request(PdfTemplate::$fields)),
            'Pdf Template retrieved successfully.'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Pdf Template retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\PdfTemplate $pdfTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(PdfTemplate $pdfTemplate)
    {
        return $this->sendPaginate(
            $this->service->update(request(PdfTemplate::$fields), $pdfTemplate),
            'Pdf Template updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PdfTemplate $pdfTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(PdfTemplate $pdfTemplate)
    {
        return $this->sendPaginate(
            $this->service->delete($pdfTemplate),
            'Pdf Template deleted successfully.'
        );
    }
}
