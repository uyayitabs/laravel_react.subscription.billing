<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateZipcodeAreacodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('zipcode_areacodes', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('zipcode_id', 35)->nullable();
			$table->string('area_code_id', 35)->nullable();
			$table->string('status', 45);
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
		Schema::drop('zipcode_areacodes');
	}

}
