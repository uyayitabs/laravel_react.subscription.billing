<?php

namespace App\Services;

use App\Models\ProductHierarchy;
use App\Models\ProductHierarchyRelationType;
use Carbon\Carbon;
use Logging;
use App\Models\Product;
use App\Models\TenantProduct;
use App\Models\BackendApi;
use App\Filters\TenantProductPriceSortFilter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedSort;

class ProductHierarchyService
{
    /**
     * Create ProductHierarchy
     *
     * @param Product $product
     * @param Product $related_product
     * @param array $data
     * @return array
     */
    public function create(Product $product, Product $related_product, array $data): array
    {
        $productHierarchy = ProductHierarchy::where([['product_id', $product->id], ['related_product_id', $related_product->id]]);
        if ($productHierarchy->exists()) {
            return [
                'success' => false,
                'message' => 'Product hierarchy already exists.'
            ];
        }
        $data['product_id'] = $product->id;
        $data['related_product_id'] = $related_product->id;
        $productHierarchy = ProductHierarchy::create($data);
        Logging::information('Created Product Hierarchy', ['product_hierarchy' => $productHierarchy->toArray()], 1, 1);
        return [
            'success' => true,
            'message' => 'Product hierarchy created successfully.'
        ];
    }

    /**
     * Get product hierarchy tree of a product
     *
     * @param Product $product
     * @return array
     */
    public function get(Product $product): array
    {
        // ProductHierarchies per product_id
        $mainProductIds = ProductHierarchy::where('product_id', $product->id)->pluck('product_id')->unique()->toArray();
        $mainProductTree = $this->getProductHierarchyTree($mainProductIds);

        // ProductHierarchies per related_product_id
        $relatedProductIds =  ProductHierarchy::where('related_product_id', $product->id)->pluck('product_id')->unique()->toArray();
        $relatedProductTree = $this->getProductHierarchyTree($relatedProductIds);

        return array_unique(array_merge($mainProductTree, $relatedProductTree), SORT_REGULAR);
    }

    /**
     * Update product hierarchy
     *
     * @param Product $product
     * @param Product $related_product
     * @param array $data
     * @return array
     */
    public function update(Product $product, Product $related_product, array $data): array
    {
        $productHierarchy = ProductHierarchy::where([['product_id', $product->id], ['related_product_id', $related_product->id]]);
        if ($productHierarchy->doesntExist()) {
            return [
                'success' => false,
                'message' => 'Product hierarchy does not exist.'
            ];
        }
        $log = [];
        $productHierarchy = $productHierarchy->first();
        $log['old_values'] = $productHierarchy->toArray();
        if (array_key_exists('relation_type', $data)) {
            $productHierarchy->relation_type = $data['relation_type'];
        }
        if (array_key_exists('json_data', $data)) {
            $productHierarchy->json_data = $data['json_data'];
        }
        $productHierarchy->save();

        $log['new_values'] = $productHierarchy->toArray();
        $log['changes'] = $productHierarchy->getChanges();

        Logging::information('Updated Product Hierarchy', $log, 1, 1, currentTenant('id'), 'product', $productHierarchy->product_id);

        return [
            'success' => true,
            'message' => 'Product hierarchy updated successfully.'
        ];
    }

    /**
     * Delete product hierarchy
     *
     * @param Product $product
     * @param Product $related_product
     * @param Request $request
     * @return array|null
     */
    public function delete(Product $product, Product $related_product, Request $request): ?bool
    {
        $productHierarchy = ProductHierarchy::where([['product_id', $product->id], ['related_product_id', $related_product->id]]);
        if ($productHierarchy->doesntExist()) {
            return null;
        }
        $productHierarchy = $productHierarchy->first();
        Logging::information('Deleted Product Hierarchy', $productHierarchy->toArray(), 1, 1, currentTenant('id'), 'product', $product->id);
        $productHierarchy->delete();
        return true;
    }

    /**
     * Get the product hierarchy
     *
     * @param array $mainProductIds
     * @return array
     */
    protected function getProductHierarchyTree(array $mainProductIds)
    {
        $productHierarchyTree = [];
        foreach ($mainProductIds as $productId) {
            $mainProductTree = [
                'product_id' => $productId,
                'description' => Product::find($productId)->description,
                'related_products' => []
            ];
            $productHierarchyTree[] = $this->getRelatedProductTree($productId, $mainProductTree);
        }
        return $productHierarchyTree;
    }

    /**
     * Get the related product hierarchy
     *
     * @param int $relatedProductId
     * @param array $relatedProductTree
     * @return array
     */
    protected function getRelatedProductTree(int $relatedProductId, array &$relatedProductTree): array
    {
        foreach (ProductHierarchy::where('product_id', $relatedProductId)->get() as $relatedProductHierarchy) {
            $hierarchyRelationType = $relatedProductHierarchy->relationType;
            $thisRelatedProductTree = [
                'product_id' => $relatedProductHierarchy->related_product_id,
                'description' => $relatedProductHierarchy->relatedProduct->description,
                'json_data' => $relatedProductHierarchy->json_data,
                'relation_type' => [
                    'id' => $hierarchyRelationType->id,
                    'type' => $hierarchyRelationType->type,
                    'description' => $hierarchyRelationType->description
                ],
                'related_products' => [],
            ];
            // Get related product sub-tree using [related_product_id]
            if (ProductHierarchy::where('product_id', $relatedProductHierarchy->related_product_id)->count()) {
                $thisRelatedProductSubTree = [];
                $this->getRelatedProductTree($relatedProductHierarchy->related_product_id, $thisRelatedProductSubTree);
                $thisRelatedProductTree['related_products'] = $thisRelatedProductSubTree['related_products'];
            }
            $relatedProductTree['related_products'][] = $thisRelatedProductTree;
        }
        return $relatedProductTree;
    }

    /**
     * Get Product Hierarchy Relation Types
     *
     * @return array
     */
    public function hierarchyRelationTypesOptionList(): array
    {
        return ProductHierarchyRelationType::select('id', 'type', 'description')->get()->toArray();
    }
}
