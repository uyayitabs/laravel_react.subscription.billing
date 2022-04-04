<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantBankAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('tenant_id');
            $table->integer('account_id');
            $table->string('iban');
            $table->string('bic');
            $table->smallInteger('status');
            $table->string('bank_name');
            $table->string('bank_api');
            $table->timestamps();
        });

        Schema::table('bank_files', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_bank_account_id');
            //Cannot make foreign key because past bank-files aren't related
            //$table->foreign('tenant_bank_account_id')->references('id')->on('tenant_bank_accounts');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_bank_account_id');
            //Cannot make foreign key because past payments aren't related
            //$table->foreign('tenant_bank_account_id')->references('id')->on('tenant_bank_accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('bank_files', function (Blueprint $table) {
            $table->dropColumn('tenant_bank_account_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('tenant_bank_account_id');
        });

        Schema::dropIfExists('tenant_bank_accounts');
    }
}
