<?php

namespace App\Http\Controllers\Log;

use App\Http\Requests\Log\IndexLogsRequest;
use App\Interfaces\LogRepositoryInterface;

/**
 * Class IndexLogsAction
 * @package App\Http\Controllers\Log
 */
class IndexLogsAction
{
    /**
     * @var LogRepositoryInterface
     */
    private $logRepository;

    /**
     * IndexLogsAction constructor.
     * @param LogRepositoryInterface $logRepository
     */
    public function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function __invoke(IndexLogsRequest $request)
    {
        dd($request);
        // TODO: Implement __invoke() method.
    }

}
