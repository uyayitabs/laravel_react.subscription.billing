<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImportCDREmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = now();
        $rows = [
            [
                [
                    'tenant_id' => 7,
                    'type' => 'cdr_import.success',
                ],
                [
                    'tenant_id' => 7,
                    'type' => 'cdr_import.success',
                    'from_name' => 'GRID',
                    'from_email' => 'grid@f2x.nl',
                    'bcc_email' => 'cdmigrations@xsprovider.nl,n.wegen@f2x.nl',
                    'subject' => '[{{ $env }}]  CDR Import for {{ $tenantName }} completed',
                    'body_html' => '<!doctype html>
    <html lang="nl">
    <head>
    <meta charset="utf-8">
    <title></title>
    </head>
    <body>
    <p>Filename: {{ $filename }}</p>
    <p>Saved record(s): {{ $processed_records }}</p>
    </body>
    </html>',
                    'created_at' => $now,
                    'updated_at' => NULL,
                ],
            ],
            [
                [
                    'tenant_id' => 7,
                    'type' => 'cdr_import.error',
                ],
                [
                    'tenant_id' => 7,
                    'type' => 'cdr_import.error',
                    'from_name' => 'GRID',
                    'from_email' => 'grid@f2x.nl',
                    'bcc_email' => 'cdmigrations@xsprovider.nl,n.wegen@f2x.nl',
                    'subject' => '[{{ $env }}]  CDR Import for {{ $tenantName }} failed',
                    'body_html' => '<!doctype html>
    <html lang="nl">
    <head>
    <meta charset="utf-8">
    <title></title>
    </head>
    <body>
    <p>Filename: {{ $filename }}</p>
    <p>Saved record(s): {{ $processed_records }}</p>
    <p>With issue(s): {{ $failed_records }}</p>
    </body>
    </html>',
                    'created_at' => $now,
                    'updated_at' => NULL,
                ]
            ],
            [
                [
                    'tenant_id' => 8,
                    'type' => 'cdr_import.success',
                ],
                [
                    'tenant_id' => 8,
                    'type' => 'cdr_import.success',
                    'from_name' => 'GRID',
                    'from_email' => 'grid@f2x.nl',
                    'bcc_email' => 'cdmigrations@xsprovider.nl,n.wegen@f2x.nl',
                    'subject' => '[{{ $env }}]  CDR Import for {{ $tenantName }} completed',
                    'body_html' => '<!doctype html>
    <html lang="nl">
    <head>
    <meta charset="utf-8">
    <title></title>
    </head>
    <body>
    <p>Filename: {{ $filename }}</p>
    <p>Saved record(s): {{ $processed_records }}</p>
    </body>
    </html>',
                    'created_at' => $now,
                    'updated_at' => NULL,
                ],
            ],
            [
                [
                    'tenant_id' => 8,
                    'type' => 'cdr_import.error',
                ],
                [
                    'tenant_id' => 8,
                    'type' => 'cdr_import.error',
                    'from_name' => 'GRID',
                    'from_email' => 'grid@f2x.nl',
                    'bcc_email' => 'cdmigrations@xsprovider.nl,n.wegen@f2x.nl',
                    'subject' => '[{{ $env }}]  CDR Import for {{ $tenantName }} failed',
                    'body_html' => '<!doctype html>
    <html lang="nl">
    <head>
    <meta charset="utf-8">
    <title></title>
    </head>
    <body>
    <p>Filename: {{ $filename }}</p>
    <p>Saved record(s): {{ $processed_records }}</p>
    <p>With issue(s): {{ $failed_records }}</p>
    </body>
    </html>',
                    'created_at' => $now,
                    'updated_at' => NULL,
                ]
            ],
        ];

        foreach ($rows as $row) {
            DB::table('email_templates')
                ->updateOrInsert(
                    $row[0],
                    $row[1]
                );
        }
    }

}
