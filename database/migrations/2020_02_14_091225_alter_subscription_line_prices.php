<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubscriptionLinePrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_line_prices', function(Blueprint $table)
		{
			$table->decimal('margin', 5, 2)->change();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_line_prices', function(Blueprint $table)
		{
			$table->decimal('margin', 2)->change();
		});
    }
}
