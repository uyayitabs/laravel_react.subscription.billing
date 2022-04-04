<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BillingRunCdrSetDateTimestamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("alter table billing_runs modify column `date` date;");
        DB::statement("alter table cdr_usage_costs change column `date` `datetime` timestamp;");
        DB::statement("update cdr_usage_costs set `datetime` = concat(date(`datetime`), ' ', `time`);");
        DB::statement("alter table cdr_usage_costs drop column `time`;");

        //Batchwork BEGIN
        $batchCount = 0;
        $count = \App\CdrUsageCost::count();
        $doingWork = $count > 0;
        while ($doingWork) {
            $batch = \App\CdrUsageCost::skip($batchCount * 1000)->take(1000)->get();
            foreach($batch as $costs) {
                if($costs->datetime == null) continue;
                $otz = \Carbon\Carbon::parse($costs->datetime->format('Y-m-d H:i:s'), 'Europe/Amsterdam');
                $newtz = $otz->setTimezone('UTC');
                $costs->datetime = $newtz;
                $costs->save();
            }
            $batchCount++;
            $count -= 1000;
            if($count <= 0) $doingWork = false;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Batchwork BEGIN
        $batchCount = 0;
        $count = \App\CdrUsageCost::count();
        $doingWork = $count > 0;
        while ($doingWork) {
            $batch = \App\CdrUsageCost::skip($batchCount * 1000)->take(1000)->get();
            foreach($batch as $costs) {
                if($costs->datetime == null) continue;
                $otz = \Carbon\Carbon::parse($costs->datetime->format('Y-m-d H:i:s'), 'UTC');
                $newtz = $otz->setTimezone('Europe/Amsterdam');
                $costs->datetime = $newtz;
                $costs->save();
            }
            $batchCount++;
            $count -= 1000;
            if($count < 0) $doingWork = false;
        }

        DB::statement("alter table billing_runs modify column `date` timestamp;");
        DB::statement("alter table cdr_usage_costs add column `time` time after `datetime`;");
        DB::statement("update cdr_usage_costs set `time` = time(`datetime`);");
        DB::statement("alter table cdr_usage_costs change column `datetime` `date` date;");
    }
}
