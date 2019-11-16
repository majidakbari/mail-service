<?php

/**
 * Class EmailFactory
 */
class EmailFactory
{
    /**
     * @var Faker
     */
    private $faker;

    /**
     * EmailFactory constructor.
     * @param Faker $faker
     */
    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    public function make()
    {
        return \App\ValueObjects\Email::fromArray();
    }
}

//
//class EmailFactory
//    /**
//     * @var Faker
//     */
//    private $faker;
//
//    /**
//     * EmailFactory constructor.
//     * @param Faker $faker
//     */
//    public function __construct(array $data)
//    {
//
//    }
//}
/** @var \Illuminate\Database\Eloquent\Factory $factory */

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

$factory->define(\App\ValueObjects\Email::class, function (Faker $faker) {
    return [
        "to" => "majidakbariiii@gmail.com",
        "subject" => "Sample email subject",
        "body" => "Hello, is it me you looking for? =>-D",
        "bodyType" => "text/markdown",
        "fromName" => "Majid Akbari",
        "fromAddress" => "majid@akbari.com",
        "cc" => [
            "majid.akbari@devolon.fi"
        ],
        "bcc" => [
            "majid.akbari@devolon.fi"
        ]
    ];
});


