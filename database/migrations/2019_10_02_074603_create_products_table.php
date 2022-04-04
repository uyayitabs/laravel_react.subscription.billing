<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('product_type_id')->unsigned()->nullable();
			$table->string('serialized', 1)->nullable();
			$table->string('status', 45)->nullable();
			$table->string('description', 191)->nullable();
			$table->text('description_long', 65535)->nullable();
			$table->string('vendor', 191)->nullable();
			$table->string('vendor_partcode', 45)->nullable();
			$table->decimal('weight', 5, 2)->unsigned()->nullable();
			$table->string('ean_code', 40)->nullable();
			$table->date('active_from')->nullable();
			$table->decimal('price', 12, 5)->nullable();
			$table->string('backend_api', 45)->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products');
	}

}
