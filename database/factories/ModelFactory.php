<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Feed::class, function (Faker\Generator $faker) {
    return [
        'service' => $faker->randomElement(['twitter', 'facebook', 'instagram']),
        'post_id' => $faker->randomNumber($nbDigits = 9),
        'image_url' => $faker->imageUrl($width = 640, $height = 480),
        'content'  => $faker->sentence(mt_rand(5,20)),
        'post_url' => $faker->url(),
        'published_at' => $faker->dateTimeBetween('-1 month', '+3 days'),
    ];
});




