<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAccountsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('accounts', function(Blueprint $table)
		{
			$table->foreign('parent_id', 'accounts_parent_id_foreign')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('tenant_id', 'accounts_tenants_foreign')->references('id')->on('tenants')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('accounts', function(Blueprint $table)
		{
			$table->dropForeign('accounts_parent_id_foreign');
			$table->dropForeign('accounts_tenants_foreign');
		});
	}

}
