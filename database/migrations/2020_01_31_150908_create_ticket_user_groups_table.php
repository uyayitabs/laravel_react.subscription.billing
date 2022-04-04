<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketUserGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket_user_groups', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('ticket_group_id')->unsigned()->nullable();
			$table->bigInteger('user_id')->unsigned()->nullable();
			$table->timestamps();
		});

		Schema::table('ticket_user_groups', function(Blueprint $table)
		{
			$table->foreign('ticket_group_id')->references('id')->on('ticket_groups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});

		Schema::table('tickets', function(Blueprint $table)
		{
			$table->foreign('ticket_group_id')->references('id')->on('ticket_groups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tickets', function(Blueprint $table)
		{
			$table->dropForeign('tickets_ticket_group_id_foreign');
		});

		Schema::drop('ticket_user_groups');
	}

}
