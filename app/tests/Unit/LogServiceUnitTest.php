<?php

namespace Tests\Unit;

use App\Entities\Log;
use App\Interfaces\LogRepositoryInterface;
use App\Services\LogService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\LegacyMockInterface;
use Tests\TestCase;
use Tests\Tools\CustomFactories\EmailFactory;

/**
 * Class LogServiceUnitTest
 * @package Tests\Unit
 */
class LogServiceUnitTest extends TestCase
{
    use WithFaker;

    /**
     * @var EmailFactory
     */
    protected $emailFactory;

    /**
     * setup tests
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->emailFactory = resolve(EmailFactory::class);
    }

    /**
     * @test
     * @group LogServiceUnitTests
     */
    public function testSuccessMethod()
    {
        $email = $this->emailFactory->make(EmailFactory::EMAIL_WITH_TEXT_BODY);

        /** @var Log $log */
        $log = factory(Log::class)->make([
            'to' => $email->getTo(),
            'body' => $email->getBody(),
            'email_metadata' => $email->getMetaData(),
            'failed_reason' => '',
            'provider' => $this->faker->randomDigit,
            'sent_at' => now(),
            'failed_at' => null
        ]);

        $this->mock(LogRepositoryInterface::class, function (LegacyMockInterface $mock) use($log) {
            $mock->shouldReceive('save')->once()->withArgs(function(Log $arg) use($log){
                return $log->toArray() == $arg->toArray();
            });
        });

        //Dependencies are mocked using mockery package
        /** @var LogService $logService */
        $logService = resolve(LogService::class);

        $logService->success($email, $log->provider);
    }

    /**
     * @test
     * @group LogServiceUnitTests
     */
    public function testFailMethod()
    {
        $email = $this->emailFactory->make(EmailFactory::EMAIL_WITH_FILE_ATTACHED);

        /** @var Log $log */
        $log = factory(Log::class)->make([
            'to' => $email->getTo(),
            'body' => $email->getBody(),
            'email_metadata' => $email->getMetaData(),
            'failed_reason' => $this->faker->sentence,
            'provider' => $this->faker->randomDigit,
            'sent_at' => null,
            'failed_at' => now()
        ]);

        $this->mock(LogRepositoryInterface::class, function (LegacyMockInterface $mock) use($log) {
            $mock->shouldReceive('save')->once()->withArgs(function(Log $arg) use($log){
                return $log->toArray() == $arg->toArray();
            });
        });

        //Dependencies are mocked using mockery package
        /** @var LogService $logService */
        $logService = resolve(LogService::class);

        $logService->fail($email, $log->provider, $log->failed_reason);
    }
}
