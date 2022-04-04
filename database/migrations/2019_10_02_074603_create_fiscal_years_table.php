<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFiscalYearsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fiscal_years', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('tenant_id')->unsigned()->index('accounting_fiscalyear_tenant_id_foreign_idx');
			$table->string('description', 45);
			$table->date('date_from');
			$table->date('date_to');
			$table->boolean('is_closed')->default(0);
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
		Schema::drop('fiscal_years');
	}

}
