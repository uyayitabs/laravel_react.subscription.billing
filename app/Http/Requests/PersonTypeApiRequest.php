<?php

namespace App\Http\Requests;

class PersonTypeApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'type' => $this->requiredOrNullable . '|min:1|max:255||unique:person_types,type',
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
            'type.required' => 'Type is required!',
        ];
    }
}
