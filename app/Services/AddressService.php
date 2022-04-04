<?php

namespace App\Services;

use App\Models\Address;
use App\Filters\RelationAddressTypeSortFilter;
use App\Filters\AddressCountrySortFilter;
use App\Filters\AddressCitySortFilter;
use App\Models\Subscription;
use Illuminate\Database\Query\Builder;
use Logging;
use App\Http\Resources\AddressResource;
use App\Http\Resources\BaseResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class AddressService
{
    /**
     * @param int|null $relationId
     * @return QueryBuilder
     */
    public function list(?int $relationId): QueryBuilder
    {
        $query = QueryBuilder::for(Address::class)
            ->allowedFields(Address::$fields)
            ->allowedSorts(Address::$fields)
            ->allowedFilters(AllowedFilter::exact('address_type_id'));

        $searchFilter = request()->query("filter", []);
        if (array_key_exists('keyword', $searchFilter)) {
            $value = $searchFilter['keyword'];
            $query->search($value);
        }

        if ($relationId) {
            $query->where('relation_id', $relationId);
        }

        return $query;
    }

    public function saveAddress(array $data)
    {
        $attributes = filterArrayByKeys($data, Address::$fields);
        Logging::information('Create Address', $attributes, 1, 1);
        $address = new Address($attributes);
        if ($address->save()) {
            return $address;
        }
        return false;
    }

    public function create(array $data)
    {
        $attributes = filterArrayByKeys($data, Address::$fields);
        $address_types = request('address_types');

        $addresses = collect($address_types)->map(function ($address_type_id) use ($attributes) {
            $attributes['address_type_id'] = $address_type_id;
            return $this->saveAddress($attributes);
        });

        foreach ($addresses as $address) {
            if ((key_exists('primary', $data) && $data['primary']) || $address->isOnlyOfType) {
                $this->setPrimaryAddress($address);
            }
        }

        return $addresses;
    }

    public function show($id)
    {
        return new AddressResource(
            Address::find($id)
        );
    }

    public function update(array $data, Address $address)
    {
        $attributes = filterArrayByKeys($data, Address::$fields);

        //Cannot alter Address information
        if (
            $this->containsNewUpdates($address, $data) && Subscription::where('status', '>', 0)
                ->where('billing_address', $address->id)
                ->orWhere('provisioning_address', $address->id)
                ->exists()
        ) {
            return [
                'success' => false,
                'message' => 'Can not update addresses that are linked to an Ongoing or Terminated Subscription.'
            ];
        }

        $log['old_values'] = $address->getRawDBData();
        $address->update($attributes);
        $log['new_values'] = $address->getRawDBData();
        $log['changes'] = $address->getChanges();

        //Can alter Address primary
        if (key_exists('primary', $data) && $data['primary']) {
            $this->setPrimaryAddress($address);
        }

        Logging::information('Update Address', $log, 1, 1);
        return [
            'success' => true,
            'data' => $this->show($address->id),
            'message' => 'Address updated successfully.'
        ];
    }

    public function delete(Address $address)
    {
        Logging::information('Delete Address', $address, 1, 1);
        return $address->delete();
    }

    private function setPrimaryAddress($newPrimaryAddress)
    {
        $addresses = Address::where([
            ['primary', 1],
            ['relation_id', $newPrimaryAddress->relation_id],
            ['address_type_id', $newPrimaryAddress->address_type_id]])->get();
        foreach ($addresses as $address) {
            if ($address->id != $newPrimaryAddress->id) {
                $address->primary = false;
                $address->save();
            }
        }
    }

    private function containsNewUpdates(Address $address, array $data)
    {
        $acceptableChanges = ['primary'];
        $addressData = $address->toArray();
        foreach ($data as $key => $value) {
            if (in_array($key, $acceptableChanges)) {
                continue;
            }
            if (array_key_exists($key, $addressData) && $addressData[$key] != $value) {
                return true;
            }
        }

        return false;
    }
}
