<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales_invoices', function ($table) {
            $table->dropForeign('sales_invoices_invoice_status_foreign');
            $table->dropForeign('sales_invoices_payment_status_foreign');
        });

        Schema::table('statuses', function ($table) {
            $table->dropPrimary();
            $table->bigInteger('id')->unsigned()->change();
            $table->string('label', 45)->nullable()->after("status");
            $table->primary(['id', 'status_type_id']);
        });

        Schema::table('sales_invoices', function ($table) {
            $table->foreign('invoice_status')->references('id')->on('statuses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('payment_status')->references('id')->on('statuses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statuses', function ($table) {
            $table->dropColumn('label');
        });
    }
}
