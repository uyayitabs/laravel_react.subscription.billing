<?php

namespace App\Mail;

use App\Models\SalesInvoice;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CdrImportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected $data = [
        "type" => null, // success | failed
        "filename" => null, // CSV filename
        "processed_records" => null, // number of records processed
        "errors" => null, // error messages in []
        "env" => null,
        "failed_csv" => null,
    ];
    protected $tenantId = null;

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
        $emailTemplate = $subject = $bodyHTML = null;
        $bccMails = [];
        $tenant = Tenant::find($this->tenantId);
        $templateType = trim("cdr_import." . $this->data['type']); // cdr_import.success
        $emailTemplate = $tenant->emailTemplatesByType($templateType)->first(); //cdr_import.success or cdr_import.failed

        if (isset($emailTemplate)) {
            $bccMails = !blank($emailTemplate->bcc_email) ? explode(",", $emailTemplate->bcc_email) : [];

            // subject
            $subject = getStringBladeView($emailTemplate->subject, [
                'env' => config('app.env'),
                'tenantName' => $tenant->name,
            ]);

            // body
            $bodyHTML = getStringBladeView($emailTemplate->body_html, [
                'filename' => $this->data['filename'],
                'processed_records' => $this->data['processed_records'],
                'failed_records' => $this->data['failed']
            ]);

            if (!blank($this->data["failed_csv"])) {
                return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
                    ->bcc($bccMails)
                    ->html($bodyHTML)
                    ->subject($subject)
                    ->attach($this->data['failed_csv']);
            } else {
                return $this->from($emailTemplate->from_email, $emailTemplate->from_name)
                    ->bcc($bccMails)
                    ->html($bodyHTML)
                    ->subject($subject);
            }
        }
        return;
    }
}
