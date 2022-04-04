<?php

namespace App\Models;

use App\Traits\HasStateTrait;
use App\Traits\HasCityTrait;
use App\Traits\HasCountryTrait;
use App\Traits\HasAddressTypeTrait;
use Spatie\QueryBuilder\AllowedFilter;

class Address extends BaseModel
{
    use HasStateTrait;
    use HasCityTrait;
    use HasCountryTrait;
    use HasAddressTypeTrait;

    protected $table = 'addresses';

    protected $fillable = [
        'relation_id',
        'address_type_id',
        'street1',
        'street2',
        'house_number',
        'house_number_suffix',
        'room',
        'zipcode',
        'zipcode_id',
        'city_id',
        'country_id',
        'state_id',
        'primary',
        'city.name',
        'city.id'
    ];

    public static $fields = [
        'id',
        'relation_id',
        'address_type_id',
        'street1',
        'street2',
        'house_number',
        'house_number_suffix',
        'room',
        'zipcode',
        'zipcode_id',
        'city_id',
        'country_id',
        'state_id',
        'primary',
        'city.name',
        'city.id'
    ];

    protected $casts = [
        'primary' => 'bool'
    ];

    protected $appends = [];

    public static $scopes = [
        'address-type',
        'city',
        'country'
    ];

    public static $withScopes = [
        'addressType',
        'city',
        'country'
    ];

    protected $searchable = [
        'street1,house_number,house_number_suffix,room,zipcode',
        'city|name',
        'country|name',
        'addressType|type'
    ];

    public static $searchableCols = [
        'street',
        'house_number',
        'house_number_suffix',
        'room',
        'zipcode',
        'city',
        'country',
        'type'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (!$model->primary) {
                $condition = [
                    ['relation_id', '=', $model->relation_id],
                    ['address_type_id', '=', $model->address_type_id],
                    ['primary', '=', 1],
                ];
                $count = Address::where($condition)->count();
                if ($count == 0) {
                    $model->primary = 1;
                }
            } else {
                Address::where([
                    ['address_type_id', '=', $model->address_type_id],
                    ['relation_id', '=', $model->relation_id]
                ])->update([
                    'primary' => false
                ]);
            }
        });
    }

    /**
     * Get relation function
     *
     * @return \Relation
     */
    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    /**
     * Get relation function
     *
     * @return \City
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get subscriptions function
     *
     * @return \Country
     */
    public function subscriptions()
    {
        return $this->hasManyThrough(
            Subscription::class,
            Relation::class,
            'id',
            'relation_id'
        )->withAll();
    }

    /**
     * full function
     *
     * @return String
     */
    public function getFullAddressAttribute()
    {
        $rawAddress = "{$this->street1} {$this->house_number}";
        if (!empty($this->house_number_suffix)) {
            $rawAddress .= " {$this->house_number_suffix}";
        }
        $rawAddress .= " {$this->room}";
        $rawAddress .= " {$this->zipcode} {$this->city_name}";
        return preg_replace("/\s{2,}/", " ", $rawAddress);
    }

    public function getIsOnlyOfTypeAttribute()
    {
        if (
            Address::where([
            ['id', '<>', $this->id],
            ['relation_id', $this->relation_id],
            ['address_type_id', $this->address_type_id]
            ])->exists()
        ) {
            return false;
        }

        return true;
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
}
