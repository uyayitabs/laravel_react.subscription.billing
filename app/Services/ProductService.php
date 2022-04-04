<?php

namespace App\Services;

use Carbon\Carbon;
use Logging;
use App\Models\Product;
use App\Models\TenantProduct;
use App\Models\BackendApi;
use App\Filters\TenantProductPriceSortFilter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedSort;

class ProductService
{
    /**
     * Return a paginated list of products
     *
     */
    public function list()
    {
        return \Querying::for(Product::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->whereHas('tenantProduct', function ($query) {
                $now = now()->format('y-m-d');
                $query->where([
                    ['tenant_id', 7],
                    ['active_from', '<', $now]])
                    ->where(function ($q) use ($now) {
                        $q->whereNull('active_to')->orWhere('active_to', '>', $now);
                    });
            });
    }

    /**
     * Store a newly created product
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        $product = Product::create($data);
        Logging::information('Create Product', $data, 1, 1);

        $attributes = request(TenantProduct::$fields);
        $product->tenantProducts()->create([
            'tenant_id' => currentTenant('id'),
            'status_id' => 1,
            'vat_code_id' => $attributes['vat_code_id'],
            'price' => $attributes['price']
        ]);

        return QueryBuilder::for(Product::where('id', $product->id))
            ->allowedIncludes(Product::$scopes);
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
        return QueryBuilder::for(Product::where('id', $id))
            ->allowedIncludes(Product::$scopes);
    }

    /**
     * @param array $data
     * @param Product $product
     * @return QueryBuilder
     */
    public function update(array $data, Product $product)
    {
        $log['old_values'] = $product->getRawDBData();
        $productAttributes = request(Product::$fields);
        $product->update($productAttributes);
        $log['new_values'] = $product->getRawDBData();
        $log['changes'] = $product->getChanges();

        Logging::information('Update Product', $log, 1, 1);

        $tenantProduct = TenantProduct::where([
            ['tenant_id', '=', currentTenant('id')],
            ['product_id', '=', $product->id],
        ])->first();

        $tenantProductAttributes = request(TenantProduct::$fields);
        if ($tenantProduct) {
            $tenantProduct->update($tenantProductAttributes);
        } else {
            $product->tenantProducts()->create([
                'tenant_id' => currentTenant('id'),
                'status_id' => 1,
                'vat_code_id' => $tenantProductAttributes['vat_code_id'],
                'price' => $tenantProductAttributes['price']
            ]);
        }

        return QueryBuilder::for(Product::where('id', $product->id))
            ->allowedIncludes(Product::$scopes);
    }

    /**
     * Return the list Product with id, description and product_type_id
     *
     * @return \Illuminate\Http\Response
     */
    public function optionList()
    {
        return Product::select('id', 'description', 'product_type_id');
    }

    /**
     * Return the Product count
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        $tenant = currentTenant();
        return $tenant ? $tenant->products->count() : 0;
    }

    public function backendApis()
    {
        return BackendApi::select(['backend_api', 'status'])->get();
    }
}
