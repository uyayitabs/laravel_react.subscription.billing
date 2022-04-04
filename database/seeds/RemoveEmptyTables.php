<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RemoveEmptyTables extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::dropIfExists('network_interfaces');
        Schema::dropIfExists('network_ports');
        Schema::dropIfExists('network_devices');
    }
}
