<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSalesInvoicesBillingRunId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_invoices', function ($table) {
            $table->bigInteger('billing_run_id')->unsigned()->nullable()->index('sales_invoices_billing_run_id_foreign')->after('invoice_status');
        });

        Schema::table('sales_invoices', function ($table) {
            $table->foreign('billing_run_id')->nullable()->references('id')->on('billing_runs')->onUpdate('RESTRICT')->onDelete('SET NULL');
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
        });
    }
}
