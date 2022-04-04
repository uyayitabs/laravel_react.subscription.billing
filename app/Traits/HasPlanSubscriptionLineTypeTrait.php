<?php

namespace App\Traits;

use App\Models\PlanSubscriptionLineType;

trait HasPlanSubscriptionLineTypeTrait
{
    /**
     * Get the line type
     *
     * @return \PlanSubscriptionLineType
     */
    public function lineType()
    {
        return $this->hasOne(PlanSubscriptionLineType::class, 'id', 'subscription_line_type');
    }

    public function getLineTypeNameAttribute()
    {
        return $this->lineType && $this->lineType->line_type ? $this->lineType->line_type : null;
    }
}
