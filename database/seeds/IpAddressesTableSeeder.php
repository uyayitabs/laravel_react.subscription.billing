<?php

use Illuminate\Database\Seeder;

class IpAddressesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('ip_addresses')->delete();
        
        
        
    }
}