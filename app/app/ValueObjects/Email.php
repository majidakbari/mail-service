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
 * @property string $attachFilePath
 * @property string $attachFileName
 * @property array $bcc
 * @property array $cc
 * @package App\ValueObjects
 */
class Email
{
    const BODY_TYPE_HTML = 'text/html';

    const BODY_TYPE_TEXT = 'text/plain';

    /**
     * @var string
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
    protected $attachFilePath;
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
     * @param string $attachFilePath
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
        string $attachFilePath,
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
        $this->attachFilePath = $attachFilePath;
        $this->attachFileName = $attachFileName;
        $this->bcc = $bcc;
        $this->cc = $cc;
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
    public function getAttachFilePath(): ?string
    {
        return $this->attachFilePath;
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
            $data['attachFilePath'] ?? null,
            $data['attachFileName'] ?? null,
            $data['bcc'] ?? [],
            $data['cc'] ?? []
        );
    }
}
