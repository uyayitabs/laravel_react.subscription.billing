<?php

use Illuminate\Database\Seeder;

class VatCodesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('vat_codes')->delete();
        
        \DB::table('vat_codes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tenant_id' => 2,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            1 => 
            array (
                'id' => 2,
                'tenant_id' => 3,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            2 => 
            array (
                'id' => 3,
                'tenant_id' => 4,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            3 => 
            array (
                'id' => 4,
                'tenant_id' => 5,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            4 => 
            array (
                'id' => 5,
                'tenant_id' => 6,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            5 => 
            array (
                'id' => 6,
                'tenant_id' => 7,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            6 => 
            array (
                'id' => 7,
                'tenant_id' => 8,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            7 => 
            array (
                'id' => 8,
                'tenant_id' => 9,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            8 => 
            array (
                'id' => 9,
                'tenant_id' => 10,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            9 => 
            array (
                'id' => 10,
                'tenant_id' => 11,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            10 => 
            array (
                'id' => 11,
                'tenant_id' => 12,
                'vat_percentage' => '0.21',
                'description' => NULL,
                'active_from' => NULL,
                'active_to' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
        ));
        
        
    }
}