<?php

use Illuminate\Database\Seeder;

class ProductsTypesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('product_types')->delete();
        
        \DB::table('product_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'Free',
                'description' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'NRC',
                'description' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'MRC',
                'description' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            3 => 
            array (
                'id' => 4,
                'type' => 'QRC',
                'description' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            4 => 
            array (
                'id' => 5,
                'type' => 'YRC',
                'description' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            5 => 
            array (
                'id' => 6,
                'type' => 'Deposit',
                'description' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            6 => 
            array (
                'id' => 7,
                'type' => 'Discount',
                'description' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
        ));
        
        
    }
}