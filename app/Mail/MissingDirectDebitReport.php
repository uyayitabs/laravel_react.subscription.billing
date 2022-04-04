<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MissingDirectDebitReport extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $list;
    public $timestamp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($list)
    {
        $this->from = [['name' => 'GRiD', 'address' => 'grid@f2x.nl']];
        $this->to(array_filter(explode(",", config('rabobank.no_bank_account_dd_report_email_cron_to_recipients'))));
        $this->cc([]);
        $this->bcc([]);
        $now = Carbon::parse(now()->format('Y-m-d H:i:s'), 'Europe/Amsterdam');
        $this->timestamp = $now->copy()->format('d-m-Y H:i');
        $this->subject = '[' . config('app.env') . '] Overzicht bankrekeningen zonder direct debit ' . $now->copy()->format('d-m-Y');
        $this->list = $list;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reports.missing_direct_debit');
    }
}
