<?php

use Illuminate\Database\Seeder;

class JsonDataTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('json_data')->delete();
        
        \DB::table('json_data')->insert(array (
            0 => 
            array (
                'id' => 1,
                'tenant_id' => 7,
                'relation_id' => NULL,
                'plan_id' => NULL,
                'plan_line_id' => NULL,
                'subscription_id' => NULL,
                'subscription_line_id' => NULL,
                'product_id' => 12,
                'json_data' => '{"m7": {"type": "stb", "productId": "324"}}',
                'transaction_id' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
            1 => 
            array (
                'id' => 2,
                'tenant_id' => 7,
                'relation_id' => NULL,
                'plan_id' => NULL,
                'plan_line_id' => NULL,
                'subscription_id' => NULL,
                'subscription_line_id' => NULL,
                'product_id' => 6,
                'json_data' => '{"m7": {"type": "basis", "productId": "316"}}',
                'transaction_id' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
            2 => 
            array (
                'id' => 3,
                'tenant_id' => 7,
                'relation_id' => NULL,
                'plan_id' => NULL,
                'plan_line_id' => NULL,
                'subscription_id' => NULL,
                'subscription_line_id' => NULL,
                'product_id' => 79,
                'json_data' => '{"m7": {"type": "basis", "productId": "319"}}',
                'transaction_id' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
            3 => 
            array (
                'id' => 4,
                'tenant_id' => 7,
                'relation_id' => NULL,
                'plan_id' => NULL,
                'plan_line_id' => NULL,
                'subscription_id' => NULL,
                'subscription_line_id' => NULL,
                'product_id' => 78,
                'json_data' => '{"m7": {"type": "basis", "productId": "318"}}',
                'transaction_id' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
            4 => 
            array (
                'id' => 5,
                'tenant_id' => 7,
                'relation_id' => NULL,
                'plan_id' => NULL,
                'plan_line_id' => NULL,
                'subscription_id' => NULL,
                'subscription_line_id' => NULL,
                'product_id' => 22,
                'json_data' => '{"m7": {"type": "addon", "productId": "320"}}',
                'transaction_id' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
            5 => 
            array (
                'id' => 6,
                'tenant_id' => 7,
                'relation_id' => NULL,
                'plan_id' => NULL,
                'plan_line_id' => NULL,
                'subscription_id' => NULL,
                'subscription_line_id' => NULL,
                'product_id' => 71,
                'json_data' => '{"m7": {"type": "addon", "productId": "323"}}',
                'transaction_id' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
            6 => 
            array (
                'id' => 7,
                'tenant_id' => 7,
                'relation_id' => NULL,
                'plan_id' => NULL,
                'plan_line_id' => NULL,
                'subscription_id' => NULL,
                'subscription_line_id' => NULL,
                'product_id' => 19,
                'json_data' => '{"m7": {"type": "addon", "productId": "322"}}',
                'transaction_id' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
            7 => 
            array (
                'id' => 8,
                'tenant_id' => 7,
                'relation_id' => NULL,
                'plan_id' => NULL,
                'plan_line_id' => NULL,
                'subscription_id' => NULL,
                'subscription_line_id' => NULL,
                'product_id' => 17,
                'json_data' => '{"m7": {"type": "addon", "productId": "322"}}',
                'transaction_id' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
            8 => 
            array (
                'id' => 9,
                'tenant_id' => 7,
                'relation_id' => NULL,
                'plan_id' => NULL,
                'plan_line_id' => NULL,
                'subscription_id' => NULL,
                'subscription_line_id' => NULL,
                'product_id' => 80,
                'json_data' => '{"m7": {"type": "addon", "productId": "321"}}',
                'transaction_id' => NULL,
                'created_at' => '2019-04-08 22:00:00',
                'updated_at' => '2019-04-08 22:00:00',
            ),
        ));
        
        
    }
}