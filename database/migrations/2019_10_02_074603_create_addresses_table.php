<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('addresses', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('relation_id')->unsigned()->index();
			$table->bigInteger('address_type_id')->unsigned()->index();
			$table->string('street1', 95)->nullable();
			$table->string('street2', 95)->nullable();
			$table->string('house_number', 35)->nullable();
			$table->string('house_number_suffix', 35)->nullable();
			$table->string('room', 35)->nullable();
			$table->string('zipcode', 10)->nullable();
			$table->string('zipcode_id', 45)->nullable();
			$table->bigInteger('country_id')->unsigned();
			$table->boolean('primary')->nullable();
			$table->bigInteger('state')->nullable();
			$table->bigInteger('city_id')->nullable();
			$table->bigInteger('municipality')->nullable();
			$table->bigInteger('state_id')->nullable()->index();
			$table->index(['state','city_id','municipality']);
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
		Schema::drop('addresses');
	}

}
