<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SalesInvoiceDeleteCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_invoice_lines', function (Blueprint $table) {
            $table->dropForeign('sales_invoices_lines_sales_invoice_id_foreign');
            $table->foreign('sales_invoice_id')
                ->references('id')->on('sales_invoices')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('cdr_usage_costs', function (Blueprint $table) {
            $table->dropForeign('cdr_usage_costs_sales_invoice_line_id_foreign');
            $table->foreign('sales_invoice_line_id')
                ->references('id')->on('sales_invoice_lines')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_invoice_lines', function (Blueprint $table) {
            $table->dropForeign('sales_invoice_lines_sales_invoice_id_foreign');
            $table->foreign('sales_invoice_id', 'sales_invoices_lines_sales_invoice_id_foreign')
                ->references('id')->on('sales_invoices')
                ->onDelete('restrict');
        });

        Schema::table('cdr_usage_costs', function (Blueprint $table) {
            $table->dropForeign('cdr_usage_costs_sales_invoice_line_id_foreign');
            $table->foreign('sales_invoice_line_id')
                ->references('id')->on('sales_invoice_lines')
                ->onDelete('restrict');
        });
    }
}
