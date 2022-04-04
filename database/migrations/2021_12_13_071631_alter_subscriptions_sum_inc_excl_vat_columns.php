<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSubscriptionsSumIncExclVatColumns extends Migration
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
            $table->decimal('price_excl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('price_incl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_yrc_excl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_yrc_incl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_qrc_excl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_qrc_incl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_mrc_excl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_mrc_incl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_nrc_excl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_nrc_incl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_deposit_excl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
            $table->decimal('sum_deposit_incl_vat', 12, 5)->nullable(true)->default(0.00)->after('wish_date');
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
            $table->dropColumn('sum_nrc_excl_vat');
            $table->dropColumn('sum_nrc_incl_vat');
            $table->dropColumn('sum_mrc_excl_vat');
            $table->dropColumn('sum_mrc_incl_vat');
            $table->dropColumn('sum_qrc_excl_vat');
            $table->dropColumn('sum_qrc_incl_vat');
            $table->dropColumn('sum_yrc_excl_vat');
            $table->dropColumn('sum_yrc_incl_vat');
            $table->dropColumn('sum_deposit_excl_vat');
            $table->dropColumn('sum_deposit_incl_vat');
            $table->dropColumn('price_excl_vat');
            $table->dropColumn('price_incl_vat');
        });
    }
}
