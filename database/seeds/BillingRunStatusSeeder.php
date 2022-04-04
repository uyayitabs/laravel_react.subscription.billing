<?php

use App\Status;
use App\StatusType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillingRunStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $billingRunStatusType = StatusType::where('type', 'billing_run')->first();
        if (!$billingRunStatusType) {
            $billingRunStatusType = StatusType::create(
                array(
                    'type' => 'billing_run',
                    'created_at' => now()->format("Y-m-d H:i:s")
                )
            );
        }

        $statusesCount = Status::where('status_type_id', $billingRunStatusType->id)->count();
        if ($statusesCount == 0) {
            DB::table('statuses')->insert(
                array(
                    array(
                        "id" => 0,
                        "status" => "new",
                        "label" => "New",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 10,
                        "status" => "creating_invoices",
                        "label" => "Creating invoices",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 11,
                        "status" => "creation_failed",
                        "label" => "Failed to create invoices",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 12,
                        "status" => "invoices_created",
                        "label" => "Invoices created",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        'id' => 20,
                        "status" => "sending_invoices",
                        "label" => "Sending invoice emails",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 21,
                        "status" => "sending_failed",
                        "label" => "Failed to send emails",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 22,
                        "status" => "invoices_sent",
                        "label" => "Invoice emails have been sent",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 30,
                        "status" => "creating_dd_file",
                        "label" => "Creating direct debit file",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 31,
                        "status" => "dd_file_failed",
                        "label" => "Direct debit file creation failed",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 32,
                        "status" => "dd_file_created",
                        "label" => "Direct debit file created",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 40,
                        "status" => "dd_file_downloaded",
                        "label" => "Direct debit file downloaded",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                    array(
                        "id" => 100,
                        "status" => "closed",
                        "label" => "Closed",
                        "status_type_id" => $billingRunStatusType->id,
                    ),
                )
            );
        }
    }
}
