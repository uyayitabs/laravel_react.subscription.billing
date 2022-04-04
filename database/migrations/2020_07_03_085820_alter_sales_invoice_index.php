<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSalesInvoiceIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_invoices', function ($table) {
            $table->index('invoice_no');
            $table->index('date');
            $table->index('description');
            $table->index('price');
            $table->index('due_date');
            $table->index('inv_output_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_invoices', function ($table) {
            $table->dropIndex('sales_invoices_invoice_no_index');
            $table->dropIndex('sales_invoices_date_index');
            $table->dropIndex('sales_invoices_description_index');
            $table->dropIndex('sales_invoices_price_index');
            $table->dropIndex('sales_invoices_due_date_index');
            $table->dropIndex('sales_invoices_inv_output_type_index');
        });
    }
}
