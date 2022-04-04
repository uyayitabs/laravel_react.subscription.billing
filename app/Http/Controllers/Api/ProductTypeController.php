<?php

namespace App\Http\Controllers\Api;

use App\Models\ProductType;
use App\Services\ProductTypeService;

class ProductTypeController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new ProductTypeService();
    }

    /**
     * Return a paginated list of product types
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->sendPaginate(
            $this->service->list(request()),
            'Product type listings retrieved successfully.'
        );
    }

    /**
     * Store a newly created productType
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        return $this->sendSingleResult(
            $this->service->create(request(ProductType::$fields)),
            'Product type created successfully.'
        );
    }

    /**
     * Display the specified productType
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Product type retrieved successfully.'
        );
    }

    /**
     * Update the specified product type
     *
     * @param \App\Models\ProductType $productType
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ProductType $productType)
    {
        return $this->sendSingleResult(
            $this->service->update(request(ProductType::$fields), $productType),
            'Product type updated successfully.'
        );
    }

    /**
     * Remove the specified productType
     *
     * @param \App\Models\ProductType $productType
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductType $productType)
    {
        $productType->delete();
        return $this->sendResponse(
            $productType,
            'Product type deleted successfully.'
        );
    }

    /**
     * Return the list product types with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return $this->sendResults(
            $this->service->optionList(),
            'Product types lists retrieved successfully.'
        );
    }
}
