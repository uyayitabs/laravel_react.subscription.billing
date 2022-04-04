<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewsForDatatables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("create or replace view v_sales_invoice_reminders as
    SELECT
        `sim`.`id` AS `id`,
        `sim`.`sales_invoice_id` AS `sales_invoice_id`,
        `sim`.`key` AS `key`,
        `sim`.`value` AS `value`,
        `sim`.`updated_at` AS `date`,
        `si`.`relation_id` AS `relation_id`,
        `si`.`price_total` AS `price_total`,
        `si`.`invoice_no` AS `invoice_no`,
        `r`.`customer_number` AS `customer_number`,
        `r`.`tenant_id` AS `tenant_id`,
        `c`.`name` AS `city_name`,
        CONCAT(`p`.`first_name`,
                ' ',
                CONCAT(COALESCE(`p`.`middle_name`, ''),
                        ' ',
                        COALESCE(`p`.`last_name`, ''))) AS `full_name`
    FROM
        (((((`sales_invoice_metas` `sim`
        LEFT JOIN `sales_invoices` `si` ON ((`si`.`id` = `sim`.`sales_invoice_id`)))
        LEFT JOIN `relations` `r` ON ((`r`.`id` = `si`.`relation_id`)))
        LEFT JOIN `addresses` `a` ON ((`a`.`id` = `si`.`invoice_address_id`)))
        LEFT JOIN `cities` `c` ON ((`c`.`id` = `a`.`city_id`)))
        LEFT JOIN `persons` `p` ON ((`p`.`id` = `si`.`invoice_person_id`)));");

        DB::statement('create or replace view v_billing_run_summaries as
    SELECT
        `br`.`id` AS `billing_run_id`,
        `br`.`tenant_id` AS `tenant_id`,
        `br`.`date` AS `date`,
        `br`.`dd_file` AS `dd_file`,
        `br`.`status_id` AS `status_id`,
        `s`.`label` AS `status_label`,
        (SELECT
                COUNT(0)
            FROM
                `sales_invoices` `sc`
            WHERE
                (`sc`.`billing_run_id` = `br`.`id`)) AS `sales_invoice_count`,
        COALESCE((SELECT
                        SUM(`sc2`.`price`)
                    FROM
                        `sales_invoices` `sc2`
                    WHERE
                        (`sc2`.`billing_run_id` = `br`.`id`)),
                0) AS `price_sum`,
        COALESCE((SELECT
                        SUM(`sc2`.`price_vat`)
                    FROM
                        `sales_invoices` `sc2`
                    WHERE
                        (`sc2`.`billing_run_id` = `br`.`id`)),
                0) AS `price_vat_sum`,
        COALESCE((SELECT
                        SUM(`sc2`.`price_total`)
                    FROM
                        `sales_invoices` `sc2`
                    WHERE
                        (`sc2`.`billing_run_id` = `br`.`id`)),
                0) AS `price_total_sum`
    FROM
        (`billing_runs` `br`
        LEFT JOIN `statuses` `s` ON (((`s`.`status_type_id` = 7)
            AND (`s`.`id` = `br`.`status_id`))));');

        DB::statement("create or replace view v_relation_summaries as
    SELECT
        `r`.`id` AS `id`,
        `r`.`tenant_id` AS `tenant_id`,
        `r`.`customer_number` AS `customer_number`,
        CONCAT(`p`.`first_name`,
                ' ',
                CONCAT(COALESCE(`p`.`middle_name`, ''),
                        ' ',
                        COALESCE(`p`.`last_name`, ''))) AS `full_name`,
        REPLACE(REPLACE(CONCAT(`a`.`street1`,
                        ' ',
                        COALESCE(`a`.`house_number`, ''),
                        ' ',
                        COALESCE(`a`.`house_number_suffix`, ''),
                        ' ',
                        COALESCE(`a`.`room`, ''),
                        ' ',
                        COALESCE(`a`.`zipcode`, ''),
                        ' ',
                        COALESCE(`c`.`name`, '')),
                '  ',
                ' '),
            '  ',
            ' ') AS `full_address`,
        `s`.`status` AS `subscription_status`,
        `s`.`description` AS `subscription_description`,
        `si`.`date` AS `sales_invoice_date`,
        `si`.`price` AS `sales_invoice_excl_price`,
        `si`.`price_total` AS `sales_invoice_incl_price`,
        `si`.`invoice_no` AS `sales_invoice_invoice_number`
    FROM
        ((((((`relations` `r`
        LEFT JOIN `relations_persons` `rp` ON (((`r`.`id` = `rp`.`relation_id`)
            AND (`rp`.`primary` = 1))))
        LEFT JOIN `persons` `p` ON ((`p`.`id` = `rp`.`person_id`)))
        LEFT JOIN `addresses` `a` ON (((`a`.`relation_id` = `r`.`id`)
            AND (`a`.`primary` = 1)
            AND (`a`.`address_type_id` = 3))))
        LEFT JOIN `cities` `c` ON ((`c`.`id` = `a`.`city_id`)))
        LEFT JOIN `subscriptions` `s` ON ((`s`.`id` = (SELECT
                `s1`.`id`
            FROM
                `subscriptions` `s1`
            WHERE
                (`s1`.`relation_id` = `r`.`id`)
            ORDER BY `s1`.`created_at` DESC
            LIMIT 1))))
        LEFT JOIN `sales_invoices` `si` ON ((`si`.`id` = (SELECT
                `si1`.`id`
            FROM
                `sales_invoices` `si1`
            WHERE
                ((`si1`.`relation_id` = `r`.`id`)
                    AND (`si1`.`invoice_status` > 0))
            ORDER BY `si1`.`created_at` DESC
            LIMIT 1))));");

        DB::statement("create or replace VIEW `v_subscription_summaries` AS
    SELECT
`s`.`id` AS `id`,
`s`.`description` AS `description`,
`s`.`subscription_start` AS `subscription_start`,
`s`.`subscription_stop` AS `subscription_stop`,
`s`.`status` AS `status`,
`r`.`customer_number` AS `customer_number`,
`r`.`id` AS `relation_id`,
`r`.`tenant_id` AS `tenant_id`,
COALESCE(sum(slp.fixed_price),0) AS `price_excl_vat`,

COALESCE(sum(slp.fixed_price * (SELECT (1 + `vc`.`vat_percentage`) FROM `vat_codes` `vc` WHERE EXISTS( SELECT 1 FROM `tenant_products` `tp`
WHERE ((`vc`.`id` = `tp`.`vat_code_id`) AND (`tp`.`tenant_id` = `r`.`tenant_id`) AND (`tp`.`product_id` = (SELECT `sl`.`product_id`
FROM `subscription_lines` `sl`
WHERE ((`sl`.`id` = `slp`.`subscription_line_id`) AND (`sl`.`subscription_id` = `s`.`id`)))))))),0) AS `price_incl_vat`
FROM `subscriptions` `s`
LEFT JOIN `relations` `r` ON ((`r`.`id` = `s`.`relation_id`))
left outer join subscription_lines sl ON sl.subscription_id = s.id and sl.subscription_line_type in (3,4,5)
left outer join subscription_line_prices slp ON slp.id = (select id from subscription_line_prices slp2 where slp2.subscription_line_id = sl.id and `slp2`.`price_valid_from` <= NOW() order by `slp2`.`price_valid_from` limit 1)
group by s.id;");

        DB::statement("create or replace view v_sales_invoice_summaries as
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
        CONCAT(`p`.`first_name`,
                ' ',
                COALESCE(`p`.`middle_name`, ''),
                ' ',
                COALESCE(`p`.`last_name`, '')) AS `full_name`,
        `p`.`email` AS `email`,
        CONCAT(`a`.`street1`,
                ' ',
                COALESCE(`a`.`house_number`, ''),
                ' ',
                COALESCE(`a`.`house_number_suffix`, ''),
                ' ',
                COALESCE(`a`.`room`, ''),
                ' ',
                COALESCE(`a`.`zipcode`, ''),
                ' ',
                COALESCE(`c`.`name`, '')) AS `full_address`,
        `sim`.`value` AS `sales_invoice_reminder`
    FROM
        (((((((`sales_invoices` `s`
        LEFT JOIN `statuses` `ss` ON (((`ss`.`id` = `s`.`invoice_status`)
            AND (`ss`.`status_type_id` = 1))))
        LEFT JOIN `relations` `r` ON ((`r`.`id` = `s`.`relation_id`)))
        LEFT JOIN `relations_persons` `rp` ON (((`r`.`id` = `rp`.`relation_id`)
            AND (`rp`.`primary` = 1))))
        LEFT JOIN `persons` `p` ON ((`p`.`id` = `rp`.`person_id`)))
        LEFT JOIN `addresses` `a` ON (((`a`.`relation_id` = `r`.`id`)
            AND (`a`.`primary` = 1)
            AND (`a`.`address_type_id` = 3))))
        LEFT JOIN `cities` `c` ON ((`c`.`id` = `a`.`city_id`)))
        LEFT JOIN `sales_invoice_metas` `sim` ON (((`sim`.`sales_invoice_id` = `s`.`id`)
            AND (`sim`.`key` = 'reminder_status'))));");

        DB::statement("create index persons_full_name_index on persons (first_name, middle_name, last_name);");
        DB::statement("create index sales_invoice_metas_sales_invoice_id on sales_invoice_metas (sales_invoice_id);");
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('drop view v_sales_invoice_reminders');
        DB::statement('drop view v_billing_run_summaries');
        DB::statement('drop view v_relation_summaries');
        DB::statement('drop view v_subscription_summaries');
        DB::statement('drop view v_sales_invoice_summaries');
    }
}
