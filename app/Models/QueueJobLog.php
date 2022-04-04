<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueJobLog extends Model
{
    protected $fillable = [
        'queue_job_id',
        'json_data'
    ];

    protected $casts = [
        'json_data' => 'array'
    ];

    public function queueJob()
    {
        return $this->belongsTo(QueueJob::class);
    }
}
