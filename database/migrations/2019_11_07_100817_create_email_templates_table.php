<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEmailTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('email_templates', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('tenant_id')->unsigned()->nullable()->index('email_templates_tenant_id_foreign');
			$table->string('type', 55)->nullable()->comment('invoice,m7,etc');
			$table->string('from_name', 191)->nullable();
			$table->string('from_email', 191)->nullable();
			$table->text('bcc_email')->nullable();
			$table->string('subject', 191)->nullable();
			$table->longtext('body_html')->nullable();
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
		Schema::drop('email_templates');
	}

}
