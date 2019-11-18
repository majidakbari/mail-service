<?php

namespace Tests\Feature;

use App\Entities\Log;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Class IndexEmailLogsTest
 * @package Tests\Feature
 */
class IndexEmailLogsTest extends TestCase
{
    use  DatabaseTransactions, WithFaker;

    /**
     * @return array
     */
    private function insertLogRecords(): array
    {
        $numberOfSuccessLogs = $this->faker->numberBetween(1, 10);
        $numberOfFailedLogs = $this->faker->numberBetween(1, 10);
        $email = $this->faker->email;

        $success = factory(Log::class, $numberOfSuccessLogs)->state('success')->create([
            'to' => $email
        ])->toArray();

        $failed = factory(Log::class, $numberOfFailedLogs)->state('failed')->create([
            'to' => $email
        ])->toArray();

        return [$numberOfSuccessLogs, $numberOfFailedLogs, $email, array_merge($success, $failed)];
    }

    /**
     * @test
     * @group FeatureIndexLogsTests
     */
    public function testSuccess(): void
    {
        list($numberOfSuccessLogs, $numberOfFailedLogs, $email, $expected) = $this->insertLogRecords();

        $response = $this->json('get', route('log.index', [], false), [
            'email' => $email,
            'perPage' => $numberOfFailedLogs + $numberOfSuccessLogs,
        ]);


        $response->assertStatus(Response::HTTP_OK)->assertJsonFragment([
                'data' => $expected,
                'total' => count($expected),
                'per_page' => $numberOfFailedLogs + $numberOfSuccessLogs,
                'current_page' => 1,
                'last_page' => 1
            ]
        )->assertJsonStructure([
            "current_page",
            "data" => [
                '*' => [
                    'to',
                    'body',
                    'email_metadata' => [
                        'subject',
                        'bodyType',
                        'fromAddress',
                        'fromName',
                        'attachment',
                    ],
                    'provider_name',
                    'failed_reason',
                    'sent_at',
                    'failed_at'
                ]
            ],
            "first_page_url",
            "from",
            "last_page",
            "last_page_url",
            "next_page_url",
            "path",
            "per_page",
            "prev_page_url",
            "to",
            "total"
        ]);
    }

    /**
     * @test
     * @expectedExceptionCode 406
     * @group FeatureIndexLogsTests
     * Wrong request headers (Accept header)
     */
    public function headerNotAcceptableTest(): void
    {
        $response = $this->get(route('log.index', [], false));

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }

    /**
     * @test
     * @expectedExceptionCode 422
     * @dataProvider validationErrorProvider
     * @group FeatureIndexLogsTests
     * @param mixed $email
     * @param mixed $fromDate
     * @param mixed $toDate
     */
    public function validationErrorTest($email, $fromDate, $toDate): void
    {
        $response = $this->json('get', route('log.index', [], false), [
            'email' => $email,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return array
     */
    public function validationErrorProvider(): array
    {
        //we do not have proper access to `$this` context in the providers
        //so we have to resolve dependencies directly from the service container
        $this->refreshApplication();
        /** @var Generator $faker */
        $faker = resolve(Generator::class);
        return [
            [null, null, null],
            [$faker->name, null, null],
            [$faker->email, $faker->dateTime, $faker->randomDigit],
            [$faker->email, $faker->randomDigit, $faker->dateTime],
            [$faker->email, $faker->date('m-d'), null],
            [$faker->email, null, $faker->date('Y/m')],
        ];
    }


    /**
     * @test
     * @group FeatureIndexLogsTests
     * @dataProvider dateTimeDataProvider
     * @param string $email
     * @param string $fromDate
     * @param string $toDate
     * @param array $excepted
     */
    public function testDateTimeFilters($email, $fromDate, $toDate, $excepted): void
    {
        $response = $this->json('get', route('log.index', [], false), [
            'email' => $email,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'perPage' => 100
        ]);

        $response->assertStatus(Response::HTTP_OK)->assertJsonFragment([
                'data' => $excepted,
                'total' => count($excepted),
                'current_page' => 1,
                'last_page' => 1
            ]
        );
    }

    /**
     * @return array
     * Creating log records with different times
     */
    public function dateTimeDataProvider(): array
    {
        //we do not have proper access to `$this` context in the providers
        //so we have to resolve dependencies directly from the service container
        $this->refreshApplication();
        /** @var Generator $faker */
        $faker = resolve(Generator::class);
        $email = $faker->email;
        //Generating different times
        $secondDate = $faker->date('Y-m-d');
        $firstDate = $faker->date('Y-m-d', $secondDate);

        $rightAtFirstDate = $this->generateRecordsForDateTimeFilterTesting($email, $firstDate, $faker);
        $rightAtSecondDate = $this->generateRecordsForDateTimeFilterTesting($email, $secondDate, $faker);
        $afterSecondDate = $this->generateRecordsForDateTimeFilterTesting(
            $email,
            Carbon::createFromFormat('Y-m-d', $secondDate)
                ->addWeeks($faker->numberBetween(1, 10))
                ->toDateString(),
            $faker
        );
        $beforeFirstDate = $this->generateRecordsForDateTimeFilterTesting(
            $email,
            Carbon::createFromFormat('Y-m-d', $firstDate)
                ->subWeeks($faker->numberBetween(1, 10))
                ->toDateString(),
            $faker
        );

        $betweenFistAndSecondDate = $this->generateRecordsForDateTimeFilterTesting(
            $email,
            $faker->dateTimeBetween($firstDate, $secondDate)->format('Y-m-d'),
            $faker
        );

        return [
            [
                $email,
                null,
                null,
                array_merge($beforeFirstDate, $rightAtFirstDate, $betweenFistAndSecondDate, $rightAtSecondDate,
                    $afterSecondDate)
            ],
            [
                $email,
                $firstDate,
                $secondDate,
                array_merge($rightAtFirstDate, $betweenFistAndSecondDate, $rightAtSecondDate)
            ],
            [$email, $secondDate, $firstDate, []],
            [
                $email,
                $firstDate,
                null,
                array_merge($rightAtFirstDate, $betweenFistAndSecondDate, $rightAtSecondDate, $afterSecondDate)
            ],
            [
                $email,
                null,
                $secondDate,
                array_merge($beforeFirstDate, $rightAtFirstDate, $betweenFistAndSecondDate, $rightAtSecondDate)
            ]
        ];
    }


    /**
     * @param string $email
     * @param string $date
     * @param Generator $faker
     * @return array
     */
    private function generateRecordsForDateTimeFilterTesting(string $email, string $date, Generator $faker): array
    {
        $success = factory(Log::class, $faker->randomDigit)->state('success')->create(
            [
                'to' => $email,
                'sent_at' => $date
            ]
        )->toArray();
        $failure = factory(Log::class, $faker->randomDigit)->state('failed')->create(
            [
                'to' => $email,
                'failed_at' => $date
            ]
        )->toArray();

        return array_merge($success, $failure);
    }

}
