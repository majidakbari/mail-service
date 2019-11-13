<?php

namespace App\ValueObjects;

/**
 * Class MailProvider
 * @property int
 * @package App\ValueObjects
 */
class MailProvider
{
    const DEFAULT_STREAM_OPTIONS = [
        'ssl' => ['allow_self_signed' => true, 'verify_peer' => false]
    ];

    /**
     * @var int|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $host;
    /**
     * @var int
     */
    protected $port;
    /**
     * @var string
     */
    protected $encryption;
    /**
     * @var string
     */
    protected $username;
    /**
     * @var string
     */
    protected $password;

    /**
     * @var array
     */
    protected $streamOptions;


    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getEncryption(): string
    {
        return $this->encryption;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return array
     */
    public function getStreamOptions(): array
    {
        return $this->streamOptions;
    }

    /**
     * MailProvider constructor.
     * @param string|int $id
     * @param string $host
     * @param int $port
     * @param string $encryption
     * @param string $username
     * @param string $password
     * @param array $streamOptions
     */
    public function __construct($id, string $host, int $port, string $encryption, string $username, string $password, array $streamOptions)
    {
        $this->id = $id;
        $this->host = $host;
        $this->port = $port;
        $this->encryption = $encryption;
        $this->username = $username;
        $this->password = $password;
        $this->streamOptions = $streamOptions;
    }

    /**
     * @param array $data
     * @return MailProvider
     */
    public static function fromArray(array $data): MailProvider
    {
        return new static(
            $data['id'] ?? '',
            $data['host'] ?? '',
            $data['port'] ?? 0,
            $data['encryption'] ?? '',
            $data['username'] ?? '',
            $data['password'] ?? '',
            $data['streamOptions'] ?? []
        );
    }
}

