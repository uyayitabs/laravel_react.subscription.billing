<?php

namespace App\Traits;

use App\Models\Status;

trait HasStatusTrait
{
    /**
     * Get accounts[] function
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statuses()
    {
        return $this->hasMany(Status::class, 'id', 'status_id');
    }
}
