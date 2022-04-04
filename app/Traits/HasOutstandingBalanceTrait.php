<?php

namespace App\Traits;

use App\Models\City;

trait HasOutstandingBalanceTrait
{
    public function getOutstandingBalanceAttribute()
    {
        $entries = $this->entries();
        $summedDebit = $entries->sum("debit");
        $summedCredit = $entries->sum("credit");
        $summedVatAmount = $entries->sum("vat_amount");
        $totalCreditAmount = $summedCredit + $summedVatAmount;
        return $summedDebit - $totalCreditAmount;
    }
}
