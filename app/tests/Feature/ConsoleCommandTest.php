<?php

namespace Tests\Feature;

use App\Console\Commands\SendEmailCommand;
use Tests\TestCase;
use Tests\Tools\CustomFactories\EmailFactory;

/**
 * Class ConsoleCommandTest
 * @package Tests\Feature
 */
class ConsoleCommandTest extends TestCase
{
    /**
     * @test
     * @group FeatureConsoleCommandTests
     */
    public function testSuccess(): void
    {
        /** @var EmailFactory $emailFactory */
        $emailFactory = resolve(EmailFactory::class);
        $email = $emailFactory->make(EmailFactory::EMAIL_WITH_FILE_ATTACHED);

        $this->artisan(SendEmailCommand::class)
            ->expectsQuestion('Enter the recipient email address', $email->getTo())
            ->expectsQuestion('Enter the email subject', $email->getSubject())
            ->expectsQuestion('Enter the email body', $email->getBody())
            ->expectsQuestion('Enter the email body type, one of the following properties: text/html, text/plain, text/markdown', $email->getBodyType())
            ->expectsQuestion('Enter the email fromName property', $email->getFromName())
            ->expectsQuestion('Enter the email fromAddress property', $email->getFromAddress())
            ->expectsQuestion('Enter the base64 encoded of file (this field is optional), press enter to skip', $email->getAttachFileCode())
            ->expectsQuestion('Enter the attached file name', $email->getAttachFileName())
            ->expectsQuestion('Enter email cc (this field is optional), press enter to skip', $email->getCc()[0])
            ->expectsQuestion('Enter email bcc (this field is optional), press enter to skip', $email->getBcc()[0])
            ->assertExitCode(0);
    }
}
