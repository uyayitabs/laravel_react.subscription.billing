<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoicePdfTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pdf_templates', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('tenant_id')->unsigned()->nullable()->index('pdf_templates_tenant_id_foreign');
			$table->longText('header_html')->nullable();
			$table->longText('main_html')->nullable();
			$table->longText('footer_html')->nullable();
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
		Schema::drop('pdf_templates');
	}

}
