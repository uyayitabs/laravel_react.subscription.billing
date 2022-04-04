<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('plans', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('tenant_id')->unsigned()->index('plans_tenant_id_foreign');
			$table->bigInteger('parent_plan')->unsigned()->nullable()->index();
			$table->string('plan_type', 45)->nullable();
			$table->string('area_code_id', 12)->nullable()->index();
			$table->bigInteger('project_id')->unsigned()->nullable()->index();
			$table->string('description', 191);
			$table->text('description_long', 65535)->nullable();
			$table->date('plan_start')->nullable();
			$table->date('plan_stop')->nullable();
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
		Schema::drop('plans');
	}

}
