<?php

use Illuminate\Database\Seeder;

class SerialsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('serials')->delete();
        
        \DB::table('serials')->insert(array (
            0 => 
            array (
                'product_id' => 12,
                'serial' => '022016J032421',
                'warehouse_id' => 1,
                'json_data' => '{"serial": {"mac": "44F034AA0001", "serial": "022016J032421"}}',
                'created_at' => '2019-09-03 22:00:00',
                'updated_at' => '2019-09-03 22:00:00',
            ),
            1 => 
            array (
                'product_id' => 12,
                'serial' => '022016J032422',
                'warehouse_id' => 1,
                'json_data' => '{"serial": {"mac": "44F034AA0002", "serial": "022016J032422"}}',
                'created_at' => '2019-09-03 22:00:00',
                'updated_at' => '2019-09-03 22:00:00',
            ),
            2 => 
            array (
                'product_id' => 12,
                'serial' => '022016J032423',
                'warehouse_id' => 1,
                'json_data' => '{"serial": {"mac": "44F034AA0003", "serial": "022016J032423"}}',
                'created_at' => '2019-09-03 22:00:00',
                'updated_at' => '2019-09-03 22:00:00',
            ),
            3 => 
            array (
                'product_id' => 12,
                'serial' => '022016J032424',
                'warehouse_id' => 1,
                'json_data' => '{"serial": {"mac": "44F034AA0004", "serial": "022016J032424"}}',
                'created_at' => '2019-09-03 22:00:00',
                'updated_at' => '2019-09-03 22:00:00',
            ),
            4 => 
            array (
                'product_id' => 12,
                'serial' => '022016J032425',
                'warehouse_id' => 1,
                'json_data' => '{"serial": {"mac": "44F034AA0005", "serial": "022016J032425"}}',
                'created_at' => '2019-09-03 22:00:00',
                'updated_at' => '2019-09-03 22:00:00',
            ),
            5 => 
            array (
                'product_id' => 12,
                'serial' => '022016J032426',
                'warehouse_id' => 1,
                'json_data' => '{"serial": {"mac": "44F034AA0006", "serial": "022016J032426"}}',
                'created_at' => '2019-09-03 22:00:00',
                'updated_at' => '2019-09-03 22:00:00',
            ),
            6 => 
            array (
                'product_id' => 12,
                'serial' => '022016J032427',
                'warehouse_id' => 1,
                'json_data' => '{"serial": {"mac": "44F034AA0007", "serial": "022016J032427"}}',
                'created_at' => '2019-09-03 22:00:00',
                'updated_at' => '2019-09-03 22:00:00',
            ),
            7 => 
            array (
                'product_id' => 12,
                'serial' => '022016J032428',
                'warehouse_id' => 1,
                'json_data' => '{"serial": {"mac": "44F034AA0008", "serial": "022016J032428"}}',
                'created_at' => '2019-09-03 22:00:00',
                'updated_at' => '2019-09-03 22:00:00',
            ),
            8 => 
            array (
                'product_id' => 12,
                'serial' => '022016J032429',
                'warehouse_id' => 1,
                'json_data' => '{"serial": {"mac": "44F034AA0009", "serial": "022016J032429"}}',
                'created_at' => '2019-09-03 22:00:00',
                'updated_at' => '2019-09-03 22:00:00',
            ),
        ));
        
        
    }
}