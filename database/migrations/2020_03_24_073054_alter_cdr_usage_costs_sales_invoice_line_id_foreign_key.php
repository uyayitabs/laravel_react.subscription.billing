<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCdrUsageCostsSalesInvoiceLineIdForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('cdr_usage_costs', function(Blueprint $table)
		{
            $table->dropForeign('cdr_usage_costs_sales_invoice_line_id_foreign');
        });

        Schema::table('cdr_usage_costs', function(Blueprint $table)
		{
            $table->foreign('sales_invoice_line_id')->references('id')->on('sales_invoice_lines')->onUpdate('RESTRICT')->onDelete('SET NULL');
        });

        \DB::update("UPDATE cdr_usage_costs SET sales_invoice_line_id=NULL WHERE `date` > \"2020-01-01\" AND sales_invoice_line_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM sales_invoice_lines WHERE sales_invoice_lines.id = cdr_usage_costs.sales_invoice_line_id);");
        \DB::update("UPDATE cdr_usage_costs SET total_cost=0, start_cost=0, minute_cost=0 WHERE `date` > '2020-01-01' AND total_cost > 0 AND recipient LIKE '%207605040';");
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('cdr_usage_costs', function(Blueprint $table)
		{
            $table->dropForeign('cdr_usage_costs_sales_invoice_line_id_foreign');
        });

        Schema::table('cdr_usage_costs', function(Blueprint $table)
		{
            $table->foreign('sales_invoice_line_id')->references('id')->on('sales_invoice_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });

        \DB::update("UPDATE cdr_usage_costs SET sales_invoice_line_id=NULL WHERE `date` > \"2020-01-01\" AND sales_invoice_line_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM sales_invoice_lines WHERE sales_invoice_lines.id = cdr_usage_costs.sales_invoice_line_id);");
        \DB::update("UPDATE cdr_usage_costs SET total_cost=0, start_cost=0, minute_cost=0 WHERE `date` > '2020-01-01' AND total_cost > 0 AND recipient LIKE '%207605040';");
        Schema::enableForeignKeyConstraints();
    }
}
