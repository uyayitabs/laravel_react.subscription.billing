<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubscriptionsBillingStart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function($table)
        {
            $table->date('billing_start')->nullable()->after('provisioning_address');
        });

        Schema::table('plans', function($table)
        {
            $table->date('billing_start')->nullable()->after('description_long');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function(Blueprint $table)
		{
            $table->dropColumn('billing_start');
        });
        
        Schema::table('plans', function(Blueprint $table)
		{
            $table->dropColumn('billing_start');
        });
    }
}
