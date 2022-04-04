<?php

namespace App\Jobs;

use App\Services\RabobankPaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessRabobankPayment implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $xmlFile;
    protected $service;
    protected $tenantBankAccount;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($xmlFile, $tenantBankAccount)
    {
        $this->xmlFile = $xmlFile;
        $this->service = new RabobankPaymentService();
        $this->tenantBankAccount = $tenantBankAccount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->service->processXmlFile($this->xmlFile, $this->tenantBankAccount);
    }
}
