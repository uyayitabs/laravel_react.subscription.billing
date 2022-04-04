<?php

namespace App\Traits;

use App\Models\UserCode;

trait HasUserCodeTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userCode()
    {
        return $this->belongsTo(UserCode::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userCodes()
    {
        return $this->hasMany(UserCode::class);
    }
}
