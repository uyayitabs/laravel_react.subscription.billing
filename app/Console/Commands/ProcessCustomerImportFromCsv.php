<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CustomerImportService;

class ProcessCustomerImportFromCsv extends Command
{
    protected $signature = 'customer:import_csv {--file=} {--tenant=} {--country=} {--action=} {--from=} {--to=}';

    protected $description = 'Import customer data from CSV file {--file=} {--tenant=} {--country=} {--action=}';

    public function handle(): void
    {
        $csvFile = $this->option('file');
        $tenant = $this->option('tenant');
        $country = $this->option('country');
        $action = $this->option('action');
        $from = $this->option('from');
        $to = $this->option('to');
        $customerImportService = new CustomerImportService($tenant, $country, $action, $from, $to);
        $customerImportService->processCSVImport($csvFile);
    }
}
