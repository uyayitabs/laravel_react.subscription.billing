<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketAttachmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket_attachments', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('ticket_message_id')->unsigned()->nullable();
			$table->longText('url')->nullable();
			$table->timestamps();
		});

		Schema::table('ticket_attachments', function(Blueprint $table)
		{
			$table->foreign('ticket_message_id')->references('id')->on('ticket_messages')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ticket_attachments');
	}

}
