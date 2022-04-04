<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateGenerateNumberRangeFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP FUNCTION IF EXISTS `GenerateNumberRange`;
            CREATE FUNCTION `GenerateNumberRange`(
                tenantId INT,
                numberRangeType VARCHAR(255),
                zeroToPad INT,
                prefixCode VARCHAR(10),
                saveToDB INT
            ) RETURNS varchar(255) CHARSET utf8
            BEGIN
                SET @vStart = (SELECT `start` FROM number_ranges WHERE tenant_id = tenantId AND `type` = numberRangeType);
                SET @vCurrent = (SELECT IFNULL(`current`, `start`) FROM number_ranges WHERE tenant_id = tenantId AND `type` = numberRangeType);

                SET @vCurrentUpdate = @vCurrent + 1;
                
                IF saveToDB = 1 THEN
                    UPDATE number_ranges SET `current` = @vCurrentUpdate  WHERE tenant_id = tenantId AND `type` = numberRangeType;
                END IF;
                
                RETURN CONCAT(RPAD(prefixCode, (zeroToPad + LENGTH(prefixCode))-LENGTH(@vCurrent), "0"), @vCurrent);
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
        DB::unprepared('DROP FUNCTION IF EXISTS `GenerateNumberRange`');
    }
}
