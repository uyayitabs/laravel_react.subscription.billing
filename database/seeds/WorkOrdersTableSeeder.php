<?php

use Illuminate\Database\Seeder;

class WorkOrdersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('work_orders')->delete();
        
        
        
    }
}