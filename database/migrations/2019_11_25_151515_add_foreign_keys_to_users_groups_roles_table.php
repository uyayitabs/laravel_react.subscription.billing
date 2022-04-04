<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToUsersGroupsRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_groups', function(Blueprint $table)
		{
			$table->bigInteger('user_id')->unsigned()->nullable()->change();
			$table->bigInteger('group_id')->unsigned()->nullable()->change();
			$table->foreign('user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('group_id')->references('id')->on('groups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});

		Schema::table('group_roles', function(Blueprint $table)
		{
			$table->bigInteger('group_id')->unsigned()->nullable()->change();
			$table->bigInteger('role_id')->unsigned()->nullable()->change();
			$table->foreign('group_id')->references('id')->on('groups')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('role_id')->references('id')->on('roles')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_groups', function(Blueprint $table)
		{
			$table->dropForeign('user_groups_user_id_foreign');
			$table->dropForeign('user_groups_group_id_foreign');
		});

		Schema::table('group_roles', function(Blueprint $table)
		{
			$table->dropForeign('group_roles_group_id_foreign');
			$table->dropForeign('group_roles_role_id_foreign');
		});
	}

}
