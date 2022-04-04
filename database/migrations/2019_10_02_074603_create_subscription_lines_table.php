<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscription_lines', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('subscription_id')->unsigned()->nullable()->index();
			$table->bigInteger('subscription_line_type')->unsigned()->nullable()->index();
			$table->bigInteger('plan_line_id')->unsigned()->nullable()->index();
			$table->bigInteger('plan_id')->unsigned()->nullable()->index();
			$table->bigInteger('product_id')->unsigned()->nullable()->index();
			$table->string('serial', 50)->nullable();
			$table->boolean('mandatory_line')->nullable();
			$table->date('subscription_start')->nullable();
			$table->date('subscription_stop')->nullable();
			$table->string('description', 191)->nullable();
			$table->text('description_long', 65535)->nullable();
			$table->bigInteger('mind_id')->nullable();
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
		Schema::drop('subscription_lines');
	}

}
