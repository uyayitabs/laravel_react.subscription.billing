<?php

use Illuminate\Database\Seeder;

class PaymentConditionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payment_conditions')->delete();
        
        \DB::table('payment_conditions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tenant_id' => 7,
                'direct_debit' => NULL,
                'created_at' => '2020-03-05 17:37:35',
                'updated_at' => '2020-03-09 15:45:38',
                'status' => 1,
                'description' => 'Vooruitbetaald',
                'pay_in_advance' => 1,
                'net_days' => 0,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'tenant_id' => 7,
                'direct_debit' => NULL,
                'created_at' => '2020-03-05 17:38:01',
                'updated_at' => '2020-03-05 17:51:06',
                'status' => 1,
                'description' => 'Rekening 30 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 30,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'tenant_id' => 7,
                'direct_debit' => NULL,
                'created_at' => '2020-03-05 17:38:23',
                'updated_at' => '2020-03-09 15:45:43',
                'status' => 1,
                'description' => 'Rekening 14 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 14,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'tenant_id' => 7,
                'direct_debit' => NULL,
                'created_at' => '2020-03-05 17:38:51',
                'updated_at' => '2020-03-09 16:01:54',
                'status' => 1,
                'description' => 'Rekening 7 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 7,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'tenant_id' => 7,
                'direct_debit' => 1,
                'created_at' => '2020-03-05 17:39:23',
                'updated_at' => '2020-03-09 16:01:54',
                'status' => 1,
                'description' => 'Automatisch Incasso',
                'pay_in_advance' => NULL,
                'net_days' => 3,
                'default' => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'tenant_id' => 8,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Vooruitbetaald',
                'pay_in_advance' => 1,
                'net_days' => 0,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'tenant_id' => 8,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Rekening 30 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 30,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'tenant_id' => 8,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Rekening 14 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 14,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'tenant_id' => 8,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Rekening 7 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 7,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'tenant_id' => 8,
                'direct_debit' => 1,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Automatisch Incasso',
                'pay_in_advance' => NULL,
                'net_days' => 3,
                'default' => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'tenant_id' => 9,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Vooruitbetaald',
                'pay_in_advance' => 1,
                'net_days' => 0,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'tenant_id' => 9,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Rekening 30 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 30,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'tenant_id' => 9,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Rekening 14 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 14,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'tenant_id' => 9,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Rekening 7 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 7,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'tenant_id' => 9,
                'direct_debit' => 1,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Automatisch Incasso',
                'pay_in_advance' => NULL,
                'net_days' => 3,
                'default' => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'tenant_id' => 9,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Vooruitbetaald',
                'pay_in_advance' => 1,
                'net_days' => 0,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'tenant_id' => 9,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Rekening 30 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 30,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'tenant_id' => 9,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Rekening 14 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 14,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'tenant_id' => 9,
                'direct_debit' => NULL,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Rekening 7 dagen netto',
                'pay_in_advance' => NULL,
                'net_days' => 7,
                'default' => 0,
                'created_by' => 1,
                'updated_by' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'tenant_id' => 9,
                'direct_debit' => 1,
                'created_at' => '2020-03-17 07:48:45',
                'updated_at' => '2020-03-17 07:48:45',
                'status' => 1,
                'description' => 'Automatisch Incasso',
                'pay_in_advance' => NULL,
                'net_days' => 3,
                'default' => 1,
                'created_by' => 1,
                'updated_by' => 1,
            ),
        ));
        
        
    }
}