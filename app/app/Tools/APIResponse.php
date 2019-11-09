<?php

namespace App\Tools;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class APIResponse
 * @package App\Tools
 */
class APIResponse
{
    const ERROR_KEY = 'error';

    const MSG_KEY = 'message';


    /**
     * @param $data
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    public static function success($data, int $statusCode = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return static::generateResponse($data, $statusCode, $headers);
    }

    /**
     * @param string $errorCode
     * @param string|array $msg
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    public static function error(
        $errorCode,
        $msg,
        int $statusCode = Response::HTTP_BAD_REQUEST,
        $headers = []
    ): JsonResponse {

        return static::generateResponse(
            [
                static::ERROR_KEY => $errorCode,
                static::MSG_KEY => $msg
            ],
            $statusCode,
            $headers
        );
    }

    /**
     * @param $data
     * @param int $statusCode
     * @param $headers
     * @return JsonResponse
     */
    private static function generateResponse($data, int $statusCode, $headers): JsonResponse
    {
        $response = response()->json($data, $statusCode);

        if (!empty($headers)) {
            $response->withHeaders($headers);
        }

        return $response;
    }
}
