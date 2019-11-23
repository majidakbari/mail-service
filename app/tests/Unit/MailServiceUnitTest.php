<?php

namespace Tests\Unit;

use App\Services\MailService;
use App\Services\MarkdownToHTMLService;
use App\Tools\FileHelper;
use App\ValueObjects\Email;
use App\ValueObjects\MailProvider;
use Exception;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Tests\TestCase;
use Tests\Tools\CustomFactories\EmailFactory;
use Tests\Tools\CustomFactories\MailProviderFactory;
use Tests\Tools\Subs\MockSwiftMailer;

/**
 * Class MailServiceUnitTest
 * @package Tests\Unit
 */
class MailServiceUnitTest extends TestCase
{
    use WithFaker;

    /**
     * @var MailProviderFactory
     */
    protected $mailProviderFactory;

    /**
     * @var EmailFactory
     */
    protected $emailFactory;

    /**
     * setup dependencies
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mailProviderFactory = resolve(MailProviderFactory::class);
        $this->emailFactory = resolve(EmailFactory::class);
    }

    /**
     * @test
     * @group MailServiceUnitTests
     */
    public function testGetMailProviderMethod(): void
    {
        /** @var MailProvider $fakeMailProvider */
        $fakeMailProvider = $this->mailProviderFactory->make();

        /** @var MailService $mailService */
        $mailService = resolve(MailService::class, [
            'mailProvider' => $fakeMailProvider
        ]);

        $this->assertEquals(
            $fakeMailProvider,
            $mailService->getMailProvider()
        );
    }

    /**
     * @test
     * @group MailServiceUnitTests
     */
    public function testSetMailProviderMethod(): void
    {
        /** @var MailProvider $newMailProvider */
        $newMailProvider = $this->mailProviderFactory->make();
        /** @var MailService $mailService */
        $mailService = resolve(MailService::class);

        $mailService->setMailProvider($newMailProvider);

        $this->assertEquals(
            $newMailProvider,
            $mailService->getMailProvider()
        );
    }

    /**
     * @test
     * @group MailServiceUnitTests
     */
    public function testPrepareMailerMethod(): void
    {
        /** @var MailProvider $mailProvider */
        $mailProvider = $this->mailProviderFactory->make();

        $this->mock(Swift_SmtpTransport::class, function (LegacyMockInterface $mock) use ($mailProvider) {
            $mock->shouldReceive('setHost')->once()->with($mailProvider->getHost())->andReturn($mock);
            $mock->shouldReceive('setPort')->once()->with($mailProvider->getPort())->andReturn($mock);
            $mock->shouldReceive('setEncryption')->once()->with($mailProvider->getEncryption())->andReturn($mock);
            $mock->shouldReceive('setUsername')->once()->with($mailProvider->getUsername())->andReturn($mock);
            $mock->shouldReceive('setPassword')->once()->with($mailProvider->getPassword())->andReturn($mock);
            $mock->shouldReceive('setStreamOptions')->once()->with($mailProvider->getStreamOptions())->andReturn($mock);
        });

        /** @var MailService $mailService */
        $mailService = resolve(MailService::class, [
            'mailProvider' => $mailProvider
        ]);

        $mailService->prepareMailer();
    }

    /**
     * @test
     * @group MailServiceUnitTests
     * @dataProvider messageDataProvider
     * @param Email $email
     */
    public function testPrepareMessageMethod(Email $email): void
    {
        $this->mock(Swift_Message::class, function (LegacyMockInterface $mock) use ($email) {
            $mock->shouldReceive('setSubject')->with($email->getSubject())->andReturn($mock);
            $mock->shouldReceive('setTo')->with($email->getTo())->andReturn($mock);
            $mock->shouldReceive('setCc')->with($email->getCc())->andReturn($mock);
            $mock->shouldReceive('setBcc')->with($email->getBcc())->andReturn($mock);
            $mock->shouldReceive('setFrom')->with(
                $email->getFromAddress(), $email->getFromName()
            )->andReturn($mock);

            if ($email->isMarkDown()) {
                $this->mock(MarkdownToHTMLService::class,
                    function (LegacyMockInterface $mockMarkDownService) use ($email) {
                        $mockMarkDownService->shouldReceive('convert')->with($email->getBody());
                    });

                $mock->shouldReceive('setBody')->with('', Email::BODY_TYPE_HTML)->andReturn($mock);

            } else {
                $mock->shouldReceive('setBody')->with(
                    $email->getBody(), $email->getBodyType()
                )->andReturn($mock);
            }

            if ($email->hasAttachment()) {
                $this->mock(Swift_Attachment::class, function (LegacyMockInterface $attachmentMock) use ($email) {
                    $this->mock(FileHelper::class, function (LegacyMockInterface $fileHelperMock) use ($email) {
                        $fileHelperMock->shouldReceive('setBase64Code')->with($email->getAttachFileCode())
                            ->andReturn($fileHelperMock);
                        $fileHelperMock->shouldReceive('getFileAddress')->andReturn("/tmp/" . $this->faker->randomNumber());
                        $fileHelperMock->shouldReceive('getMimeType');
                    });
                    $attachmentMock->shouldReceive('fromPath')->andReturn($attachmentMock);
                    $attachmentMock->shouldReceive('setFilename')->andReturn($attachmentMock);
                });
                $mock->shouldReceive('attach')->andReturn($mock);
            }
        });

        /** @var MailService $mailService */
        $mailService = resolve(MailService::class);

        $mailService->prepareMessage($email);
    }

    /**
     * @return array
     * inside the dataProvider we do not have access to $this context
     */
    public function messageDataProvider(): array
    {
        /** @var EmailFactory $emailFactory */
        $emailFactory = resolve(EmailFactory::class);

        return [
            [$emailFactory->make(EmailFactory::EMAIL_WITH_TEXT_BODY)],
            [$emailFactory->make(EmailFactory::EMAIL_WITHOUT_OPTIONAL_PROPERTIES)],
            [$emailFactory->make(EmailFactory::EMAIL_WITH_HTML_BODY)],
            [$emailFactory->make(EmailFactory::EMAIL_WITH_MARKDOWN_BODY)],
            [$emailFactory->make(EmailFactory::EMAIL_WITH_FILE_ATTACHED)],
        ];
    }

    /**
     * @test
     * @group MailServiceUnitTests
     * @throws Exception
     */
    public function testSendMethod()
    {
        $this->mock(Swift_Mailer::class, function (MockInterface $mockMailer) {
            $mockMailer->shouldReceive('send');
        });

        /** @var MailService $mailService */
        $mailService = resolve(MailService::class, ['mailProvider' => $this->mailProviderFactory->make()]);
        $email = $this->emailFactory->make(EmailFactory::EMAIL_WITH_MARKDOWN_BODY);
        $this->app->bind(Swift_Mailer::class, MockSwiftMailer::class);

        $mailService->send($email);
    }
}
