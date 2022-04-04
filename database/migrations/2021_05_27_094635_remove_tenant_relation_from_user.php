<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveTenantRelationFromUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_tenant_id_foreign');
            $table->dropColumn('tenant_id');
            $table->dropColumn('relation_id');
            $table->dropColumn('type');
            $table->unsignedBigInteger('last_tenant_id');
        });

        DB::statement('CREATE OR REPLACE VIEW `v_users_tenants` AS
SELECT
 u.id as user_id, u.username as username,
 p.id as person_id, r.id as relation_id,
 t.id as tenant_id, (select count(*) from tenants t2 where t2.parent_id = t.id) as children
FROM users u
 left join persons p on p.id = u.person_id
 left join relations_persons rp on rp.person_id = p.id
 left join relations r on r.id = rp.relation_id
 left join tenants t on t.id = r.tenant_id;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_tenant_id');
            $table->unsignedBigInteger('relation_id');
            $table->unsignedBigInteger('tenant_id');
            $table->tinyInteger('type');
        });

        //re-set the tenant id's in users
        DB::statement('update users set tenant_id = (select tenant_id from v_users_tenants where v_users_tenants.user_id = users.id limit 1)');
        DB::statement('drop view v_users_tenants');

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

    }
}
