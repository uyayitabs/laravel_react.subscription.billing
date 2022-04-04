<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterLogActivitiesAddHighPrecisionTimestampColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_activities', function ($table) {
            $table->unsignedBigInteger('hp_timestamp')->after('agent');
        });

        $sqlStatement = "UPDATE log_activities SET hp_timestamp=ROUND(UNIX_TIMESTAMP(created_at) * 1000) ";
        $sqlStatement .= " WHERE hp_timestamp='';";
        DB::update($sqlStatement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_activities', function ($table) {
            $table->dropColumn('hp_timestamp');
        });
    }
}
