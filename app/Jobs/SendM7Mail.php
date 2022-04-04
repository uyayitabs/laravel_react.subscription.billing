<?php

namespace App\Jobs;

use Logging;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\M7Mail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendM7Mail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $email;
    protected $shouldQueue;
    protected $method;
    protected $tenant;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenant, $data, $email, $method, $shouldQueue = false)
    {
        $email =  setEmailAddress($email);
        $this->data = $data;
        $this->email = $email;
        $this->method = $method;
        $this->shouldQueue = $shouldQueue;
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new M7Mail($this->tenant, $this->method, $this->data));
        $mail = Mail::to($this->email);
        Logging::information('Sending m7 email', ['email' => $this->email, 'method' => $this->method], 16, 0);
        $this->shouldQueue ? $mail->queue($message) : $mail->send($message);
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        $message = "#SendM7Mail";
        $message .= " to {$this->email}";
        $message .= " method {$this->method}";
        Logging::exceptionWithData($exception, 'Sending m7 email failed', $message, 16, 'error', 1);
        $this->delete();
    }
}
