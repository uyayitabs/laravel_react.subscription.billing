<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToJournalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('journals', function(Blueprint $table)
		{
			$table->foreign('invoice_id')->references('id')->on('sales_invoices')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('journals', function(Blueprint $table)
		{
			$table->dropForeign('journals_invoice_id_foreign');
			$table->dropForeign('journals_tenant_id_foreign');
		});
	}

}
