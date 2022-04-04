<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionLineMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_line_metas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('subscription_line_id')->unsigned();
            $table->string('key');
            $table->string('value');
            $table->timestamps();

            $table->foreign('subscription_line_id')->references('id')->on('subscription_lines')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_line_metas');
    }
}
