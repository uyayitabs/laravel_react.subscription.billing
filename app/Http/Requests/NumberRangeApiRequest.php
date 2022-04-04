<?php

namespace App\Http\Requests;

use App\Models\NumberRange;
use Illuminate\Validation\Rule;

class NumberRangeApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            "tenant_id" => $this->requiredOrNullable . "|integer|exists:tenants,id",
            "type" => [
                $this->requiredOrNullable,
                "string",
                Rule::in(NumberRange::$constTypes)
            ],
            "description" =>  $this->requiredOrNullable . "|string|min:1|max:190",
            "start" =>$this->requiredOrNullable . "|integer|min:0|max:2147483647",
            "end" => $this->requiredOrNullable . "|integer|min:0|max:2147483647",
            "format" => [
                $this->requiredOrNullable,
                "string",
                "regex:/\{\:\d{1,}number\}/"
            ],
            "randomized" => "nullable|boolean",
            "current" => "nullable|integer|min:0|max:2147483647"
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
            "format.regex" => "The format is invalid.",
        ];
    }
}
