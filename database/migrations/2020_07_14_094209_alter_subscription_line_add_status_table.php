<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubscriptionLineAddStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_lines', function ($table) {
            $table->bigInteger('status_id')->unsigned()->nullable()->after("mind_id");
        });

        Schema::table('subscription_lines', function ($table) {
            $table->foreign('status_id')->references('id')->on('statuses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_lines', function ($table) {
            $table->dropForeign('subscription_lines_status_id_foreign');
        });
        Schema::table('subscription_lines', function ($table) {
            $table->dropColumn('status_id');
        });
    }
}
