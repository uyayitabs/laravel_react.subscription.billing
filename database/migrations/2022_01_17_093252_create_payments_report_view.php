<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsReportView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE OR REPLACE VIEW `v_payments_report` AS
                SELECT
                    `payments`.`id`,
                    `date`,
                    `amount`,
                    `descr`,
                    `account_iban` AS `iban`,
                    `account_name`,
                    `return_code`,
                    `return_reason`,
                    `tenants`.`id` AS `tenant_id`,
                    `tenants`.`name` AS `tenant_name`,
                    `bank_accounts`.`relation_id`,
                    `payments`.`type`,
                    `payments`.`created_at`
                FROM `payments`
                LEFT JOIN `tenant_bank_accounts` ON `tenant_bank_accounts`.`id` = `payments`.`tenant_bank_account_id`
                LEFT JOIN `tenants` ON `tenants`.`id` = `tenant_bank_accounts`.`tenant_id`
                LEFT JOIN `bank_accounts` ON `bank_accounts`.`iban` = `payments`.`account_iban`;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('drop view v_payments_report');
    }
}
