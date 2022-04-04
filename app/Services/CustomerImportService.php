<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Str;
use App\Services\RelationService;
use App\Services\PersonService;
use App\Services\AddressService;
use App\Services\BankAccountService;
use App\Models\Tenant;
use App\Models\City;
use App\Models\State;
use App\Models\Zipcode;
use App\Models\Relation;
use App\Models\Address;

class CustomerImportService extends BaseService
{
    protected $columns = [
            'NAW Title' => 'title',
            'NAW First Name' => 'first_name',
            'NAW Middle Name' => 'middle_name',
            'NAW Last Name' => 'last_name',
            'NAW Street' => 'street1',
            'NAW Street Nr' => 'house_number',
            'NAW Street Nr Ext' => 'house_number_suffix',
            'NAW Street Nr Room' => 'room',
            'NAW Postal Code' => 'zipcode',
            'NAW City' => 'city',
            'NAW Contact Phone' => 'phone',
            'NAW Contact Mobile' => 'mobile',
            'NAW Contact Email' => 'email',
            'NAW Bank Account' => 'iban',
            'NAW Birthday' => 'birthdate',
            'NAW CUST NR' => 'customer_number'
        ],
        $tenant,
        $relationService,
        $personService,
        $addressService,
        $bankAccountService,
        $countryId,
        $states,
        $cities = [],
        $zipcodes = [],
        $action,
        $from,
        $to;

    public function __construct($tenantId, $countryId, $action, $from, $to)
    {
        $this->tenant = Tenant::find($tenantId);
        $this->countryId = $countryId;
        $this->relationService = new RelationService();
        $this->personService = new PersonService();
        $this->addressService = new AddressService();
        $this->bankAccountService = new BankAccountService();
        $this->states = State::where('country_id', $this->countryId)->pluck('id');
        $this->action = $action;
        $this->from = $from;
        $this->to = $to;
    }

    public function processCSVImport($csvFile)
    {
        $invalidData = (new FastExcel())
            ->configureCsv(',', '"', '\n', 'UTF-8')
            ->import(
                $csvFile,
                function ($line) {
                    $this->processImport($line);
                }
            );
    }

    public function processImport(array $line)
    {
        $attributes = [];
        foreach ($line as $k => $v) {
            $attributes[$this->columns[$k]] = $v;
        }

        if ($attributes['city'] != ''  && !isset($this->cities[$attributes['city']])) {
            $city = City::whereIn('state_id', $this->states)->where('name', $attributes['city'])->first();
            if ($city) {
                $this->cities[$attributes['city']] = $city->id;
            }
        }

        if ($this->action != 'new') {
            $relation = Relation::where('email', $attributes['email'])
                ->whereBetween('created_at', [Str::replaceArray('?', $this->from, '? 00:00:00'), Str::replaceArray('?', $this->to, '? 23:59:59')])
                ->first();

            if ($relation && $attributes['city'] != '' && isset($this->cities[$attributes['city']])) {
                Address::where([
                    ['relation_id', '=', $relation->id],
                    ['city_id', '<>', $this->cities[$attributes['city']]]
                ])->update(['city_id' => $this->cities[$attributes['city']]]);
            }
        } else {
            if ($attributes['zipcode'] != '' && !isset($this->zipcodes[$attributes['zipcode']])) {
                $zipcode = Zipcode::where('zipcode', $attributes['zipcode'])->first();
                if ($zipcode) {
                    $this->zipcodes[$attributes['zipcode']] = $zipcode->id;
                }
            }

            $attributes['tenant_id'] = $this->tenant->id;
            $relation = $this->relationService->saveRelation($attributes);

            if ($relation) {
                $attributes['relation_id'] = $relation->id;
                $person = $this->savePerson($attributes);

                $attributes['account_holder'] = $person->full_name;
                $this->saveBankAccount($relation, $attributes);

                $attributes['address_type_id'] = 2;
                $this->saveAddress($attributes);

                $attributes['address_type_id'] = 3;
                $this->saveAddress($attributes);
            }
        }
    }

    private function saveAddress($attributes)
    {
        $attributes['country_id'] = $this->countryId;
        if ($attributes['city'] != '' && isset($this->cities[$attributes['city']])) {
            $attributes['city_id'] = $this->cities[$attributes['city']];
        }
        if (isset($this->zipcodes[$attributes['zipcode']])) {
            $attributes['zipcode_id'] = $this->zipcodes[$attributes['zipcode']];
        }
        $attributes['primary'] = 1;
        $this->addressService->saveAddress($attributes);
    }

    private function saveBankAccount($relation, $attributes)
    {
        $attributes['description'] = $attributes['account_holder'];
        $attributes['status'] = 1;
        $this->bankAccountService->saveBankAccount($relation, $attributes);
    }

    private function savePerson($attributes)
    {
        $attributes['primary'] = 1;
        $attributes['language'] = 'nl-NL';
        $attributes['person_type_id'] = 1;
        $attributes['status'] = 1;
        return $this->personService->savePerson($attributes);
    }
}
