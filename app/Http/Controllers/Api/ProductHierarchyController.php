<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\ProductHierarchy;
use App\Http\Requests\ProductHierarchyApiRequest;
use App\Services\ProductHierarchyService;
use Illuminate\Http\Request;

class ProductHierarchyController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new ProductHierarchyService();
    }

    /**
     * Return product hierarchy list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
    }

    /**
     * Store a newly created product
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Product $product, Product $related_product, ProductHierarchyApiRequest $request)
    {
        $response = $this->service->create($product, $related_product, jsonRecode($request->all(ProductHierarchy::$fields)));
        if (is_array($response) && $response['success']) {
            return $this->show($product, $response['message']);
        }
        return $this->sendError($response['message'] ?? 'Product hierarchy not created.', [], 500);
    }

    /**
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product, $message = null)
    {
        $productHierarchy = $this->service->get($product);
        if (blank($productHierarchy)) {
            return $this->sendResponse(null, 'No product hierarchy retrieved.');
        }
        return $this->sendResponse($productHierarchy, $message ?? 'Product hierarchy retrieved successfully.');
    }

    /**
     * Update product hierarchy
     *
     * @param Product $product
     * @param Product $related_product
     * @param ProductHierarchyApiRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Product $product, Product $related_product, ProductHierarchyApiRequest $request)
    {
        $response = $this->service->update($product, $related_product, jsonRecode(array_filter($request->all(ProductHierarchy::$fields))));
        if ($response['success']) {
            return $this->show($product, $response['message']);
        }
        return $this->sendError($response['message'] ?? 'Product hierarchy not updated.', [], 500);
    }

    /**
     * Delete specific ProductHierarchy
     *
     * @param \App\Models\ProductHierarchy $productHierarchy
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Product $related_product, Request $request)
    {
        $deleted = $this->service->delete($product, $related_product, $request);
        if ($deleted) {
            return $this->show($product, 'Product Hierarchy deleted successfully.');
        }
        return $this->sendError('Product hierarchy does not exist.', [], 500);
    }

    /**
     * Return the list of ProductHierarchy
     *
     * @return \Illuminate\Http\Response
     */
    public function showHierarchyRelationTypesOpts()
    {
        return $this->sendResponse(
            $this->service->hierarchyRelationTypesOptionList(),
            'Product hierarchy relation types retrieved successfully.'
        );
    }
}
