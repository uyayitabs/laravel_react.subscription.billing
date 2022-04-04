<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSubscriptionSummariesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE OR REPLACE VIEW `v_subscription_summaries` AS
                SELECT
                    `s`.`id` AS `id`,
                    `s`.`description` AS `description`,
                    `s`.`subscription_start` AS `subscription_start`,
                    `s`.`subscription_stop` AS `subscription_stop`,
                    `s`.`status` AS `status`,
                    `r`.`customer_number` AS `customer_number`,
                    `r`.`id` AS `relation_id`,
                    `r`.`tenant_id` AS `tenant_id`,
                    `s`.`price_excl_vat` AS `price_excl_vat`,
                    `s`.`price_incl_vat` AS `price_incl_vat`
                FROM `subscriptions` `s`
                LEFT JOIN `relations` `r` ON `r`.`id` = `s`.`relation_id`;
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
            CREATE OR REPLACE VIEW `v_subscription_summaries` AS
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
                LEFT OUTER JOIN subscription_lines sl ON sl.subscription_id = s.id and sl.subscription_line_type in (3,4,5)
                LEFT OUTER JOIN subscription_line_prices slp ON slp.id = (select id from subscription_line_prices slp2 where slp2.subscription_line_id = sl.id and `slp2`.`price_valid_from` <= NOW() order by `slp2`.`price_valid_from` limit 1)
                GROUP BY s.id;
        ');
    }
}
