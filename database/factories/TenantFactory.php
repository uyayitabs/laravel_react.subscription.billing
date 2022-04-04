<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(\App\Tenant::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'invoice_start_calculation' => '2019-01-01'
    ];
});
