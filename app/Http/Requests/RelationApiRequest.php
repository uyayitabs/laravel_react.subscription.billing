<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class RelationApiRequest extends BaseApiRequest
{
    protected $relation;

    public function authorize(): bool
    {
        $this->relation = $this->route("relation");
        return true;
    }


    public function rules(): array
    {
        $relation = $this->relation;
        $paymentConditionIdParam = request('payment_condition_id');
        $relationTypeIdParam = request('relation_type_id');
        $isBusiness = filter_var(request('is_business'), FILTER_VALIDATE_BOOLEAN);
        $isBusRequiredOrNullable = $isBusiness ? "required" : "nullable";
        return [
            'email' => ['email', $this->requiredOrNullable],
            'inv_output_type' => [$this->requiredOrNullable, Rule::in(['email', 'paper'])],
            'payment_condition_id' => [
                $this->requiredOrNullable,
                'integer',
                Rule::exists('payment_conditions', 'id')
                    ->where(
                        function ($query) use ($relation, $paymentConditionIdParam) {
                            $query->where([['tenant_id', $relation->tenant_id], ['id', $paymentConditionIdParam]]);
                        }
                    )
            ],
            'relation_type_id' => [
                $this->requiredOrNullable,
                'integer',
                Rule::exists('relation_types', 'id')
                    ->where(function ($query) use ($relationTypeIdParam) {
                        $query->where('id', $relationTypeIdParam);
                    })
            ],
            'status' => [$this->requiredOrNullable, 'integer', Rule::in([0, 1])],
            'company_name' => [$isBusRequiredOrNullable, 'string', 'max:191'],
            'kvk' => [$isBusRequiredOrNullable, 'string', 'max:45'],
            'vat_no' => [$isBusRequiredOrNullable, 'string', 'max:45'],
            'credit_limit' => 'nullable|numeric',
            'fax' => 'nullable|string|max:45',
            'phone' => 'nullable|numeric',
            'website' => 'nullable|string|max:191',
        ];
    }
    public function messages(): array
    {
        return [
            'email.required' => 'Email is required!',
            'email.email' => 'Please use a valid e-mail address!',
        ];
    }
}
