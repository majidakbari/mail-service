<?php

namespace App\Exceptions\HttpException;

use App\Exceptions\HTTPException\Abstraction\HTTPException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InvalidAcceptHeaderException
 * @package App\Exceptions\HttpException
 */
class InvalidAcceptHeaderException extends HttpException
{
    /**
     * InvalidAcceptHeaderException constructor.
     */
    public function __construct()
    {
        parent::__construct(Response::HTTP_NOT_ACCEPTABLE);
    }
}
