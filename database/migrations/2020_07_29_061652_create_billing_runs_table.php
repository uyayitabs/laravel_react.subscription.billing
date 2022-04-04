<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingRunsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_runs', function (Blueprint $table) {
            $table->bigIncrements('id', true)->unsigned();
            $table->bigInteger('tenant_id')->unsigned()->index('billing_runs_tenant_id_foreign');
            $table->bigInteger('status_id')->unsigned()->default(0)->index('billing_runs_status_id_foreign');
            $table->text('dd_file', 65535)->nullable();
            $table->text('last_error', 65535)->nullable();
            $table->dateTime('date')->nullable();
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
        Schema::dropIfExists('billing_runs');
    }
}
