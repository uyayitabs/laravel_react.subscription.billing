<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserAddTypeRelationIdLastLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->tinyInteger('type')->nullable()->after('remember_token');
            $table->bigInteger('relation_id')->unsigned()->nullable()->after('type');
            $table->dateTime('last_login')->nullable()->after('relation_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('type');
            $table->dropColumn('relation_id');
            $table->dropColumn('last_login');
        });
    }
}
