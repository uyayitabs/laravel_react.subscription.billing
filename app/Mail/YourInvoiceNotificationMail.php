<?php

namespace App\Mail;

use App\Models\SalesInvoice;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class YourInvoiceNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [
        "tenant_id" => null,
        "company_name" => null,
        "user_fullname" => null,
        "datePeriodDescription" => null,
    ];
    //
    protected $invoicePdf = null;
    protected $cdrUsagePdf = null;
    protected $tenantId = null;
    protected $salesInvoiceId = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $invoicePdf, $cdrUsagePdf, $tenantId, $salesInvoiceId)
    {
        $this->data = $data;
        $this->invoicePdf = $invoicePdf;
        $this->cdrUsagePdf = $cdrUsagePdf;
        $this->tenantId = $tenantId;
        $this->salesInvoiceId = $salesInvoiceId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $tenant = Tenant::find($this->tenantId);
        $salesInvoice = SalesInvoice::find($this->salesInvoiceId);
        $emailTemplate = $subject = $templateHTML = null;

        if ($salesInvoice->getAttribute('is_deposit_invoice')) {
            $emailTemplate = $tenant->emailTemplatesByType('deposit_invoice')->first();
            $subject = getStringBladeView($emailTemplate->subject, [
                'totalWithVAT' => $this->data['total_with_vat'],
                'tenantName' => $this->data['company_name'],
                'datePeriodDescription' => $this->data['datePeriodDescription']
            ]);
        } elseif ($salesInvoice->relation->isBusiness()) {
            $emailTemplate = $tenant->emailTemplatesByType('invoice.bus')->first();
            if (!$emailTemplate) {
                $emailTemplate = $tenant->emailTemplatesByType('invoice')->first();
            }
            $subject = getStringBladeView($emailTemplate->subject, [
                'totalWithVAT' => $this->data['total_with_vat'],
                'tenantName' => $this->data['company_name'],
                'datePeriodDescription' => $this->data['datePeriodDescription']
            ]);
        } else {
            $emailTemplate = $tenant->emailTemplatesByType('invoice')->first();
            $subject = getStringBladeView($emailTemplate->subject, [
                'totalWithVAT' => $this->data['total_with_vat'],
                'tenantName' => $this->data['company_name'],
                'datePeriodDescription' => $this->data['datePeriodDescription']
            ]);
        }

        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        $mail =  $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->attach($this->invoicePdf)
            ->subject($subject);
        if ($this->cdrUsagePdf) {
            $mail->attach($this->cdrUsagePdf);
        }
        return $mail;
    }
}
