<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('entries', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('entry_no', 191)->nullable();
			$table->date('date');
			$table->string('description', 120)->nullable();
			$table->bigInteger('journal_id')->unsigned()->index('entries_journal_id_foreign_idx');
			$table->bigInteger('relation_id')->unsigned()->nullable()->index('entries_relation_id_foreign_idx');
			$table->bigInteger('invoice_id')->unsigned()->nullable()->index('entries_invoice_id_foreign_idx');
			$table->bigInteger('invoice_line_id')->unsigned()->nullable()->index('entries_invoice_line_foreign_idx');
			$table->bigInteger('account_id')->unsigned()->nullable()->index('accounting_transactions_account_id_foreign_idx');
			$table->bigInteger('period_id')->unsigned()->nullable();
			$table->decimal('credit', 12, 5)->nullable()->default(0.00000);
			$table->decimal('debit', 12, 5)->nullable()->default(0.00000);
			$table->bigInteger('vatcode_id')->unsigned()->nullable()->index('entries_vatcode_id_foreign_idx');
			$table->decimal('vat_percentage', 5)->nullable();
			$table->decimal('vat_amount', 12, 5)->default(0.00000)->nullable();
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
		Schema::drop('entries');
	}

}
