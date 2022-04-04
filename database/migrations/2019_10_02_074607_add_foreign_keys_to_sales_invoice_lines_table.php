<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSalesInvoiceLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sales_invoice_lines', function(Blueprint $table)
		{
			$table->foreign('plan_line_id')->references('id')->on('plan_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('product_id', 'sales_invoices_lines_product_id_foreign')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('sales_invoice_id', 'sales_invoices_lines_sales_invoice_id_foreign')->references('id')->on('sales_invoices')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('subscription_line_id', 'sales_invoices_lines_subscription_line_id_foreign')->references('id')->on('subscription_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('vat_code', 'sales_invoices_lines_vat_code_foreign')->references('id')->on('vat_codes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sales_invoice_lines', function(Blueprint $table)
		{
			$table->dropForeign('sales_invoice_lines_plan_line_id_foreign');
			$table->dropForeign('sales_invoices_lines_product_id_foreign');
			$table->dropForeign('sales_invoices_lines_sales_invoice_id_foreign');
			$table->dropForeign('sales_invoices_lines_subscription_line_id_foreign');
			$table->dropForeign('sales_invoices_lines_vat_code_foreign');
		});
	}

}
