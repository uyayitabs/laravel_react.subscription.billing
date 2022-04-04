<?php

namespace App\Jobs;

use Logging;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;
use App\Mail\SalesInvoiceReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SalesInvoiceReminderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $salesInvoice, $shouldQueue;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($salesInvoice, $shouldQueue = true)
    {
        $this->salesInvoice = $salesInvoice;
        $this->shouldQueue = $shouldQueue;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $salesInvoice = $this->salesInvoice;
        $salesInvoiceMeta = $salesInvoice->salesInvoiceMetas()->where('key', 'reminder_status')->first();
        if (!$salesInvoiceMeta || $salesInvoiceMeta->value != 'final_notice_sent' && $salesInvoiceMeta->value != 'sent_to_collection_agency') {
            $invoicePerson = $salesInvoice->invoicePerson;
            $invoiceAddress = $salesInvoice->invoiceAddress;
            $relation = $salesInvoice->relation;

            $rawAddress1 = "{$invoiceAddress->street1} {$invoiceAddress->house_number}";
            if (!empty($invoiceAddress->house_number_suffix)) {
                $rawAddress1 .= " {$invoiceAddress->house_number_suffix}";
            }
            $rawAddress1 .= " {$invoiceAddress->room}";
            $rawAddress2 = " {$invoiceAddress->zipcode} {$invoiceAddress->city_name}";
            $bankAccount = $relation->bankAccounts()->where('status', 1)->first();
            $data = [
                'user_fullname' => $invoicePerson->full_name,
                'user_initial' => $invoicePerson->initials,
                'user_lastname' => $invoicePerson->last_name,
                'street' => $rawAddress1,
                'address' =>   $rawAddress2,
                'date' => Carbon::parse('UTC'),
                'iban' => $bankAccount ? $bankAccount->iban : '--',
                'customer_number' => $relation->customer_number,
                'title' => $invoicePerson->title ?? '',
                'invoiceId' => $salesInvoice->id,
                'invoice_number' => $salesInvoice->invoice_no,
                'invoice_date' => $salesInvoice->date,
                'amount' => $salesInvoice->price_total
            ];

            $key = 'first_reminder';
            if ($salesInvoiceMeta) {
                switch ($salesInvoiceMeta->value) {
                    case 'first_reminder_sent':
                        $key = 'second_reminder';
                        break;

                    case 'second_reminder_sent':
                        $key = 'warning';
                        break;

                    case 'warning_sent':
                        $key = 'final_notice';
                        break;
                }
            }

            if (filter_var($invoicePerson->customer_email, FILTER_VALIDATE_EMAIL)) {
                $emailTemplate = $salesInvoice->tenant->emailTemplatesByType("sales_invoice.$key")->first();
                $subject = getStringBladeView($emailTemplate->subject, ['invoice_number' => $data['invoice_number']]);
                $data['subject'] = $subject;

                Logging::information(
                    'SALES INVOCE REMINDER',
                    [
                        'salesInvoice' => $salesInvoice->id,
                        'invoice no' => $salesInvoice->invoice_no,
                        'salesInvoiceMeta->value' => $salesInvoiceMeta ? $salesInvoiceMeta->value : null,
                        'key' => $key,
                        'data' => $data
                    ],
                    17,
                    1,
                    null
                );

                $message = (new SalesInvoiceReminderMail($data, $emailTemplate, $salesInvoice->invoice_file_full_path));
                $mail = Mail::to($invoicePerson->customer_email);
                $res = $this->shouldQueue ? $mail->queue($message) : $mail->send($message);
            }
        } else {
            $res = true;
        }

        return $res;
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        $logData = [];
        if (!empty($this->email)) {
            $logData['email'] = $this->email;
        }
        if (!empty($this->invoiceNo)) {
            $logData['invoice_number'] = $this->salesInvoice->invoice_number;
        }
        $logData['error_stacktrace'] = $exception->getTraceAsString();
        Logging::exception(
            $exception,
            17,
            0,
            null
        );
        $this->delete();
    }
}
