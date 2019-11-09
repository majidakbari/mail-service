<?php

namespace App\Services;

use App\ValueObjects\Email;
use App\ValueObjects\MailProvider;
use Exception;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

/**
 * Class MailService
 * @package App\Services
 */
class MailService
{
    /**
     * @var MailProvider
     */
    private $mailProvider;

    /**
     * MailService constructor.
     * @param MailProvider $mailProvider
     */
    public function __construct(MailProvider $mailProvider)
    {
        $this->mailProvider = $mailProvider;
    }

    /**
     * @return MailProvider
     */
    public function getMailProvider(): MailProvider
    {
        return $this->mailProvider;
    }

    /**
     * @param MailProvider $mailProvider
     */
    public function setMailProvider(MailProvider $mailProvider): void
    {
        $this->mailProvider = $mailProvider;
    }

    /**
     * @param Email $email
     * @return bool
     */
    public function send(Email $email)
    {
        $mailer = $this->prepareMailer();
        $message = $this->prepareMessage($email);

        try {
            $mailer->send($message);
            $mailer->getTransport()->stop();

            return true;

        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * @return Swift_Mailer
     */
    private function prepareMailer(): Swift_Mailer
    {
        $provider = $this->getMailProvider();
        $swiftSMTPTransport = new Swift_SmtpTransport(
            $provider->getHost(),
            $provider->getPort(),
            $provider->getEncryption()
        );
        $transport = $swiftSMTPTransport
            ->setUsername($provider->getUsername())
            ->setPassword($provider->getPassword())
            ->setStreamOptions($provider->getStreamOptions());

        return new Swift_Mailer($transport);
    }

    /**
     * @param Email $email
     * @return Swift_Message
     */
    private function prepareMessage(Email $email): Swift_Message
    {
        $message = (new Swift_Message())
            ->setSubject($email->getSubject())
            ->setFrom($email->getFromAddress(), $email->getFromName())
            ->setTo($email->getTo())
            ->setBody($email->getBody(), $email->getBodyType())
            ->setCc($email->getCc())
            ->setBcc($email->getBcc());

        if ($email->getAttachFilePath()) {
            if (file_exists($email->getAttachFilePath())) {
                $message->attach(
                    Swift_Attachment::fromPath($email->getAttachFilePath())
                        ->setFilename($email->getAttachFileName())
                );
            }
        }

        return $message;
    }
}
