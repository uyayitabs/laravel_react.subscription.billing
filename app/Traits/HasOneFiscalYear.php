<?php

namespace App\Traits;

use App\Models\FiscalYear;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasOneFiscalYear
{
    public function fiscalYear(): HasOne
    {
        return $this->hasOne(FiscalYear::class, 'id', 'fiscal_year_id');
    }
}
