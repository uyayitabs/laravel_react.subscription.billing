<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\HasOneFiscalYear;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends BaseModel
{
    use BelongsToTenant;
    use HasOneFiscalYear;

    protected $fillable = [
        'tenant_id',
        'fiscal_year_id',
        'description',
        'date_from',
        'date_to',
        'is_closed',
    ];

    public static $fields = [
        'tenant_id',
        'fiscal_year_id',
        'description',
        'date_from',
        'date_to',
        'is_closed',
    ];

    protected $casts = [
        'date_from' => 'datetime:Y-m-d',
        'date_to' => 'datetime:Y-m-d',
    ];

    public static $includes = [
        'fiscal-year'
    ];

    public static $scopes = [];
    public static $sorts = [
        'id',
        'description',
        'date_from',
        'date_to',
        'is_closed',
    ];

    /**
     * Get entries under period
     */
    public function entries()
    {
        return $this->hasMany(Entry::class, 'period_id', 'id');
    }

    /**
     * Get accounting_period by tenant ID and date parameter
     *
     * @param int $tenantId
     * @param string $dateParam = 'Y-m-d'
     * @return AccountingPeriod
     */
    public static function findByTenantIdAndDate($tenantId, $dateParam): AccountingPeriod
    {
        return AccountingPeriod::where([
                ['tenant_id', '=', $tenantId],
                ['date_from', '<=', $dateParam],
                ['date_to', '>=', $dateParam],
            ])->first();
    }
}
