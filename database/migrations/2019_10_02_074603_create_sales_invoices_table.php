<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_invoices', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('invoice_no', 191)->nullable();
			$table->date('date')->nullable();
			$table->date('due_date')->nullable();
			$table->string('description', 191)->nullable();
			$table->bigInteger('tenant_id')->unsigned()->nullable()->index('sales_invoices_tenant_id_foreign');
			$table->bigInteger('relation_id')->unsigned()->nullable()->index();
			$table->bigInteger('shipping_person_id')->unsigned()->nullable()->index();
			$table->bigInteger('invoice_person_id')->unsigned()->nullable()->index();
			$table->bigInteger('invoice_address_id')->unsigned()->nullable()->index();
			$table->bigInteger('shipping_address_id')->unsigned()->nullable()->index();
			$table->decimal('price', 12, 5)->nullable();
			$table->decimal('price_vat', 12, 5)->nullable();
			$table->decimal('price_total', 12, 5)->nullable();
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
		Schema::drop('sales_invoices');
	}

}
