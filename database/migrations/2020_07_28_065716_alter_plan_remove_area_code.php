<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlanRemoveAreaCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('plans', function ($table) {
        //     $table->dropForeign('plans_area_code_id_foreign');
        // });

        Schema::table('plans', function ($table) {
            $table->dropForeign('plans_area_code_id_foreign');
            $table->dropColumn('area_code_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function ($table) {
            $table->string('area_code_id', 12)->nullable()->index();
            $table->foreign('area_code_id')->references('id')->on('area_codes')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }
}
