<?php

namespace App\Traits;

use App\Models\BillingRun;

trait HasBillingRunTrait
{
    /**
     * Get accounts[] function
     *
     * @return \app\BillingRun[]
     */
    public function billingRuns()
    {
        return $this->hasMany(BillingRun::class, 'tenant_id', 'id');
    }
}
