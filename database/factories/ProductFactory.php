<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Product::class, function (Faker $faker) {
    return [
        'price' => $faker->randomFloat(4, 0, 100),
        'product_type_id' => $faker->numberBetween(1, 6),
        'description' => $faker->sentence(4),
    ];
});

$factory->define(\App\Models\TenantProduct::class, function (Faker $faker) {
   return [
       'status' => 1,
       'vat_code_id' => function () { return factory(\App\Models\VatCode::class)->create()->id; }
   ];
});
