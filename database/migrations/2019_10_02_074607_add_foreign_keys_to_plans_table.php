<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('plans', function(Blueprint $table)
		{
			$table->foreign('area_code_id')->references('id')->on('area_codes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('parent_plan')->references('id')->on('plans')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('project_id')->references('id')->on('projects')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('plans', function(Blueprint $table)
		{
			$table->dropForeign('plans_area_code_id_foreign');
			$table->dropForeign('plans_parent_plan_foreign');
			$table->dropForeign('plans_project_id_foreign');
			$table->dropForeign('plans_tenant_id_foreign');
		});
	}

}
