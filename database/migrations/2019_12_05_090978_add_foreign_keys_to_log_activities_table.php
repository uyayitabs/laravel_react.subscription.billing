<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToLogActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('log_activities', function(Blueprint $table)
		{
			$table->bigInteger('user_id')->unsigned()->nullable()->change()->nullable();
			$table->bigInteger('facility_id')->unsigned()->nullable()->change();
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('facility_id')->references('id')->on('facility')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('log_activities', function(Blueprint $table)
		{
			$table->dropForeign('log_activities_user_id_foreign');
			$table->dropForeign('log_activities_facility_id_foreign');
		});
	}

}
