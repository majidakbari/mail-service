<?php

namespace Tests\Unit;

use App\Exceptions\HttpException\InvalidAcceptHeaderException;
use App\Http\Middleware\OnlyJsonResponseMiddleware;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Class OnlyJsonResponseMiddlewareTest
 * @package Tests\Unit
 */
class OnlyJsonResponseMiddlewareTest extends TestCase
{
    /**
     * @test
     * @group CustomMiddlewareUnitTests
     * @dataProvider successTestDataProvider
     * @param string $header
     * @param string $expectedHeader
     */
    public function testSuccess($header, $expectedHeader)
    {
        /** @var Request $request */
        /** @var OnlyJsonResponseMiddleware $middleware */
        list($request, $middleware) = $this->init($header);

        $middleware->handle($request, function (Request $req) use($expectedHeader) {
            $this->assertEquals($req->header('Accept') , $expectedHeader);
        });
    }

    /**
     * @return array
     */
    public function successTestDataProvider()
    {
        return [
            ['*/*', 'application/json'],
            ['application/json', 'application/json']
        ];
    }

    /**
     * @test
     * @group CustomMiddlewareUnitTests
     * @param string $header
     * @dataProvider failedDataProvider
     */
    public function testFailed($header)
    {
        /** @var Request $request */
        /** @var OnlyJsonResponseMiddleware $middleware */
        list($request, $middleware) = $this->init($header);

        $this->expectException(InvalidAcceptHeaderException::class);

        $middleware->handle($request, function ($req) {});
    }

    /**
     * @return array
     */
    public function failedDataProvider()
    {
        return [
            ['image/jpeg'],
            ['image/gif'],
            ['image/webp'],
            ['application/x-ms-application'],
            ['application/xaml+xml'],
            ['image/pjpeg'],
            ['application/x-ms-xbap'],
            ['application/x-shockwave-flash'],
            ['application/msword'],
            ['text/html'],
            ['text/cmd'],
            ['text/css'],
            ['text/csv'],
            ['text/plain'],
            ['text/vcard'],
            ['text/xml'],
            ['application/xhtml+xml'],
            ['application/xml']
        ];
    }

    /**
     * Init function for testSuccess and testFailed unit tests
     * @param string $header
     * @return array
     */
    private function init($header): array
    {
        $request = new Request();
        $middleware = new OnlyJsonResponseMiddleware();
        $request->headers->set('Accept', $header);

        return [$request, $middleware];
    }
}
