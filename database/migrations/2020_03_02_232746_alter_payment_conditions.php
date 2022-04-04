<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPaymentConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('payment_conditions', 'status')) {
            Schema::table('payment_conditions', function ($table) {
                $table->dropColumn('status');
            });
        }

        Schema::table('payment_conditions', function ($table) {
            $table->tinyInteger('status')->nullable();
            $table->text('description')->nullable();
            $table->boolean('pay_in_advance')->nullable();
            $table->integer('net_days')->nullable();
            $table->boolean('default')->default(1)->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
        });

        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**tab
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_conditions', function (Blueprint $table) {
            $table->dropForeign('payment_conditions_tenant_id_foreign');
            $table->dropForeign('payment_conditions_created_by_foreign');
            $table->dropForeign('payment_conditions_updated_by_foreign');
        });

        if (Schema::hasColumn('payment_conditions', 'status')) {
            Schema::table('payment_conditions', function ($table) {
                $table->string('status', 45)->change();
            });
        }

        Schema::table('payment_conditions', function ($table) {
            $table->dropColumn('description');
            $table->dropColumn('pay_in_advance');
            $table->dropColumn('net_days');
            $table->dropColumn('default');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
