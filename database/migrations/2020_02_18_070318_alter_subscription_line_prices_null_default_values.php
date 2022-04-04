<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSubscriptionLinePricesNullDefaultValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_line_prices', function ($table) {
            $table->decimal('fixed_price', 12, 5)->nullable(true)->default(null)->change();
            $table->decimal('margin', 5, 2)->nullable(true)->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscription_line_prices', function ($table) {
            $table->decimal('fixed_price', 12, 5)->nullable(true)->default(null)->change();
            $table->decimal('margin', 5, 2)->nullable(true)->default(null)->change();
        });
    }
}
