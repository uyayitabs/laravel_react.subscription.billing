<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\QueueJob;

class ProcessQueueJobs extends Command
{
    protected $signature = 'queuejob:run';

    protected $description = 'Process new queue job';

    public function handle(): void
    {
        QueueJob::dispatchNow();
    }
}
