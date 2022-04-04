<?php

namespace App\Mail;

use App\Models\BillingRun;
use App\Models\EmailTemplate;
use App\Models\SalesInvoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\View\Compilers\BladeCompiler;

class InvoiceCheck extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $billingRun;
    public $data;

    /**
     * Create a new message instance.
     *
     * @param $invoiceLists
     * @param $invoiceCount
     */
    public function __construct(
        $billingRun,
        $invoiceLists,
        $invoiceCount,
        $subscriptionCount,
        $sumExcludingVat,
        $sumIncludingVat,
        $noPriceFoundMessages
    ) {
        $this->billingRun = $billingRun;
        $this->data = [
            'billingRunId' => $billingRun->id,
            'tenantName' => $billingRun->tenant->name,
            'date' => dateFormat($billingRun->date),
            'invoiceLists' => $invoiceLists,
            'countInvoices' => $invoiceCount,
            'countSubscriptions' => $subscriptionCount,
            'sumExcludingVat' => $sumExcludingVat,
            'sumIncludingVat' => $sumIncludingVat,
            'noPriceFoundMessages' => $noPriceFoundMessages
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $emailTemplate = EmailTemplate::where('type', 'billing_run_concept.success')->first();
        $subject = getStringBladeView($emailTemplate->subject, [
            'billingRunId' => $this->billingRun->id,
            'env' => config('app.env')
        ]);

        $templateHTML = getStringBladeView($emailTemplate->body_html, $this->data);
        $bccMails = !empty($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

        return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
            ->bcc($bccMails)
            ->html($templateHTML)
            ->subject($subject);
    }
}
