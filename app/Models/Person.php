<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Person extends BaseModel
{
    use Notifiable;

    protected $table = 'persons';

    protected $fillable = [
        'gender',
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'language',
        'linkedin',
        'facebook',
        'birthdate'
    ];

    protected $appends = [
        'full_name',
        'relation_type'
    ];

    public static $fields = [
        'id',
        'gender',
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'language',
        'linkedin',
        'facebook',
        'birthdate'
    ];

    public static $scopes = [
        'user',
        'relation',
        'relations-person'
    ];

    public static $withScopes = [
        'user',
        'relation',
        'relationsPerson'
    ];

    protected $casts = [
        'birthdate' => 'datetime:Y-m-d'
    ];

    protected $searchable = [
        'first_name,middle_name,last_name,email,phone,mobile,primary'
    ];

    public static $searchableCols = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'mobile'
    ];

    /**
     * Boot method
     */
    public static function boot()
    {
        parent::boot();

        // before delete() method call this
        static::deleting(function ($person) {
            RelationsPerson::where('person_id', $person->id)->delete();
        });
    }

    /**
     * full function
     *
     * @return String
     */
    public function getFullNameAttribute()
    {
        if (!is_null($this->first_name) && !is_null($this->last_name)) {
            $pcs = [];
            if ($this->first_name) {
                $pcs[] = $this->first_name;
            }
            if ($this->middle_name) {
                $pcs[] = $this->middle_name;
            }
            if ($this->last_name) {
                $pcs[] = $this->last_name;
            }
            $rawFullName = implode(' ', $pcs);
            return preg_replace("/\s{2,}/", " ", $rawFullName);
        }
        return "";
    }

    public function getRelationTypeAttribute()
    {
        $id = $this->id;
        $relationsPerson = RelationsPerson::where('person_id', $id)->first();
        $relation = $relationsPerson ? $relationsPerson->relation : null;
        $type = $relation && $relation->relation_type_id ? $relation->relation_type_id : null;
        return $type;
    }

    /**
     * Get billing address function
     *
     * @return String
     */
    // public function billingAddress()
    // {
    //     return $this->addresses()->where("address_type_id", 3)->first();
    // }

    /**
     * Get shipping address function
     *
     * @return String
     */
    // public function shippingAddress()
    // {
    //     return $this->addresses()->where("address_type_id", 1)->first();
    // }

    public function type()
    {
        return $this->hasOne(PersonType::class, 'id', 'person_type_id');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Get the relations that belong to the person.
     */
    public function relations()
    {
        return $this->belongsToMany(Relation::class, 'relations_persons');
    }
    public function relation()
    {
        // return $this->belongsToMany(RelationsPerson::class, 'relationsPerson');
        return $this->hasOneThrough(RelationsPerson::class, Relation::class, 'id', 'relation_id', 'id');
    }

    public function relationsPerson()
    {
        return $this->belongsTo(RelationsPerson::class, 'id', 'person_id')->with('relation');
    }

    /**
     * Get Address[] function
     *
     * @return \Address[]
     */
    // public function addresses()
    // {
    //     return $this->hasManyThrough(
    //         Address::class,
    //         Relation::class,
    //         'id',
    //         'relation_id',
    //         '',
    //         ''
    //     );
    // }

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
     * Scopes to return all the relationships
     *
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithRelations($query, $relations = [])
    {
        return $query->with($relations);
    }

    public function setBirthdateAttribute($value)
    {
        $this->attributes['birthdate'] = dateFormat($value);
    }

    public function getAgeAttribute()
    {
        return $this->birthdate ? now()->diffInYears(Carbon::parse($this->birthdate)) : $this->birthdate;
    }

    public function getCustomerEmailAttribute()
    {
        return setEmailAddress($this->email);
    }

    public function subscriptionProvisionings()
    {
        return $this->hasMany(Subscription::class, 'provisioning_person');
    }

    public function subscriptionBillings()
    {
        return $this->hasMany(Subscription::class, 'billing_person');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'invoice_person_id');
    }

    public function salesShippingInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'shipping_person_id');
    }

    public function getRemovableAttribute()
    {
        $countSubscriptionBilling = $this->subscriptionBillings()->count();
        $countSubscriptionProvisionung = $this->subscriptionProvisionings()->count();
        $countSalesInvoice = $this->salesInvoices()->count();
        $countShippingInvoice = $this->salesShippingInvoices()->count();

        $removable = !$this->user &&
            0 == $countSubscriptionBilling &&
            0 == $countSubscriptionProvisionung &&
            0 == $countSalesInvoice &&
            0 == $countShippingInvoice;


        return $removable ? 1 : 0;
    }
}
