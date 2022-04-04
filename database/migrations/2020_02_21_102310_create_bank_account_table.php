<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('relation_id')->unsigned();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->string('bank_name', 255)->nullable();
            $table->string('iban', 45);
            $table->string('bic')->nullable();
            $table->boolean('dd_default')->default(0);
            $table->string('mndt_id', 50);
            $table->date('dt_of_sgntr')->useCurrent();
            $table->string('amdmnt_ind')->nullable();
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
        Schema::dropIfExists('bank_accounts');
    }
}
