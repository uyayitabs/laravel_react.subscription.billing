<?php

namespace App\Http\Requests;
class ContractPeriodApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'period' => 'required|string',
            'net_days' => 'required|integer',
        ];
    }
}
