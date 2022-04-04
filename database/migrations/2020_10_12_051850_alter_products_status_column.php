<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductsStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update('UPDATE products SET `status`=1 WHERE `status` = "ACTIVE"');

        Schema::table('products', function ($table) {
            $table->renameColumn('status', 'status_id');
        });

        Schema::table('products', function ($table) {
            $table->bigInteger('status_id')->unsigned()->index('products_status_id_foreign')->collation(NULL)->charset(NULL)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('status_id')->references('id')->on('statuses')->onUpdate('RESTRICT')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_status_id_foreign');
        });

        Schema::table('products', function ($table) {
            $table->renameColumn('status_id', 'status');
        });

        Schema::table('products', function ($table) {
            $table->string('status', 45)->change();
        });
    }
}
