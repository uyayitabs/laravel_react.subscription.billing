<?php

use Illuminate\Database\Seeder;

class DunningsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('dunnings')->delete();
        
        
        
    }
}