<?php

use Illuminate\Database\Seeder;
use App\StatusType;
use Illuminate\Support\Facades\DB;

class QueueJobStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statusType = StatusType::create(['type' => 'job']);
        DB::table('statuses')->insert(
            array(
                0 => array(
                    'id' => 100,
                    'status' => 'new',
                    'label' => 'New',
                    'status_type_id' => $statusType->id
                ),
                1 => array(
                    'id' => 101,
                    'status' => 'in_progress',
                    'label' => 'In progress',
                    'status_type_id' => $statusType->id
                ),
                2 => array(
                    'id' => 102,
                    'status' => 'done',
                    'label' => 'Done',
                    'status_type_id' => $statusType->id
                ),
                3 => array(
                    'id' => 103,
                    'status' => 'failed',
                    'label' => 'Failed',
                    'status_type_id' => $statusType->id
                ),
            )
        );
    }
}
