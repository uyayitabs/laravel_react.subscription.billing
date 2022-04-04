<?php

namespace App\Traits;

use App\Models\Account;

trait HasAccountTrait
{
    /**
     * Get accounts[] function
     *
     * @return \app\Accounts[]
     */
    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    /**
     * Get account function
     *
     * @return \app\Account
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
