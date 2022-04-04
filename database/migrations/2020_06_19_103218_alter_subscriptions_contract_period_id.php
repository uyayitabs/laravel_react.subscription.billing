<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubscriptionsContractPeriodId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function ($table) {
            $table->bigInteger('contract_period_id')->nullable()->unsigned()->after('subscription_stop');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreign('contract_period_id')->references('id')->on('contract_periods')->onUpdate('RESTRICT')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign('subscriptions_contract_period_id_foreign');
        });


        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('contract_period_id');
        });
    }
}
