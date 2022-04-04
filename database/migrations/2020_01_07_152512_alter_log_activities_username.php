<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLogActivitiesUsername extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_activities', function($table)
        {
            $table->string('username')->nullable()->after('user_id');
            $table->string('facility')->nullable()->after('facility_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


        Schema::table('log_activities', function(Blueprint $table)
        {
			$table->dropColumn('username');
			$table->dropColumn('facility');
        });
    }
}
