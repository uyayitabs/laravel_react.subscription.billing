<?php

use App\StatusType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now()->format('Y-m-d H:i:s');

        // Create status_type if not yet existing
        $orderStatusType = StatusType::where('type', 'order')->first();
        if (blank($orderStatusType)) {
            $orderStatusType = StatusType::create([
                'type' => 'order'
            ]);
        }

        // Array order statuses
        $items = array(
            array(
                'id' => 0,
                'status' => 'new',
                'label' => 'New',
                'status_type_id' => $orderStatusType->id,
                'created_at' => $now,
                'updated_at' => NULL,
            ),
            array(
                'id' => 1,
                'status' => 'processed',
                'label' => 'Processed',
                'status_type_id' => $orderStatusType->id,
                'created_at' => $now,
                'updated_at' => NULL,
            ),
        );

        // Loop through each $item
        foreach ($items as $item) {
            DB::table('statuses')->updateOrInsert([
                'id' => $item['id'],
                'status_type_id' => $item['status_type_id']
            ], $item);
        }

        // Delete other statuses other than [new, processed]
        DB::table('statuses')
            ->where('status_type_id', $orderStatusType->id)
            ->whereNotIn('id', Arr::pluck($items, 'id'))->delete();
    }
}
