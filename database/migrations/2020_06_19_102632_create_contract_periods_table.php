<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractPeriodsTable extends Migration
{

    public function up()
    {
        Schema::create('contract_periods', function (Blueprint $table) {
            $table->bigIncrements('id', true)->unsigned();
            $table->bigInteger('tenant_id')->unsigned()->nullable()->index('relations_tenant_id_foreign');
            $table->string('period', 191)->nullable();
            $table->unsignedInteger('net_days')->nullable(); //0 - 365 days, etc
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('contract_periods');
    }
}
