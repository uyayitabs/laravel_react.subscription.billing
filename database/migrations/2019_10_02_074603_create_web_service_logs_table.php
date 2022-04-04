<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWebServiceLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('web_service_logs', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('provider', 191);
			$table->string('ip', 191);
			$table->string('token', 191)->nullable();
			$table->json('req_data')->nullable();
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
		Schema::drop('web_service_logs');
	}

}
