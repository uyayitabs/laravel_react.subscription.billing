<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNumberRangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('number_ranges', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('tenant_id')->unsigned()->index('number_ranges_tenant_id_foreign');
			$table->string('type', 45);
			$table->string('description', 191);
			$table->bigInteger('start')->unsigned();
			$table->bigInteger('end')->unsigned();
			$table->string('format', 191);
			$table->string('randomized', 1);
			$table->bigInteger('current')->unsigned()->nullable();
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
		Schema::drop('number_ranges');
	}

}
