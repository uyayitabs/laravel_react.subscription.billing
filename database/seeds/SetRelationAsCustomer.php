<?php

use Illuminate\Database\Seeder;
use App\Relation;

class SetRelationAsCustomer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Relation::where(function ($query) {
            $query->whereNotIn('tenant_id', [1, 2, 3, 4]);
            $query->whereNull('relation_type_id');
        })->update(['relation_type_id' => 1]);
    }
}
