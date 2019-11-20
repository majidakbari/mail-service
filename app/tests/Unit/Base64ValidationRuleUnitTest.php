<?php

namespace Tests\Unit;

use App\Rules\Base64Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class Base64ValidationRuleUnitTest
 * @package Tests\Unit
 */
class Base64ValidationRuleUnitTest extends TestCase
{
    /**
     * @test
     * @group CustomValidatorUnitTests
     * @dataProvider base64ValidationRuleDataProvider
     * @param string $sampleBase64
     * @param array $mimeTypes
     * @param bool $expected
     */
    public function testValidationRule($sampleBase64, $mimeTypes, $expected): void
    {
        $validationRule = resolve(Base64Validator::class, ['mimes' => $mimeTypes]);

        $this->assertEquals(
            $expected,
            $validationRule->passes('', $sampleBase64)
        );
    }


    /**
     * @return array
     */
    public function base64ValidationRuleDataProvider(): array
    {
        $this->refreshApplication();

        $sampleString = base64_encode(Str::random(32));

        return [
            [
                $sampleString,
               $this->getImageMimeTypes(),
                false
            ],
            [
                $sampleString,
                $this->getVideoMimeTypes(),
                false
            ],
            [
                $sampleString,
                $this->getAudioMimeTypes(),
                false
            ],
            [
                $sampleString,
                $this->getDocumentMimeTypes(),
                true
            ]
        ];
    }

    /**
     * @return array
     */
    private function getImageMimeTypes(): array
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
    private function getVideoMimeTypes(): array
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
    private function getAudioMimeTypes(): array
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
    private function getDocumentMimeTypes(): array
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
}
