<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateM7SubscriptionLinesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE OR REPLACE VIEW `v_m7_subscription_lines` AS
                SELECT
                    `sl`.`subscription_id`,
                    `sl`.`id` AS `subscription_line_id`,
                    `sl`.`subscription_start` AS `subscription_line_start`,
                    `sl`.`subscription_stop` AS `subscription_line_end`,
                    `sl`.`description` AS `descr`,
                    `s`.`relation_id`,
                    `t`.`name` AS `tenant`,
                    `r`.`customer_number`
                FROM `subscription_lines` AS `sl`
                LEFT JOIN `products` AS `p` ON `sl`.`product_id` = `p`.`id`
                LEFT JOIN `subscriptions` AS `s` ON `sl`.`subscription_id` = `s`.`id`
                LEFT JOIN `relations` AS `r` ON `s`.`relation_id` = `r`.`id`
                LEFT JOIN `tenants` AS `t` ON `r`.`tenant_id` = `t`.`id`
                WHERE
                    `sl`.`subscription_stop` < CURDATE() AND
                    `p`.`backend_api` = "m7"
                ORDER BY `sl`.`subscription_id` DESC;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('drop view v_m7_subscription_lines');
    }
}
