<?php

namespace App\Traits;

use App\Models\SubscriptionLine;

trait HasSubscriptionLineTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriptionLine()
    {
        return $this->belongsTo(SubscriptionLine::class);
    }
}
