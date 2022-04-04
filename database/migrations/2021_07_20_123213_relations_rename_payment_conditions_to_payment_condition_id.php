<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RelationsRenamePaymentConditionsToPaymentConditionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::table('relations', function (Blueprint $table) {
                $table->foreign('payment_condition_id')
                    ->references('id')->on('payment_conditions')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
                $table->dropColumn('payment_conditions');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {
            Schema::table('relations', function (Blueprint $table) {
                $table->string('payment_conditions', 45)
                    ->nullable();
                $table->dropForeign('relations_payment_condition_id_foreign');
            });
        });
    }
}
