<?php

use Illuminate\Database\Seeder;

class PersonTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('person_types')->delete();
        
        \DB::table('person_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'Customer',
                'created_at' => '2019-07-09 09:09:57',
                'updated_at' => '2019-07-09 09:09:57',
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'Supplier',
                'created_at' => '2019-07-09 09:09:57',
                'updated_at' => '2019-07-09 09:09:57',
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'Prospect',
                'created_at' => '2019-07-09 09:09:57',
                'updated_at' => '2019-07-09 09:09:57',
            ),
        ));
        
        
    }
}