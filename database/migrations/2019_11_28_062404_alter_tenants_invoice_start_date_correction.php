<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTenantsInvoiceStartDateCorrection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function($table)
        {
            $table->date('invoice_start_calculation')->nullable()->after('billing_day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenants', function(Blueprint $table)
		{
            $table->dropColumn('invoice_start_calculation');
		});
    }
}
