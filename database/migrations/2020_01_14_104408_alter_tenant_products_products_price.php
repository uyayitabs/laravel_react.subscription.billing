<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterTenantProductsProductsPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_products', function($table)
        {
            $table->decimal('price', 12, 5)->nullable()->after('product_id');;
        });
        
        // DB::statement('UPDATE tenant_products `tp` LEFT JOIN products `p` ON tp.product_id = p.id SET tp.price = p.price');

        // Schema::table('products', function(Blueprint $table)
		// {
        //     $table->dropColumn('price');
		// });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        // Schema::table('products', function($table)
        // {
        //     $table->decimal('price', 12, 5)->nullable()->after('active_from');;
        // });

        // DB::statement('UPDATE products `p` LEFT JOIN tenant_products `tp` ON p.id = tp.product_id SET p.price=tp.price');
 
        Schema::table('tenant_products', function(Blueprint $table)
		{
            $table->dropColumn('price');
        });
    }
}
