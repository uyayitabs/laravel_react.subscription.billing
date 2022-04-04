<?php

use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('accounts')->delete();
        
        \DB::table('accounts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tenant_id' => 7,
                'description' => 'Bill run',
                'type' => 'Bill run',
                'code' => '1300A',
                'parent_id' => NULL,
                'export_code' => '1300A',
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            1 => 
            array (
                'id' => 2,
                'tenant_id' => 7,
                'description' => 'Revenue',
                'type' => 'Revenue',
                'code' => '8000',
                'parent_id' => NULL,
                'export_code' => '8000',
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            2 => 
            array (
                'id' => 3,
                'tenant_id' => 7,
                'description' => 'Revenue prepayment',
                'type' => 'Revenue prepayment',
                'code' => '1350',
                'parent_id' => NULL,
                'export_code' => '1350',
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            3 => 
            array (
                'id' => 4,
                'tenant_id' => 7,
                'description' => 'Af te dragen BTW',
                'type' => 'Af te dragen BTW',
                'code' => '1500',
                'parent_id' => NULL,
                'export_code' => '1500',
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            4 => 
            array (
                'id' => 5,
                'tenant_id' => 7,
                'description' => 'VAT Calculated Discount',
                'type' => 'VAT CD',
                'code' => '1418A',
                'parent_id' => NULL,
                'export_code' => '1418A',
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            5 => 
            array (
                'id' => 6,
                'tenant_id' => 7,
                'description' => 'Revenue Calculated Discount',
                'type' => 'Revenue CD',
                'code' => '1418B',
                'parent_id' => NULL,
                'export_code' => '1418B',
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            6 => 
            array (
                'id' => 7,
                'tenant_id' => 7,
            'description' => 'Direct Debit (Credit)',
                'type' => 'Direct Debit',
                'code' => '1300B',
                'parent_id' => NULL,
                'export_code' => '1300B',
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
            7 => 
            array (
                'id' => 8,
                'tenant_id' => 7,
            'description' => 'Direct Debit (Debit)',
                'type' => 'Direct Debit',
                'code' => '2700',
                'parent_id' => NULL,
                'export_code' => '2700',
                'created_at' => '2018-01-01 14:55:32',
                'updated_at' => '2018-01-01 14:55:32',
            ),
        ));
        
        
    }
}