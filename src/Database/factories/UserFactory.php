<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'phone' => $faker->unique()->safeEmail,
        'email' => $faker->unique()->phoneNumber,
        'api_token' => $faker->unique()->str_rand(30),
        'password' => $password ?: $password = 123,
        'remember_token' => str_random(10),
    ];
});
