<?php

namespace App\Http\Requests;

class PersonApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'relation_id' => $this->requiredOrNullable . "|integer|exists:relations,id",
            'first_name' => $this->requiredOrNullable . '|string|max:191',
            'last_name' => $this->requiredOrNullable . '|string|max:191',
            'middle_name' => 'nullable|string|max:191',
            'birthdate' => 'nullable|date',
            'email' => 'nullable|email'
        ];
    }

    public function messages(): array
    {
        return [
            'relation_id.required' => 'Relation ID is required.',
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'mobile.required' => 'Mobile number is required.',
        ];
    }
}
