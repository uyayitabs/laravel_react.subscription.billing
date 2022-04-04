<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Traits\HasBillingRunTrait;
use App\Traits\HasCountryTrait;

class Tenant extends BaseModel
{
    // use NodeTrait;
    use HasCountryTrait;
    use HasBillingRunTrait;

    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'parent_id',
        'relation_id',
        'billing_day',
        'billing_schedule',
        'invoice_start_calculation',
        'use_accounting',
        'default_country_id',
        'service_url',
        'service_number',
        'service_email',
        'identifier',
        'settings'
    ];

    public static $fields = [
        'id',
        'parent_id',
        'relation_id',
        'name',
        'billing_day',
        'billing_schedule',
        'invoice_start_calculation',
        'use_accounting',
        'default_country_id',
        'service_url',
        'service_number',
        'service_email',
        'identifier',
        'settings'
    ];

    public static $scopes = [
        'parent',
        'children',
        'relations',
        'relations.type',
        'relations.persons',
        'payment_conditions'
    ];

    public static $withScopes = [
        'parent',
        'children',
        'relations',
        'relations.type',
        'relations.persons',
        'payment_conditions'
    ];

    protected $searchable = [
        'name,billing_day'
    ];

    public static $searchableCols = [
        'name',
        'billing_day'
    ];

    protected $casts = [
        'invoice_start_calculation' => 'datetime:Y-m-d',
        'settings' => 'array'
    ];


    /**
     * Boot method
     *
     * @return Illuminate\Database\Eloquent\Builder|null
     */
    public static function boot()
    {
        parent::boot();
    }

    /**
     * Get Subscription with subscription_start only function
     *
     * @return \Relation[]
     */
    public function scopeNotRoot()
    {
        return $this->where("id", ">", 1)->orWhere("name", "<>", "Root");
    }

    /**
     * Get parent tenant
     *
     * @return Tenant
     */
    public function parent()
    {
        return $this->belongsTo(Tenant::class, 'parent_id');
    }

    /**
     * Get resellers
     *
     * @return Tenant
     */
    public function children()
    {
        return $this->hasMany(Tenant::class, 'parent_id', 'id')->orderBy('name', 'asc');
    }

    /**
     * Get nested resellers
     *
     * @return Tenant
     */
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * Get nested resellers by id
     *
     * @return int[]
     */
    public function getChildrenList()
    {
        $childrenList = [];

        foreach ($this->children as $child) {
            $childrenList[count($childrenList)] = $child->id;
            $childrenList = array_merge($childrenList, $child->getChildrenList());
        }
        return $childrenList;
    }

    /**
     * Get Brands function
     *
     * @return \Brand[]
     */
    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    /**
     * Get Relation function
     *
     * @return \Relation[]
     */
    public function relations()
    {
        return $this->hasMany(Relation::class, 'tenant_id', 'id');
    }

    /**
     * Get Warehouse function
     *
     * @return \Warehouse[]
     */
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    /**
     * Get SalesInvoice function
     *
     * @return \SalesInvoice[]
     */
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }

    /**
     * Get NumberRange function
     *
     * @return NumberRange
     */
    public function numberRange()
    {
        return $this->hasOne(NumberRange::class, 'tenant_id', 'id');
    }

    /**
     * Get VatCode function
     *
     * @return VatCode[]
     */
    public function vatCodes()
    {
        return $this->hasMany(VatCode::class);
    }

    /**
     * Get the products that belong to the tenant.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'tenant_products');
    }

    /**
     * Get the products that belong to the tenant.
     */
    public function tenantProducts()
    {
        return $this->hasMany(TenantProduct::class);
    }

    public function paymentConditions()
    {
        return $this->hasMany(PaymentCondition::class)->active()->orderBy("default", "DESC");
    }

    /**
     * Get JsonData
     *
     * @return \JsonData
     */
    public function jsonDatas()
    {
        return $this->hasMany(JsonData::class);
    }

    /**
     * Scopes to return all the relationships
     *
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithAll($query)
    {
        return $query->with(self::$withScopes);
    }

    /**
     * Scopes to return specified relations
     *
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithRelations($query, $relations = [])
    {
        return $query->with($relations);
    }

    /**
     * Scope a query to get the child tenants
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMytenants($query)
    {
        $result = $this->childrenRecursive()->where('parent_id', currentTenant('id'))->get();

        return $query->whereIn('id', $result->pluck('id'));
    }


    /**
     * Get tenant_ids whose billing_day = $billingDay
     *
     * @param integer $billingDay
     * @return array
     */
    public function scopeGetIdsInvoicingToday($query, $billingDay)
    {
        return $query->where("billing_day", $billingDay)
            ->pluck("id")
            ->toArray();
    }



    /**
     * pdfTemplates of a tenant
     *
     * @return \Tenant
     */
    public function pdfTemplates()
    {
        return $this->hasMany(PdfTemplate::class, 'tenant_id', 'id');
    }

    public function getPdfTemplate($type)
    {
        return $this->pdfTemplates()
            ->where([
                ['type', '=', $type],
                ['version', '=', 'final']
            ]);
    }

    /**
     * Email template(s) of a tenant
     *
     * @return \Tenant
     */
    public function emailTemplates()
    {
        return $this->hasMany(EmailTemplate::class, 'tenant_id', 'id');
    }

    /**
     * Get email template(s) by type param
     *
     * @param mixed $type
     * @return App\EmailTemplate[]
     */
    public function emailTemplatesByType($type)
    {
        return $this->emailTemplates()->where("type", $type);
    }

    public function getSluggedNameAttribute()
    {
        return Str::slug($this->name);
    }

    /**
     * Get contract period
     *
     * @return \ContractPeriod
     */
    public function contractPeriods()
    {
        return $this->hasMany(ContractPeriod::class, 'tenant_id', 'id');
    }
}
