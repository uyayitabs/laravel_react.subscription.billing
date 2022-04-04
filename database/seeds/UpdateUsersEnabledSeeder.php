<?php

use Illuminate\Database\Seeder;
use App\Tenant;
use App\Relation;
use App\Person;

class UpdateUsersEnabledSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tenants = Tenant::whereIn('id', [1, 10])->get();

        foreach ($tenants as $tenant) {
            $relations = $tenant->relations;
            foreach ($relations as $relation) {
                $relationsPersons = $relation->relationsPersons;
                foreach ($relationsPersons as $relationsPerson) {
                    $person = $relationsPerson->person;
                    $user = $person->user;
                    if ($user) {
                        $user->enabled = true;
                        $user->save();
                    }
                }
            }
        }
    }
}
