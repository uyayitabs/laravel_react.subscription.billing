<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BillingRunsDeleteCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropForeign('sales_invoices_billing_run_id_foreign');
            $table->foreign('billing_run_id')
                ->references('id')->on('billing_runs')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropForeign('sales_invoices_billing_run_id_foreign');
            $table->foreign('billing_run_id')
                ->references('id')->on('billing_runs')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }
}
