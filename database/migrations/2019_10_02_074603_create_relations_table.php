<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRelationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('relations', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('tenant_id')->unsigned()->nullable()->index('relations_tenant_id_foreign');
			$table->bigInteger('relation_type_id')->unsigned()->nullable()->index();
			$table->string('company_name')->nullable();
			$table->string('customer_number', 50)->nullable();
			$table->string('type', 191)->nullable();
			$table->boolean('status')->nullable();
			$table->string('kvk', 45)->nullable();
			$table->string('email', 191)->nullable();
			$table->string('phone', 45)->nullable();
			$table->string('fax', 45)->nullable();
			$table->string('website', 191)->nullable();
			$table->string('vat_no', 45)->nullable();
			$table->string('bank_account', 45)->nullable();
			$table->string('iban', 45)->nullable();
			$table->string('bic', 45)->nullable();
			$table->decimal('credit_limit')->nullable();
			$table->string('payment_conditions', 45)->nullable();
			$table->binary('info', 65535)->nullable();
			$table->boolean('is_business')->nullable()->default(0);
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
		Schema::drop('relations');
	}

}
