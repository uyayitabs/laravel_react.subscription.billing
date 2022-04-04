<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTenantProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tenant_products', function(Blueprint $table)
		{
			$table->bigInteger('tenant_id')->unsigned();
			$table->bigInteger('product_id')->unsigned()->index('company_products_product_id_index');
			$table->bigInteger('vat_code_id')->unsigned();
			$table->bigInteger('account_id')->unsigned()->nullable();
			$table->boolean('status')->nullable();
			$table->timestamps();
			$table->primary(['tenant_id','product_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tenant_products');
	}

}
