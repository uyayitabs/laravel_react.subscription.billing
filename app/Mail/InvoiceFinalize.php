<?php

namespace App\Mail;

use App\Models\BillingRun;
use App\Models\EmailTemplate;
use App\Models\SalesInvoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceFinalize extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $billingRunId;
    public $data;

    /**
     * Create a new message instance.
     *
     * @param $pdfFileCount
     * @param $invoiceCount
     * @param $started_at
     * @param $ended_at
     */
    public function __construct($billingRunId, $pdfFileCount, $invoiceCount, $started_at, $time_taken)
    {
        $this->billingRunId = $billingRunId;
        $this->data = [
            'pdfFileCount' => $pdfFileCount,
            'invoiceCount' => $invoiceCount,
            'started_at' => $started_at,
            'time_taken' => $time_taken
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = EmailTemplate::where('type', 'billing_run_finalize.success')->first();
        $subject = getStringBladeView($emailTemplate->subject, [
            'billingRunId' => $this->billingRunId,
            'env' => config('app.env')
        ]);

        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->subject($subject);
    }
}
