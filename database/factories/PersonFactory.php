<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Models\Person;
use Faker\Generator as Faker;

$factory->define(Person::class, function (Faker $faker) {
    return [
        'person_type_id' => 1,
        'gender' => 'Male',
        'status' => 'active',
        'title' => 'Mr.',
        'first_name' => $faker->firstNameMale,
        'middle_name' => $faker->firstNameMale,
        'last_name' => $faker->firstNameMale,
        'email' => $faker->email,
        'phone' => '',
        'mobile' => '',
        'language' => '',
        'linkedin' => '',
        'facebook' => ''
    ];
});

$factory->define(\App\Models\RelationsPerson::class, function (Faker $faker) {
    return [
        'status' => 3
    ];
});
