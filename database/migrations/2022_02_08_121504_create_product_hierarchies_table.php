<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductHierarchiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_hierarchies', function (Blueprint $table) {
            $table->bigInteger('product_id')->unsigned()->index('product_hierarchies_product_id_foreign');
            $table->bigInteger('related_product_id')->unsigned()->index('product_hierarchies_related_product_id_foreign');
            $table->bigInteger('relation_type')->nullable()->unsigned()->index('product_hierarchies_relation_type_foreign');
            $table->json('json_data')->nullable();
            $table->timestamps();
            $table->primary(['product_id', 'related_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_hierarchies');
    }
}
