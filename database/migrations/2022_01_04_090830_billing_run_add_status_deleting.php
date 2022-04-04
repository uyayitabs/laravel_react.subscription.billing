<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BillingRunAddStatusDeleting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Status::create([
            'id' => 91,
            'status' => 'deleting_billing_run',
            'label' => 'Deleting concept invoices and billing run',
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
        \App\Status::where([['id', 91], ['status_type_id', 7]])->first()->delete();
    }
}
