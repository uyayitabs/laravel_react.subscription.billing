<?php

use Illuminate\Database\Seeder;

class NumberRangesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('number_ranges')->delete();
        
        \DB::table('number_ranges')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tenant_id' => 2,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FX{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            1 => 
            array (
                'id' => 2,
                'tenant_id' => 3,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FH{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            2 => 
            array (
                'id' => 3,
                'tenant_id' => 4,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FV{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            3 => 
            array (
                'id' => 4,
                'tenant_id' => 5,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FT{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            4 => 
            array (
                'id' => 5,
                'tenant_id' => 6,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FB{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            5 => 
            array (
                'id' => 6,
                'tenant_id' => 7,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1900000,
                'end' => 9999999,
                'format' => 'FS{:7number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            6 => 
            array (
                'id' => 7,
                'tenant_id' => 8,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FF{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            7 => 
            array (
                'id' => 8,
                'tenant_id' => 9,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FG{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            8 => 
            array (
                'id' => 9,
                'tenant_id' => 10,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FS{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            9 => 
            array (
                'id' => 10,
                'tenant_id' => 11,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FC{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            10 => 
            array (
                'id' => 11,
                'tenant_id' => 11,
                'type' => 'invoice_no',
                'description' => 'invoice number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FT{:6number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
            11 => 
            array (
                'id' => 12,
                'tenant_id' => 7,
                'type' => 'customer_number',
                'description' => 'customer number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FP{:6number}',
                'randomized' => '1',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => NULL,
            ),
            12 => 
            array (
                'id' => 13,
                'tenant_id' => 7,
                'type' => 'journal_no',
                'description' => 'journal number format',
                'start' => 1,
                'end' => 100000,
                'format' => 'FJ{:8number}',
                'randomized' => '0',
                'current' => NULL,
                'created_at' => '2019-07-09 15:10:02',
                'updated_at' => '2019-07-09 15:10:02',
            ),
        ));
        
        
    }
}