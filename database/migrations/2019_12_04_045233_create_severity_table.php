<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeverityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('severity', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('severity');
            $table->string('keyword'); //'emerg', 'alert', 'crit', 'err', 'warning', 'notice', 'info', 'debug'
            $table->string('description')->nullable();
            $table->string('condition')->nullable();
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
        Schema::dropIfExists('severity');
    }
}
