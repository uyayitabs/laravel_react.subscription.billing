<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Relation;
use Faker\Generator as Faker;

$factory->define(Relation::class, function (Faker $faker) {
    return [
        'company_name' => $faker->company,
        'relation_type_id' => 1,
        'email' => $faker->email
    ];
});
