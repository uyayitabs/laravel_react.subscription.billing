<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('entries', function(Blueprint $table)
		{
			$table->foreign('account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('relation_id')->references('id')->on('relations')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('invoice_id', 'entries_invoice_foreign')->references('id')->on('sales_invoices')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('invoice_line_id', 'entries_invoice_line_foreign')->references('id')->on('sales_invoice_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('journal_id')->references('id')->on('journals')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('vatcode_id')->references('id')->on('vat_codes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('entries', function(Blueprint $table)
		{
			$table->dropForeign('entries_account_id_foreign');
			$table->dropForeign('entries_relation_id_foreign');
			$table->dropForeign('entries_invoice_foreign');
			$table->dropForeign('entries_invoice_line_foreign');
			$table->dropForeign('entries_journal_id_foreign');
			$table->dropForeign('entries_vatcode_id_foreign');
		});
	}

}
