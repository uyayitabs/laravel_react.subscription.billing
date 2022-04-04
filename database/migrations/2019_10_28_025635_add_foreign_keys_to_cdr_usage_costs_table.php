<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCdrUsageCostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('cdr_usage_costs', function(Blueprint $table)
		{
			$table->foreign('subscription_id')->references('id')->on('subscriptions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('sales_invoice_line_id')->references('id')->on('sales_invoice_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('cdr_usage_costs', function(Blueprint $table)
		{
			$table->dropForeign('cdr_usage_costs_subscription_id_foreign');
			$table->dropForeign('cdr_usage_costs_sales_invoice_line_id_foreign');
		});
	}

}
