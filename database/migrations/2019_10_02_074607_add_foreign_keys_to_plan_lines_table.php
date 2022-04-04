<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPlanLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('plan_lines', function(Blueprint $table)
		{
			$table->foreign('parent_plan_line_id')->references('id')->on('plan_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('product_id')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('plan_lines', function(Blueprint $table)
		{
			$table->dropForeign('plan_lines_parent_plan_line_id_foreign');
			$table->dropForeign('plan_lines_plan_id_foreign');
			$table->dropForeign('plan_lines_product_id_foreign');
		});
	}

}
