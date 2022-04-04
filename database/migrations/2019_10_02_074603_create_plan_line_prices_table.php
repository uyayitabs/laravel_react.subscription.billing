<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlanLinePricesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plan_line_prices', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('plan_line_id')->unsigned()->nullable()->index();
			$table->bigInteger('parent_plan_line_id')->unsigned()->nullable()->index();
			$table->decimal('fixed_price', 12, 5)->nullable();
			$table->decimal('margin')->nullable();
			$table->dateTime('price_valid_from')->nullable();
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
		Schema::drop('plan_line_prices');
	}

}
