<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSalesInvoiceLinesSalesInvoiceLineType extends Migration
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
            $table->dropForeign('sales_invoices_lines_subscription_line_type_foreign');
        });


        Schema::table('sales_invoice_lines', function(Blueprint $table)
        {
			$table->dropColumn('subscription_line_type');
        });

        Schema::table('sales_invoice_lines', function($table)
        {
            $table->bigInteger('sales_invoice_line_type')->unsigned()->nullable()->index()->after('sales_invoice_id');
        });

        Schema::table('sales_invoice_lines', function(Blueprint $table) 
        {
            $table->foreign('sales_invoice_line_type', 'sales_invoices_lines_sales_invoice_line_type_foreign')->references('id')->on('plan_subscription_line_types')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
            $table->dropForeign('sales_invoices_lines_sales_invoice_line_type_foreign');
        });


        Schema::table('sales_invoice_lines', function(Blueprint $table)
        {
			$table->dropColumn('sales_invoice_line_type');
        });
    }
}
