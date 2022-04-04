<?php

namespace App\Console\Commands;

use App\Mail\DeprovisioningReport;
use App\Mail\DirectDebitReversalReport;
use App\Services\DeprovisioningService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class ReportDeprovisioning extends Command
{
    protected $signature = 'grid:report-deprovisioning';

    protected $description = 'Reports subscriptions that ended but have not been deprovisioned';

    public function handle(): void
    {
        $now = Carbon::parse(now()->format('Y-m-d H:i:s'), 'Europe/Amsterdam');
        $deprovService = new DeprovisioningService();
        $result = $deprovService->checkDeprovisioning();
        $subLines = $result->main;
        $files = [];
        if (!empty($subLines)) {
            $filename = 'Deprovision_report_' . $now->format('d-m-Y_H:i') . '.csv';
            $filename = $deprovService->generateCsvFile($filename, $subLines);
            $files[] = $filename;
        }
        // Send mail
        Mail::send(new DeprovisioningReport($subLines, $result->notOk, $now, $files));
        // After sending mail, delete the generated CSV file (NOTE: From original code)
        if (isset($filename)) {
            unlink($filename);
        }
    }
}
