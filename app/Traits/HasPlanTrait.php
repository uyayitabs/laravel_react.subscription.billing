<?php

namespace App\Traits;

use App\Models\Plan;

trait HasPlanTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
