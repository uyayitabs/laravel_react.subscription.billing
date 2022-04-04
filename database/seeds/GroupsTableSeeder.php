<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('groups')->delete();
        
        \DB::table('groups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Admin (Full)',
                'description' => 'Admin Users for Fiber NL',
                'tenant_id' => 7,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Financial Controller',
                'description' => 'Financial Controller Users for Fiber NL',
                'tenant_id' => 7,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => '1st Level Engineer',
                'description' => '1st Level Engineer for Fiber NL',
                'tenant_id' => 7,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => '2nd Level Engineer (NOC)',
                'description' => '1st Level Engineer for Fiber NL',
                'tenant_id' => 7,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => '3rd Level Engineer (NOC)',
                'description' => '1st Level Engineer for Fiber NL',
                'tenant_id' => 7,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Backoffice Tenants Readonly',
                'description' => '1st Level Engineer for Fiber NL',
                'tenant_id' => 7,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
        ));
        
        
    }
}