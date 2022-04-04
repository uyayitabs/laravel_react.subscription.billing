<?php

namespace App\Services;

use App\Models\BankAccount;
use Illuminate\Http\Resources\Json\JsonResource;
use Intervention\Validation\Rules\Iban;
use Intervention\Validation\Validator;
use Logging;
use App\Models\Relation;
use App\Http\Resources\BankAccountResource;

class BankAccountService
{
    public function list(Relation $relation)
    {
        $query = $relation->bankAccounts();
        if (request()->has('filter') && isset(request()->filter['keyword'])) {
            $query->search(request()->filter['keyword']);
        }
        return new BankAccountResource($query->get());
    }

    public function saveBankAccount(Relation $relation, array $data)
    {
        $attributes = filterArrayByKeys($data, BankAccount::$fields);
        $attributes['iban'] = strtoupper(preg_replace("/[!@#$%^&*(,. ]/", "", request('iban')));
        $attributes['mndt_id'] = $relation->customer_number . '-' .
            ($relation->bankAccounts()->count() + 1);

        $attributes['dt_of_sgntr'] = now();

        return $relation->bankAccounts()->create($attributes);
    }

    public function create(Relation $relation, array $data)
    {
        $data['mndt_id'] = $relation->customer_number . '-' .
            ($relation->bankAccounts()->count() + 1);

        if (
            !$data['dd_default'] && $relation->bankAccounts()->where('dd_default', 1)->count() == 0 &&
            strtolower($relation->paymentCondition->description) === 'automatisch incasso'
        ) {
            return [
                'success' => false,
                'errorMessage' => 'At least one DirectDebit must be enabled if Payment Condition on Relation is Automatische Incasso.',
                'data' => []
            ];
        }

        $validator = new Validator(new Iban());
        if (isset($data['iban']) && $validator->validate($data['iban'])) {
            $data['iban'] = str_replace(" ", "", $data['iban']);
        } else {
            return [
                'success' => false,
                'errorMessage' => 'IBAN is not valid.',
                'data' => []
            ];
        }

        Logging::information('Create Bank-Account', $data, 1, 1);
        $bankAccount = $relation->bankAccounts()->create($data);

        if ($bankAccount && $bankAccount->dd_default) {
            $relation->bankAccounts()
                ->where('dd_default', 1)
                ->whereNotIn('id', [$bankAccount->id])
                ->update(['dd_default' => 0]);
        }
        return [
            'success' => true,
            'message' => 'BankAccount created successfully.',
            'data' => new BankAccountResource($bankAccount)
        ];
    }

    /**
     * Update the specified bank account
     *
     * @param BankAccount $bankAccount
     * @param array $data
     * @return array
     */
    public function update(BankAccount $bankAccount, array $data): array
    {
        if (
            !$data['dd_default'] && $bankAccount->relation->bankAccounts()->where([['dd_default', 1],['id', '!=', $bankAccount->id]])->count() == 0 &&
            strtolower($bankAccount->relation->paymentCondition()->first()->description) === 'automatisch incasso'
        ) {
            return [
                'success' => false,
                'errorMessage' => 'At least one DirectDebit must be enabled if Payment Condition on Relation is Automatische Incasso.',
                'data' => []
            ];
        }

        $log['old_values'] = $bankAccount->getRawDBData();
        $validator = new Validator(new Iban());
        if (isset($data['iban']) && $validator->validate(str_replace(" ", "", $data['iban']))) {
            $data['iban'] = str_replace(" ", "", $data['iban']);
        } else {
            return [
                'success' => false,
                'errorMessage' => 'IBAN is not valid.',
                'data' => []
            ];
        }

        $bankAccount->update($data);
        if ($bankAccount && $bankAccount->dd_default) {
            $bankAccount->relation->bankAccounts()
                ->where('dd_default', 1)
                ->whereNotIn('id', [$bankAccount->id])
                ->update(['dd_default' => 0]);
        }

        $log['new_values'] = $bankAccount->getRawDBData();
        $log['changes'] = $bankAccount->getChanges();

        Logging::information('Update BankAccount', $log, 1);
        return [
            'success' => true,
            'message' => 'BankAccount updated successfully.',
            'data' => new BankAccountResource($bankAccount)
        ];
    }

    public function info($id)
    {
        return BankAccount::find($id);
    }

    public function exists($relation)
    {
        return $relation->bankAccounts()
            ->where('iban', $relation->iban)->exists();
    }

    public function nextMndtId(Relation $relation)
    {
        return $relation->customer_number . '-' .
            ($relation->bankAccounts()->count() + 1);
    }
}
