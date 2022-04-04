<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSubscriptionLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subscription_lines', function(Blueprint $table)
		{
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('plan_line_id')->references('id')->on('plan_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('subscription_id')->references('id')->on('subscriptions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('subscription_line_type')->references('id')->on('plan_subscription_line_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subscription_lines', function(Blueprint $table)
		{
			$table->dropForeign('subscription_lines_plan_id_foreign');
			$table->dropForeign('subscription_lines_plan_line_id_foreign');
			$table->dropForeign('subscription_lines_product_id_foreign');
			$table->dropForeign('subscription_lines_subscription_id_foreign');
			$table->dropForeign('subscription_lines_subscription_line_type_foreign');
		});
	}

}
