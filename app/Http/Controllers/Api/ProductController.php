<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Requests\ProductApiRequest;
use App\Services\ProductService;

class ProductController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new ProductService();
    }

    /**
     * Return a paginated list of products
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->sendPaginateOrResult(
            $this->service->list(),
            'Product listing retrieved successfully',
            function (Product $product) {
                $product->price = $product->priceExclVat;
                return $product;
            }
        );
    }

    /**
     * Store a newly created product
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ProductApiRequest $request)
    {
        $datas = jsonRecode($request->all(Product::$fields));
        return $this->sendSingleResult(
            $this->service->create($datas),
            'Product created succeully.'
        );
    }

    /**
     * Return the specified product
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->sendSingleResult(
            $this->service->show($id),
            'Product retrieved successfully.'
        );
    }

    /**
     * Update the specified product
     *
     * @param \App\Models\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Product $product, ProductApiRequest $request)
    {
        $datas = jsonRecode($request->all(Product::$fields));
        return $this->sendSingleResult(
            $this->service->update($datas, $product),
            'Product updated successfully.'
        );
    }

    /**
     * Remove the specified product
     *
     * @param \App\Models\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return $this->sendResponse(
            $product,
            'Subscription deleted successfully.'
        );
    }

    /**
     * Return the list products with id and name
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        return $this->sendResults(
            $this->service->optionList(),
            'Product lists retrieved successfully.'
        );
    }

    /**
     * Get product record counts
     */
    public function count()
    {
        return $this->sendResult(
            $this->service->count(),
            'Product counts retrieved successfully.'
        );
    }

    /**
     * Get backend_apis
     */
    public function backendApis()
    {
        return $this->sendResult(
            $this->service->backendApis(),
            'BackendApis retrieved successfully.'
        );
    }
}
