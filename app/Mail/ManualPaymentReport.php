<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ManualPaymentReport extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $payments;
    public $timestamp;
    public $since;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payments, Carbon $timestamp, Carbon $since, $files)
    {
        $this->from = [['name' => 'GRiD', 'address' => 'grid@f2x.nl']];
        $this->to(array_filter(explode(",", config('rabobank.payments_report_email_cron_to_recipients'))));
        $this->cc(array_filter(explode(",", config('rabobank.payments_report_email_cron_cc_recipients'))));
        $this->bcc(array_filter(explode(",", config('rabobank.payments_report_email_cron_bcc_recipients'))));

        $this->subject = '[' . config('app.env') . '] Overzicht handmatige betalingen ' . $timestamp->format('d-m-Y');
        $this->payments = $payments;
        $this->timestamp = $timestamp->format('d-m-Y H:i');
        $this->since = $since->format('d-m-Y H:i');

        foreach ($files as $file) {
            $this->attach($file);
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reports.manual_payments');
    }
}
