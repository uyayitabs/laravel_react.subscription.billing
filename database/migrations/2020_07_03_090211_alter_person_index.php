<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPersonIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('persons', function ($table) {
            $table->index('first_name');
            $table->index('middle_name');
            $table->index('last_name');
            $table->index('email');
            $table->index('phone');
            $table->index('mobile');
            $table->index('primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persons', function ($table) {
            $table->dropIndex('persons_first_name_index');
            $table->dropIndex('persons_middle_name_index');
            $table->dropIndex('persons_last_name_index');
            $table->dropIndex('persons_email_index');
            $table->dropIndex('persons_phone_index');
            $table->dropIndex('persons_mobile_index');
            $table->dropIndex('persons_primary_index');
        });
    }
}
