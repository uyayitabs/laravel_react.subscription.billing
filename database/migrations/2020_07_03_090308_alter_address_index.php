<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddressIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function ($table) {
            $table->index('street1');
            $table->index('house_number');
            $table->index('room');
            $table->index('zipcode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function ($table) {
            $table->dropIndex('addresses_street1_index');
            $table->dropIndex('addresses_house_number_index');
            $table->dropIndex('addresses_room_index');
            $table->dropIndex('addresses_zipcode_index');
        });
    }
}
