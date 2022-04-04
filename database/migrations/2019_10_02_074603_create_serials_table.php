<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSerialsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('serials', function(Blueprint $table)
		{
			$table->bigInteger('product_id')->unsigned();
			$table->string('serial', 50);
			$table->bigInteger('warehouse_id')->unsigned()->index();
			$table->json('json_data')->comment("{\"serial\": {\"mac\": \"\", \"serial\": \"\"}}")->nullable();
			$table->primary(['product_id','serial']);
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
		Schema::drop('serials');
	}

}
