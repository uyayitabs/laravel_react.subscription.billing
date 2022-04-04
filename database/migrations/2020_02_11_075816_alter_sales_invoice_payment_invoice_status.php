<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSalesInvoicePaymentInvoiceStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_invoices', function($table)
        {
            $table->renameColumn('status', 'invoice_status');
            $table->smallInteger('payment_status')->default(0)->comment('0=open_not_overdue, 1=open_overdue, 2=paid, 3=partial_paid')->after('price_total');
        });
        DB::statement('UPDATE sales_invoices SET invoice_status=1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_invoices', function($table)
        {
            $table->renameColumn('invoice_status', 'status');
            $table->dropColumn('payment_status');
        });
    }


}
