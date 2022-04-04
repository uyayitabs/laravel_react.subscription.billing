<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RabobankPaymentProcessErrorEmailSeeder extends Seeder
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
                    'type' => 'rabobank_payment_processing.error',
                ],
                [
                    'tenant_id' => NULL,
                    'product_id' => NULL,
                    'type' => 'rabobank_payment_processing.error',
                    'from_name' => 'GRID',
                    'from_email' => 'noreply@fiber.nl',
                    'bcc_email' => 'mark@f2x.nl',
                    'subject' => 'Unexpected payment type found in Rabobank file',
                    'body_html' => '<!doctype html>
<html lang="nl">
<head>
<meta charset="utf-8">
<title></title>
</head>
<body>
<p>
<b>code: </b>{!! $code !!}<br>
<b>xml: </b>{!! $xml_file !!}
</p>
<p><pre>{!! $error_details !!}</pre></p>
</body>
</html>',
                    'created_at' => '2020-12-10 00:25:15',
                    'updated_at' => NULL,
                ]
            );
    }
}
