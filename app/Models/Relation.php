<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Traits\HasBankAccountTrait;
use App\Traits\HasTenantTrait;
use App\Traits\HasSubscriptionTrait;
use App\Traits\HasAddressTrait;
use Carbon\Carbon;
use Intervention\Validation\Validator;
use Intervention\Validation\Rules\Iban;

class Relation extends BaseModel
{
    use HasBankAccountTrait;
    use HasTenantTrait;
    use HasSubscriptionTrait;
    use HasAddressTrait;

    protected $table = 'relations';

    protected $fillable = [
        'tenant_id',
        'bank_account',
        'bic',
        'credit_limit',
        'customer_number',
        'email',
        'fax',
        'kvk',
        'payment_condition_id',
        'phone',
        'relation_type_id',
        'status',
        'vat_no',
        'website',
        'company_name',
        'is_business',
        'type',
        'inv_output_type'
    ];

    protected $searchable = [
        'id,customer_number,email,phone,bank_account',
        'email',
        'addresses|street1,house_number,house_number_suffix,room,zipcode,city:name,country:name',
        'persons|email,first_name,middle_name,last_name',
        'bankAccounts|iban'
    ];

    public static $searchables = [
        'customer_number',
        'email',
        'iban',
        'full_name',
        'full_address',
        'subscription_description',
        'sales_invoice_invoice_number'
    ];

    public static $searchableCols = [
        'id',
        'customer_number',
        'email',
        'phone',
        'street',
        'company_name',
        'street',
        'house_number',
        'house_number_suffix',
        'room',
        'zipcode',
        'city',
        'country',
        'iban',
        'first_name',
        'middle_name',
        'last_name'
    ];

    protected $appends = [];

    public static $fields = [
        'id',
        'tenant_id',
        'relation_type_id',
        'customer_number',
        'status',
        'kvk',
        'email',
        'phone',
        'fax',
        'website',
        'vat_no',
        'bank_account',
        'bic',
        'credit_limit',
        'payment_condition_id',
        'company_name',
        'type',
        'inv_output_type',
         'is_business',
    ];

    public static $sortables = [
        'id',
        'tenant_id',
        'relation_type_id',
        'customer_number',
        'status',
        'kvk',
        'email',
        'phone',
        'fax',
        'website',
        'vat_no',
        'bank_account',
        'bic',
        'credit_limit',
        'payment_condition_id',
        'company_name',
        'info',
        'type',
    ];

    public static $scopes = [
        'addresses',
        'primary-address',
        'type',
        'relations-persons',
        'primary-person',
        'persons',
        'tenant',
        'subscriptions',
        'payment_condition'
    ];

    public static $withScopes = [
        'tenant',
        'subscriptions',
        'type',
        'relationsPersons',
        'persons',
        'addresses',
        'addresses.city',
        'addresses.country',
        'payment_condition'
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
     * Get active Relations only function
     *
     * @return \Relation[]
     */
    public function scopeActive()
    {
        return $this->where('status', 'ACTIVE');
    }

    /**
     * Get inactive Relations only function
     *
     * @return \Relation[]
     */
    public function scopeInactive()
    {
        return $this->where('status', 'INACTIVE');
    }

    /**
     * Get relation[] type function
     *
     * @return \RelationType
     */
    public function type()
    {
        return $this->hasOne(RelationType::class, 'id', 'relation_type_id');
    }


    public function relationsPersons()
    {
        return $this->hasMany(RelationsPerson::class);
    }

    /**
     * Get the persons that belong to the relation.
     */
    public function persons()
    {
        return $this->belongsToMany(Person::class, 'relations_persons')->orderBy('primary', 'DESC');
    }

    public function primaryPerson()
    {
        return $this->persons()->where('primary', 1);
    }

    /**
     * Get connectionPerson function
     *
     * @return \Person
     */
    public function primaryConnectionPerson()
    {
        return $this->primaryPerson()->first();
    }

    /**
     * Get SalesInvoices function
     *
     * @return \SalesInvoice[]
     */
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function paymentCondition()
    {
        if ($this->payment_condition_id) {
            return $this->belongsTo(PaymentCondition::class)->where('status', 1);
        } else {
            return $this->tenant->paymentConditions()->where([['status', 1], ['default', 1]]);
        }
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
     * Scopes to return specific relationships
     * @param $query
     *
     * @return object|array data related models
     */
    public function scopeWithRelations($query, $relations = [])
    {
        return $query->with($relations);
    }

    /**
     * Get generated relation_no without the format
     *
     * @param int $tenantId
     * @return array
     */
    public static function getGeneratedCustomerNumbers($tenantId)
    {
        $customerNumbers = Relation::select("customer_number")
            ->where("tenant_id", $tenantId)
            ->orderBy("customer_number", "ASC")
            ->get()
            ->pluck('customer_number')
            ->toArray();

        $results = array_map(
            function ($customerNumber) {
                $digitResults = [];
                $withMatch = preg_match("/\d{1,}/", $customerNumber, $digitResults);
                if ($withMatch) {
                    return intval($digitResults[0]);
                }
                return null;
            },
            $customerNumbers
        );
        return $results;
    }

    /**
     * Check whether a relation record is for a business or not
     *
     * @return boolean
     */
    public function isBusiness()
    {
        if (!empty($this->is_business)) {
            return !empty($this->company_name);
        }

        return false;
    }

    /**
     * Get the customer's full concatenated name.
     *
     * @return string
     */

    public function getCustomerNameAttribute()
    {
        $customerName = $this->customer_number;

        if (!empty($this->company_name)) {
            $customerName = "{$this->company_name} > {$this->customer_number}";
        }

        return $customerName;
    }

    public function getCustomerEmailAttribute()
    {
        return setEmailAddress($this->email);
    }

    public function getIsEndUserAttribute()
    {
        return $this->tenant->relation_id ? true : false;
    }

    public function getPrimaryPersonFullNameAttribute()
    {
        $primaryPerson = $this->primaryPerson()->first();
        return $primaryPerson ? $primaryPerson->full_name : null;
    }

    public function getBillingAddressAttribute()
    {

        $addresses = Address::where('address_type_id', 3)->where('relation_id', $this->id)->first();

        $rawAddress = '';

        if (!empty($addresses->street1)) {
            $rawAddress .= "{$addresses->street1} ";
        }

        if (!empty($addresses->house_number)) {
            $rawAddress .= "{$addresses->house_number} ";
        }

        if (!empty($addresses->house_number_suffix)) {
            $rawAddress .= "{$addresses->house_number_suffix} ";
        }

        if (!empty($addresses->room)) {
            $rawAddress .= "{$addresses->room} ";
        }

        if (!empty($addresses->zipcode)) {
            $rawAddress .= "{$addresses->zipcode} ";
        }

        if (!empty($addresses->city_name)) {
            $rawAddress .= "{$addresses->city_name} ";
        }

        $fullAddress = preg_replace("/\s{2,}/", " ", $rawAddress);

        return $fullAddress ? $fullAddress : null;
    }

    /**
     * Get default iban of bank_account
     * return string
     */
    public function getIban($directDebitReady = false)
    {
        $bankAccount = $this->bankAccount()->first();
        if ($bankAccount) {
            if ($directDebitReady) {
                $validator = new Validator(new Iban());
                $newIban = strtoupper(preg_replace("/\s{1,}/", "", $bankAccount->iban));
                if ($validator->validate($newIban)) {
                    return $newIban;
                }
                return null;
            } else {
                return $bankAccount->iban;
            }
        }
        return  null;
    }

    /**
     * Get default mndt_id of bank_account
     * return string
     */
    public function getMndtIdAttribute()
    {
        $bankAccount = $this->bankAccount()->first();
        return $bankAccount ? $bankAccount->mndt_id : null;
    }

    /**
     * Get default dt_of_sgntr of bank_account
     * return string
     */
    public function getDtOfSgntrAttribute()
    {
        $bankAccount = $this->bankAccount()->first();
        return $bankAccount ? Carbon::parse($bankAccount->dt_of_sgntr)->format("Y-m-d") : null;
    }

    public function getDefaultPaymentConditionIdAttribute()
    {
        if (!empty($this->payment_condition_id)) {
            return $this->paymentCondition()->first();
        } else {
            $tenant = $this->tenant;
            $paymentCondition = count($tenant->payment_conditions) ? $tenant->payment_conditions[0] : null;
            return !empty($paymentCondition) ? $paymentCondition->id : null;
        }
        return null;
    }

    public function getPhoneFax()
    {
        $phoneFax = [
            $this->phone,
            $this->fax
        ];
        return implode(" / ", $phoneFax);
    }

    /**
     * Get billing address 1
     *
     * @return string
     */
    public function getBillingAddress1Attribute()
    {
        $address = Address::where([['relation_id', '=', $this->id], ['address_type_id', '=', 3]])->first();
        if (!blank($address)) {
            $address1 = "{$address->street1} {$address->house_number}";
            if (!blank($address->house_number_suffix)) {
                $address1 .= "-{$address->house_number_suffix}";
            }
            $address1 .= " {$address->room}";
            return $address1;
        }
        return "";
    }

    /**
     * Get billing address 2
     *
     * @return string
     */
    public function getBillingAddress2Attribute()
    {
        $address = Address::where([['relation_id', '=', $this->id], ['address_type_id', '=', 3]])->first();
        if (!blank($address)) {
            return "{$address->zipcode} {$address->city_name}";
        }
        return "";
    }

    /**
     * Get contact address 1
     *
     * @return string
     */
    public function getContactAddress1Attribute()
    {
        $address = Address::where([['relation_id', '=', $this->id], ['address_type_id', '=', 1]])->first();
        if (blank($address)) {
            $address = $this->addresses()->first();
        }

        if (!blank($address)) {
            $address1 = "{$address->street1} {$address->house_number}";
            if (!blank($address->house_number_suffix)) {
                $address1 .= "-{$address->house_number_suffix}";
            }
            $address1 .= " {$address->room}";
            return $address1;
        }
        return "";
    }

    /**
     * Get contact address 2
     *
     * @return string
     */
    public function getContactAddress2Attribute()
    {
        $address = Address::where([['relation_id', '=', $this->id], ['address_type_id', '=', 1]])->first();
        if (blank($address)) {
            $address = $this->addresses()->first();
        }
        if (!blank($address)) {
            return "{$address->zipcode} {$address->city_name}";
        }
        return "";
    }
}
