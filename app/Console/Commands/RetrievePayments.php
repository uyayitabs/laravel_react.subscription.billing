<?php

namespace App\Console\Commands;

use Logging;
use App\Models\TenantBankAccount;
use Illuminate\Console\Command;
use Exception;

class RetrievePayments extends Command
{
    protected $signature = 'payments:retrieveAll';
    protected $description = 'For all tentants, retrieve payments from their bank.';

    public function handle(): void
    {
        $bankAccounts = TenantBankAccount::all();
        foreach ($bankAccounts as $bankAccount) {
            switch (strtolower($bankAccount->bank_api)) {
                case 'rabobank':
                    $this->call(
                        'rabobank:proccess',
                        ['tenantBankAccount' => $bankAccount->id]
                    );
                    break;
                default:
                    Logging::warning(
                        'Retrieve Payments: bank_api field is empty',
                        [
                            'data' => $bankAccount->id,
                            'error' => 'bank_api is empty for ' . $bankAccount->id
                        ],
                        9,
                        0
                    );
                    break;
            }
        }
    }
}
