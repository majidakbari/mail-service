<?php

namespace Tests\Unit;

use App\Services\MarkdownToHTMLService;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery\LegacyMockInterface;
use Parsedown;
use Tests\TestCase;

/**
 * Class MarkdownToHTMLServiceTest
 * @package Tests\Unit
 */
class MarkdownToHTMLServiceTest extends TestCase
{
    use WithFaker;

    /**
     * @var LegacyMockInterface
     */
    protected $parseDown;

    /**
     * @var MarkdownToHTMLService
     */
    protected $markdownToHtmlService;


    /**
     * @test
     * @group MarkdownToHtmlServiceUnitTest
     * @return void
     */
    public function testConvertFunction()
    {
        $this->mock(Parsedown::class, function (LegacyMockInterface $mock) {
            $mock->shouldReceive('text')->once()->andReturn($this->faker->sentence);
        });

        $markdownToHtmlService = resolve(MarkdownToHTMLService::class);

        $markdownToHtmlService->convert($this->faker->sentence);
    }

    /**
     * @test
     * @group MarkdownToHtmlServiceUnitTest
     */
    public function testGetter()
    {
        /** @var MarkdownToHTMLService $markdownToHtmlService */
        $markdownToHtmlService = resolve(MarkdownToHTMLService::class);

        $this->assertTrue(get_class($markdownToHtmlService->getParserService()) === Parsedown::class);
    }
}
