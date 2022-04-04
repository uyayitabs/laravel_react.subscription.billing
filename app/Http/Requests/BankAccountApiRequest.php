<?php

namespace App\Http\Requests;

class BankAccountApiRequest extends BaseApiRequest
{
    protected $relation;

    public function authorize(): bool
    {
        $this->relation = $this->route("relation");
        return parent::authorize();
    }
    public function rules(): array
    {
        return [
            'relation_id' => 'nullable|integer|exists:relations,id',
            'status' => 'required|integer|min:0|max:1',
            'iban' => 'required|string',
            'dd_default' => 'required|boolean',
            'mndt_id' => 'required|string|max:50',
            'dt_of_sgntr' => 'required|date',
            'bank_name' => 'nullable|string|max:255'
        ];
    }
}
