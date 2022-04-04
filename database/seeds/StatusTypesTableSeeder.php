<?php

use Illuminate\Database\Seeder;

class StatusTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('status_types')->delete();

        \DB::table('status_types')->insert(array(
            0 =>
            array(
                'id' => 1,
                'type' => 'invoice',
                'created_at' => '2020-02-17 18:51:12',
                'updated_at' => NULL,
            ),
            1 =>
            array(
                'id' => 2,
                'type' => 'payment',
                'created_at' => '2020-02-17 18:51:12',
                'updated_at' => NULL,
            ),
            2 =>
            array(
                'id' => 3,
                'type' => 'connection',
                'created_at' => '2020-07-16 14:00:00',
                'updated_at' => NULL,
            ),
            3 =>
            array(
                'id' => 4,
                'type' => 'subscription',
                'created_at' => '2020-07-23 14:00:00',
                'updated_at' => NULL,
            ),
            4 =>
            array(
                'id' => 5,
                'type' => 'product',
                'created_at' => '2020-07-23 14:00:00',
                'updated_at' => NULL,
            ),
            5 =>
            array(
                'id' => 6,
                'type' => 'billing_run',
                'created_at' => '2020-07-29 14:00:00',
                'updated_at' => NULL,
            ),
        ));
    }
}
