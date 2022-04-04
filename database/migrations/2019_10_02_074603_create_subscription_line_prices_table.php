<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionLinePricesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscription_line_prices', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('subscription_line_id')->unsigned()->index();
			$table->bigInteger('parent_plan_line_id')->unsigned()->nullable()->index();
			$table->decimal('fixed_price', 12, 5)->default(0.00);
			$table->decimal('margin', 2)->default(0.00);
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
		Schema::drop('subscription_line_prices');
	}

}
