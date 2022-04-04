<?php

namespace App\Http\Requests;

class CountryApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'numeric' => $this->requiredOrNullable . '|string|min:1|max:3',
            'alpha2' => $this->requiredOrNullable . '|string|min:1|max:2',
            'alpha3' => $this->requiredOrNullable . '|string|min:1|max:3',
            'name' => $this->requiredOrNullable . '|string',
            'official_name' => $this->requiredOrNullable . '|string',
            'sovereignty' => $this->requiredOrNullable . '|string|min:1|max:3',
        ];
    }

    public function messages(): array
    {
        return [
            'numeric.required' => 'Country numeric code is required!',
            'alpha2.required' => 'Alpha2 is required!',
            'alpha3.required' => 'Alpha3 is required!',
            'name.required' => 'Country name is required!',
            'official_name.required' => 'Official country name is required!',
            'sovereignty.required' => 'Sovereignty is required!',
        ];
    }
}
