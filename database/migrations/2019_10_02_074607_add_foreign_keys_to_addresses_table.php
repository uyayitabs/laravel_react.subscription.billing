<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAddressesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('addresses', function(Blueprint $table)
		{
			$table->foreign('address_type_id')->references('id')->on('address_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('relation_id')->references('id')->on('relations')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('addresses', function(Blueprint $table)
		{
			$table->dropForeign('addresses_address_type_id_foreign');
			$table->dropForeign('addresses_relation_id_foreign');
		});
	}

}
