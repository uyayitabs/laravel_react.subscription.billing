<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRelationsPersonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('relations_persons', function(Blueprint $table)
		{
			$table->foreign('person_id')->references('id')->on('persons')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('relation_id')->references('id')->on('relations')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('relations_persons', function(Blueprint $table)
		{
			$table->dropForeign('relations_persons_person_id_foreign');
			$table->dropForeign('relations_persons_relation_id_foreign');
		});
	}

}
