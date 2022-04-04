<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAreaCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('area_codes', function(Blueprint $table)
		{
			$table->string('id', 35)->primary();
			$table->string('layer_1', 95)->nullable();
			$table->string('layer_2', 95)->nullable();
			$table->string('sub_area', 95)->nullable();
			$table->bigInteger('max_speed_down')->unsigned()->nullable();
			$table->bigInteger('max_speed_up')->unsigned()->nullable();
			$table->smallInteger('status')->unsigned();
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
		Schema::drop('area_codes');
	}

}
