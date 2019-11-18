<?php

namespace Tests\Feature;

use App\Entities\Log;
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
     * @return array
     */
    private function insertLogRecords(): array
    {
        $numberOfSuccessLogs = $this->faker->randomDigit;
        $numberOfFailedLogs = $this->faker->randomDigit;
        $email = $this->faker->email;

        factory(Log::class, $numberOfSuccessLogs)->state('success')->create([
            'to' => $email
        ]);
        factory(Log::class, $numberOfFailedLogs)->state('failed')->create([
            'to' => $email
        ]);

        return [$numberOfSuccessLogs, $numberOfFailedLogs, $email];
    }

    /**
     * @test
     * @group FeatureIndexLogs
     */
    public function testSuccess(): void
    {
        list($numberOfSuccessLogs, $numberOfFailedLogs, $email) = $this->insertLogRecords();

        $response = $this->json('get', route('log.index', [], false), [
            'email' => $email,
            'perPage' => $this->faker->randomDigit,
            'page' => $this->faker->randomDigit
        ]);

        $response->assertStatus(Response::HTTP_OK)->assertJson([
            'total' => $numberOfFailedLogs + $numberOfSuccessLogs
        ])->assertJsonStructure([
            "current_page",
            "data",
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
     * @group FeatureIndexLogs
     */
    public function testPagination()
    {
        list($numberOfSuccessLogs, $numberOfFailedLogs, $email) = $this->insertLogRecords();

        $response = $this->json('get', route('log.index', [], false), [
            'email' => $email,
            'perPage' => $numberOfFailedLogs + $numberOfSuccessLogs,
            'page' => 1
        ]);

        $expected = Log::query()->get();

        $response->assertStatus(Response::HTTP_OK)->assertJsonFragment([
                'data' => $expected->toArray(),
                'total' => $expected->count(),
                'per_page' => $numberOfFailedLogs + $numberOfSuccessLogs,
                'current_page' => 1,
                'last_page' => 1
            ]
        );
    }

    /**
     * @test
     * @group FeatureIndexLogs
     */
    public function testDateTimeFilters()
    {
        $email = $this->faker->email;

        $result = factory(Log::class, $this->faker->randomNumber(2))->create([
            'failed_at' => $this->faker->dateTimeBetween('-3years', '-2years'),
            'sent_at' => $this->faker->dateTimeBetween('-4years', '-3years'),
            'to' => $email
        ]);

        $response = $this->json('get', route('log.index', [], false), [
            'email' => $email,
            'fromDate' => $this->faker->dateTimeBetween('-1year', 'now')->format('Y-m-d'),
            'toDate' => now()->toDateString()
        ]);

        $response->assertStatus(Response::HTTP_OK)->assertJsonFragment([
                'data' => [],
                'total' => 0,
                'current_page' => 1,
                'last_page' => 1
            ]
        );
    }

    /**
     * @test
     * @expectedExceptionCode 422
     * @dataProvider validationErrorProvider
     * @group FeatureIndexLogs
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
     * @test
     * @expectedExceptionCode 406
     * @group FeatureIndexLogs
     * Wrong request headers (Accept header)
     */
    public function headerNotAcceptableTest(): void
    {
        $response = $this->get(route('log.index', [], false));

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }
}
