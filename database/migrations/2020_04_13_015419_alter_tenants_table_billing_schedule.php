<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterTenantsTableBillingSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function ($table) {
            $table->bigInteger('billing_schedule')->unsigned()->nullable()->after('billing_day');
        });

        DB::statement('UPDATE tenants SET billing_schedule=30 WHERE id=7');
        DB::statement('UPDATE tenants SET billing_schedule=16, invoice_start_calculation="2020-01-16" WHERE id=8');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenants', function ($table) {
            $table->dropColumn('billing_schedule');
        });
    }
}
