<?php

namespace Tests\Tools\CustomFactories;

use App\ValueObjects\MailProvider;
use Faker\Generator as Faker;

/**
 * Because MailProvider value object is not an Eloquent model and also it has different constructor rather than
 * Eloquent models, I have to create a custom factory class which generates fake Email objects
 * Class EmailFactory
 */
class MailProviderFactory
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

    /**
     * @return Faker
     */
    public function getFaker(): Faker
    {
        return $this->faker;
    }

    /**
     * @return MailProvider
     */
    public function make(): MailProvider
    {
        $faker = $this->getFaker();

        $properties = [
            'id' => $this->faker->randomNumber(),
            'host' => $this->faker->domainName,
            'port' => $this->faker->randomElement([25, 465, 587]),
            'encryption' => $this->faker->randomElement(['', 'ssl', 'tls']),
            'username' => $this->faker->email,
            'password' => $this->faker->word,
            'streamOptions' => []
        ];

        return MailProvider::fromArray($properties);
    }
}
