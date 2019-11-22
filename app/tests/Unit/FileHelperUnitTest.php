<?php

namespace Tests\Unit;

use App\Tools\FileHelper;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class FileHelperUnitTest
 * @package Tests\Unit
 */
class FileHelperUnitTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     * @group FileHelperUnitTests
     * testing getter method
     */
    public function testGetBase64CodeMethod()
    {
        /** @var FileHelper $fileHelper */
        list(, $base64Code, $fileHelper) = $this->getFileHelperInstance();

        $this->assertEquals(
            $base64Code,
            $fileHelper->getBase64Code()
        );
    }

    /**
     * @test
     * @group FileHelperUnitTests
     */
    public function testGetFileAddressMethod()
    {
        /** @var FileHelper $fileHelper */
        list($sampleString, , $fileHelper) = $this->getFileHelperInstance();

        $fileAddress = $fileHelper->getFileAddress();

        $this->assertTrue(file_exists($fileAddress));
        $this->assertEquals(
            file_get_contents($fileAddress),
            $sampleString
        );
    }

    /**
     * @test
     * @group FileHelperUnitTests
     */
    public function testSetBase64CodeMethod()
    {
        /** @var FileHelper $fileHelper */
        $fileHelper = resolve(FileHelper::class);
        $sampleBase64 = base64_encode($this->faker->sentence);
        $fileHelperChangedInstance = $fileHelper->setBase64Code($sampleBase64);

        $this->assertEquals(
            $sampleBase64,
            $fileHelper->getBase64Code()
        );
    }

    /**
     * @test
     * @group FileHelperUnitTests
     */
    public function testDecodeMethod()
    {
        /** @var FileHelper $fileHelper */
        list($sampleString, , $fileHelper) = $this->getFileHelperInstance();

        $this->assertEquals(
            $sampleString,
            $fileHelper->decode()
        );
    }

    /**
     * @test
     * @group FileHelperUnitTests
     */
    public function testGetMimeTypeMethod()
    {
        /** @var FileHelper $fileHelper */
        list(,,$fileHelper) = $this->getFileHelperInstance();

        //we need to test other mime types, but in this project I skipped them
        $this->assertEquals(
            'text/plain',
            $fileHelper->getMimeType()
        );
    }

    /**
     * @return array
     */
    private function getFileHelperInstance(): array
    {
        $sampleString = $this->faker->sentence;
        $base64Code = base64_encode($sampleString);
        $fileHelper = resolve(FileHelper::class, ['base64Code' => $base64Code]);

        return [$sampleString, $base64Code, $fileHelper];
    }
}
