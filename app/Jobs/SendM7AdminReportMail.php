<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\AdminErrorReportMail;
use Exception;
use Mail;
use Carbon\Carbon;

class SendM7AdminReportMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $emails;
    protected $shouldQueue;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $admin_emails, $subject = 'M7 Error report', $shouldQueue = false)
    {
        $data['dt'] = Carbon::parse('UTC')->format('Y-m-d H:i:s');
        $data['env'] = config('app.env');
        $data['tenant'] = '';
        $this->emails = $admin_emails;
        $this->data = $data;
        $this->shouldQueue = $shouldQueue;
        $this->subject = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new AdminErrorReportMail($this->data, $this->subject));
        $mail = Mail::to($this->emails);
        $this->shouldQueue ? $mail->queue($message) : $mail->send($message);
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        $this->delete();
    }
}
