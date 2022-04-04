<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlanLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plan_lines', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('plan_id')->unsigned()->nullable()->index();
			$table->bigInteger('product_id')->unsigned()->nullable()->index();
			$table->bigInteger('plan_line_type')->unsigned()->nullable();
			$table->bigInteger('parent_plan_line_id')->unsigned()->nullable()->index();
			$table->boolean('mandatory_line')->nullable();
			$table->dateTime('plan_start')->nullable();
			$table->dateTime('plan_stop')->nullable();
			$table->string('description')->nullable();
			$table->text('description_long', 65535)->nullable();
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
		Schema::drop('plan_lines');
	}

}
