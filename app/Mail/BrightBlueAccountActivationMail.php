<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;

class BrightBlueAccountActivationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $method;
    protected $data = [];
    //
    protected $attachedFile = null;
    protected $service;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tenant, $method, $data, $attachedFile = null, $emailTemplateId = null)
    {
        $data['asset_url'] = config('app.asset_url');
        $this->method = $method;
        $this->data = $data;
        $this->attachedFile = $attachedFile;
        $this->service = new EmailTemplateService($tenant, $data, $emailTemplateId);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $this->service->setType('brightblue.create_account');
        if ($this->attachedFile) {
            return $this->from($this->service->fromEmail(), $this->service->fromName())
                ->bcc($this->service->bcc())
                ->html($this->service->content())
                ->attach($this->attachedFile)
                ->subject($this->service->subject());
        } else {
            return $this->from($this->service->fromEmail(), $this->service->fromName())
                ->bcc($this->service->bcc())
                ->html($this->service->content())
                ->subject($this->service->subject());
        }
    }
}
