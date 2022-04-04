<?php

namespace App\Mail;

use App\Models\SalesInvoice;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PainDirectDebitMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [
        "company_name" => null,
        "user_fullname" => null,
        "bill_run_date" => null,
        "billingRunId" => null,
        "dd_file_url" => null,
    ];
    protected $tenantId = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $tenantId)
    {
        $this->data = $data;
        $this->tenantId = $tenantId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tenant = Tenant::find($this->tenantId);
        $emailTemplate = $subject = $templateHTML = null;

        $emailTemplate = $tenant->emailTemplatesByType('pain_dd_file')->first();
        $subject = getStringBladeView(
            $emailTemplate->subject,
            [
                'tenantName' => $this->data['company_name'],
                'date' => $this->data['bill_run_date'],
                'billingRunId' => $this->data['billingRunId'],
                'env' => config('app.env')
            ]
        );
        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->subject($subject);
    }
}
