<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGetDateDiffFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP function IF EXISTS `GetDateDiff`;
            CREATE FUNCTION `GetDateDiff`(startDate VARCHAR(24), endDate VARCHAR(24)) RETURNS decimal(8,4)
            BEGIN
                DECLARE days DECIMAL(12,8);
                DECLARE period1 DECIMAL(12,8);
                DECLARE period2 DECIMAL(12,8);
                DECLARE period3 DECIMAL(12,8);
                
                SET days = TIMESTAMPDIFF(DAY, (SELECT startdate), (SELECT enddate))+1;
                SET period1 = IF(DAY((SELECT startdate)) = DAY((SELECT enddate))+1,0, IF(MONTH((SELECT startdate)) = MONTH((SELECT enddate)) AND YEAR((SELECT startdate)) = YEAR((SELECT enddate)), 0, (TIMESTAMPDIFF(DAY, (SELECT startdate), LAST_DAY((SELECT startdate)) + INTERVAL 1 DAY)) / DAY(LAST_DAY((SELECT startdate)))));
                SET period2 = IF(DAY((SELECT startdate)) = DAY((SELECT enddate))+1, TIMESTAMPDIFF(MONTH, LAST_DAY((SELECT startdate))+ INTERVAL 1 DAY, LAST_DAY((SELECT enddate))+ INTERVAL 1 DAY), TIMESTAMPDIFF(MONTH, LAST_DAY((SELECT startdate))+ INTERVAL 1 DAY, LAST_DAY((SELECT enddate))));
                SET period3 = IF(DAY((SELECT startdate)) = DAY((SELECT enddate))+1,0, IF (MONTH((SELECT startdate)) = MONTH((SELECT enddate)) AND YEAR((SELECT startdate)) = YEAR((SELECT enddate)), (SELECT days), DAY((SELECT enddate))) / DAY(LAST_DAY((SELECT enddate))));

                RETURN (SELECT FORMAT((period1 + period2 + period3), 4));
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
        DB::unprepared('DROP FUNCTION IF EXISTS `GetDateDiff`');
    }
}
