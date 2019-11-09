<?php

namespace App\Exceptions\HTTPException\Abstraction;

/**
 * Class HTTPException
 * @package App\Exceptions\HTTPException
 */
abstract class HTTPException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $error;

    /**
     * HttpException constructor.
     * @param int $statusCode
     */
    public function __construct(int $statusCode)
    {
        $error = get_class_name($this);

        $this->error = $error;

        parent::__construct(
            trans("app.$error"),
            $statusCode,
            null
        );
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
