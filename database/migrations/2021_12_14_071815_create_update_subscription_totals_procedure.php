<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateSubscriptionTotalsProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP PROCEDURE IF EXISTS `UpdateSubscriptionTotals`;
            CREATE PROCEDURE `UpdateSubscriptionTotals`(subscriptionId INT)
            BEGIN
                DECLARE done INT DEFAULT 0;
                DECLARE vSubscriptionLineType INT DEFAULT 0;
                DECLARE vPriceIncVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vPriceExcVat DECIMAL(12, 5) DEFAULT 0.00;
                -- Variables for updating subscriptions.price_incl_vat & subscriptions.price_excl_vat
                DECLARE vSubscriptionType INT DEFAULT 0;
                DECLARE vSumNRCPriceIncVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSumNRCPriceExcVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSumMRCPriceIncVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSumMRCPriceExcVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSumQRCPriceIncVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSumQRCPriceExcVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSumYRCPriceIncVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSumYRCPriceExcVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSumDepositPriceIncVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSumDepositPriceExcVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSubscriptionPriceIncVat DECIMAL(12, 5) DEFAULT 0.00;
                DECLARE vSubscriptionPriceExcVat DECIMAL(12, 5) DEFAULT 0.00;

                -- Subscription cursor for updating subscriptions.price_incl_vat & subscriptions.price_excl_vat
                DECLARE cur_subscription CURSOR FOR
                    SELECT
                        `type`, `sum_nrc_incl_vat`, `sum_nrc_excl_vat`, `sum_mrc_incl_vat`, `sum_mrc_excl_vat`,
                        `sum_qrc_incl_vat`, `sum_qrc_excl_vat`, `sum_yrc_incl_vat`, `sum_yrc_excl_vat`, `sum_deposit_incl_vat`, `sum_deposit_excl_vat`
                    FROM `subscriptions` `s` WHERE `s`.`id` = subscriptionId;

                DECLARE cur_subscription_lines CURSOR FOR
                    SELECT
                        `sl`.`subscription_line_type`,
                        COALESCE(SUM(`slp`.`fixed_price`), 0) AS `price_excl_vat`,
                        COALESCE(SUM(
                            (`slp`.`fixed_price` * (
                                SELECT (1 + `vc`.`vat_percentage`)
                                FROM `vat_codes` `vc`
                                WHERE EXISTS(
                                    SELECT 1
                                    FROM `tenant_products` `tp`
                                    WHERE (
                                        `vc`.`id` = `tp`.`vat_code_id` AND
                                        `tp`.`tenant_id` = `r`.`tenant_id` AND
                                        (`tp`.`product_id` = (SELECT  `sl`.`product_id` FROM `subscription_lines` `sl` WHERE `sl`.`id` = `slp`.`subscription_line_id` AND `sl`.`subscription_id` = `s`.`id`))
                                    )
                                )
                            ))), 0) AS `price_incl_vat`
                    FROM `subscriptions` `s`
                    LEFT JOIN `relations` `r` ON `r`.`id` = `s`.`relation_id`
                    LEFT JOIN `subscription_lines` `sl` ON `sl`.`subscription_id` = `s`.`id`
                    LEFT JOIN `subscription_line_prices` `slp` ON (`slp`.`id` = (SELECT `slp2`.`id` FROM `subscription_line_prices` `slp2` WHERE ((`slp2`.`subscription_line_id` = `sl`.`id`) AND (`slp2`.`price_valid_from` <= NOW())) ORDER BY `slp2`.`price_valid_from` DESC LIMIT 1))
                    WHERE `s`.`id` = subscriptionId
                    GROUP BY `sl`.`subscription_line_type`;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                -- Compute and update subscriptions.[nrc,mrc,qrc,yrc & deposit incl/excl] values
                OPEN cur_subscription_lines;
                    subscription_lines_loop : LOOP
                        FETCH cur_subscription_lines INTO vSubscriptionLineType, vPriceExcVat, vPriceIncVat;
                        IF done = 1 THEN
                            LEAVE subscription_lines_loop;
                        END IF;

                        IF vSubscriptionLineType = 2 THEN
                            UPDATE subscriptions SET `sum_nrc_incl_vat` = vPriceIncVat, `sum_nrc_excl_vat` = vPriceExcVat WHERE `id` = subscriptionId;
                        ELSEIF vSubscriptionLineType = 3 THEN
                            UPDATE subscriptions SET `sum_mrc_incl_vat` = vPriceIncVat, `sum_mrc_excl_vat` = vPriceExcVat WHERE `id` = subscriptionId;
                        ELSEIF vSubscriptionLineType = 4 THEN
                            UPDATE subscriptions SET `sum_qrc_incl_vat` = vPriceIncVat, `sum_qrc_excl_vat` = vPriceExcVat WHERE `id` = subscriptionId;
                        ELSEIF vSubscriptionLineType = 5 THEN
                            UPDATE subscriptions SET `sum_yrc_incl_vat` = vPriceIncVat, `sum_yrc_excl_vat` = vPriceExcVat WHERE `id` = subscriptionId;
                        ELSEIF vSubscriptionLineType = 6 THEN
                            UPDATE subscriptions SET `sum_deposit_incl_vat` = vPriceIncVat, `sum_deposit_excl_vat` = vPriceExcVat WHERE `id` = subscriptionId;
                        END IF;
                    END LOOP subscription_lines_loop;

                -- Compute for the price_excl_vat & price_incl_vat then set subscriptions.price_excl_vat & subscriptions.price_incl_vat values
                OPEN cur_subscription;
                FETCH cur_subscription INTO vSubscriptionType, vSumNRCPriceIncVat, vSumNRCPriceExcVat, vSumMRCPriceIncVat, vSumMRCPriceExcVat, vSumQRCPriceIncVat, vSumQRCPriceExcVat, vSumYRCPriceIncVat, vSumYRCPriceExcVat, vSumDepositPriceIncVat, vSumDepositPriceExcVat;

                IF vSubscriptionType = 2 THEN  -- NRC
                    SET vSubscriptionPriceIncVat := vSumNRCPriceIncVat;
                    SET vSubscriptionPriceExcVat := vSumNRCPriceExcVat;
                ELSEIF vSubscriptionType = 3 THEN -- MRC
                    SET vSubscriptionPriceIncVat := vSumMRCPriceIncVat + (vSumQRCPriceIncVat / 3) + (vSumYRCPriceIncVat / 12);
                    SET vSubscriptionPriceExcVat := vSumMRCPriceExcVat + (vSumQRCPriceExcVat / 3) + (vSumYRCPriceExcVat / 12);
                ELSEIF vSubscriptionType = 4 THEN -- QRC
                    SET vSubscriptionPriceIncVat := (vSumMRCPriceIncVat * 3) + (vSumQRCPriceIncVat) + (vSumYRCPriceIncVat / 4);
                    SET vSubscriptionPriceExcVat := (vSumMRCPriceExcVat * 3) + (vSumQRCPriceExcVat) + (vSumYRCPriceExcVat / 4);
                ELSEIF vSubscriptionType = 5 THEN -- YRC
                    SET vSubscriptionPriceIncVat := (vSumMRCPriceIncVat * 12) + (vSumQRCPriceIncVat * 4) + (vSumYRCPriceIncVat);
                    SET vSubscriptionPriceExcVat := (vSumMRCPriceExcVat * 12) + (vSumQRCPriceExcVat * 4) + (vSumYRCPriceExcVat);
                ELSEIF vSubscriptionType = 6 THEN -- Deposit
                    SET vSubscriptionPriceIncVat := vSumDepositPriceIncVat;
                    SET vSubscriptionPriceExcVat := vSumDepositPriceExcVat;
                END IF;
                UPDATE subscriptions SET `price_incl_vat` = vSubscriptionPriceIncVat, `price_excl_vat` = vSubscriptionPriceExcVat WHERE `id` = subscriptionId;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS `UpdateSubscriptionTotals`');
    }
}
