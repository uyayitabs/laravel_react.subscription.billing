<?php

use Illuminate\Database\Seeder;

class CompaniesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tenants')->delete();
        
        \DB::table('tenants')->insert(array (
            0 => 
            array (
                'id' => 1,
                'parent_id' => NULL,
                'relation_id' => NULL,
                'name' => 'Root',
                'billing_day' => NULL,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            1 => 
            array (
                'id' => 2,
                'parent_id' => 1,
                'relation_id' => NULL,
                'name' => 'F2X Operator B.V.',
                'billing_day' => 1,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            2 => 
            array (
                'id' => 3,
                'parent_id' => 2,
                'relation_id' => NULL,
                'name' => 'Teleplaza Holding B.V.',
                'billing_day' => 1,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            3 => 
            array (
                'id' => 4,
                'parent_id' => 3,
                'relation_id' => NULL,
                'name' => 'XSHOLD B.V.',
                'billing_day' => 1,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            4 => 
            array (
                'id' => 5,
                'parent_id' => 3,
                'relation_id' => NULL,
                'name' => 'Teleplaza Networks B.V.',
                'billing_day' => 1,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            5 => 
            array (
                'id' => 6,
                'parent_id' => 4,
                'relation_id' => NULL,
                'name' => 'XS Provider B.V.',
                'billing_day' => 1,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            6 => 
            array (
                'id' => 7,
                'parent_id' => 5,
                'relation_id' => NULL,
                'name' => 'Fiber NL',
                'billing_day' => 20,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-26 17:26:27',
            ),
            7 => 
            array (
                'id' => 8,
                'parent_id' => 5,
                'relation_id' => NULL,
                'name' => 'Stipte',
                'billing_day' => 20,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            8 => 
            array (
                'id' => 9,
                'parent_id' => 5,
                'relation_id' => NULL,
                'name' => 'Hollands Glas',
                'billing_day' => 20,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            9 => 
            array (
                'id' => 10,
                'parent_id' => 4,
                'relation_id' => NULL,
                'name' => 'Stipte B.V.',
                'billing_day' => 20,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            10 => 
            array (
                'id' => 11,
                'parent_id' => 3,
                'relation_id' => NULL,
                'name' => 'XS Comfort B.V.',
                'billing_day' => 1,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
            11 => 
            array (
                'id' => 12,
                'parent_id' => 3,
                'relation_id' => NULL,
                'name' => 'Teleplaza Services B.V.',
                'billing_day' => 1,
                'created_at' => '2019-07-09 09:10:02',
                'updated_at' => '2019-07-09 09:10:02',
            ),
        ));
        
        
    }
}