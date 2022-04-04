<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVatCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vat_codes', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('tenant_id')->unsigned()->nullable()->index('vat_codes_tenant_id_foreign');
			$table->bigInteger('account_id')->unsigned()->nullable();
			$table->decimal('vat_percentage')->unsigned()->nullable();
			$table->string('description', 45)->nullable();
			$table->date('active_from')->nullable();
			$table->date('active_to')->nullable();
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
		Schema::drop('vat_codes');
	}

}
