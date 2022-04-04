<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateOrderErrorEmailSeeder extends Seeder
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
                    'type' => 'order.error',
                ],
                [
                    'tenant_id' => NULL,
                    'product_id' => NULL,
                    'type' => 'order.error',
                    'from_name' => 'GRID',
                    'from_email' => 'grid@f2x.nl',
                    'bcc_email' => 'mark@f2x.nl,marisa.vanvelzen@xsprovider.nl',
                    'subject' => 'Error saving new order',
                    'body_html' => '<!doctype html>
<html lang="nl">
<head>
<meta charset="utf-8">
<title></title>
</head>
<body>
<p>There was an error processing a new order.</p>
<p>Error: </p>
<p><pre>{!! $error_details !!}</pre></p>
</body>
</html>',
                    'created_at' => now(),
                    'updated_at' => NULL
                ]
            );
    }
}
