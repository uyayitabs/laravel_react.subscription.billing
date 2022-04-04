<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSalesInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sales_invoices', function(Blueprint $table)
		{
			$table->foreign('invoice_person_id')->references('id')->on('persons')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('shipping_person_id')->references('id')->on('persons')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('sales_invoices', function(Blueprint $table)
		{
			$table->dropForeign('sales_invoices_invoice_person_id_foreign');
			$table->dropForeign('sales_invoices_shipping_person_id_foreign');
			$table->dropForeign('sales_invoices_tenant_id_foreign');
		});
	}

}
