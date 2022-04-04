<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSalesInvoiceSummaryViewAddBillingRunId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE OR REPLACE VIEW `v_sales_invoice_summaries` AS
                SELECT
                    `s`.`id` AS `id`,
                    `s`.`date` AS `date`,
                    `s`.`billing_run_id` AS `billing_run_id`,
                    `s`.`price` AS `price_excl_vat`,
                    `s`.`price_total` AS `price_incl_vat`,
                    `s`.`invoice_status` AS `invoice_status`,
                    `ss`.`label` AS `invoice_status_label`,
                    `s`.`invoice_no` AS `invoice_no`,
                    `r`.`customer_number` AS `customer_number`,
                    `r`.`id` AS `relation_id`,
                    `r`.`tenant_id` AS `tenant_id`,
                    CONCAT(`p`.`first_name`," ",COALESCE(`p`.`middle_name`,"")," ",COALESCE(`p`.`last_name`,"")) AS `full_name`,
                    `p`.`email` AS `email`,
                    CONCAT(`a`.`street1`," ",COALESCE(`a`.`house_number`,"")," ",COALESCE(`a`.`house_number_suffix`,"")," ",COALESCE(`a`.`room`,"")," ",COALESCE(`a`.`zipcode`,"")," ",COALESCE(`c`.`name`,"")) AS `full_address`,
                    `sim`.`value` AS `sales_invoice_reminder`
                FROM `sales_invoices` `s`
                LEFT JOIN `statuses` `ss` ON `ss`.`id` = `s`.`invoice_status` AND `ss`.`status_type_id` = 1
                LEFT JOIN `relations` `r` ON `r`.`id` = `s`.`relation_id`
                LEFT JOIN `relations_persons` `rp` ON `r`.`id` = `rp`.`relation_id` AND `rp`.`primary` = 1
                LEFT JOIN `persons` `p` ON `p`.`id` = `rp`.`person_id`
                LEFT JOIN `addresses` `a` ON `a`.`relation_id` = `r`.`id` AND `a`.`primary` = 1 AND `a`.`address_type_id` = 3
                LEFT JOIN `cities` `c` ON `c`.`id` = `a`.`city_id`
                LEFT JOIN `sales_invoice_metas` `sim` on `sim`.`sales_invoice_id` = `s`.`id` AND `sim`.`key` = "reminder_status";
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('
            CREATE OR REPLACE VIEW `v_sales_invoice_summaries` AS
                SELECT
                    `s`.`id` AS `id`,
                    `s`.`date` AS `date`,
                    `s`.`price` AS `price_excl_vat`,
                    `s`.`price_total` AS `price_incl_vat`,
                    `s`.`invoice_status` AS `invoice_status`,
                    `ss`.`label` AS `invoice_status_label`,
                    `s`.`invoice_no` AS `invoice_no`,
                    `r`.`customer_number` AS `customer_number`,
                    `r`.`id` AS `relation_id`,
                    `r`.`tenant_id` AS `tenant_id`,
                    CONCAT(`p`.`first_name`," ",COALESCE(`p`.`middle_name`,"")," ",COALESCE(`p`.`last_name`,"")) AS `full_name`,
                    `p`.`email` AS `email`,
                    CONCAT(`a`.`street1`," ",COALESCE(`a`.`house_number`,"")," ",COALESCE(`a`.`house_number_suffix`,"")," ",COALESCE(`a`.`room`,"")," ",COALESCE(`a`.`zipcode`,"")," ",COALESCE(`c`.`name`,"")) AS `full_address`,
                    `sim`.`value` AS `sales_invoice_reminder`
                FROM `sales_invoices` `s`
                LEFT JOIN `statuses` `ss` ON `ss`.`id` = `s`.`invoice_status` AND `ss`.`status_type_id` = 1
                LEFT JOIN `relations` `r` ON `r`.`id` = `s`.`relation_id`
                LEFT JOIN `relations_persons` `rp` ON `r`.`id` = `rp`.`relation_id` AND `rp`.`primary` = 1
                LEFT JOIN `persons` `p` ON `p`.`id` = `rp`.`person_id`
                LEFT JOIN `addresses` `a` ON `a`.`relation_id` = `r`.`id` AND `a`.`primary` = 1 AND `a`.`address_type_id` = 3
                LEFT JOIN `cities` `c` ON `c`.`id` = `a`.`city_id`
                LEFT JOIN `sales_invoice_metas` `sim` on `sim`.`sales_invoice_id` = `s`.`id` AND `sim`.`key` = "reminder_status";
        ');
    }
}
