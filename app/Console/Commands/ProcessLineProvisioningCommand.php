<?php

namespace App\Console\Commands;

use App\Jobs\ProcessLineProvisioning;
use Illuminate\Console\Command;

class ProcessLineProvisioningCommand extends Command
{
    protected $signature = 'process:line_provisioning';

    protected $description = 'Process provisioning of subscription lines ' .
      'with products.backend_api=lineProvisioning';

    public function handle(): void
    {
        ProcessLineProvisioning::dispatchNow();
    }
}
