<?php

namespace Tests\Unit;

use App\Entities\Log;
use App\Tools\APIResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Tests\TestCase;

/**
 * Class APIResponseUnitTest
 * @package Tests\Unit
 */
class APIResponseUnitTest extends TestCase
{
    use WithFaker;

    /**
     * @var array
     */
    protected $data;

    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();

        //just for creating a random multi level array
        /** @var Log $record */
        $record = factory(Log::class)->make();
        $this->data = $record->toArray();
    }


    /**
     * Just an example of popular HTTP response headers with values to make sure that APIResponse tool can set those headers
     */
    const POPULAR_RESPONSE_HEADERS = [
        'Cache-Control' => 'no-cache, private',
        'Date' => 'Thu, 21 Nov 2019 22:02:45 GMT',
        'Connection' => 'Keep-Alive',
        'Age' => '12',
        'Content-Encoding' => 'gzip',
        'Content-Language' => 'en',
        'Content-Length' => '348',
        'Expires' => 'Thu, 21 Nov 2019 22:02:45 GMT',
        'Etag' => '1232312cads2',
        'Last-Modified' => 'Thu, 21 Nov 2019 22:02:45 GMT',
        'Pragma' => 'no-cache',
        'Server' => 'Apache/2.4.38 (Debian)'
    ];

    const APPLICATION_SUCCESS_STATUSES = [
        Response::HTTP_NO_CONTENT,
        Response::HTTP_OK,
    ];

    const APPLICATION_ERROR_STATUSES = [
        Response::HTTP_METHOD_NOT_ALLOWED,
        Response::HTTP_NOT_ACCEPTABLE,
        Response::HTTP_TOO_MANY_REQUESTS,
        Response::HTTP_INTERNAL_SERVER_ERROR,
        Response::HTTP_UNPROCESSABLE_ENTITY,
        Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
    ];

    /**
     * @test
     * @group APIResponseUnitTests
     * @return void
     */
    public function testSuccessResponse(): void
    {
        $statusCode = $this->faker->randomElement(self::APPLICATION_SUCCESS_STATUSES);
        $headers = $this->getRandomResponseHeaders();
        $response = APIResponse::success($this->data, $statusCode, $headers);

        $this->assertEquals(
            $this->data, $response->getData(true)
        );
        $this->assertEquals(
            $statusCode, $response->status()
        );
        $this->assertEquals(
            $response->headers->all(),
            array_merge((new ResponseHeaderBag($headers))->all(), ["content-type" => ["application/json"]])
        );
    }

    /**
     * @test
     * @group APIResponseUnitTests
     * @return void
     */
    public function testErrorResponse(): void
    {
        $statusCode = $this->faker->randomElement(self::APPLICATION_ERROR_STATUSES);
        $headers = $this->getRandomResponseHeaders();
        $errorCode = $this->faker->languageCode;

        $response = APIResponse::error($errorCode, $this->data, $statusCode, $headers);

        $this->assertEquals(
            [
                APIResponse::ERROR_KEY => $errorCode,
                APIResponse::MSG_KEY => $this->data
            ],
            $response->getData(true)
        );
        $this->assertEquals(
            $statusCode, $response->status()
        );
        $this->assertEquals(
            $response->headers->all(),
            array_merge((new ResponseHeaderBag($headers))->all(), ["content-type" => ["application/json"]])
        );
    }

    /**
     * @return array
     */
    private function getRandomResponseHeaders(): array
    {
        $popularHeaders = self::POPULAR_RESPONSE_HEADERS;
        $headers = array_rand(self::POPULAR_RESPONSE_HEADERS, $this->faker->numberBetween(2, count($popularHeaders)));

        return array_intersect_key($popularHeaders, array_flip($headers));
    }
}
