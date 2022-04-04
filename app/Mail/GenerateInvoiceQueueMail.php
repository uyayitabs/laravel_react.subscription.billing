<?php

namespace App\Mail;

use App\Models\BillingRun;
use App\Models\SalesInvoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateInvoiceQueueMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [
        'adminToolsUrl' => null,
        'errorData' => null
    ];
    protected $billingRunId = null;
    protected $isSuccess = false;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $billingRunId, $isSuccess = false)
    {
        $this->data = $data;
        $this->billingRunId = $billingRunId;
        $this->isSuccess = $isSuccess;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $billingRun = BillingRun::find($this->billingRunId);
        $templateType = $this->isSuccess ? 'invoice_queue.success' : 'invoice_queue.error';
        $tenant = $billingRun->tenant;

        $emailTemplate = $tenant->emailTemplatesByType($templateType)->first();
        $subject = getStringBladeView($emailTemplate->subject, [
            'billingRunId' => $billingRun->id,
            'tenantName' => $tenant->name,
        ]);

        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->subject("[" . config('app.env') . "]{$subject}");
    }
}
