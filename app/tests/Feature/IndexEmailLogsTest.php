<?php

namespace Tests\Feature;

use App\Entities\Log;
use Carbon\Carbon;
use DateTime;
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
     * @var string
     */
    protected $email;

    /**
     * @var DateTime
     */
    protected $toDate;

    /**
     * @var DateTime
     */
    protected $fromDate;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->email = $this->faker->email;
        $this->toDate = $this->faker->date('Y-m-d');
        $this->fromDate = $this->faker->date('Y-m-d', $this->toDate);
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

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE)->assertJson([
            'error' => 'InvalidAcceptHeaderException',
            'message' => trans('app.InvalidAcceptHeaderException')
        ]);
    }

    /**
     * @test
     * @expectedExceptionCode 422
     * @dataProvider validationErrorProvider
     * @group FeatureIndexLogsTests
     * @param string $email
     * @param string|null $fromDate
     * @param string|null $toDate
     * @param array $expectedStructure
     */
    public function validationErrorTest($email, $fromDate, $toDate, $expectedStructure): void
    {
        $response = $this->json('get', route('log.index', [], false), [
            'email' => $email,
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure($expectedStructure);
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
            [
                null,
                null,
                null,
                [
                    'message' => ['email']
                ]
            ],
            [
                $faker->name,
                null,
                null,
                [
                    'message' => ['email']
                ]
            ],
            [
                $faker->email,
                $faker->dateTime,
                $faker->randomDigit,
                [
                    'message' => ['toDate']
                ]
            ],
            [
                $faker->email,
                $faker->randomDigit,
                $faker->dateTime,
                [
                    'message' => ['fromDate']
                ]
            ],
            [
                $faker->email,
                $faker->date('m-d'),
                null,
                [
                    'message' => ['fromDate']
                ]
            ],
            [
                $faker->email,
                null,
                $faker->date('Y/m'),
                [
                    'message' => ['toDate']
                ]
            ],
        ];
    }


    /**
     * @group FeatureIndexLogsTests
     * @param string $fromDate
     * @param string $toDate
     * @param array $excepted
     */
    public function runDateTest($fromDate, $toDate, $excepted): void
    {
        $response = $this->json('get', route('log.index', [], false), [
            'email' => $this->email,
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
     * @test
     * @group FeatureIndexLogsTests
     * Test when both fromDate and toDate properties are null, all records should be returned in the API response
     */
    public function firstDateTest(): void
    {
        list($rightAtFromDate, $rightAtToDate, $afterToDate, $beforeFromDate, $betweenFromDateAndToDate) = $this->initDateTimeTest();

        $this->runDateTest(
            null,
            null,
            array_merge($rightAtFromDate, $rightAtToDate, $afterToDate, $beforeFromDate, $betweenFromDateAndToDate)
        );
    }

    /**
     * @test
     * @group FeatureIndexLogsTests
     *  Test when both fromDate and toDate properties exist, all records with dateTime between these two
     *  should be returned in the API response
     */
    public function secondDateTest(): void
    {
        list($rightAtFromDate, $rightAtToDate, , , $betweenFromDateAndToDate) = $this->initDateTimeTest();

        $this->runDateTest(
            $this->fromDate,
            $this->toDate,
            array_merge($rightAtFromDate, $betweenFromDateAndToDate, $rightAtToDate)
        );
    }

    /**
     * @test
     * @group FeatureIndexLogsTests
     * Test when both fromDate and toDate properties exist, but fromDate is ahead of toDate! so an empty array should be
     * returned in the API response
     */
    public function thirdDateTest(): void
    {
        $this->initDateTimeTest();

        $this->runDateTest(
            $this->toDate,
            $this->fromDate,
            []
        );
    }

    /**
     * @test
     * @group FeatureIndexLogsTests
     * Test when only fromDate filter exists, all the records with datetime after this time, should be returned in the
     * API response
     */
    public function fourthDateTest(): void
    {
        list($rightAtFromDate, $rightAtToDate, $afterToDate, , $betweenFromDateAndToDate) = $this->initDateTimeTest();

        $this->runDateTest(
            $this->fromDate,
            null,
            array_merge($rightAtFromDate, $betweenFromDateAndToDate, $rightAtToDate, $afterToDate)
        );
    }

    /**
     * @test
     * @group FeatureIndexLogsTests
     * Test when only toDate filter exists, all the records with datetime before this time, should be returned in the
     * API response
     */
    public function fifthDateTest(): void
    {
        list($rightAtFromDate, $rightAtToDate, , $beforeFromDate, $betweenFromDateAndToDate) = $this->initDateTimeTest();

        $this->runDateTest(
            null,
            $this->toDate,
            array_merge($beforeFromDate, $rightAtFromDate, $betweenFromDateAndToDate, $rightAtToDate)
        );
    }

    /**
     * @return array
     * Generating log records for different test cases
     */
    protected function initDateTimeTest(): array
    {
        $rightAtFromDate = $this->generateRecordsForDateTimeFilterTesting($this->fromDate);
        $rightAtToDate = $this->generateRecordsForDateTimeFilterTesting($this->toDate);
        $afterToDate = $this->generateRecordsForDateTimeFilterTesting(Carbon::createFromFormat('Y-m-d', $this->toDate)
            ->addWeeks($this->faker->numberBetween(1, 10))
            ->toDateString()
        );
        $beforeFromDate = $this->generateRecordsForDateTimeFilterTesting(Carbon::createFromFormat('Y-m-d',
            $this->fromDate)
            ->subWeeks($this->faker->numberBetween(1, 10))
            ->toDateString()
        );
        $betweenFromDateAndToDate = $this->generateRecordsForDateTimeFilterTesting(
            $this->faker->dateTimeBetween($this->fromDate, $this->toDate)->format('Y-m-d')
        );

        return [$rightAtFromDate, $rightAtToDate, $afterToDate, $beforeFromDate, $betweenFromDateAndToDate];
    }

    /**
     * @param string $date
     * @return array
     */
    protected function generateRecordsForDateTimeFilterTesting(string $date): array
    {
        $success = factory(Log::class, $this->faker->randomDigit)->state('success')->create(
            [
                'to' => $this->email,
                'sent_at' => $date
            ]
        )->toArray();

        $failure = factory(Log::class, $this->faker->randomDigit)->state('failed')->create(
            [
                'to' => $this->email,
                'failed_at' => $date
            ]
        )->toArray();

        return array_merge($success, $failure);
    }

    /**
     * @return array
     */
    protected function insertLogRecords(): array
    {
        $numberOfSuccessLogs = $this->faker->numberBetween(1, 10);
        $numberOfFailedLogs = $this->faker->numberBetween(1, 10);

        $success = factory(Log::class, $numberOfSuccessLogs)->state('success')->create([
            'to' => $this->email
        ])->toArray();

        $failed = factory(Log::class, $numberOfFailedLogs)->state('failed')->create([
            'to' => $this->email
        ])->toArray();

        return [$numberOfSuccessLogs, $numberOfFailedLogs, array_merge($success, $failed)];
    }


    /**
     * @test
     * @group FeatureIndexLogsTests
     */
    public function testSuccess(): void
    {
        list($numberOfSuccessLogs, $numberOfFailedLogs, $expected) = $this->insertLogRecords();

        $response = $this->json('get', route('log.index', [], false), [
            'email' => $this->email,
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
}
