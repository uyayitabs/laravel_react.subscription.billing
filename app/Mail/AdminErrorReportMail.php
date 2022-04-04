<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminErrorReportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [];
    protected $type;
    protected $fromMail;
    protected $bccMails = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $subject, $from = 'admin@f2x.nl')
    {
        $this->data = $data;
        $this->fromMail = $from;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.m7.admin_report')
            ->from($this->fromMail)
            ->with($this->data)
            ->bcc($this->bccMails)
            ->subject($this->subject);
    }
}
