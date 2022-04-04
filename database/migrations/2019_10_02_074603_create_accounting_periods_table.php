<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAccountingPeriodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('accounting_periods', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('tenant_id')->unsigned()->index('accounting_periods_tenant_id_foreign_idx');
			$table->bigInteger('fiscal_year_id')->unsigned()->index('accounting_periods_fiscal_year_id_foreign_idx');
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
		Schema::drop('accounting_periods');
	}

}
