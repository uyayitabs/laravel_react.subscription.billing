<?php

use Illuminate\Database\Seeder;
use App\Product;
use App\TenantProduct;
use App\VatCode;

class TenantProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $query = TenantProduct::where('product_id', $product->id);
            $query2 = clone $query;
            $tenantProductFiber = $query->where('tenant_id', 7)->first();
            $tenantProductStipte = $query2->where('tenant_id', 8)->first();

            if (!$tenantProductFiber) {
                $vatCode0 = VatCode::where([
                    ['tenant_id', '=', 7],
                    ['vat_percentage', '=', '0.00']
                ])->first();

                $vatCode21 = VatCode::where([
                    ['tenant_id', '=', 7],
                    ['vat_percentage', '=', '0.21']
                ])->first();

                TenantProduct::create([
                    'product_id' => $product->id,
                    'tenant_id' => 7,
                    'price' => $product->price,
                    'status' => 1,
                    'vat_code_id' => $product->product_type_id == 3 || $product->product_type_id == 4 || $product->product_type_id == 5 ? $vatCode21->id : $vatCode0->id
                ]);
            } else {
                if (!$tenantProductFiber->price) {
                    $tenantProductFiber->price = $product->price;
                    $tenantProductFiber->save();
                }
            }

            if (!$tenantProductStipte) {
                $vatCode0 = VatCode::where([
                    ['tenant_id', '=', 8],
                    ['vat_percentage', '=', '0.00']
                ])->first();

                $vatCode21 = VatCode::where([
                    ['tenant_id', '=', 8],
                    ['vat_percentage', '=', '0.21']
                ])->first();

                TenantProduct::create([
                    'product_id' => $product->id,
                    'tenant_id' => 8,
                    'price' => $product->price,
                    'status' => 1,
                    'vat_code_id' => $product->product_type_id == 3 || $product->product_type_id == 4 || $product->product_type_id == 5 ? $vatCode21->id : $vatCode0->id
                ]);
            } else {
                if (!$tenantProductStipte->price) {
                    $tenantProductStipte->price = $product->price;
                    $tenantProductStipte->save();
                }
            }
        }
    }
}
