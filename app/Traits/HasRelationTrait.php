<?php

namespace App\Traits;

use App\Models\Relation;

trait HasRelationTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }
}
