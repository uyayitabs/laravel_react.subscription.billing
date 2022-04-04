<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubscriptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscriptions', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('subscription_no', 50)->nullable();
			$table->string('description', 191)->nullable();
			$table->text('description_long', 65535)->nullable();
			$table->string('type', 191)->nullable();
			$table->bigInteger('relation_id')->unsigned()->index();
			$table->bigInteger('plan_id')->unsigned()->nullable()->index();
			$table->bigInteger('billing_person')->unsigned()->nullable()->index();
			$table->bigInteger('provisioning_person')->unsigned()->nullable()->index();
			$table->bigInteger('billing_address')->unsigned()->nullable()->index();
			$table->bigInteger('provisioning_address')->unsigned()->nullable()->index();
			$table->date('subscription_start')->nullable();
			$table->date('subscription_stop')->nullable();
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
		Schema::drop('subscriptions');
	}

}
