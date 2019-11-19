<?php

namespace App\Services;

use App\Tools\FileHelper;
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
     * @var Swift_SmtpTransport
     */
    private $SMTPTransport;

    /**
     * @var MarkdownToHTMLService
     */
    private $markdownToHTMLService;

    /**
     * @var Swift_Message
     */
    private $swift_Message;

    /**
     * @var FileHelper
     */
    private $fileHelper;

    /**
     * MailService constructor.
     * @param Swift_SmtpTransport $SMTPTransport
     * @param MarkdownToHTMLService $markdownToHTMLService
     * @param Swift_Message $swift_Message
     * @param FileHelper $fileHelper
     * @param MailProvider|null $mailProvider
     */
    public function __construct(
        Swift_SmtpTransport $SMTPTransport,
        MarkdownToHTMLService $markdownToHTMLService,
        Swift_Message $swift_Message,
        FileHelper $fileHelper,
        $mailProvider = null
    ) {
        $this->SMTPTransport = $SMTPTransport;
        $this->markdownToHTMLService = $markdownToHTMLService;
        $this->swift_Message = $swift_Message;
        $this->fileHelper = $fileHelper;
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
     * @return MailService
     */
    public function setMailProvider(MailProvider $mailProvider): MailService
    {
        $this->mailProvider = $mailProvider;

        return $this;
    }

    /**
     * @param Email $email
     * @return bool
     * @throws Exception
     */
    public function send(Email $email)
    {
        try {
            $mailer = $this->prepareMailer();
            $message = $this->prepareMessage($email);
            $mailer->send($message);
            $mailer->getTransport()->stop();

            return true;

        } catch (Exception $e) {
            //We can have some kind of log here to monitor our SMTP relay performance, mailbox addresses and etc...
            throw $e;
        }
    }


    /**
     * @return Swift_Mailer
     */
    private function prepareMailer(): Swift_Mailer
    {
        $provider = $this->getMailProvider();
        $swiftSMTPTransport = $this->SMTPTransport->setHost($provider->getHost())
            ->setPort($provider->getPort())
            ->setEncryption($provider->getEncryption());

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
        if ($email->isMarkDown()) {
            $email->setBody($this->markdownToHTMLService->convert($email->getBody()))->setBodyType(Email::BODY_TYPE_HTML);
        }

        $message = $this->swift_Message
            ->setSubject($email->getSubject())
            ->setFrom($email->getFromAddress(), $email->getFromName())
            ->setTo($email->getTo())
            ->setBody($email->getBody(), $email->getBodyType())
            ->setCc($email->getCc())
            ->setBcc($email->getBcc());

        if ($file = $email->getAttachFileCode()) {
            /** @var FileHelper $fileHelper */
            $fileHelper = $this->fileHelper->setBase64Code($file);
            $message->attach(Swift_Attachment::fromPath($fileHelper->getFileAddress(),
                $fileHelper->getMimeType())->setFilename($email->getAttachFileName())
            );
        }

        return $message;
    }
}
