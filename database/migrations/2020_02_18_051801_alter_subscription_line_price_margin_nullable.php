<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSubscriptionLinePriceMarginNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscription_line_prices', function ($table) {
            $table->decimal('fixed_price', 12, 5)->nullable(true)->change();
            $table->decimal('margin', 5, 2)->nullable(true)->change();
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
            $table->decimal('fixed_price', 12, 5)->nullable(true)->change();
            $table->decimal('margin', 5, 2)->nullable(true)->change();
        });
    }
}
