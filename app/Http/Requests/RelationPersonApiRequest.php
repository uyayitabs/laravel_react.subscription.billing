<?php

namespace App\Http\Requests;

class RelationPersonApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'person_type_id' => $this->requiredOrNullable . '|integer|exists:person_types,id',
            'status' => $this->requiredOrNullable . '|integer',
            'first_name' => $this->requiredOrNullable . '|string|max:191',
            'last_name' => $this->requiredOrNullable . '|string|max:191',
            'middle_name' => 'nullable|string|max:191',
            'birthdate' => 'nullable|date',
            'email' => 'nullable|email'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status is required!',
            'first_name.required' => 'First name is required!',
            'last_name.required' => 'Last name is required!',
            'mobile.required' => 'Mobile number is required!',
        ];
    }
}
