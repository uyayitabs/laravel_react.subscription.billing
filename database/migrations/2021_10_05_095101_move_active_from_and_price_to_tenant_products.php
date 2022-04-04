<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveActiveFromAndPriceToTenantProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenant_products', function (Blueprint $table) {
            $table->date('active_from')->after('status')->default(\Carbon\Carbon::now()->format('y-m-d'));
            $table->date('active_to')->after('active_from')->nullable();
        });

        DB::statement('update tenant_products tp inner join products p on p.id = tp.product_id set tp.active_from = p.active_from where p.active_from is not null;');
        DB::statement('update tenant_products tp inner join products p on p.id = tp.product_id set tp.price = p.price;');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('active_from');
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('active_from')->after('ean_code')->default(\Carbon\Carbon::now()->format('y-m-d'));
            $table->decimal('price')->after('active_from')->nullable();
        });

        DB::statement('update products p inner join tenant_products tp on p.id = tp.product_id set p.active_from = tp.active_from where tp.active_from is not null;');
        DB::statement('update products p inner join tenant_products tp on p.id = tp.product_id set p.price = tp.price;');

        Schema::table('tenant_products', function (Blueprint $table) {
            $table->dropColumn('active_from');
            $table->dropColumn('active_to');
        });
    }
}
