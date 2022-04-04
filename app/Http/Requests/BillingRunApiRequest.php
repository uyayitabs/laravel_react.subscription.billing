<?php

namespace App\Http\Requests;

use App\Models\StatusType;
use Illuminate\Validation\Rule;

class BillingRunApiRequest extends BaseApiRequest
{
    public function rules(): array
    {
        $statusIdParam = request('status_id');
        $statusIdRequiredNullable = $this->isPost ? "required" : "nullable";

        $billingRunStatusType = StatusType::where('type', 'billing_run')->first();
        $statusTypeId = $billingRunStatusType->id ?? null;

        if (empty($statusIdParam) && !empty($this->status)) {
            $statusIdParam = $this->status->id;
        }

        return [
            'tenant_id' => 'nullable|integer|exists:tenants,id',
            'status_id' => [
                $statusIdRequiredNullable,
                'integer',
                Rule::exists("statuses", "id")
                    ->where(
                        function ($query) use ($statusIdParam, $statusTypeId) {
                            $query->where([
                                ["id", "=", $statusIdParam],
                                ["status_type_id", "=", $statusTypeId]
                            ]);
                        }
                    )
            ],
        ];
    }
}
