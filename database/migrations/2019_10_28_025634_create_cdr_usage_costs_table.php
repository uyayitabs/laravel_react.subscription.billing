<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCdrUsageCostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cdr_usage_costs', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('unique_id', 50)->nullable();
			$table->string('customer_number', 50)->nullable();
			$table->bigInteger('relation_id')->unsigned()->nullable()->index('usage_costs_relation_id_foreign_idx');
			$table->bigInteger('subscription_id')->unsigned()->nullable()->index('usage_costs_subscription_id_foreign_idx');
			$table->bigInteger('sales_invoice_line_id')->unsigned()->nullable()->index('usage_costs_sales_invoice_line_id_foreign_idx');
			$table->string('channel_id', 50)->nullable();
			$table->date('date')->nullable();
			$table->time('time')->nullable();
			$table->string('sender', 50)->nullable();
			$table->string('recipient', 50)->nullable();
			$table->integer('duration')->nullable();
			$table->string('platform', 50)->nullable();
			$table->decimal('total_cost', 12, 5)->nullable();
			$table->decimal('start_cost', 12, 5)->nullable();
			$table->decimal('minute_cost', 12, 5)->nullable();
			$table->integer('traffic_class')->nullable();
			$table->string('direction', 50)->nullable();
			$table->string('extension', 50)->nullable();
			$table->string('roaming', 50)->nullable();
			$table->string('bundle', 50)->nullable();
			$table->string('order_number', 50)->nullable();
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
		Schema::drop('cdr_usage_costs');
	}

}
