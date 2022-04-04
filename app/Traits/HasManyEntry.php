<?php

namespace App\Traits;

use App\Models\Entry;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyEntry
{
    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class, 'account_id', 'id');
    }
}
