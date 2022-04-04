<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueJobLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue_job_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('queue_job_id');
            $table->json('json_data');
            $table->timestamps();
            $table->foreign('queue_job_id')->references('id')->on('queue_jobs')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('queue_job_logs');
    }
}
