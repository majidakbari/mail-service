<?php

namespace App\Exceptions;

use App\Tools\APIResponse;
use App\Exceptions\HTTPException\Abstraction\HTTPException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HTTPResponse;
use Symfony\Component\HttpKernel\Exception\HttpException as SymfonyHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @var array
     */
    protected $appExceptions = [
        NotFoundHttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        HttpResponseException::class,
        MethodNotAllowedHttpException::class,
        AuthenticationException::class,
        UnauthorizedException::class,
        SymfonyHttpException::class,
        ThrottleRequestsException::class,
        Exception::class
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  \Exception  $exception
     * @return JsonResponse|Response|HTTPResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof HttpException) {
            return $this->generateJsonResponseForHttpExceptions($exception);
        }
        if (in_array(get_class($exception), $this->appExceptions)) {
            return $this->generateJsonResponseForAppExceptions($exception);
        }

        return parent::render($request, $exception);
    }


    /**
     * @param HttpException $e
     * @return JsonResponse
     */
    public function generateJsonResponseForHttpExceptions(HttpException $e)
    {
        return APIResponse::error(
            $e->getError(),
            $e->getMessage(),
            $e->getCode()
        );
    }


    /**
     * @param $e
     * @return JsonResponse
     */
    public function generateJsonResponseForAppExceptions(\Exception $e)
    {
        $class = get_class($e);

        switch ($class) {
            case NotFoundHttpException::class:
            case ModelNotFoundException::class:
                $statusCode = HTTPResponse::HTTP_NOT_FOUND;
                break;
            case ValidationException::class:
                /** @var ValidationException $e */
                $statusCode = HTTPResponse::HTTP_UNPROCESSABLE_ENTITY;
                $msg = $e->validator->errors();
                break;
            case HttpResponseException::class:
                $statusCode = HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
                break;
            case MethodNotAllowedHttpException::class:
                $statusCode = HTTPResponse::HTTP_METHOD_NOT_ALLOWED;
                break;
            case AuthenticationException::class:
                $statusCode = HTTPResponse::HTTP_UNAUTHORIZED;
                break;
            case UnauthorizedException::class:
                $statusCode = HTTPResponse::HTTP_FORBIDDEN;
                $msg = ($e->getMessage()) ?? trans('app.' . get_class_name($e));
                break;
            case ThrottleRequestsException::class:
                $statusCode = HTTPResponse::HTTP_TOO_MANY_REQUESTS;
                break;
            default:
                $statusCode = HTTPResponse::HTTP_INTERNAL_SERVER_ERROR;
                $msg = 'Error';
        }


        return APIResponse::error(
            get_class_name($e),
            $msg ?? trans('app.' . get_class_name($e)),
            $statusCode
        );
    }
}
