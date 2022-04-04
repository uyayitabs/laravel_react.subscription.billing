<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Mail;
use Exception;
use App\Mail\QueueJobMail;

class SendQueueJobMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $email;
    protected $message;
    protected $attachedFile;
    protected $subject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $email, $subject, $attachedFile)
    {
        $this->email = $email;
        $this->data = $data;
        $this->subject = $subject;
        $this->attachedFile = $attachedFile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new QueueJobMail($this->data, $this->subject, $this->attachedFile));
        $mail = Mail::to($this->email);
        $mail->send($message);
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        \Logging::exceptionWithMessage($exception, 'Failed to send QueueJobMail', 1);
        $this->delete();
    }
}
