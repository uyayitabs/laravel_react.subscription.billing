<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesInvoiceLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_invoice_lines', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('sales_invoice_id')->unsigned()->nullable()->index();
			$table->bigInteger('product_id')->unsigned()->nullable()->index();
			$table->bigInteger('order_line_id')->unsigned()->nullable()->index();
			$table->bigInteger('subscription_line_id')->unsigned()->nullable()->index();
			$table->date('invoice_start')->nullable();
			$table->date('invoice_stop')->nullable();
			$table->bigInteger('plan_line_id')->unsigned()->nullable()->index();
			$table->string('description', 191)->nullable();
			$table->text('description_long', 65535)->nullable();
			$table->decimal('price_per_piece', 12, 5)->nullable();
			$table->decimal('quantity')->nullable();
			$table->decimal('price', 12, 5)->nullable();
			$table->bigInteger('vat_code')->unsigned()->nullable()->index();
			$table->decimal('vat_percentage', 2)->unsigned()->nullable();
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
		Schema::drop('sales_invoice_lines');
	}

}
