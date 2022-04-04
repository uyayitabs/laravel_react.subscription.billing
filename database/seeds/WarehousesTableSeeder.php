<?php

use Illuminate\Database\Seeder;

class WarehousesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('warehouses')->delete();
        
        \DB::table('warehouses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tenant_id' => 7,
                'warehouse_location' => 1,
                'description' => 'Kantoor Almere',
                'status' => 'ACTIVE',
                'active_from' => '2019-01-01',
                'active_to' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
        ));
        
        
    }
}