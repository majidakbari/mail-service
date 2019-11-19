<?php

namespace App\Tools;

/**
 * Class FileHelper
 * @package App\Tools
 */
class FileHelper
{
    /**
     * @var string
     */
    private $base64Code;

    /**
     * FileHelper constructor.
     * @param string|null $base64Code
     */
    public function __construct(string $base64Code = null)
    {
        $this->base64Code = $base64Code;
    }

    /**
     * @return string
     */
    public function getBase64Code(): string
    {
        return $this->base64Code;
    }

    /**
     * @return string
     */
    public function getFileAddress(): string
    {
        $address = "/tmp/" . uniqid();
        file_put_contents($address, $this->decode());

        return $address;
    }

    /**
     * @param string $base64Code
     * @return FileHelper
     */
    public function setBase64Code(string $base64Code): FileHelper
    {
        $this->base64Code = $base64Code;

        return $this;
    }

    /**
     * @return string
     */
    public function decode(): string
    {
        return base64_decode($this->getBase64Code());
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        $f = finfo_open();

        return finfo_buffer($f, $this->decode(), FILEINFO_MIME_TYPE);
    }


    /**
     * @return array
     */
    public static function getImageMimeTypes()
    {
        return [
            'image/jpeg',
            'image/jpeg',
            'image/jpeg',
            'image/gif',
            'image/png',
            'image/bmp',
            'image/tiff',
            'image/tiff',
            'image/x-icon'
        ];
    }

    /**
     * @return array
     */
    public static function getVideoMimeTypes()
    {
        return [
            'video/mp4',
            'application/octet-stream',
            'video/x-flv',
            'video/x-matroska',
            'video/x-msvideo',
            'video/x-ms-asf',
            'video/x-ms-asf',
            'video/x-ms-wmv',
            'video/mpeg',
            'video/quicktime'
        ];
    }

    /**
     * @return array
     */
    public static function getAudioMimeTypes()
    {
        return [
            'audio/mpeg',
            'audio/mpeg',
            'audio/mpeg',
            'audio/x-realaudio',
            'audio/x-realaudio',
            'audio/x-wav',
            'application/ogg',
            'application/ogg',
            'audio/midi',
            'audio/midi',
            'audio/x-ms-wma',
            'application/octet-stream',
            'audio/aac'
        ];
    }

    /**
     * @return array
     */
    public static function getDocumentMimeTypes()
    {
        return [
            'text/csv',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/css',
            'text/html',
            'text/html',
            'text/plain'
        ];
    }

    /**
     * @return array
     */
    public static function getAllValidMimeTypes()
    {
        return array_merge(
            static::getAudioMimeTypes(),
            static::getImageMimeTypes(),
            static::getVideoMimeTypes(),
            static::getDocumentMimeTypes()
        );
    }
}
