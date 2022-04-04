<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscription_data', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('subscription_id')->unsigned()->nullable();
			$table->bigInteger('subscription_line_id')->unsigned()->nullable();
			$table->bigInteger('relation_id')->unsigned()->nullable();
			$table->string('transaction_id', 191)->nullable();
			$table->string('vendor', 100)->nullable();
			$table->json('data')->nullable();
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
		Schema::drop('subscription_data');
	}

}
