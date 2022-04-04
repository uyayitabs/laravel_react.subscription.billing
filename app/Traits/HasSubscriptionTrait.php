<?php

namespace App\Traits;

use App\Models\Subscription;

trait HasSubscriptionTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get subscriptions[] function
     *
     * @return \Subscription
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
