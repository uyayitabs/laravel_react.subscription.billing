<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlanSubscriptionLineTypesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plan_subscription_line_types', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('line_type', 45);
			$table->string('description', 191);
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
		Schema::drop('plan_subscription_line_types');
	}

}
