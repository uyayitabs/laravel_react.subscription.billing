<?php

namespace App\Http\Requests;

class RelationCsApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $ibanValidationRules  = [
            'required',
            'string'
        ];

        return [
            // relation
            'title' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'birthdate' => 'required|date|before:today',

            // address
            "street1" => "required|string|max:95",
            "house_number" => "required|string|max:10",
            "house_number_suffix" => "nullable|string|max:35",
            "room" => "nullable|string|max:35",
            "city_id" => 'required|numeric',
            'country_id' => 'required|numeric',

            'phone' => 'required|numeric',
            'mobile' => 'nullable|numeric',
            'email' => 'required|unique:relations,email|email',

            'iban' => $ibanValidationRules,
            'account_holder' => 'required',

            'plan' => 'required|numeric',
            'contract_period' => 'required|numeric',
            'subscription_start' => 'nullable|date',
            'billing_start' => 'nullable|date',
            'network_operator' => 'required|exists:network_operators,id',
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Please use a valid e-mail address!',
            'email.unique' => 'This email has already been taken!',
            'city_id.required' => 'The City is required'
        ];
    }
}
