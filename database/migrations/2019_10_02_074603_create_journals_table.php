<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJournalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('journals', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('journal_no', 191)->nullable();
			$table->bigInteger('tenant_id')->unsigned()->index('journals_tenant_id_foreign_idx');
			$table->bigInteger('invoice_id')->unsigned()->nullable()->index('entries_invoice_foreign_idx');
			$table->date('date');
			$table->string('description', 120)->nullable();
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
		Schema::drop('journals');
	}

}
