<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLogActivityIndexs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_activities', function ($table) {
            $table->index('created_at');
            $table->index('username');
            $table->index('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_activities', function ($table) {
            $table->dropIndex('log_activities_created_at_index');
            $table->dropIndex('log_activities_username_index');
            $table->dropIndex('log_activities_message_index');
        });
    }
}
