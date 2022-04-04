<?php

namespace App\Http\Requests;

class AddressApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $addressTypesRequired =  $this->isPost && !$this->isPut ? "required" : "nullable";
        $addressTypeRequired =  $this->isPost && !$this->isPut ? "nullable" : "required";
        return [
            "relation_id" => $this->requiredOrNullable . "|integer|exists:relations,id",
            "address_type_id" => "{$addressTypeRequired}|integer|exists:address_types,id",
            "address_types" => "{$addressTypesRequired}|array|exists:address_types,id",
            "country_id" => $this->requiredOrNullable . "|integer|exists:countries,id",
            "street1" => "nullable|string|max:95",
            "street2" => "nullable|string|max:95",
            "house_number" => "nullable|string|max:10",
            "house_number_suffix" => "nullable|string|max:10",
            "room" => "nullable|string|max:35",
        ];
    }

    public function messages(): array
    {
        return [
            'address_types.required' => 'Select at least 1 types!',
        ];
    }
}
