<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLogActivityIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_activities', function ($table) {
            $table->index('related_entity_id');
            $table->index('related_entity_type');
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
            $table->dropIndex('log_activities_related_entity_id_index');
            $table->dropIndex('log_activities_related_entity_type_index');
        });
    }
}
