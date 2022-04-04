<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        'App\Console\Commands\ProcessInvoiceBilling',
    ];
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command("invoice:process_billing {$dateNow}");
        if (config('app.enable_cron')) {
            $schedule->command("provider:process_subscription m7 new null all")->hourlyAt(45)->withoutOverlapping();
        }
        // products.backend_api=lineProvisioning CRON handling
        if (config('app.enable_line_provisioning_cron')) {
            $schedule->command("process:line_provisioning")->everyFifteenMinutes()->withoutOverlapping();
        }
        if (config('app.enable_queue_cron')) {
            $schedule->command("queuejob:run")->everyMinute()->withoutOverlapping();
        }
        if (config('rabobank.enable_rabobank_cron')) {
            // NOTE: production laravel app is in UTC timezone
            // so get NL time, then convert it to UTC time
            $twoAm = Carbon::parse(now()->format('Y-m-d 02:00:00'), 'Europe/Amsterdam')->setTimezone('UTC')->format('H:i:s');
            $schedule->command("payments:retrieveAll")->at($twoAm);
        }
        $threeAm = Carbon::parse(now()->format('Y-m-d 03:00:00'), 'Europe/Amsterdam')->setTimezone('UTC')->format('H:i:s');
        if (config('m7.enable_deprovisioning_report_email_cron')) {
            $schedule->command("grid:report-deprovisioning")->at($threeAm);
        }
        if (config('rabobank.enable_payments_report_email_cron')) {
            $schedule->command("grid:report-payments")->at($threeAm);
        }
        if (config('rabobank.enable_no_bank_account_dd_report_email_cron')) {
            $schedule->command("grid:report-missing-dd")->at($threeAm);
        }
    }
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
