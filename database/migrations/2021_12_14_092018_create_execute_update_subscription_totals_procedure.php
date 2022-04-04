<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExecuteUpdateSubscriptionTotalsProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP PROCEDURE IF EXISTS `ExecuteUpdateSubscriptionTotals`;
            CREATE PROCEDURE `ExecuteUpdateSubscriptionTotals`()
            BEGIN
                DECLARE done INT DEFAULT 0;
                DECLARE vSubscriptionId INT DEFAULT 0;

                DECLARE cur_subscriptions CURSOR FOR SELECT id FROM `subscriptions`;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                OPEN cur_subscriptions;
                    cur_subscriptions_loop : LOOP
                        FETCH cur_subscriptions INTO vSubscriptionId;

                        IF done = 1 THEN
                            LEAVE cur_subscriptions_loop;
                        END IF;

                        CALL UpdateSubscriptionTotals(vSubscriptionId);
                    END LOOP cur_subscriptions_loop;
                -- Add log_activity entry
                INSERT INTO `log_activities` (`tenant_id`, `related_entity_type`, `related_entity_id`, `message`, `json_data`, `facility_id`, `facility`, `severity`, `status`, `user_id`, `username`, `url`, `method`, `ip`, `agent`, `hp_timestamp`, `created_at`, `updated_at`) VALUES (null, "subscription", null, "Update subscription totals", null,  1, "MYSQL Event", "info", null, null, null, "", "", "", null, CAST(1000 * UNIX_TIMESTAMP(current_timestamp(3)) AS UNSIGNED INTEGER), NOW(), NOW());
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `ExecuteUpdateSubscriptionTotals`;');
    }
}
