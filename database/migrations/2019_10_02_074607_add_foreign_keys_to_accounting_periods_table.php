<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAccountingPeriodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('accounting_periods', function(Blueprint $table)
		{
			$table->foreign('fiscal_year_id')->references('id')->on('fiscal_years')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('accounting_periods', function(Blueprint $table)
		{
			$table->dropForeign('accounting_periods_fiscal_year_id_foreign');
			$table->dropForeign('accounting_periods_tenant_id_foreign');
		});
	}

}
