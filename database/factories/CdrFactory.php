<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Brand;
use Faker\Generator as Faker;

$factory->define(\App\CdrUsageCost::class, function (Faker $faker) {
    return [
        'unique_id' => $faker->numberBetween(50000, 250000),
        'customer_number' => 'FP123456',
        'channel_id' => $faker->numberBetween(250000, 1000000),
        'sender' => $faker->e164PhoneNumber,
        'recipient' => $faker->e164PhoneNumber,
        'duration' => $faker->numberBetween(1, 300),
        'platform' => 'Voice Connect',
        'total_cost' => $faker->randomFloat(5, 0, 1),
        'start_cost' => 0.10,
        'direction' => $faker->boolean ? 'ingaand' : 'uitgaand',
        'extension' => 'voice'
    ];
});
