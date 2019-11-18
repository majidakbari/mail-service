<?php

/** @var Factory $factory */

use App\Entities\Log;
use Illuminate\Database\Eloquent\Factory;
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

$factory->define(Log::class, function (Faker $faker) {
    return [
        'to' => $faker->email,
        'body' => trim($faker->randomHtml()),
        'email_metadata' => [
            'subject' => $faker->sentence,
            'bodyType' => \App\ValueObjects\Email::BODY_TYPE_HTML,
            'fromAddress' => $faker->email,
            'fromName' => $faker->name,
            'attachment' => false,
        ],
        'provider' => $faker->randomDigit,
        'failed_reason' => $faker->sentence,
        'sent_at' => $faker->dateTime,
        'failed_at' => $faker->dateTime
    ];
});

$factory->state(Log::class, 'success', function (Faker $faker) {
    return [
        'failed_reason' => null,
        'sent_at' => $faker->dateTimeBetween("-1year"),
        'failed_at' => null,
    ];
});

$factory->state(Log::class, 'failed', function (Faker $faker) {
    return [
        'failed_reason' => $faker->sentence,
        'sent_at' => null,
        'failed_at' => $faker->dateTimeBetween("-1year")
    ];
});
