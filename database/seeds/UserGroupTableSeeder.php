<?php

use Illuminate\Database\Seeder;

class UserGroupTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('user_groups')->delete();
        
        \DB::table('user_groups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'group_id' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 2,
                'group_id' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 3,
                'group_id' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 4,
                'group_id' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 5,
                'group_id' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => 6,
                'group_id' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ),
            6 => 
            array (
                'id' => 7,
                'user_id' => 7,
                'group_id' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            )
        ));
        
        
    }
}