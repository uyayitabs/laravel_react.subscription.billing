<?php

namespace App\Mail;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Logging;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SalesInvoiceReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $emailTemplate;
    protected $attachedFile;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $emailTemplate, $attachedFile)
    {
        $this->data = $data;
        $this->emailTemplate = $emailTemplate;
        $this->attachedFile = $attachedFile;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = $this->emailTemplate;
        $data = $this->data;

        $templateHTML = getStringBladeView($emailTemplate->body_html, $data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        if (!\File::exists($this->attachedFile)) {
            Logging::error('Attached file not found. Sending mail will fail.', [], 0);
            throw new FileNotFoundException($this->attachedFile . " was not found on system.");
        }

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->attach($this->attachedFile)
            ->subject($data['subject']);
    }
}
