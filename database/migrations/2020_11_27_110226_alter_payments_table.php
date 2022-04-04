<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('payments');
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('bank_file_id')->unsigned();
            $table->date('date')->nullable();
            $table->decimal('amount', 12, 5)->nullable();
            $table->text('descr', 65535)->nullable();
            $table->string('batch_id')->nullable();
            $table->bigInteger('batch_trx')->unsigned()->nullable();
            $table->string('account_iban', 45)->nullable();
            $table->string('account_name', 191)->nullable();
            $table->string('bank_code', 191)->nullable();
            $table->enum('type', ['batch', 'direct_debit', 'direct_debit_manual', 'direct_debit_rejection', 'direct_debit_reversal', 'credit_transfer', 'digital_transfer', 'transfer']);
            $table->string('return_code', 191)->nullable();
            $table->text('return_reason', 65535)->nullable();
            $table->timestamps();

            $table->foreign('bank_file_id')->references('id')->on('bank_files')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
        Schema::create('payments', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->timestamps();
        });
    }
}
