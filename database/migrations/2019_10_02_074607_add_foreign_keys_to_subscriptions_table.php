<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSubscriptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subscriptions', function(Blueprint $table)
		{
			$table->foreign('billing_address')->references('id')->on('addresses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('billing_person')->references('id')->on('persons')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('plan_id')->references('id')->on('plans')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('provisioning_address')->references('id')->on('addresses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('provisioning_person')->references('id')->on('persons')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('relation_id')->references('id')->on('relations')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subscriptions', function(Blueprint $table)
		{
			$table->dropForeign('subscriptions_billing_address_foreign');
			$table->dropForeign('subscriptions_billing_person_foreign');
			$table->dropForeign('subscriptions_plan_id_foreign');
			$table->dropForeign('subscriptions_provisioning_address_foreign');
			$table->dropForeign('subscriptions_provisioning_person_foreign');
			$table->dropForeign('subscriptions_relation_id_foreign');
		});
	}

}
