<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueJob extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job',
        'data',
        'user_id',
        'status_id',
        'tenant_id',
        'type'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function queueJobLog()
    {
        return $this->hasOne(QueueJobLog::class, 'queue_job_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class)->whereHas('type', function ($query) {
            $query->where('type', 'job');
        });
    }

    public function scopeStatus($query, $status)
    {
        return $query->whereHas('status');
    }
}
