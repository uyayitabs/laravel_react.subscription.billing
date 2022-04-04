<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class QueueJobMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [];
    protected $type;
    protected $fromMail;
    protected $bccMails = [];
    protected $attachedFile;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $subject, $attachedFile = null, $from = 'admin@f2x.nl')
    {
        $this->data = $data;
        $this->fromMail = $from;
        $this->subject = $subject;
        $this->attachedFile = $attachedFile;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->markdown('emails.queue_job.' . $this->data['type'] . '.' . $this->data['template'])
            ->from($this->fromMail)
            ->with($this->data)
            ->bcc($this->bccMails);

        if ($this->attachedFile) {
            $mail->attach($this->attachedFile);
        }

        return $mail->subject($this->subject);
    }
}
