<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToProductHierarchiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_hierarchies', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('related_product_id')->references('id')->on('products')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('relation_type')->references('id')->on('product_hierarchies_relation_types')->onUpdate('NO ACTION')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_hierarchies', function (Blueprint $table) {
            $table->dropForeign('product_hierarchies_product_id_foreign');
            $table->dropForeign('product_hierarchies_related_product_id_foreign');
            $table->dropForeign('product_hierarchies_relation_type_foreign');
        });
    }
}
