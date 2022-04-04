<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\BillingRun::class, function (Faker $faker) {
    return [
        'status_id' => 0,
        'date' => \Carbon\Carbon::now()
    ];
});
