<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJsonDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('json_data', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('tenant_id')->unsigned()->nullable()->index();
			$table->bigInteger('relation_id')->unsigned()->nullable()->index();
			$table->bigInteger('plan_id')->unsigned()->nullable()->index();
			$table->bigInteger('plan_line_id')->unsigned()->nullable()->index();
			$table->bigInteger('subscription_id')->unsigned()->nullable()->index();
			$table->bigInteger('subscription_line_id')->unsigned()->nullable()->index();
			$table->bigInteger('product_id')->nullable();
			$table->json('json_data')->comment("{\"m7\": {\"type\": \"basis|addon|stb\", \"productId\": \"\"}}");
			$table->string('transaction_id', 191)->nullable()->index();
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
		Schema::drop('json_data');
	}

}
