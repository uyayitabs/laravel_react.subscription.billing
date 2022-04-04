<?php

namespace App\Mail;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceLine;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CdrSummaryMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [
        "user_fullname" => null,
        "datePeriodDescription" => null,
    ];
    protected $attachedFile = null;
    protected $salesInvoiceId = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $attachedFile, $salesInvoiceId)
    {
        $this->data = $data;
        $this->attachedFile = $attachedFile;
        $this->salesInvoiceId = $salesInvoiceId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $salesInvoice = SalesInvoice::find($this->salesInvoiceId);
        $tenant = $salesInvoice->tenant()->first();
        $emailTemplate = $tenant->emailTemplatesByType('cdr_summary')->first();
        $subject = getStringBladeView(
            $emailTemplate->subject,
            [
                'tenantName' => $tenant->name,
                'datePeriodDescription' => generateInvoiceDate($salesInvoice->id)
            ]
        );

        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->attach($this->attachedFile)
            ->subject($subject);
    }
}
