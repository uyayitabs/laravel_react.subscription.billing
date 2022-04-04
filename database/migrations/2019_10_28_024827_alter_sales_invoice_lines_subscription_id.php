<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSalesInvoiceLinesSubscriptionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_invoice_lines', function($table)
        {
            $table->bigInteger('subscription_id')->unsigned()->nullable()->index()->after('order_line_id');
        });

        Schema::table('sales_invoice_lines', function(Blueprint $table) 
        {
            $table->foreign('subscription_id', 'sales_invoices_lines_subscription_id_foreign')->references('id')->on('subscriptions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('sales_invoice_lines', function($table) 
        {
            $table->dropForeign('sales_invoices_lines_subscription_id_foreign');
        });


        Schema::table('sales_invoice_lines', function(Blueprint $table)
        {
			$table->dropColumn('subscription_id');
        });
    }
}
