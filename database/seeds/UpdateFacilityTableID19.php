<?php

use Illuminate\Database\Seeder;

class UpdateFacilityTableID19 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::update('UPDATE facility SET DESCRIPTION="Layer23" WHERE id = 19;');
        \DB::update('UPDATE facility SET DESCRIPTION="M7" WHERE id = 16;');
    }
}
