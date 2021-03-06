<?php

namespace Tests\Feature;

use App\Jobs\SendSingleEmailJob;
use App\ValueObjects\QueueManager;
use App\ValueObjects\Email;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;
use Tests\Tools\CustomFactories\EmailFactory;
use Tests\TestCase;

/**
 * Class SendSingleEmailTest
 * @package Tests\Feature
 */
class SendSingleEmailTest extends TestCase
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
     * @group FeatureSendSingleEmailTests
     */
    public function testSuccess(Email $email): void
    {
        $response = $this->json('post', route('email.send.single', [], false), $email->toArray());

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        Queue::assertPushedOn(QueueManager::SINGLE_EMAIL_QUEUE, SendSingleEmailJob::class,
            function (SendSingleEmailJob $job) use ($email) {
                return $job->getEmail()->toArray() == $email->toArray();
            }
        );
    }

    /**
     * @test
     * @expectedExceptionCode 422
     * @group FeatureSendSingleEmailTests
     * Validation error test
     */
    public function validationErrorTest(): void
    {
        /** @var EmailFactory $emailFactory */
        $emailFactory = resolve(EmailFactory::class);
        $email = $emailFactory->make(EmailFactory::EMAIL_UNCOMPLETED_BODY);
        $response = $this->json('post', route('email.send.single', [], false), $email->toArray());

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure([
            'message' => ['body',]
        ]);

        Queue::assertNothingPushed();
    }

    /**
     * @test
     * @expectedExceptionCode 406
     * @group FeatureSendSingleEmailTests
     * Wrong request headers (Accept header)
     */
    public function headerNotAcceptableTest(): void
    {
        $response = $this->post(route('email.send.single', [], false), []);
        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE)->assertJson([
            'error' => 'InvalidAcceptHeaderException',
            'message' => trans('app.InvalidAcceptHeaderException')
        ]);

        Queue::assertNothingPushed();
    }
}
