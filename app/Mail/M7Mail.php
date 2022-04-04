<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;

class M7Mail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $bccMails = [
        "chej@f2x.nl",
        "n.wegen@f2x.nl"
    ];
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
    public function __construct($tenant, $method, $data, $attachedFile = null)
    {
        $data['asset_url'] = config('app.asset_url');
        $this->method = $method;
        $this->data = $data;
        $this->attachedFile = $attachedFile;
        $this->service = new EmailTemplateService($tenant, $data);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        switch ($this->method) {
            case 'CreateMyAccount':
            case 'ChangeMyAccount':
                $this->service->setType('m7.create_my_account');
                break;

            default:
                # code...
                break;
        }

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
