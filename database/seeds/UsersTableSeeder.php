<?php

use Illuminate\Database\Seeder;
use App\Role;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        $role_user = Role::where('name', 'User')->first();
        $role_author = Role::where('name', 'Author')->first();
        $role_admin = Role::where('name', 'Admin')->first();

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'username' => 'n.wegen@f2x.nl',
                'person_id' => 3163,
                'tenant_id' => 7,
                'password' => '$2y$10$xxM8pShmUUl8Vqa5movt1uuzL11i.b14ZvAGM43Y8yrJ3KcrkaJrK',
                'remember_token' => NULL,
                'created_at' => '2019-10-08 06:09:35',
                'updated_at' => '2019-10-08 06:09:35',
            ),
            1 => 
            array (
                'id' => 2,
                'username' => 'chej@f2x.nl',
                'person_id' => 2087,
                'tenant_id' => 7,
                'password' => '$2y$10$xxM8pShmUUl8Vqa5movt1uuzL11i.b14ZvAGM43Y8yrJ3KcrkaJrK',
                'remember_token' => NULL,
                'created_at' => '2019-08-06 14:55:25',
                'updated_at' => '2019-08-06 14:55:25',
            ),
            2 => 
            array (
                'id' => 3,
                'username' => 'bryan@f2x.nl',
                'person_id' => 2088,
                'tenant_id' => 7,
                'password' => '$2y$10$xxM8pShmUUl8Vqa5movt1uuzL11i.b14ZvAGM43Y8yrJ3KcrkaJrK',
                'remember_token' => NULL,
                'created_at' => '2019-06-15 05:47:29',
                'updated_at' => '2019-06-15 05:47:29',
            ),
            3 => 
            array (
                'id' => 4,
                'username' => 'mark@f2x.nl',
                'person_id' => 2089,
                'tenant_id' => 7,
                'password' => '$2y$10$HB1Ot04YF5GrnejmNxKaBub5NjuGMDgTpA0ki5Qz8OMrTKAzNt2ue',
                'remember_token' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'username' => 'jan@f2x.nl',
                'person_id' => 2090,
                'tenant_id' => 7,
                'password' => '$2y$10$/A4vMpNupYWgrHqN6GAZQ.hOwvvVLai0QoPNUv5Zv8QimDIuXkJV6',
                'remember_token' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'username' => 'martijn.schafstad@xsprovider.nl',
                'person_id' => 2092,
                'tenant_id' => 7,
                'password' => '$2y$10$gTScR/TVaiHigWt8iDPG2./NgX4LJOAUho8Im5j/Fbuo.2O7y4h4C',
                'remember_token' => NULL,
                'created_at' => '2019-07-25 05:18:24',
                'updated_at' => '2019-07-25 05:18:24',
            ),
            6 => 
            array (
                'id' => 7,
                'username' => 'dennis.peters@xsprovider.nl',
                'person_id' => 3164,
                'tenant_id' => 1,
                'password' => '$2y$10$kHVEvoCnHqXSjqLCB7D8b.kbVh/lcbXN9n0ROb.B/w5XZwg.K/B1G',
                'remember_token' => NULL,
                'created_at' => '2019-10-08 05:47:29',
                'updated_at' => '2019-10-08 05:47:29',
            ),
        ));
        
        
    }
}