<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTicketKnowledgeBaseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ticket_knowledge_base', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->string('category');
			$table->bigInteger('tenant_id')->unsigned()->nullable()->index();
			$table->longText('question')->nullable();
			$table->longText('answer')->nullable();
			$table->string('keywords')->nullable();
			$table->timestamps();
		});

		Schema::table('ticket_knowledge_base', function(Blueprint $table)
		{
			$table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ticket_knowledge_base');
	}

}
