<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LastInvoiceDateOnSubscriptionLine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_lines', function ($table) {
            $table->date('last_invoice_stop')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_lines', function (Blueprint $table) {
            $table->dropColumn('last_invoice_stop');
        });
    }
}
