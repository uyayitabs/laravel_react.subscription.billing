<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Address::class, function (Faker $faker) {
    return [
        'address_type_id' => 3,
        'street1' => $faker->streetName,
        'house_number' => $faker->numberBetween(1, 255),
        'zipcode' => $faker->postcode,
        'country_id' => 155
    ];
});
