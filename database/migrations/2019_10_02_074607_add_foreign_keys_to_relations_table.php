<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('relations', function(Blueprint $table)
		{
			$table->foreign('relation_type_id')->references('id')->on('relation_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
		Schema::table('relations', function(Blueprint $table)
		{
			$table->dropForeign('relations_relation_type_id_foreign');
			$table->dropForeign('relations_tenant_id_foreign');
		});
	}

}
