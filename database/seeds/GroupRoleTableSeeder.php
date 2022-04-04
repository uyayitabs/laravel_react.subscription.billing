<?php

use Illuminate\Database\Seeder;

class GroupRoleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('group_roles')->delete();
        
        \DB::table('group_roles')->insert([
            //Admin
            [ //Relation
                'id' => 1, 'group_id' => 1, 'role_id' => 1,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15'
            ],[ //Tenant
                'id' => 2, 'group_id' => 1, 'role_id' => 2,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Plan
                'id' => 3, 'group_id' => 1, 'role_id' => 3,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //SalesInvoice
                'id' => 4, 'group_id' => 1, 'role_id' => 4,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Subscription
                'id' => 5, 'group_id' => 1, 'role_id' => 5,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Product
                'id' => 6, 'group_id' => 1, 'role_id' => 6,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //User
                'id' => 7, 'group_id' => 1, 'role_id' => 7,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],
            
            //Financial Controller
            [ //Tenant
                'id' => 8, 'group_id' => 2, 'role_id' => 2,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Relation
                'id' => 9, 'group_id' => 2, 'role_id' => 1,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15'
            ],[ //SalesInvoice
                'id' => 10, 'group_id' => 2, 'role_id' => 4,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Subscription
                'id' => 11, 'group_id' => 2, 'role_id' => 5,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Product
                'id' => 12, 'group_id' => 2, 'role_id' => 6,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Plan 
                'id' => 13, 'group_id' => 2, 'role_id' => 3,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //User 
                'id' => 14, 'group_id' => 2, 'role_id' => 7,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],
            
            //1st Level Engineer
            [ //Tenant
                'id' => 15, 'group_id' => 3, 'role_id' => 2,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Relation
                'id' => 16, 'group_id' => 3, 'role_id' => 1,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15'
            ],[ //SalesInvoice
                'id' => 17, 'group_id' => 3, 'role_id' => 4,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Subscription
                'id' => 18, 'group_id' => 3, 'role_id' => 5,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Product
                'id' => 19, 'group_id' => 3, 'role_id' => 6,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Plan 
                'id' => 20, 'group_id' => 3, 'role_id' => 3,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //User 
                'id' => 21, 'group_id' => 3, 'role_id' => 7,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],
            
            //2nd Level Engineer (NOC)
            [ //Tenant
                'id' => 22, 'group_id' => 4, 'role_id' => 2,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Relation
                'id' => 23, 'group_id' => 4, 'role_id' => 1,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15'
            ],[ //SalesInvoice
                'id' => 24, 'group_id' => 4, 'role_id' => 4,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Subscription
                'id' => 25, 'group_id' => 4, 'role_id' => 5,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Product
                'id' => 26, 'group_id' => 4, 'role_id' => 6,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Plan 
                'id' => 27, 'group_id' => 4, 'role_id' => 3,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //User 
                'id' => 28, 'group_id' => 4, 'role_id' => 7,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],
            
            //3nd  Level Engineer (NOC)
            [ //Tenant
                'id' => 29, 'group_id' => 5, 'role_id' => 2,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Relation
                'id' => 30, 'group_id' => 5, 'role_id' => 1,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15'
            ],[ //SalesInvoice
                'id' => 31, 'group_id' => 5, 'role_id' => 4,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Subscription
                'id' => 32, 'group_id' => 5, 'role_id' => 5,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Product
                'id' => 33, 'group_id' => 5, 'role_id' => 6,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Plan 
                'id' => 34, 'group_id' => 5, 'role_id' => 3,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //User 
                'id' => 35, 'group_id' => 5, 'role_id' => 7,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],
            
            //Backoffice Tenants Readonly
            [ //Tenant
                'id' => 36, 'group_id' => 6, 'role_id' => 2,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Relation
                'id' => 37, 'group_id' => 6, 'role_id' => 1,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15'
            ],[ //SalesInvoice
                'id' => 38, 'group_id' => 6, 'role_id' => 4,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Subscription
                'id' => 39, 'group_id' => 6, 'role_id' => 5,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Product
                'id' => 40, 'group_id' => 6, 'role_id' => 6,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //Plan 
                'id' => 41, 'group_id' => 6, 'role_id' => 3,
                'write' => 1, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],[ //User 
                'id' => 42, 'group_id' => 6, 'role_id' => 7,
                'write' => 0, 'read' => 1,
                'created_at' => '2019-10-25 15:15:15',
                'updated_at' => '2019-10-25 15:15:15',
            ],
        ]);        
    }
}