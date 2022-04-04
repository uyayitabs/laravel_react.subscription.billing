<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\VatCode::class, function (Faker $faker) {
    $vatPercentage = $faker->randomFloat(3, 0, 1);
    return [
        'vat_percentage' => $vatPercentage,
        'description' => 'BTW '.$vatPercentage,
        'active_from' => \Carbon\Carbon::now()->subMonth(12)
    ];
});
