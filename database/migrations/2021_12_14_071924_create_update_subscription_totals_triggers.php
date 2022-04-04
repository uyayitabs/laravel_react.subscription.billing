<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateSubscriptionTotalsTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            DROP TRIGGER IF EXISTS `subscription_line_prices_AFTER_INSERT`;
            CREATE TRIGGER `subscription_line_prices_AFTER_INSERT` AFTER INSERT ON `subscription_line_prices` FOR EACH ROW
            BEGIN
                SET @subscriptionId := (SELECT subscription_id FROM subscription_lines WHERE id = NEW.subscription_line_id);
                CALL `UpdateSubscriptionTotals`(@subscriptionId);
            END;
        ');
        DB::unprepared('
            DROP TRIGGER IF EXISTS `subscription_line_prices_AFTER_UPDATE`;
            CREATE TRIGGER `subscription_line_prices_AFTER_UPDATE` AFTER UPDATE ON `subscription_line_prices` FOR EACH ROW
            BEGIN
                SET @subscriptionId := (SELECT subscription_id FROM subscription_lines WHERE id = NEW.subscription_line_id);
                CALL `UpdateSubscriptionTotals`(@subscriptionId);
            END;
        ');
        DB::unprepared('
            DROP TRIGGER IF EXISTS `subscription_line_prices_AFTER_DELETE`;
            CREATE TRIGGER `subscription_line_prices_AFTER_DELETE` AFTER DELETE ON `subscription_line_prices` FOR EACH ROW
            BEGIN
                SET @subscriptionId := (SELECT subscription_id FROM subscription_lines WHERE id = OLD.subscription_line_id);
                CALL `UpdateSubscriptionTotals`(@subscriptionId);
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
        DB::unprepared('DROP TRIGGER IF EXISTS `subscription_line_prices_AFTER_INSERT`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `subscription_line_prices_AFTER_UPDATE`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `subscription_line_prices_AFTER_DELETE`;');
    }
}
