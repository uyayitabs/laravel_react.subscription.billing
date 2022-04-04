<?php

namespace App\Traits;

use App\Models\State;

trait HasStateTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }

    public function getStateNameAttribute()
    {
        $state = $this->state()->first();
        return $state ? $state->getAttribute('name') : '';
    }
}
