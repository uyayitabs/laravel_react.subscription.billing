<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketTimeLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket_time_logs', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('ticket_id')->unsigned()->nullable();
			$table->bigInteger('ticket_message_id')->unsigned()->nullable();
			$table->bigInteger('user_id')->unsigned()->nullable();
			$table->dateTime('starttime')->nullable();
			$table->dateTime('endtime')->nullable();
			$table->decimal('billability', 4, 2)->nullable();
			$table->longText('notes')->nullable();
			$table->timestamps();
		});

		Schema::table('ticket_time_logs', function(Blueprint $table)
		{
			$table->foreign('ticket_id')->references('id')->on('tickets')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('ticket_message_id')->references('id')->on('ticket_messages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ticket_time_logs');
	}

}
