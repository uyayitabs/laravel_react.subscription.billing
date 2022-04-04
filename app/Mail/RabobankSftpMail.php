<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RabobankSftpMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data, $fromMail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $subject, $fromMail = 'admin@f2x.nl')
    {
        $this->data = $data;
        $this->subject = $subject;
        $this->fromMail = $fromMail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.rabobank.index')
            ->from($this->fromMail)
            ->with($this->data)
            ->subject($this->subject);
    }
}
