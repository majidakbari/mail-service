<?php

namespace App\ValueObjects;

/**
 * Class Email
 * @property string $to
 * @property string $fromAddress
 * @property string $fromName
 * @property string $subject
 * @property string $body
 * @property string $bodyType
 * @property string $attachFileCode
 * @property string $attachFileName
 * @property array $bcc
 * @property array $cc
 * @package App\ValueObjects
 */
class Email
{
    const BODY_TYPE_HTML = 'text/html';

    const BODY_TYPE_TEXT = 'text/plain';

    const BODY_TYPE_MARKDOWN = 'text/markdown';

    /**
     * @var string
     * array of emails
     */
    protected $to;
    /**
     * @var string
     */
    protected $fromAddress;
    /**
     * @var string
     */
    protected $fromName;
    /**
     * @var string
     */
    protected $subject;
    /**
     * @var string
     */
    protected $body;
    /**
     * @var string
     */
    protected $bodyType;
    /**
     * @var string|null
     */
    protected $attachFileCode;
    /**
     * @var string|null
     */
    protected $attachFileName;
    /**
     * @var array
     */
    protected $bcc;
    /**
     * @var array
     */
    private $cc;


    /**
     * Email constructor.
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param string $bodyType
     * @param string $fromAddress
     * @param string $fromName
     * @param string $attachFileCode
     * @param string $attachFileName
     * @param array $cc
     * @param array $bcc
     */
    public function __construct(
        string $to,
        string $subject,
        string $body,
        string $bodyType,
        string $fromAddress,
        string $fromName,
        string $attachFileCode,
        string $attachFileName,
        array $cc,
        array $bcc
    ) {
        $this->to = $to;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->subject = $subject;
        $this->body = $body;
        $this->bodyType = $bodyType;
        $this->attachFileCode = $attachFileCode;
        $this->attachFileName = $attachFileName;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getFromAddress(): string
    {
        return $this->fromAddress;
    }

    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getBodyType(): string
    {
        return $this->bodyType;
    }

    /**
     * @return string|null
     */
    public function getAttachFileCode(): ?string
    {
        return $this->attachFileCode;
    }

    /**
     * @return string|null
     */
    public function getAttachFileName(): ?string
    {
        return $this->attachFileName;
    }

    /**
     * @return array
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    /**
     * @return array
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    /**
     * @param string|null $attachFileCode
     * @return Email
     */
    public function setAttachFileCode(?string $attachFileCode): Email
    {
        $this->attachFileCode = $attachFileCode;

        return $this;
    }

    /**
     * @param string|null $attachFileName
     * @return Email
     */
    public function setAttachFileName(?string $attachFileName): Email
    {
        $this->attachFileName = $attachFileName;

        return $this;
    }

    /**
     * @param string $body
     * @return Email
     */
    public function setBody(string $body): Email
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param string $bodyType
     * @return Email
     */
    public function setBodyType(string $bodyType): Email
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    /**
     * @param array $data
     * @return Email
     */
    public static function fromArray(array $data): Email
    {
        return new static(
            $data['to'] ?? '',
            $data['subject'] ?? '',
            $data['body'] ?? '',
            $data['bodyType'] ?? self::BODY_TYPE_TEXT,
            $data['fromAddress'] ?? config('mail.from.address'),
            $data['fromName'] ?? config('mail.from.name'),
            $data['attachFileCode'] ?? '',
            $data['attachFileName'] ?? '',
            $data['cc'] ?? [],
            $data['bcc'] ?? []
        );
    }

    /**
     * @return array
     */
    public static function getValidBodyTypes()
    {
        return [
            self::BODY_TYPE_TEXT,
            self::BODY_TYPE_HTML,
            self::BODY_TYPE_MARKDOWN
        ];
    }

    /**
     * @return bool
     */
    public function isMarkDown(): bool
    {
        return $this->getBodyType() == self::BODY_TYPE_MARKDOWN;
    }

    /**
     * @return bool
     */
    public function hasAttachment(): bool
    {
        return !empty($this->getAttachFileCode());
    }

    /**
     * @return array
     */
    public function getMetaData()
    {
        return [
            'subject' => $this->getSubject(),
            'bodyType' => $this->getBodyType(),
            'fromAddress' => $this->getFromAddress(),
            'fromName' => $this->getFromName(),
            'attachment' => !empty($this->getAttachFileCode()),
            'cc' => $this->getCc(),
            'bcc' => $this->getBcc(),
        ];
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'to' => [$this->getTo()],
            'subject' => $this->getSubject(),
            'body' => $this->getBody(),
            'bodyType' => $this->getBodyType(),
            'fromAddress' => $this->getFromAddress(),
            'fromName' => $this->getFromName(),
            'attachFileCode' => $this->getAttachFileCode(),
            'attachFileName' => $this->getAttachFileName(),
            'bcc' => $this->getBcc(),
            'cc' => $this->getCc()
        ];
    }
}
