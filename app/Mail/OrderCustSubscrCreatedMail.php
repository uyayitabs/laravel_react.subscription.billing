<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\SalesInvoice;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderCustSubscrCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [
        "customer_name" => null,
        "customer_email" => null,
        "customer_number" => null,
        "customer_url" => null,
        "subscription_url" => null,
        "order_details" => null,
        "order_id" => null,
        "order_address_city" => null,
        "order_address_status" => null,
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

        $emailTemplate = EmailTemplate::where('type', 'order.cust_subsc.success')->first();
        $subject = getStringBladeView(
            $emailTemplate->subject,
            [
                'order_address_city' => $this->data['order_address_city'],
                'order_address_status' => $this->data['order_address_status']
            ]
        );
        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->subject($subject);
    }
}
