<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVUserPrivilegesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            create or replace view `v_user_privileges` as
                select 
                    `u`.`id` as `user_id`,
                    `u`.`username`,
                    `u`.`person_id`,
                    `ug`.`group_id`,
                    `g`.`name` as `group_name`,
                    `g`.`description` as `group_description`,
                    `gr`.`write`,
                    `gr`.`read`,
                    `r`.`module` as `role_module`,
                    `r`.`name` as `role_description`
                from users `u`
                left join user_groups `ug` on u.id = ug.user_id
                left join `groups` `g` on g.id = ug.group_id
                left join group_roles `gr` on ug.group_id = `gr`.group_id
                left join roles `r` on gr.role_id = r.id;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('drop view v_user_privileges');
    }
}
