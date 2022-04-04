<?php

use Illuminate\Database\Seeder;

class StocksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('stocks')->delete();
        
        
        
    }
}