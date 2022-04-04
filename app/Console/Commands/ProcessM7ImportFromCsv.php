<?php

namespace App\Console\Commands;

use App\Services\M7ImportService;
use Illuminate\Console\Command;

class ProcessM7ImportFromCsv extends Command
{
    protected $signature = 'm7:import_csv {--file=}';

    protected $description = 'Import m7 data from CSV file';

    public function handle(): void
    {
        $csvFile = $this->option('file');
        $cdrUsageCostService = new M7ImportService();
        $cdrUsageCostService->processCSVImport($csvFile);
    }
}
