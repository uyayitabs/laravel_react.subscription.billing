<?php

use Illuminate\Database\Seeder;
use App\Relation;
use App\BankAccount;
use App\Services\BankAccountService;

class RelationBankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $relations = Relation::get();
        $bankAccountService = new BankAccountService();

        foreach ($relations as $relation) {
            $exists = $bankAccountService->exists($relation);
            if ($exists || !$relation->iban) continue;
            $data = [
                'status' => 1,
                'iban' => $relation->iban,
                'bic' => $relation->bic,
                'dd_default' => 1,
                'dt_of_sgntr' => now()
            ];
            $bankAccountService->create($relation, $data);
        }
    }
}
