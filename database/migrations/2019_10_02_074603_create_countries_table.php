<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCountriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('countries', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('numeric', 3);
			$table->string('alpha2', 2)->nullable();
			$table->string('alpha3', 3)->nullable();
			$table->string('name', 191);
			$table->string('official_name', 191);
			$table->string('sovereignty', 3)->nullable();
			$table->integer('dial_code')->nullable();
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
		Schema::drop('countries');
	}

}
