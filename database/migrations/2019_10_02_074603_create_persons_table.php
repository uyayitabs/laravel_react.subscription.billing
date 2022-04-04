<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePersonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('persons', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('person_type_id')->unsigned()->index();
			$table->string('status', 191);
			$table->string('gender', 191);
			$table->string('title', 191)->nullable();
			$table->string('initials', 16)->nullable();
			$table->string('first_name', 191);
			$table->string('middle_name', 191)->nullable();
			$table->string('last_name', 191);
			$table->date('birthdate')->nullable();
			$table->string('email', 191)->nullable();
			$table->string('phone', 191)->nullable();
			$table->string('mobile', 191)->nullable();
			$table->string('language', 191)->nullable();
			$table->string('linkedin', 191)->nullable();
			$table->string('facebook', 191)->nullable();
			$table->boolean('primary')->nullable();
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
		Schema::drop('persons');
	}

}
