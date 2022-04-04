<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateOrderToCustomerSubscriptionSuccessSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      DB::table('email_templates')
         ->updateOrInsert(
            [
               'type' => 'order.cust_subsc.success',
               'tenant_id' => 7,
            ],
            [
               'tenant_id' => 7,
               'product_id' => NULL,
               'type' => 'order.cust_subsc.success',
               'from_name' => 'GRID',
               'from_email' => 'grid@f2x.nl',
               'bcc_email' => 'mark@f2x.nl,marisa.vanvelzen@xsprovider.nl',
               'subject' => 'Nieuwe order {{$order_address_city}} ({{$order_address_status}})',
               'body_html' => '<!doctype html>
               <html lang="nl">
               <head>
               <meta charset="utf-8">
               <title></title>
               </head>
               <body>
               <p>Customer details: <br>
               <b>Customer number:</b> {{$customer_number}}<br>
               <b>Name:</b> {{$customer_name}}<br>
               <b>Email:</b> {{$customer_email}}<br>
               <a href="{!! $customer_url !!}" _target="blank">{!! $customer_url !!}</a><br>
               <a href="{!! $subscription_url !!}" _target="blank">{!! $subscription_url !!}</a>
               </p>
               <p><pre>{!! $order_details !!}</pre></p>
               </body>
               </html>',
               'created_at' => now(),
               'updated_at' => NULL
            ]
         );
   }
}
