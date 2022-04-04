<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\SalesInvoice;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderSuccessMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $tenantId;
    protected $data = [
        "order" => null,
        "extras" => null,
    ];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $tenantId)
    {
        $this->data = $data;
        $this->tenantId = $tenantId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = $subject = $templateHTML = null;
        $whereParam = [
            ['type', '=', 'order.success'],
            ['tenant_id', '=', $this->tenantId]
        ];
        $emailTemplate = EmailTemplate::where($whereParam)->first();
        $subject = getStringBladeView($emailTemplate->subject, [
            'order' => $this->data['order'],
            'extras' => $this->data['extras']
        ]);
        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->subject($subject);
    }
}
