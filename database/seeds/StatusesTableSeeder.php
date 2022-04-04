<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class StatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        \DB::update('SET sql_mode="NO_AUTO_VALUE_ON_ZERO";');

        \DB::table('statuses')->delete();

        \DB::table('statuses')->insert(array(
            0 =>
            array(
                'id' => 0,
                'status' => 'Concept',
                'label' => 'Concept',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            1 =>
            array(
                'id' => 0,
                'status' => 'Concept',
                'label' => 'CONCEPT / DRAFT',
                'status_type_id' => 4,
                'created_at' => '2020-07-23 14:00:00',
                'updated_at' => NULL,
            ),
            2 =>
            array(
                'id' => 0,
                'status' => 'Disabled',
                'label' => 'DISABLED',
                'status_type_id' => 5,
                'created_at' => '2020-07-23 14:00:00',
                'updated_at' => NULL,
            ),
            3 =>
            array(
                'id' => 0,
                'status' => 'new',
                'label' => 'New',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            4 =>
            array(
                'id' => 1,
                'status' => 'Finalizing',
                'label' => 'Finalizing',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            5 =>
            array(
                'id' => 1,
                'status' => 'Ongoing',
                'label' => 'ONGOING',
                'status_type_id' => 4,
                'created_at' => '2020-07-23 14:00:00',
                'updated_at' => NULL,
            ),
            6 =>
            array(
                'id' => 1,
                'status' => 'Active',
                'label' => 'ACTIVE',
                'status_type_id' => 5,
                'created_at' => '2020-07-23 14:00:00',
                'updated_at' => NULL,
            ),
            7 =>
            array(
                'id' => 2,
                'status' => 'Invoice processing',
                'label' => 'Invoice processing',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            8 =>
            array(
                'id' => 2,
                'status' => 'Terminated',
                'label' => 'TERMINATED',
                'status_type_id' => 4,
                'created_at' => '2020-07-23 14:00:00',
                'updated_at' => NULL,
            ),
            9 =>
            array(
                'id' => 2,
                'status' => 'Unavailable',
                'label' => 'UNAVAILABLE',
                'status_type_id' => 5,
                'created_at' => '2020-07-23 14:00:00',
                'updated_at' => NULL,
            ),
            10 =>
            array(
                'id' => 5,
                'status' => 'Creating PDF',
                'label' => 'Creating PDF',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            11 =>
            array(
                'id' => 6,
                'status' => 'Sending email',
                'label' => 'Sending email',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            12 =>
            array(
                'id' => 7,
                'status' => 'Printing on paper',
                'label' => 'Printing on paper',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            13 =>
            array(
                'id' => 10,
                'status' => 'Close',
                'label' => 'Close',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            14 =>
            array(
                'id' => 10,
                'status' => 'inactive',
                'label' => 'Inactive',
                'status_type_id' => 3,
                'created_at' => '2020-07-16 14:00:00',
                'updated_at' => NULL,
            ),
            15 =>
            array(
                'id' => 10,
                'status' => 'creating_invoices',
                'label' => 'Creating invoices',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            16 =>
            array(
                'id' => 11,
                'status' => 'creation_failed',
                'label' => 'Failed to create invoices',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            17 =>
            array(
                'id' => 12,
                'status' => 'invoices_created',
                'label' => 'Invoices created',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            18 =>
            array(
                'id' => 20,
                'status' => 'Open',
                'label' => 'Open',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            19 =>
            array(
                'id' => 20,
                'status' => 'eosavailabilitycheck',
                'label' => 'Provisioning',
                'status_type_id' => 3,
                'created_at' => '2020-07-16 14:00:00',
                'updated_at' => NULL,
            ),
            20 =>
            array(
                'id' => 20,
                'status' => 'sending_invoices',
                'label' => 'Sending invoice emails',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            21 =>
            array(
                'id' => 21,
                'status' => 'sending_failed',
                'label' => 'Failed to send emails',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            22 =>
            array(
                'id' => 22,
                'status' => 'invoices_sent',
                'label' => 'Invoice emails have been sent',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            23 =>
            array(
                'id' => 30,
                'status' => 'Direct Debit in transit',
                'label' => 'Direct Debit in transit',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            24 =>
            array(
                'id' => 30,
                'status' => 'creating_dd_file',
                'label' => 'Creating direct debit file',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            25 =>
            array(
                'id' => 31,
                'status' => 'dd_file_failed',
                'label' => 'Direct debit file creation failed',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            26 =>
            array(
                'id' => 32,
                'status' => 'dd_file_created',
                'label' => 'Direct debit file created',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            27 =>
            array(
                'id' => 40,
                'status' => 'Not Available',
                'label' => 'Not Available',
                'status_type_id' => 3,
                'created_at' => '2020-07-16 14:00:00',
                'updated_at' => NULL,
            ),
            28 =>
            array(
                'id' => 40,
                'status' => 'dd_file_downloaded',
                'label' => 'Direct debit file downloaded',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
            29 =>
            array(
                'id' => 50,
                'status' => 'Paid',
                'label' => 'Paid',
                'status_type_id' => 1,
                'created_at' => '2020-02-14 21:46:26',
                'updated_at' => NULL,
            ),
            30 =>
            array(
                'id' => 50,
                'status' => 'Error',
                'label' => 'Error',
                'status_type_id' => 3,
                'created_at' => '2020-07-16 14:00:00',
                'updated_at' => NULL,
            ),
            31 =>
            array(
                'id' => 60,
                'status' => 'Active',
                'label' => 'Active',
                'status_type_id' => 3,
                'created_at' => '2020-07-16 14:00:00',
                'updated_at' => NULL,
            ),
            32 =>
            array(
                'id' => 70,
                'status' => 'Terminating',
                'label' => 'Terminating',
                'status_type_id' => 3,
                'created_at' => '2020-07-16 14:00:00',
                'updated_at' => NULL,
            ),
            33 =>
            array(
                'id' => 80,
                'status' => 'Terminated',
                'label' => 'Terminated',
                'status_type_id' => 3,
                'created_at' => '2020-07-16 14:00:00',
                'updated_at' => NULL,
            ),
            34 =>
            array(
                'id' => 100,
                'status' => 'closed',
                'label' => 'Closed',
                'status_type_id' => 6,
                'created_at' => '2020-08-05 14:00:00',
                'updated_at' => NULL,
            ),
        ));

        \DB::update('UPDATE sales_invoices SET invoice_status=1 WHERE invoice_status=5;');
        \DB::update('UPDATE sales_invoices SET invoice_status=5 WHERE invoice_status=6;');
        \DB::update('UPDATE sales_invoices SET invoice_status=6 WHERE invoice_status=7;');
        \DB::update('UPDATE sales_invoices SET invoice_status=7 WHERE invoice_status=8;');

        Schema::enableForeignKeyConstraints();
    }
}
