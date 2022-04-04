<?php

namespace App\Jobs;

use Logging;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;
use App\Mail\UserMail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendUserMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $data;
    protected $email;
    protected $type;
    protected $tenantId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $user, $type, $tenantId)
    {
        $this->data = $data;
        $this->email = $user->person->customerEmail;
        $this->type = 'user.' . $type;
        $this->tenantId = $tenantId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new UserMail($this->data, $this->type, $this->tenantId));
        $mail = Mail::to($this->email);
        $mail->send($message);
        Logging::information(
            'Send validation code',
            [
                'data' => $this->data,
                'type' => $this->type,
                'email' => $this->email
            ],
            1,
            1,
            $this->tenantId
        );
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Exception $exception)
    {
        $message = [
            "action" => "#SendUserMail",
            "to" => $this->email,
            "type" => $this->type,
            "tenant_id" => $this->tenantId,
            "exception_message" => $exception->getMessage(),
            "exception" => $exception->getTraceAsString()
        ];
        Logging::exceptionWithData($exception, 'Failed to send mail', $message, 1, 0, $this->tenantId);
        $this->delete();
    }
}
