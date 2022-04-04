<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExecuteUpdateSubscriptionTotalMysqlEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            SET GLOBAL event_scheduler = ON;
            DROP EVENT IF EXISTS `ExecuteUpdateSusbcriptionTotalsEvent`;

            CREATE EVENT `ExecuteUpdateSusbcriptionTotalsEvent`
            ON SCHEDULE EVERY 1 DAY STARTS (TIMESTAMP(CURRENT_DATE) + INTERVAL 1 DAY) -- STARTS AT MIDNIGHT
            ON COMPLETION NOT PRESERVE ENABLE
            DO
            BEGIN
                CALL `f2x`.`ExecuteUpdateSubscriptionTotals`();
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP EVENT IF EXISTS `ExecuteUpdateSusbcriptionTotalsEvent`;');
    }
}
