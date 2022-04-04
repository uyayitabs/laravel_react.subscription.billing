<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [];
    protected $type;
    protected $tenantId;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $type, $tenantId)
    {
        $this->data = $data;
        $this->type = $type;
        $this->tenantId = $tenantId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = EmailTemplate::where([['tenant_id', $this->tenantId],['type', $this->type]])->first();
        if (!$emailTemplate) {
            $emailTemplate = EmailTemplate::where('type', $this->type)->first();
        }
        $subject = $emailTemplate->subject;

        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->subject($subject);
    }
}
