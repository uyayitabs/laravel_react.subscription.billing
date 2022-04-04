<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use Logging;
use App\Services\BrandService;

class BrandController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new BrandService();
    }

    /**
     * Return a paginated list of brands
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Brand listing retrieved successfully'
        );
    }

    /**
     * Store a newly created brand
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->sendSingleResult(
            $this->service->create(request(Brand::$fields)),
            'Brand created successfully.'
        );
    }

    /**
     * Display the specified brand
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Brand retrieved successfully.'
        );
    }

    /**
     * Update the specified brand
     *
     * @param \App\Models\Brand $brand
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Brand $brand)
    {
        return $this->sendSingleResult(
            $this->service->update(request(Brand::$fields), $brand),
            'Brand updated successfully.'
        );
    }

    /**
     * Remove the specified brand
     *
     * @param  App\Brand $brand
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        Logging::information('Delete Brand', $brand, 1, 1);

        return $this->sendResponse(
            $brand->delete(),
            'Brand deleted successfully.'
        );
    }
}
