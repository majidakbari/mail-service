<?php

namespace App\Http\Middleware;

use App\Exceptions\HttpException\InvalidAcceptHeaderException;
use Closure;
use Illuminate\Http\Request;

/**
 * Class OnlyJsonResponseMiddleware
 * @package App\Middleware
 */
class OnlyJsonResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!in_array($request->header('accept'), ['*/*', 'application/json'])){
            throw new InvalidAcceptHeaderException();
        }
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
