<?php

use Illuminate\Database\Seeder;

class SalesOrdersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('sales_orders')->delete();
        
        
        
    }
}