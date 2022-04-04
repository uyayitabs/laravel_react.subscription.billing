<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PaymentRelationToRelationAndInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('statuses')->updateOrInsert(
            ['id' => 0, 'status' => 'new', 'label' => 'New', 'status_type_id' => 2],
            ['id' => 0, 'status' => 'new', 'label' => 'New', 'status_type_id' => 2]
        );
        DB::table('statuses')->updateOrInsert(
            ['id' => 10, 'status' => 'failed', 'label' => 'Failed', 'status_type_id' => 2],
            ['id' => 10, 'status' => 'failed', 'label' => 'Failed', 'status_type_id' => 2]
        );
        DB::table('statuses')->updateOrInsert(
            ['id' => 100, 'status' => 'processed', 'label' => 'Processed', 'status_type_id' => 2],
            ['id' => 100, 'status' => 'processed', 'label' => 'Processed', 'status_type_id' => 2]
        );

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('relation_id')->nullable()->after('bank_file_id');
            $table->unsignedBigInteger('sales_invoice_id')->nullable()->after('bank_file_id');
            $table->unsignedBigInteger('status_id')->default(0)->after('return_reason');

            $table->foreign('relation_id')->references('id')->on('relations')->onUpdate('SET NULL')->onDelete('SET NULL');
            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onUpdate('SET NULL')->onDelete('SET NULL');
        });

        DB::statement('ALTER TABLE `payments` CHANGE COLUMN `tenant_bank_account_id` `tenant_bank_account_id` BIGINT(20) UNSIGNED NOT NULL AFTER `id`');

        DB::table('payments')->where('tenant_bank_account_id', 0)->update(['tenant_bank_account_id' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::table('statuses')->where('status_type_id', 2)->delete();

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_relation_id_foreign');
            $table->dropForeign('payments_sales_invoice_id_foreign');

            $table->dropColumn('relation_id');
            $table->dropColumn('sales_invoice_id');
            $table->dropColumn('status_id');
        });
    }
}
