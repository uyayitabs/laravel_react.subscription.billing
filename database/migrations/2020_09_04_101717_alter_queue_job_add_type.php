<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterQueueJobAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('queue_jobs', function ($table) {
            $table->string('type', 45)->nullable()->after("job");
            $table->unsignedBigInteger('tenant_id')->nullable()->after("type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('queue_jobs', function ($table) {
            $table->dropColumn('type');
        });
    }
}
