<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLogActivitiesAddRelatedEntityTypeRelatedEntityId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_activities', function ($table) {
            $table->unsignedBigInteger('related_entity_id')->nullable()->after('tenant_id');
            $table->enum('related_entity_type', ['subscription', 'invoice', 'relation', 'product', 'billing_run', 'queue_job'])
                ->nullable()
                ->default(NULL)
                ->after('tenant_id');
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
            $table->dropColumn('related_entity_type');
            $table->dropColumn('related_entity_id');
        });
    }
}
