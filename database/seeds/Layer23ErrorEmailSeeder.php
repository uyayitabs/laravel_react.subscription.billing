<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Layer23ErrorEmailSeeder extends Seeder
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
                    'tenant_id' => 7,
                    'product_id' => NULL,
                    'type' => 'layer23.error',
                ],
                [
                    'tenant_id' => 7,
                    'product_id' => NULL,
                    'type' => 'layer23.error',
                    'from_name' => 'GRID',
                    'from_email' => 'noreply@fiber.nl',
                    'bcc_email' => 'mark@f2x.nl',
                    'subject' => 'Layer23 - {{$error_subject}}',
                    'body_html' => '<!doctype html>
<html lang="nl">
<head>
<meta charset="utf-8">
<title></title>
</head>
<body>
<p><pre>{!! $error_details !!}</pre></p>
<p><a href="{!! $subscription_url !!}" _target="blank">{!! $subscription_url !!}</a></p>
</body>
</html>',
                    'created_at' => '2020-10-21 18:25:15',
                    'updated_at' => NULL
                ]
            );
    }
}
