<?php

namespace App\Traits;

use App\Models\PlanLine;

trait HasPlanLineTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function planLine()
    {
        return $this->belongsTo(PlanLine::class);
    }
}
