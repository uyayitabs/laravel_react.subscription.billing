<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRelationsPersonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('relations_persons', function(Blueprint $table)
		{
			$table->bigInteger('relation_id')->unsigned()->index('relations_persons_relation_id_foreign');
			$table->bigInteger('person_id')->unsigned()->index('relations_persons_person_id_foreign');
			$table->string('status', 191);
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
		Schema::drop('relations_persons');
	}

}
