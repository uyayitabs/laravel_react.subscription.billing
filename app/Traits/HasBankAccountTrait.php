<?php

namespace App\Traits;

use App\Models\BankAccount;

trait HasBankAccountTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bankAccount()
    {
        return $this->bankAccounts()->where([['dd_default', '=', 1], ['status', '=', 1]]);
    }

    /**
     * Get default account iban
     * return string
     */
    public function getIbanAttribute()
    {
        $bankAccount = $this->bankAccounts()->where('status', 1)->first();
        return $bankAccount ? $bankAccount->iban : null;
    }
}
