<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BillingRunFinalizeInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Status::create([
            'id' => 13,
            'status' => 'finalizing_invoices',
            'label' => 'Busy finalizing invoices',
            'status_type_id' => 7
        ]);
        \App\Status::create([
            'id' => 14,
            'status' => 'finalizing_failed',
            'label' => 'Failed to finalize invoices',
            'status_type_id' => 7
        ]);
        \App\Status::create([
            'id' => 15,
            'status' => 'invoices_finalized',
            'label' => 'Finished finalizing invoices',
            'status_type_id' => 7
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Status::where([['id', 13],['status_type_id', 7]])->first()->delete();
        \App\Status::where([['id', 14],['status_type_id', 7]])->first()->delete();
        \App\Status::where([['id', 15],['status_type_id', 7]])->first()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
