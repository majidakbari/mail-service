<?php

namespace Tests\Tools\CustomFactories;

use App\ValueObjects\Email;
use Faker\Generator as Faker;

/**
 * Because Email value object is not an Eloquent model and also it has different constructor rather than
 * Eloquent models, I have to create a custom factory class which generates fake Email objects
 * Class EmailFactory
 */
class EmailFactory
{
    const EMAIL_WITH_FILE_ATTACHED = 'file-attached-email';

    const EMAIL_WITH_HTML_BODY = 'html-email';

    const EMAIL_WITH_MARKDOWN_BODY = 'markdown-email';

    const EMAIL_WITH_TEXT_BODY = 'text-email';

    const EMAIL_WITHOUT_OPTIONAL_PROPERTIES = 'without-optional-properties-email';

    const EMAIL_UNCOMPLETED_BODY = 'uncompleted-body-email';

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
     * @param string $emailType
     * @return Email
     */
    public function make($emailType): Email
    {
        $faker = $this->getFaker();

        $commonProperties = [
            "to" => $faker->email,
            "subject" => $faker->sentence(),
            "fromName" => $faker->name,
            "fromAddress" =>$faker->email,
        ];

        switch ($emailType) {
            case self::EMAIL_WITH_HTML_BODY:
                $otherProperties = [
                    "bodyType" => Email::BODY_TYPE_HTML,
                    "body" => $this->generateEmailBody(Email::BODY_TYPE_HTML),
                    "cc" => $this->getRandomArrayOfEmails(),
                    "bcc" => $this->getRandomArrayOfEmails()
                ];
                break;
            case self::EMAIL_WITH_MARKDOWN_BODY:
                $otherProperties = [
                    "bodyType" => Email::BODY_TYPE_MARKDOWN,
                    "body" => $this->generateEmailBody(Email::BODY_TYPE_MARKDOWN),
                    "cc" => $this->getRandomArrayOfEmails(),
                    "bcc" => $this->getRandomArrayOfEmails()
                ];
                break;
            case self::EMAIL_WITH_TEXT_BODY:
                $otherProperties = [
                    "bodyType" => Email::BODY_TYPE_TEXT,
                    "body" => $this->generateEmailBody(Email::BODY_TYPE_TEXT),
                    "cc" => $this->getRandomArrayOfEmails(),
                    "bcc" => $this->getRandomArrayOfEmails()
                ];
                break;
            case self::EMAIL_WITHOUT_OPTIONAL_PROPERTIES:
                $otherProperties = [
                    "bodyType" => Email::BODY_TYPE_TEXT,
                    "body" => $this->generateEmailBody(Email::BODY_TYPE_TEXT),
                ];
                break;
            case self::EMAIL_WITH_FILE_ATTACHED:
                $otherProperties = [
                    "bodyType" => Email::BODY_TYPE_HTML,
                    "body" => $this->generateEmailBody(Email::BODY_TYPE_HTML),
                    "cc" => $this->getRandomArrayOfEmails(),
                    "bcc" => $this->getRandomArrayOfEmails(),
                    "attachFileCode" => base64_encode($faker->sentence),
                    "attachFileName" => $faker->name . '.txt'
                ];
                break;

            case self::EMAIL_UNCOMPLETED_BODY;
                $otherProperties = [];
                break;
            default:
                throw new InvalidArgumentException();
        }


        return Email::fromArray(array_merge($commonProperties, $otherProperties));
    }

    /**
     * @param $type
     * @param $numberOfInstances
     * @return array
     */
    public function makeMany($type, $numberOfInstances = null): array
    {
        if (is_null($numberOfInstances)) {
            $numberOfInstances = $this->faker->numberBetween(1, 10) ;
        }

        $result = [];
        for ($i = 1; $i <= $numberOfInstances; $i++) {
            $result[] = $this->make($type);
        }

        return $result;
    }

    /**
     * @param string $bodyType
     * @return string
     */
    private function generateEmailBody(string $bodyType): string
    {
        switch ($bodyType) {
            case Email::BODY_TYPE_TEXT:
            case Email::BODY_TYPE_MARKDOWN:
                return $this->getFaker()->sentence;
                break;
            case Email::BODY_TYPE_HTML:
                return trim($this->getFaker()->randomHtml());
                break;
            default:
                throw new \InvalidArgumentException();
        }
    }

    /**
     * @return array
     */
    private function getRandomArrayOfEmails(): array
    {
        $faker = $this->getFaker();
        $numberOfEmails = $faker->numberBetween(0, $faker->randomDigit);
        $result = [];
        for ($i = 0; $i <= $numberOfEmails; $i++) {
            $result[] = $faker->email;
        }

        return $result;
    }
}
