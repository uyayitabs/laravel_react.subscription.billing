<?php

namespace App\Jobs;

use App\Services\SalesInvoiceService;
use Logging;
use App\Models\SalesInvoice;
use App\Mail\YourInvoiceNotificationMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $customerId;
    protected $salesInvoiceId;
    protected $customerEmail;
    protected $invoiceNo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $salesInvoiceId)
    {
        $this->salesInvoiceId = $salesInvoiceId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invoiceId = $this->salesInvoiceId;

        Redis::throttle('default')
            ->allow(240)
            ->every(60)
            ->block(240)
            ->then(
                function () use ($invoiceId) {
                    $this->$invoiceId = $invoiceId;

                    $service = new SalesInvoiceService();
                    $service->sendEmail($invoiceId);
                },
                // Limit reached
                function () {
                }
            );
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        $logData = [];
        if (!empty($this->invoiceId)) {
            $logData['invoice_number'] = $this->invoiceNo;
        }
        Logging::exceptionWithData(
            $exception,
            'INVOICING - SENDING MAIL EXCEPTION',
            $logData,
            17,
            0,
            null
        );
        $this->delete();
    }
}
