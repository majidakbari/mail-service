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
    public function emailDataProvider(): array
    {
        $this->refreshApplication();

        /** @var EmailFactory $emailFactory */
        $emailFactory = resolve(EmailFactory::class);

        return [
            [$emailFactory->make(EmailFactory::EMAIL_WITH_FILE_ATTACHED)],
            [$emailFactory->make(EmailFactory::EMAIL_WITH_HTML_BODY)],
            [$emailFactory->make(EmailFactory::EMAIL_WITH_MARKDOWN_BODY)],
            [$emailFactory->make(EmailFactory::EMAIL_WITH_TEXT_BODY)],
            [$emailFactory->make(EmailFactory::EMAIL_WITHOUT_OPTIONAL_PROPERTIES)],
        ];
    }

    /**
     * @test
     * @param Email $email
     * @dataProvider emailDataProvider
     * @group FeatureSendSingleEmail
     */
    public function testSuccess(Email $email)
    {
        $response = $this->json('post', route('email.send.single', [], false), $email->toArray());

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Queue::assertPushedOn(QueueManager::SINGLE_EMAIL_QUEUE, SendSingleEmailJob::class,
            function (SendSingleEmailJob $job) use ($email) {
                return $job->getEmail()->toArray() == $email->toArray();
            });
    }

    /**
     * @test
     * @expectedExceptionCode 422
     * @group FeatureSendSingleEmail
     * Validation error test
     */
    public function validationErrorTest()
    {
        /** @var EmailFactory $emailFactory */
        $emailFactory = resolve(EmailFactory::class);
        $email = $emailFactory->make(EmailFactory::EMAIL_UNCOMPLETED_BODY);
        $response = $this->json('post', route('email.send.single', [], false), $email->toArray());
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @test
     * @expectedExceptionCode 406
     * @group FeatureSendSingleEmail
     * Wrong request headers (Accept header)
     */
    public function headerNotAcceptableTest()
    {
        $response = $this->post(route('email.send.single', [], false), []);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }
}
