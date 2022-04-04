<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSubscriptionLinePricesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subscription_line_prices', function(Blueprint $table)
		{
			$table->foreign('parent_plan_line_id')->references('id')->on('plan_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('subscription_line_id')->references('id')->on('subscription_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subscription_line_prices', function(Blueprint $table)
		{
			$table->dropForeign('subscription_line_prices_parent_plan_line_id_foreign');
			$table->dropForeign('subscription_line_prices_subscription_line_id_foreign');
		});
	}

}
