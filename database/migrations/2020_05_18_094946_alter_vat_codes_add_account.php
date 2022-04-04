<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterVatCodesAddAccount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('vat_codes', function ($table) {
        //     $table->bigInteger('account_id')->unsigned()->nullable()->after('tenant_id');
        // });

        // Schema::table('vat_codes', function ($table) {
        //     $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vat_codes', function ($table) {
            $table->dropForeign('vat_codes_account_id_foreign');
            $table->dropColumn('account_id');
        });
    }
}
