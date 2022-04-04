<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\SalesInvoice;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderErrorMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [
        "error_subject" => null,
        "error_details" => null,
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

        $emailTemplate = EmailTemplate::where('type', 'order.error')->first();
        $subject = getStringBladeView($emailTemplate->subject, ['error_subject' => $this->data['error_subject']]);
        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->subject($subject);
    }
}
