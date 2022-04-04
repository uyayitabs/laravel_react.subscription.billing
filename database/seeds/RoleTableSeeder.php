<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->delete();

        $role_user = new Role();
        $role_user->module = 'RelationController';
        $role_user->name = 'Relation Controller Access';
        $role_user->save();
        
        $role_user = new Role();
        $role_user->module = 'TenantController';
        $role_user->name = 'Tenant Controller Access';
        $role_user->save();
        
        $role_user = new Role();
        $role_user->module = 'PlanController';
        $role_user->name = 'Plan Controller Access';
        $role_user->save();
        
        $role_user = new Role();
        $role_user->module = 'SalesInvoiceController';
        $role_user->name = 'Invoice Controller Access';
        $role_user->save();
        
        $role_user = new Role();
        $role_user->module = 'SubscriptionController';
        $role_user->name = 'Subscription Controller Access';
        $role_user->save();
        
        $role_user = new Role();
        $role_user->module = 'ProductController';
        $role_user->name = 'Product Controller Access';
        $role_user->save();
        
        $role_user = new Role();
        $role_user->module = 'UserController';
        $role_user->name = 'User Controller Access';
        $role_user->save();
    }
}
