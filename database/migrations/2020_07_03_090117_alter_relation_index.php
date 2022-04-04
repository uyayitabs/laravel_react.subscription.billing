<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRelationIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('relations', function ($table) {
            $table->index('customer_number');
            $table->index('company_name');
            $table->index('email');
            $table->index('phone');
            $table->index('bank_account');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('relations', function ($table) {
            $table->dropIndex('relations_customer_number_index');
            $table->dropIndex('relations_company_name_index');
            $table->dropIndex('relations_email_index');
            $table->dropIndex('relations_phone_index');
            $table->dropIndex('relations_bank_account_index');
        });
    }
}
