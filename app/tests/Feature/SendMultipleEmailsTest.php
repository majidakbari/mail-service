<?php

namespace Tests\Feature;

use App\Jobs\SendSingleEmailJob;
use App\ValueObjects\QueueManager;
use EmailFactory;
use App\ValueObjects\Email;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;
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
     * @group FeatureSendMultipleEmails
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
     * @group FeatureSendMultipleEmails
     * @dataProvider wrongEmailNumberDataProvider
     * Validation error test
     * @param array $data
     */
    public function validationErrorTest(array $data)
    {
        $response = $this->json('post', route('email.send.multiple', [], false), [
            'data' => $data
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     * @expectedExceptionCode 406
     * @group FeatureSendMultipleEmails
     * Wrong request headers (Accept header)
     */
    public function headerNotAcceptableTest()
    {
        $response = $this->post(route('email.send.multiple', [], false), []);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }
}
