<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeprovisioningReport extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $subLines;
    public $subLinesNotOk;
    public $timestamp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subLines, $notOk, Carbon $timestamp, $files)
    {
        $this->from = [['name' => 'GRiD', 'address' => 'grid@f2x.nl']];
        $this->to(array_filter(explode(",", config('m7.deprovisioning_email_cron_to_recipients'))));
        $this->cc(array_filter(explode(",", config('m7.deprovisioning_email_cron_cc_recipients'))));
        $this->bcc(array_filter(explode(",", config('m7.deprovisioning_email_cron_bcc_recipients'))));

        $this->subject = '[' . config('app.env') . '] Overzicht deprovisioning ' . $timestamp->format('d-m-Y');
        $this->subLines = $subLines;
        $this->subLinesNotOk = $notOk;
        $this->timestamp = $timestamp->format('d-m-Y H:i');

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
        return $this->view('emails.reports.deprovisioning_report');
    }
}
