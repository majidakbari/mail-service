<?php

namespace Tests\Unit;

use App\Entities\Log;
use App\Interfaces\LogRepositoryInterface;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class LogRepositoryTest
 * @package Tests\Unit
 */
class LogRepositoryTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /**
     * @var LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * resolve dependencies
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->logRepository = resolve(LogRepositoryInterface::class);
    }

    /**
     * @test
     * @group LogRepositoryUnitTests
     *
     * Important:
     * Because `email_metadata` property is stored as `json` field in the DB,
     * the `assertDatabaseHas` method does not work properly in this case
     * Laravel issues!!!
     */
    public function testSaveMethod()
    {
        /** @var Log $log */
        $log = factory(Log::class)->make();

        $this->logRepository->save($log);

        $this->assertEquals(Log::query()->find($log->id)->toArray(), $log->toArray());
    }

    /**
     * @test
     * @group LogRepositoryUnitTests
     * @throws Exception
     */
    public function testDeleteMethod()
    {
        /** @var Log $log */
        $log = factory(Log::class)->create()->setAppends([]);

        $this->logRepository->delete($log);

        $this->assertDatabaseMissing('logs', $log->toArray());
    }

    /**
     * @test
     * @group LogRepositoryUnitTests
     */
    public function testFindByIdMethod()
    {
        /** @var Log $log */
        $log = factory(Log::class)->create();

        $dbRecord = $this->logRepository->findById($log->id);

        $this->assertEquals($dbRecord->toArray(), $log->toArray());
    }

    /**
     * @test
     * @group LogRepositoryUnitTests
     */
    public function testFindOneByCriteriaMethod()
    {
        /** @var Log $log */
        $log = factory(Log::class)->create();

        $dbRecord = $this->logRepository->findOneByCriteria([
            'to' => $log->to,
            'body' => $log->body,
            'provider' => $log->provider,
            'failed_reason' => $log->failed_reason,
        ]);

        $this->assertEquals($log->toArray(), $dbRecord->toArray());
    }

    /**
     * @test
     * @group LogRepositoryUnitTests
     */
    public function testFindManyByEmailAndDateMethod()
    {
        /** @var Log $log */
        $log = factory(Log::class)->create();

        $dbRecord = $this->logRepository->findManyLogsByEmailAndDate(
            $log->to,
            $log->sent_at->subWeeks($this->faker->randomDigit),
            $log->sent_at->addWeeks($this->faker->randomDigit)
        );

        $this->assertEquals($dbRecord->items()[0]->toArray(), $log->toArray());
    }
}
