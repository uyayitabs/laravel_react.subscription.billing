<?php

namespace App\Console\Commands;

use App\Models\TenantBankAccount;
use Illuminate\Console\Command;
use App\Jobs\RabobankSftpJob;

class RabobankSftp extends Command
{
    protected $signature = 'rabobank:proccess {tenantBankAccount : id of tenant_bank_account}';

    protected $description = 'Rabobank get file from sftp';

    public function handle(): void
    {
        $tenantBankAccount = TenantBankAccount::find($this->argument('tenantBankAccount'));
        RabobankSftpJob::dispatchNow($tenantBankAccount);
    }
}
