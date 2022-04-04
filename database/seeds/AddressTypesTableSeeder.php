<?php

use Illuminate\Database\Seeder;

class AddressTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('address_types')->delete();
        
        \DB::table('address_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'Contact',
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'Provisioning',
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'Billing',
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
        ));
        
        
    }
}