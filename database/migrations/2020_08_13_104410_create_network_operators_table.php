<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetworkOperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('network_operators', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('network_id')->unsigned();
            $table->bigInteger('operator_id')->unsigned();
            $table->timestamps();

            $table->foreign('network_id')->references('id')->on('networks')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('operator_id')->references('id')->on('operators')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('network_operators');
    }
}
