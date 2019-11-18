<?php

namespace Tests\Feature;

use App\Jobs\SendSingleEmailJob;
use App\ValueObjects\QueueManager;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;
use Tests\Tools\CustomFactories\EmailFactory;
use Tests\TestCase;

/**
 * Class SendMultipleEmailsTest
 * @package Tests\Feature
 */
class SendMultipleEmailsTest extends TestCase
{

    /**
     * Mock queue
     */
    public function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    /**
     * @return array
     */
    public function emailsDataProvider(): array
    {
        $this->refreshApplication();

        /** @var EmailFactory $emailFactory */
        $emailFactory = resolve(EmailFactory::class);

        return [
            [objects_to_array($emailFactory->makeMany(EmailFactory::EMAIL_WITH_FILE_ATTACHED))],
            [objects_to_array($emailFactory->makeMany(EmailFactory::EMAIL_WITH_HTML_BODY))],
            [objects_to_array($emailFactory->makeMany(EmailFactory::EMAIL_WITH_MARKDOWN_BODY))],
            [objects_to_array($emailFactory->makeMany(EmailFactory::EMAIL_WITH_TEXT_BODY))],
            [objects_to_array($emailFactory->makeMany(EmailFactory::EMAIL_WITHOUT_OPTIONAL_PROPERTIES))],
        ];
    }

    /**
     * @return array
     */
    public function wrongEmailNumberDataProvider(): array
    {
        $this->refreshApplication();

        /** @var EmailFactory $emailFactory */
        $emailFactory = resolve(EmailFactory::class);

        return [
            [objects_to_array($emailFactory->makeMany(EmailFactory::EMAIL_WITHOUT_OPTIONAL_PROPERTIES, 101))],
            [array()],
        ];
    }


    /**
     * @test
     * @param array $emails
     * @dataProvider emailsDataProvider
     * @group FeatureSendMultipleEmailsTests
     */
    public function testSuccess(array $emails): void
    {
        $response = $this->json('post', route('email.send.multiple', [], false), [
            'data' => $emails
        ]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Queue::assertPushedOn(QueueManager::BULK_EMAIL_QUEUE, SendSingleEmailJob::class);
        Queue::assertPushed(SendSingleEmailJob::class, count($emails));
    }

    /**
     * @test
     * @expectedExceptionCode 422
     * @group FeatureSendMultipleEmailsTests
     * @dataProvider wrongEmailNumberDataProvider
     * Validation error test
     * @param array $data
     */
    public function validationErrorTest(array $data): void
    {
        $response = $this->json('post', route('email.send.multiple', [], false), [
            'data' => $data
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        Queue::assertNothingPushed();
    }

    /**
     * @test
     * @expectedExceptionCode 406
     * @group FeatureSendMultipleEmailsTests
     * Wrong request headers (Accept header)
     */
    public function headerNotAcceptableTest(): void
    {
        $response = $this->post(route('email.send.multiple', [], false), []);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
        Queue::assertNothingPushed();
    }
}
