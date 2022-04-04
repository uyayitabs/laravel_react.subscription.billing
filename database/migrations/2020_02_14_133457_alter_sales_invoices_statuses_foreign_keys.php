<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesInvoicesStatusesForeignKeys extends Migration
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
            $table->dropColumn('invoice_status');
            $table->dropColumn('payment_status');
        });

        Schema::table('sales_invoices', function($table)
        {
            $table->bigInteger('invoice_status')->unsigned()->nullable()->after('price_total');
            $table->bigInteger('payment_status')->unsigned()->nullable()->after('price_total');

            $table->foreign('invoice_status')->references('id')->on('statuses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('payment_status')->references('id')->on('statuses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
        // DB::statement('UPDATE sales_invoices SET invoice_status=null payment_status=null');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


        Schema::disableForeignKeyConstraints();

        Schema::table('sales_invoices', function($table)
        {
            $table->dropForeign('sales_invoices_invoice_status_foreign');
            $table->dropForeign('sales_invoices_payment_status_foreign');
        });

        Schema::table('sales_invoices', function($table)
        {
            $table->dropColumn('invoice_status');
            $table->dropColumn('payment_status');
        });

        Schema::table('sales_invoices', function($table)
        {
            $table->smallInteger('invoice_status')->unsigned()->nullable()->after('price_total');
            $table->smallInteger('payment_status')->unsigned()->nullable()->after('price_total');
        });
        
        Schema::enableForeignKeyConstraints();
        
    }
}
