<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class InsertOrUpdateLineProvisioningStatuses extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = array(
            array(
                'id' => 10,
                'status' => 'inactive',
                'label' => 'Inactive',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 20,
                'status' => 'check_pending',
                'label' => 'Pending provisioning',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 21,
                'status' => 'order_pending',
                'label' => 'Order pending',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 22,
                'status' => 'migration_tbc',
                'label' => 'Mig. to be confirmed',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 23,
                'status' => 'migration_pending',
                'label' => 'Migration pending',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 24,
                'status' => 'migration_confirmed',
                'label' => 'Migration pending',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 30,
                'status' => 'cancel_pending',
                'label' => 'Cancel pending',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 40,
                'status' => 'not_available',
                'label' => 'Not available',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 60,
                'status' => 'active',
                'label' => 'Line active',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 70,
                'status' => 'failed',
                'label' => 'Failed',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            36 =>
            array(
                'id' => 80,
                'status' => 'cancelled',
                'label' => 'Cancelled',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            37 =>
            array(
                'id' => 81,
                'status' => 'rejected',
                'label' => 'Rejected',
                'status_type_id' => 3,
                'created_at' => '2020-08-20 21:27:00',
                'updated_at' => NULL,
            ),
            38 =>
            array(
                'id' => 90,
                'status' => 'pending_termination',
                'label' => 'Pending termination',
                'status_type_id' => 3,
                'created_at' => '2020-10-16 21:27:00',
                'updated_at' => NULL,
            ),
            39 =>
            array(
                'id' => 91,
                'status' => 'terminated',
                'label' => 'Terminated',
                'status_type_id' => 3,
                'created_at' => '2020-10-16 21:27:00',
                'updated_at' => NULL,
            ),
            array(
                'id' => 500,
                'status' => 'error',
                'label' => 'Error',
                'status_type_id' => 3,
                'created_at' => '2020-10-16 21:27:00',
                'updated_at' => NULL,
            ),
            46 =>
            array(
                'id' => 501,
                'status' => 'check_error',
                'label' => 'Check error',
                'status_type_id' => 3,
                'created_at' => '2020-10-16 21:27:00',
                'updated_at' => NULL,
            ),
            47 =>
            array(
                'id' => 502,
                'status' => 'order_error',
                'label' => 'Order_error',
                'status_type_id' => 3,
                'created_at' => '2020-10-16 21:27:00',
                'updated_at' => NULL,
            ),
            48 =>
            array(
                'id' => 503,
                'status' => 'migration_error',
                'label' => 'Migration error',
                'status_type_id' => 3,
                'created_at' => '2020-10-16 21:27:00',
                'updated_at' => NULL,
            ),
            49 =>
            array(
                'id' => 504,
                'status' => 'cancel_error',
                'label' => 'Cancel error',
                'status_type_id' => 3,
                'created_at' => '2020-10-16 21:27:00',
                'updated_at' => NULL,
            ),
        );

        foreach ($items as $item) {
            DB::table('statuses')->updateOrInsert([
                'id' => $item['id'],
                'status_type_id' => $item['status_type_id']
            ], $item);
        }
        DB::table('statuses')
            ->where('status_type_id', 3)
            ->whereNotIn('id', Arr::pluck($items, 'id'))->delete();
    }
}
