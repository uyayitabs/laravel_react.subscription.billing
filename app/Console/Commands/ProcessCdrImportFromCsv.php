<?php

namespace App\Console\Commands;

use App\Helpers\Facades\Logging;
use App\Jobs\SendQueueJobMail;
use App\Mail\CdrImportMail;
use App\Services\CdrUsageCostService;
use App\Models\Tenant;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProcessCdrImportFromCsv extends Command
{
    protected $signature = 'cdr:import_csv {--file=} {--tenant_id=} {--user_id=}';

    protected $description = 'Import cdr_usage_costs data from CSV file';

    public function handle(): void
    {
        $csvFile = $this->option('file');
        $tenantId = $this->option('tenant_id');
        $userId = $this->option('user_id');
        $cdrUsageCostService = new CdrUsageCostService();
        $result = $cdrUsageCostService->processCSVImport($csvFile, $tenantId);

        $data = [];

        // number of successfully saved records
        $data['processed_records'] = (array_key_exists('processed', $result) &&
            $result['processed']) ? $result['processed'] : 0;

        // number of records not saved (with issue(s))
        $data['failed'] = (array_key_exists('failed', $result) && $result['failed']) ? $result['failed'] : 0;
        $data['failed_csv'] = (array_key_exists('failed_filepath', $result) &&
            $result['failed_filepath']) ?
            $result['failed_filepath'] : null;

        $data['filename'] = basename($csvFile);
        $data['errors'] = null;

        if (isset($data['failed']) && $data['failed'] > 0) {
            $data['type'] = 'error';
            $data['errors'] = $data['failed'];
        } else {
            $data['type'] = 'success';
        }

        $person = User::find($userId)->person;
        $email = $person->customer_email;

        $tenant = Tenant::find($tenantId);
        $tenantName = $tenant->name;
        $data['tenant'] = $tenantName;

        try {
            $cdrImportMailable = new CdrImportMail($data, $tenantId);
            Logging::information(
                'Sending mail - CDR Import',
                $email,
                17,
                1,
                $tenantId
            );
            Mail::to($email)->queue($cdrImportMailable);
        } catch (Exception $e) {
            Logging::error(
                'Sending mail - CDR Import',
                [
                    'success' => false,
                    'message' => "Encountered an error while generating the email."
                ],
                17,
                1,
                $tenantId
            );
        }
    }
}
