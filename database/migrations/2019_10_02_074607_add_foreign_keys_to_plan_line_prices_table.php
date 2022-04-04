<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPlanLinePricesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('plan_line_prices', function(Blueprint $table)
		{
			$table->foreign('parent_plan_line_id')->references('id')->on('plan_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('plan_line_id')->references('id')->on('plan_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('plan_line_prices', function(Blueprint $table)
		{
			$table->dropForeign('plan_line_prices_parent_plan_line_id_foreign');
			$table->dropForeign('plan_line_prices_plan_line_id_foreign');
		});
	}

}
