<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\SalesInvoice;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RabobankPaymentProcessingErrorMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [
        "error_subject" => null,
        "error_details" => null,
        "code" => null,
        "xml_file" => null,
    ];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = $subject = $templateHTML = null;
        $emailTemplate = EmailTemplate::where('type', 'rabobank_payment_processing.error')->first();
        $subject = getStringBladeView($emailTemplate->subject, [
            'error_subject' => $this->data['error_subject']
        ]);
        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = [];
        if (!blank($emailTemplate->bcc_email)) {
            $bccMails = explode(",", $emailTemplate->bcc_email);
        }

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->subject($subject);
    }
}
