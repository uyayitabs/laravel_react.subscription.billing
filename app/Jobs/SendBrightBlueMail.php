<?php

namespace App\Jobs;

use Logging;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\BrightBlueAccountActivationMail;
use Exception;

class SendBrightBlueMail implements ShouldQueue
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
    protected $emailTemplateId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenant, $data, $email, $method, $shouldQueue = true, $emailTemplateId)
    {
        $email = setEmailAddress($email);
        $this->data = $data;
        $this->email = $email;
        $this->method = $method;
        $this->shouldQueue = $shouldQueue;
        $this->tenant = $tenant;
        $this->emailTemplateId = $emailTemplateId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new BrightBlueAccountActivationMail(
            $this->tenant,
            $this->method,
            $this->data,
            null,
            $this->emailTemplateId
        ));
        $mail = Mail::to($this->email);
        $this->shouldQueue ? $mail->queue($message) : $mail->send($message);
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        $logData = [
            'email' => $this->email,
            'method' => $this->method,
            'error' => $exception->getMessage(),
            'error_stacktrace' => $exception->getTraceAsString()
        ];

        Logging::exceptionWithData(
            $exception,
            "brightblue",
            $logData,
            18,
            0,
            !empty($this->tenant) ? $this->tenant : null
        );

        $this->delete();
    }
}
