<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateZipcodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('zipcodes', function(Blueprint $table)
		{
			$table->string('id', 35)->primary();
			$table->string('zipcode', 10);
			$table->string('housenumber', 35)->nullable();
			$table->string('housenumber_suffix', 35)->nullable();
			$table->string('room', 35)->nullable();
			$table->string('street1', 95)->nullable();
			$table->string('street2', 95)->nullable();
			$table->string('city', 35);
			$table->bigInteger('country_id')->unsigned();
			$table->decimal('latitude')->nullable();
			$table->decimal('longitude')->nullable();
			$table->decimal('rdxCoordinate')->nullable();
			$table->decimal('rdyCoordinate')->nullable();
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
		Schema::drop('zipcodes');
	}

}
