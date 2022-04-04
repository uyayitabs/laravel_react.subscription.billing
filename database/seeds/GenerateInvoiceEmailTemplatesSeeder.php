<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenerateInvoiceEmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = array(
            array(
                'tenant_id' => 7,
                'type' => 'invoice_queue.success',
                'from_name' => 'GRID',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => NULL,
                'subject' => 'Invoice generation for billing run #{{$billingRunId}} for {{$tenantName}} has finished successfully',
                'body_html' => '<!doctype html>
    <html lang="nl">
    <head>
    <meta charset="utf-8">
    <title></title>
    </head>
    <body>
    <p>You can now review invoices and send invoice emails.<br>
    <a href="{!! $adminToolsUrl !!}" _target="blank">Open the Admin tools ></a>
    </p>
    </body>
    </html>',
                'created_at' => '2020-09-24 00:00:00',
                'updated_at' => NULL,
            ),
            array(
                'tenant_id' => 7,
                'type' => 'invoice_queue.error',
                'from_name' => 'GRID',
                'from_email' => 'noreply@fiber.nl',
                'bcc_email' => NULL,
                'subject' => 'Error: Invoice generation for billing run #{{$billingRunId}} for {{$tenantName}} failed',
                'body_html' => '<!doctype html>
    <html lang="nl">
    <head>
    <meta charset="utf-8">
    <title></title>
    </head>
    <body>
    <p>The following error occurred while generating invoices:<</p>
    <pre>{!! $errorData !!}</pre>
    </body>
    </html>',
                'created_at' => '2020-09-24 00:00:00',
                'updated_at' => NULL,
            ),
            array(
                'tenant_id' => 8,
                'type' => 'invoice_queue.success',
                'from_name' => 'GRID',
                'from_email' => 'noreply@stipte.nl',
                'bcc_email' => NULL,
                'subject' => 'Invoice generation for billing run #{{$billingRunId}} for {{$tenantName}} has finished successfully',
                'body_html' => '<!doctype html>
    <html lang="nl">
    <head>
    <meta charset="utf-8">
    <title></title>
    </head>
    <body>
    <p>You can now review invoices and send invoice emails.<br>
    <a href="{!! $adminToolsUrl !!}" _target="blank">Open the Admin tools ></a>
    </p>
    </body>
    </html>',
                'created_at' => '2020-09-24 00:00:00',
                'updated_at' => NULL,
            ),
            array(
                'tenant_id' => 8,
                'type' => 'invoice_queue.error',
                'from_name' => 'GRID',
                'from_email' => 'noreply@stipte.nl',
                'bcc_email' => NULL,
                'subject' => 'Error: Invoice generation for billing run #{{$billingRunId}} for {{$tenantName}} failed',
                'body_html' => '<!doctype html>
    <html lang="nl">
    <head>
    <meta charset="utf-8">
    <title></title>
    </head>
    <body>
    <p>The following error occurred while generating invoices:<</p>
    <pre>{!! $errorData !!}</pre>
    </body>
    </html>',
                'created_at' => '2020-09-24 00:00:00',
                'updated_at' => NULL,
            ),
        );

        DB::table('email_templates')->insert($items);
    }
}
