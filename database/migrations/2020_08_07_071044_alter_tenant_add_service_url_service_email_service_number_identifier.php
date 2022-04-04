<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTenantAddServiceUrlServiceEmailServiceNumberIdentifier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function ($table) {
            $table->string('service_url')->nullable()->after('default_country_id');
            $table->string('service_number')->nullable()->after('service_url');
            $table->string('service_email')->nullable()->after('service_number');
            $table->string('identifier')->nullable()->after('service_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenants', function ($table) {
            $table->dropColumn('service_url');
            $table->dropColumn('service_number');
            $table->dropColumn('service_email');
            $table->dropColumn('identifier');
        });
    }
}
