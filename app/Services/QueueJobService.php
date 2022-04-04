<?php

namespace App\Services;

use Logging;
use Illuminate\Support\Facades\Artisan;
use App\Models\QueueJob;
use Carbon\Carbon;

class QueueJobService
{
    protected $statusService;

    public function __construct()
    {
        $this->statusService = new StatusService();
    }

    public function list($tatus = 'all')
    {
        return QueueJob::get();
    }

    public function create($job, $data, $type = null, $user_id = null, $tenant_id = null)
    {
        if (!$tenant_id) {
            $tenant_id = currentTenant('id');
        }
        $status = $this->statusService->getStatus('job', 'new');
        return QueueJob::create([
            'job' => $job,
            'type' => $type,
            'data' => $data,
            'status_id' => $status->id,
            'user_id' => $user_id,
            'tenant_id' => $tenant_id
        ]);
    }

    public function find($id)
    {
        return QueueJob::find($id);
    }

    public function findBy($where)
    {
        return QueueJob::where($where);
    }

    public function log($queuejob, $data)
    {
        return $queuejob->queueJobLog()->create($data);
    }

    public function update($queuejob)
    {
    }

    /**
     * Run the artisan command for 1 queue job that is new
     */
    public function run()
    {
        $status = $this->statusService->getStatus('job', 'new');
        $queuejob = QueueJob::where('status_id', $status->id)->first();

        Logging::information('Queue job start', $queuejob, 9, 1);

        if ($queuejob) {
            $arguments = $queuejob->data;
            if (isset($arguments['--date'])) {
                $arguments['--date'] = Carbon::parse($arguments['--date'])->format('Y-m-d');
            }
            $statusInProgress = $this->statusService->getStatus('job', 'in_progress');
            $queuejob->status_id = $statusInProgress->id;
            $queuejob->save();
            ini_set('memory_limit', '-1');
            try {
                Artisan::call($queuejob->job, $arguments);
                $statusDone = $this->statusService->getStatus('job', 'done');
                $queuejob->status_id = $statusDone->id;
                $queuejob->save();

                Logging::information(
                    'Queue job',
                    [
                        'job' => $queuejob->job,
                        'arguments' => $arguments,
                        'status' => 'done',
                        'message' => 'done'
                    ],
                    9,
                    1,
                    $queuejob->tenant_id,
                    'queue_job',
                    $queuejob->id
                );
            } catch (\Exception $exception) {
                Logging::exception($exception, 9, 0);
                $statusFailed = $this->statusService->getStatus('job', 'failed');
                $queuejob->status_id = $statusFailed->id;
                $queuejob->save();

                //TODO SEND EXCEPTION MAIL

                Logging::exceptionWithData(
                    $exception,
                    'Queue job failed: ' . $exception->getMessage(),
                    [
                        'job' => $queuejob->job,
                        'arguments' => $arguments,
                        'status' => 'failed',
                    ],
                    9,
                    0,
                    $queuejob->tenant->id,
                    'queue_job',
                    $queuejob->id
                );
            }
        }
    }
}
